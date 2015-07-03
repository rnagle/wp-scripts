<?php

Requests::register_autoloader();

class WPScriptsControl {

	public $headers = array('User-Agent' => 'Anything goes here');

	function __construct($options, $kwargs=array()) {
		foreach ($options as $key => $val)
			$this->{$key} = $val;

		$this->kwargs = $kwargs;

		// URL-related attributes
		$this->protocol = (!empty($this->https))? 'https':'http';
		$this->base = sprintf('%s://%s/%s/%s.php',
			$this->protocol, $this->domain, $this->scripts_dir, $this->command);
		$this->query_args = http_build_query($kwargs);
		$this->url = $this->base . '?' . $this->query_args;

		// Location for the cookies file
		$this->cookies_file = sprintf('/tmp/cookies-%s.txt', $this->domain);
	}

	function execute() {
		if (!empty($this->json_data)) {
			# Make sure we have valid json before proceeding
			$handle = fopen($this->json_data, 'r+');
			$json_data = json_decode(fread($handle, filesize($this->json_data)));
			fclose($handle);

			if (json_last_error !== JSON_ERROR_NONE)
				throw new Exception('Could not parse JSON data');

			$ret = $this->post();
		} else {
			$ret = $this->get();
		}

		print $ret->body;
	}

	function authenticate() {
		echo sprintf("Enter your credentials for %s\n", $this->domain);
		$username = \cli\prompt('Username', false, ':', false);
		$password = \cli\prompt('Password', false, ':', true);

		$url = sprintf('%s://%s/wp-login.php', $this->protocol, $this->domain);

		// TODO: parse the login page to get the action url.
		// What follows is non-working pseudo-code...
		// $markup = pq($wp_login->content);
		// $form = $markup->find('form');
		// $action = $form->attr('action');

		$form_data = array(
			'log' => $username,
			'pwd' => $password,
			'submit' => 'Log in',
			'testcookie' => '1'
		);
		return Requests::post($url, $headers=$this->headers, $data=$form_data);
	}

	function post($data=false, $json=false) {
		$options = array();
		if (!$this->authenticated()) {
			$ret = $this->authenticate();
			$this->save_cookies($ret->cookies);
			$options['cookies'] = $ret->cookies;
		} else {
			$options['cookies'] = $this->load_cookies();
		}

		return Requests::post($this->url, $data=$data, $json=$json);
	}

	function get() {
		if (!$this->authenticated()) {
			$ret = $this->authenticate();
			$options['cookies'] = $ret->cookies;
			$this->save_cookies($ret->cookies);
		} else
			$options['cookies'] = $this->load_cookies();

		return Requests::get($this->url, $headers=$this->headers, $options=$options);
	}

	function authenticated() {
		$url = sprintf('%s://%s/wp-admin/', $this->protocol, $this->domain);
		$options = array(
			'cookies' => $this->load_cookies()
		);
		$ret = Requests::get($url, $headers=$this->headers, $options=$options);
		if ($ret->redirects > 0) {
			if (in_array($ret->history[0]->status_code, array(301, 302)))
				return false;
		} else if ($ret->status_code == 200)
            return true;
	}

	function load_cookies() {
		$cookies = false;

		if (file_exists($this->cookies_file)) {
			$handle = fopen($this->cookies_file, 'r');
			$fsize = filesize($this->cookies_file);
			if ($fsize > 0) {
				$contents = fread($handle, $fsize);
				$cookies = unserialize($contents);
			}
			fclose($handle);
		}

		return $cookies;
	}

	function save_cookies($cookie_jar) {
		$handle = fopen($this->cookies_file, 'w+');
		fwrite($handle, serialize($cookie_jar));
		fclose($handle);
	}

}
