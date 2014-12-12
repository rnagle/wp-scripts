<?php

// Load WordPress
require(dirname(dirname(__DIR__)) . '/wp-blog-header.php');

if (!is_user_logged_in()) {
	header('HTTP/1.0 403 Forbidden', true, 403);
	print "Forbidden.";
	wp_die();
}
