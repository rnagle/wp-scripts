<?php

include_once __DIR__ . '/class-cmd.php';

class OptionsCmd extends WPScriptCmd {

	function main() {
		if ($this->action == 'dump')
			return $this->dump();
		if ($this->action == 'load')
			return $this->load();
	}

	function dump() {
		if (!empty($this->blog_id))
			switch_to_blog($this->blog_id);

		$all_options = wp_load_alloptions();
		$options = array();
		foreach( $all_options as $name => $value ) {
			if(!stristr($name, '_transient'))
				$options[$name] = $value;
		}

		$ret = json_encode($options);

		if (!empty($this->blog_id))
			restore_current_blog();

		return $ret;
	}

	function load() {
		if (!empty($this->blog_id))
			switch_to_blog($this->blog_id);

		$options = json_decode(stripslashes($_POST['json']), true, 2048);

		foreach ($options as $option => $value)
			update_option($option, maybe_unserialize($value));

		if (!empty($this->blog_id))
			restore_current_blog();
	}

}
