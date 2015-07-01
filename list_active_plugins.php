<?php

include_once __DIR__ . '/inc/class-cmd-list-active-plugins.php';

$cmd = new ListActivePluginsCmd($_GET);
$cmd->execute();
