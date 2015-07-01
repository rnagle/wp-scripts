<?php

include_once __DIR__ . '/inc/class-cmd-sidebars.php';

$cmd = new SidebarsCmd('load', array_merge($_GET, $_POST));
$cmd->execute();
