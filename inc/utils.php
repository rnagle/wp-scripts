<?php

function get_widget_basename($slug) {
	if (preg_match('/^(.*)\-\d+$/', $slug, $matches)) {
		return $matches[1];
	}
	return false;
}

function get_widget_number($slug) {
	if (preg_match('/^.*\-(\d+)$/', $slug, $matches)) {
		return $matches[1];
	}
	return false;
}
