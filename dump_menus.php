<?php

if ($_SERVER['REQUEST_METHOD'] !== 'GET')
	print "Invalid request method.";

include_once dirname(__FILE__) . '/inc/load.php';
include_once dirname(__FILE__) . '/inc/utils.php';

function main() {
	header('HTTP/1.1 200 OK', true, 200);

	if (!empty($_GET['blog_id']))
		switch_to_blog($_GET['blog_id']);

	$menu_data = array();
	$locations = get_theme_mod('nav_menu_locations');
	foreach ($locations as $slug => $menu_id) {
		$menu_data[$slug] = wp_get_nav_menu_object($menu_id);
		if (!empty($menu_data[$slug]))
			$menu_data[$slug]->items = wp_get_nav_menu_items($menu_data[$slug]->term_id);
	}

	print json_encode($menu_data);
	die();
}

main();
