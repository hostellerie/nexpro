<?php

if (strpos ($_SERVER['PHP_SELF'], 'functions.php') !== false) {
    die ('This file can not be used on its own!');
}

$_IMAGE_TYPE = 'png';
if (!defined('XHTML')) {
    define('XHTML', ''); // change this to ' /' for XHTML
}

$_TRACE_TEMPLATES = false;


$result = DB_query ("SELECT onleft,name FROM {$_TABLES['blocks']}");
$nrows = DB_numRows ($result);
for ($i = 0; $i < $nrows; $i++) {
    $A = DB_fetchArray ($result);
        if ($A['onleft'] == 1) {
            $_BLOCK_TEMPLATE[$A['name']] = 'blockheader-left.thtml,blockfooter-left.thtml';
        } else {
            $_BLOCK_TEMPLATE[$A['name']] = 'blockheader-right.thtml,blockfooter-right.thtml';
    }
}
$_BLOCK_TEMPLATE['section_block'] = 'blockheader-listblock.thtml,blockfooter-listblock.thtml';
$_BLOCK_TEMPLATE['admin_block'] = 'blockheader-listblock.thtml,blockfooter-listblock.thtml';
if (!COM_isAnonUser()) {
    $_BLOCK_TEMPLATE['user_block'] = 'blockheader-listblock.thtml,blockfooter-listblock.thtml';
}
$_BLOCK_TEMPLATE['configmanager_block'] = 'blockheader-left.thtml,blockfooter-left.thtml';
$_BLOCK_TEMPLATE['configmanager_subblock'] = 'blockheader-left.thtml,blockfooter-left.thtml';



$_BLOCK_TEMPLATE['_msg_block'] = 'blockheader-message.thtml,blockfooter-message.thtml';
$_BLOCK_TEMPLATE['customlogin'] = 'customlogin-header.thtml,customlogin-footer.thtml';
$_BLOCK_TEMPLATE['whats_related_block'] = 'blockheader-related.thtml,blockfooter-related.thtml';
$_BLOCK_TEMPLATE['story_options_block'] = 'blockheader-related.thtml,blockfooter-related.thtml';


?>