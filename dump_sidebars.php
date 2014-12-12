<?php

if ($_SERVER['REQUEST_METHOD'] !== 'GET')
	print "Invalid request method.";

include_once dirname(__FILE__) . '/inc/load.php';
include_once dirname(__FILE__) . '/inc/utils.php';

function main() {
	header('HTTP/1.1 200 OK', true, 200);

	if (!empty($_GET['blog_id']))
		switch_to_blog($_GET['blog_id']);

	$data = array(
		'sidebars' => get_option('sidebars_widgets', array()),
		'widgets' => array()
	);

	foreach ($data['sidebars'] as $sidebar => $widgets) {
		foreach ($widgets as $widget) {
			$basename = get_widget_basename($widget);
			$number = get_widget_number($widget);
			if (!empty($basename)) {
				$widget_option = get_option('widget_' . $basename, false);
				$data['widgets'][$widget] = $widget_option[$number];
			}
		}
	}
	print json_encode($data);

	if (!empty($_GET['blog_id']))
		switch_to_blog($_GET['blog_id']);
}

main();
