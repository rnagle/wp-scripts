<?php

include_once __DIR__ . '/inc/class-cmd-options.php';

$cmd = new OptionsCmd('dump', $_GET);
$cmd->execute();
