<?php

class WPScriptsArguments extends \cli\Arguments {
	function parseRemainder() {
		$invalid_args = $this->getInvalidArguments();
		$even = array();
		$odd = array();
		foreach ($invalid_args as $idx => $val) {
			if ($idx % 2 != 0)
				array_push($odd, str_replace('-', '', $val));
			else
				array_push($even, str_replace('-', '', $val));
		}
		return array_combine($even, $odd);
	}
}
