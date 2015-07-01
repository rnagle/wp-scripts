<?php

if (file_exists(dirname(__DIR__) . '/acl.php')) {
	include dirname(__DIR__) . '/acl.php';

	foreach ($ACL as $cidr) {
		if (!cidr_match($_SERVER['REMOTE_ADDR'], $cidr)) {
			header('HTTP/1.0 403 Forbidden', true, 403);
			print "Forbidden.";
			die();
		}
	}

	function cidr_match($ip, $cidr) {
		list($subnet, $mask) = explode('/', $cidr);
		if ((ip2long($ip) & ~((1 << (32 - $mask)) - 1) ) == ip2long($subnet))
			return true;
		return false;
	}
}
