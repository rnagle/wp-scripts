<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
	print "Invalid request method.";

include_once dirname(__FILE__) . '/inc/load.php';
include_once dirname(__FILE__) . '/inc/utils.php';

function main() {
	header('HTTP/1.1 200 OK', true, 200);

	if (!empty($_GET['blog_id']))
		switch_to_blog($_GET['blog_id']);

	$options = json_decode(stripslashes($_POST['json']), true, 2048);

	foreach ($options as $option => $value)
		update_option($option, maybe_unserialize($value));

	if (!empty($_GET['blog_id']))
		restore_current_blog();

	die();
}

main();
