<?php

include_once __DIR__ . '/class-cmd.php';

class SidebarsCmd extends WPScriptCmd {

	function main() {
		if ($this->action == 'dump')
			return $this->dump();
		if ($this->action == 'load')
			return $this->load();
	}

	function dump() {
		if (!empty($this->blog_id))
			switch_to_blog($this->blog_id);

		$data = array(
			'sidebars' => get_option('sidebars_widgets', array()),
			'widgets' => array()
		);

		foreach ($data['sidebars'] as $sidebar => $widgets) {
			foreach ($widgets as $widget) {
				$basename = $this->get_widget_basename($widget);
				$number = $this->get_widget_number($widget);
				if (!empty($basename)) {
					$widget_option = get_option('widget_' . $basename, false);
					$data['widgets'][$widget] = $widget_option[$number];
				}
			}
		}
		$ret = json_encode($data);

		if (!empty($this->blog_id))
			restore_current_blog();

		return $ret;
	}

	function load() {
		if (!empty($this->blog_id))
			switch_to_blog($this->blog_id);

		$json = json_decode(stripslashes($this->json), true, 2048);

		$sidebars = $json['sidebars'];
		$widgets = $json['widgets'];

		if (empty($sidebars) || empty($widgets)) {
			throw Exception("Invalid sidebar data.");
		}

		foreach ($widgets as $label => $values) {
			$basename = $this->get_widget_basename($label);
			$number = $this->get_widget_number($label);
			$options = get_option('widget_' . $basename, array());
			$options[(int)$number] = $values;
			update_option('widget_' . $basename, $options);
		}

		update_option('sidebars_widgets', $sidebars);

		if (!empty($this->blog_id))
			restore_current_blog();
	}

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

}
