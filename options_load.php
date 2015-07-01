<?php

include_once __DIR__ . '/inc/class-cmd-options.php';

$cmd = new OptionsCmd('load', array_merge($_GET, $_POST));
$cmd->execute();
