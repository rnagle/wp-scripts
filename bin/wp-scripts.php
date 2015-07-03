<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

if (php_sapi_name() == 'cli') {

	include_once dirname(__DIR__) . '/inc/cli/class-wp-scripts-arguments.php';
	include_once dirname(__DIR__) . '/inc/cli/class-wp-scripts-control.php';

	$strict = in_array('--strict', $_SERVER['argv']);
	$args = new WPScriptsArguments(compact('strict'));

	$args->addFlag(array('help', 'h'), 'Show this help screen');
	$args->addFlag(array('https', 's'), 'Use HTTPS');

	$args->addOption(array('command', 'c'), 'The slug of the script/command to run.');
	$args->addOption(array('json_data', 'j'), 'Location of json file with data to be sent over the wire.');
	$args->addOption(array('output', 'o'), 'Write the script/command response to a file.');
	$args->addOption(array('domain', 'd'), 'Domain name of the site on which to run the script/command.');
	$args->addOption(array('scripts_dir', 's'), array(
		'default' => 'wp-scripts',
		'description' => 'Domain name of the site on which to run the script/command.'
	));

	$args->parse();

	if ($args['help']) {
		echo $args->getHelpScreen() . "\n\n";
		exit();
	}

	$options = array_merge(array('scripts_dir' => 'wp-scripts'), $args->getArguments());

	if (!in_array('domain', array_keys($options)))
		die("The domain argument is required.");
	if (!in_array('command', array_keys($options)))
		die("The command argument is required.");

	$control = new WPScriptsControl($options, $args->parseRemainder());
	$control->execute();
} else
	die('Must run from the command line.');
