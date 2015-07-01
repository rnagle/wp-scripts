<?php

include_once __DIR__ . '/class-cmd.php';

class MenusCmd extends WPScriptCmd {

	function main() {
		if ($this->action == 'dump')
			return $this->dump();
		if ($this->action == 'load')
			return $this->load();
	}

	function load() {
		if (!empty($this->blog_id))
			switch_to_blog($this->blog_id);

		$locations = json_decode(stripslashes($this->json), true, 2048);
		$nav_menu_locations = get_theme_mod('nav_menu_locations');

		foreach ($nav_menu_locations as $slug => $id) {
			wp_delete_nav_menu($id);
		}

		foreach ($locations as $slug => $menu) {
			if (empty($menu))
				continue;

			wp_delete_nav_menu($menu['slug']);

			$new_menu_obj = array(
				'taxonomy' => $menu['taxonomy'],
				'slug' => $menu['slug'],
				'menu-name' => $menu['name'],
				'parent' => $menu['parent'],
				'term_group' => $menu['term_group'],
				'description' => $menu['description']
			);

			$mid = wp_update_nav_menu_object(0, $new_menu_obj);

			$nav_menu_locations[$slug] = $mid;

			foreach ($menu['items'] as $item) {
				$mitem = array(
					'menu-item-db-id' => 0,
					'menu-item-object-id' => $item['object_id'],
					'menu-item-object' => $item['object'],
					'menu-item-parent-id' => $item['menu_item_parent'],
					'menu-item-position' => 0,
					'menu-item-title' => $item['title'],
					'menu-item-url' => $item['url'],
					'menu-item-description' => $item['description'],
					'menu-item-attr-title' => $item['attr_title'],
					'menu-item-target' => $item['attr_title'],
					'menu-item-classes' => $item['classes'],
					'menu-item-xfn' => $item['xfn'],
					'menu-item-status' => $item['post_status'],
					'menu-item-type' => $item['type']
				);
				$ret = wp_update_nav_menu_item($mid, 0, $mitem);
			}
		}

		set_theme_mod('nav_menu_locations', $nav_menu_locations);
	}

	function dump() {
		if (!empty($this->blog_id))
			switch_to_blog($this->blog_id);

		$menu_data = array();
		$locations = get_theme_mod('nav_menu_locations');
		foreach ($locations as $slug => $menu_id) {
			$menu_data[$slug] = wp_get_nav_menu_object($menu_id);
			if (!empty($menu_data[$slug]))
				$menu_data[$slug]->items = wp_get_nav_menu_items($menu_data[$slug]->term_id);
		}

		return json_encode($menu_data);
	}

}
