<?php

include_once __DIR__ . '/class-cmd.php';

class ListActivePluginsCmd extends WPScriptCmd {

	function __construct($attributes) {
		parent::__construct(null, $attributes);
	}

	function main() {
		global $wpdb;

		if (!empty($this->blog_id))
			$site_blog_ids = array((object) array('blog_id' => $this->blog_id));
		else
			$site_blog_ids = $wpdb->get_results($wpdb->prepare(
				"SELECT blog_id FROM wp_blogs where blog_id > 1"));

		$ret = '';
		foreach ($site_blog_ids as $b) {
			switch_to_blog($b->blog_id);
			$plugins = get_option('active_plugins');

			if ($this->type == 'html') {
				$ret .= "<div style='margin:20px 0;'>";
				$ret .= "<h2>" . get_bloginfo('name') . "</h2>";
				$ret .= "<ul>";
			} else
				$ret .= "" . get_bloginfo('name') . "\n";

			foreach ($plugins as $plugin) {
				if ($this->type == 'html')
					$ret .= "<li>" . $plugin . "</li>";
				else
					$ret .= "  " . $plugin . "\n";
			}

			if ($this->type == 'html') {
				$ret .= "</ul>";
				$ret .= "</div>";
			} else
				$ret .= "\n";

			restore_current_blog();
		}

		return $ret;
	}
}
