<?php

include_once __DIR__ . '/inc/class-cmd-menus.php';

$cmd = new MenusCmd('dump', $_GET);
$cmd->execute();
