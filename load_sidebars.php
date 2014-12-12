<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
	print "Invalid request method.";

include_once dirname(__FILE__) . '/inc/load.php';
include_once dirname(__FILE__) . '/inc/utils.php';

function main() {
	header('HTTP/1.1 200 OK', true, 200);

	if (!empty($_GET['blog_id']))
		switch_to_blog($_GET['blog_id']);

	$json = json_decode(stripslashes($_POST['json']), true, 2048);

	$sidebars = $json['sidebars'];
	$widgets = $json['widgets'];

	if (empty($sidebars) || empty($widgets)) {
		print "Invalid sidebar data.";
		return;
	}

	foreach ($widgets as $label => $values) {
		$basename = get_widget_basename($label);
		$number = get_widget_number($label);
		$options = get_option('widget_' . $basename, array());
		$options[(int)$number] = $values;
		update_option('widget_' . $basename, $options);
	}

	update_option('sidebars_widgets', $sidebars);

	if (!empty($_GET['blog_id']))
		restore_current_blog();
}

main();
