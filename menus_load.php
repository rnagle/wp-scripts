<?php

include_once __DIR__ . '/inc/class-cmd-menus.php';

$cmd = new MenusCmd('load', array_merge($_GET, $_POST));
$cmd->execute();
