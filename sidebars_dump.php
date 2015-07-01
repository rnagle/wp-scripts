<?php

include_once __DIR__ . '/inc/class-cmd-sidebars.php';

$cmd = new SidebarsCmd('dump', $_GET);
$cmd->execute();
