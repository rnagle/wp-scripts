<?php

if ($_SERVER['REQUEST_METHOD'] !== 'GET')
	print "Invalid request method.";

include_once dirname(__FILE__) . '/inc/load.php';
include_once dirname(__FILE__) . '/inc/utils.php';

function main() {
	header('HTTP/1.1 200 OK', true, 200);

	if (!empty($_GET['blog_id']))
		switch_to_blog($_GET['blog_id']);

	$all_options = wp_load_alloptions();
	$options = array();
	foreach( $all_options as $name => $value ) {
		if(!stristr($name, '_transient'))
			$options[$name] = $value;
	}

	print json_encode($options);

	if (!empty($_GET['blog_id']))
		restore_current_blog();

	die();
}

main();
