<?php

include_once __DIR__ . '/inc/class-cmd-update-author-terms.php';

$cmd = new UpdateAuthorTermsCmd($_GET);
$cmd->execute();
