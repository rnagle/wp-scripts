<?php


include_once __DIR__ . '/class-cmd.php';

class UpdateAuthorTermsCmd extends WPScriptCmd {

	function __construct($attributes) {
		parent::__construct(null, $attributes);
	}

	function main() {
		if (!empty($this->blog_id))
			switch_to_blog($this->blog_id);

		if (file_exists(WP_PLUGIN_DIR . '/co-authors-plus/co-authors-plus.php')) {
			include WP_PLUGIN_DIR . '/co-authors-plus/co-authors-plus.php';
			$this->update_the_authors();
		} else
			throw new Exception('The Co Authors Plus plugin is not installed');

		if (!empty($this->blog_id))
			restore_current_blog();
	}

	function update_the_authors() {
		$ret = '';

		global $coauthors_plus;

		$author_terms = get_terms( $coauthors_plus->coauthor_taxonomy, array( 'hide_empty' => false ) );
		$ret .= "Now updating " . count( $author_terms ) . " terms\n";
		foreach( $author_terms as $author_term ) {
			$old_count = $author_term->count;
			$coauthor = $coauthors_plus->get_coauthor_by( 'user_nicename', $author_term->slug );
			$coauthors_plus->update_author_term( $coauthor );
			$coauthors_plus->update_author_term_post_count( $author_term );
			wp_cache_delete( $author_term->term_id, $coauthors_plus->coauthor_taxonomy );
			$new_count = get_term_by( 'id', $author_term->term_id, $coauthors_plus->coauthor_taxonomy )->count;
			$ret .= "Term {$author_term->slug} ({$author_term->term_id}) changed from {$old_count} to {$new_count} and the description was refreshed\n";
		}
		// Create author terms for any users that don't have them
		$users = get_users();
		foreach( $users as $user ) {
			$term = $coauthors_plus->get_author_term( $user );
			if ( empty( $term ) || empty( $term->description ) ) {
				$coauthors_plus->update_author_term( $user );
				$ret .= "Created author term for {$user->user_login}\n";
			}
		}

		// And create author terms for any Guest Authors that don't have them
		if ( $coauthors_plus->is_guest_authors_enabled() && $coauthors_plus->guest_authors instanceof CoAuthors_Guest_Authors ) {
			$args = array(
				'order'             => 'ASC',
				'orderby'           => 'ID',
				'post_type'         => $coauthors_plus->guest_authors->post_type,
				'posts_per_page'    => 100,
				'paged'             => 1,
				'update_meta_cache' => false,
				'fields'            => 'ids'
			);

			$posts = new WP_Query( $args );
			$count = 0;
			$ret .= "Now inspecting or updating {$posts->found_posts} Guest Authors.\n";

			while( $posts->post_count ) {
				foreach( $posts->posts as $guest_author_id ) {
					$count++;

					$guest_author = $coauthors_plus->guest_authors->get_guest_author_by( 'ID', $guest_author_id );

					if ( ! $guest_author ) {
						$ret .= 'Failed to load guest author ' . $guest_author_id . "\n";

						continue;
					}

					$term = $coauthors_plus->get_author_term( $guest_author );

					if ( empty( $term ) || empty( $term->description ) ) {
						$coauthors_plus->update_author_term( $guest_author );

						$ret .= "Created author term for Guest Author {$guest_author->user_nicename}\n";
					}
				}

				$this->stop_the_insanity();

				$args['paged']++;
				$posts = new WP_Query( $args );
			}
		}

		$ret .= "All done\n";
	}
}
