<?php
// +---------------------------------------------------------------------------+
// | CAPTCHA v3 Plugin                                                         |
// +---------------------------------------------------------------------------+
// | $Id: install.php,v 1.2 2007/09/12 18:02:48 eric Exp $|
// | install.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007 by the following authors:                              |
// |                                                                           |
// | Author: Mark R. Evans - mevans@ecsnet.com                                 |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+
//
require_once('../../../lib-common.php');
require_once($_CONF['path'] . '/plugins/captcha/config.php');
require_once($_CONF['path'] . '/plugins/captcha/functions.inc');

$pi_name = 'captcha';                       // Plugin name  Must be 15 chars or less
$pi_version = $_CP_CONF['version'];         // Plugin Version
$gl_version = '1.4.1';                      // GL Version plugin for
$pi_url = 'http://www.gllabs.org';    // Plugin Homepage

// Only let Root users access this page
if (!SEC_inGroup('Root')) {
    // Someone is trying to illegally access this page
    COM_errorLog("Someone has tried to illegally access the CAPTCHA install/uninstall page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
    $display = COM_siteHeader();
    $display .= COM_startBlock($LANG_CP00['access_denied']);
    $display .= $LANG_CP00['access_denied_msg'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
}

function plugin_install_captcha()
{
    global $pi_name, $pi_version, $gl_version, $pi_url, $NEWTABLE, $DEFVALUES, $NEWFEATURE;
    global $_TABLES, $_CONF, $LANG_CP00, $_DB_dbms;

    COM_errorLog("Attempting to install the $pi_name Plugin",1);

    $_SQL['cp_config'] =
        "CREATE TABLE {$_TABLES['cp_config']} ( " .
        "  `config_name` varchar(255) NOT NULL default '', " .
        "  `config_value` varchar(255) NOT NULL default '', " .
        "   PRIMARY KEY  (`config_name`) " .
        " );";
    $_SQL['cp_sessions'] =
        "CREATE TABLE {$_TABLES['cp_sessions']} ( " .
        "  `session_id` varchar(40) NOT NULL default '', " .
        "  `cptime`  INT(11) NOT NULL default 0, " .
        "  `validation` varchar(40) NOT NULL default '', " .
        "  `counter`    TINYINT(4) NOT NULL default 0, " .
        "  PRIMARY KEY (`session_id`) " .
        " );";

    foreach ($_SQL as $table => $sql) {
        COM_errorLog("Creating $table table",1);
        DB_query($sql,1);
        if (DB_error()) {
            COM_errorLog("Error Creating $table table",1);
            plugin_uninstall_captcha();
            return false;
            exit;
        }
        COM_errorLog("Success - Created $table table",1);
    }

    $SQL_DEFAULTS = "INSERT INTO `{$_TABLES['cp_config']}` (`config_name`, `config_value`) VALUES " .
                    " ('anonymous_only', '1'), " .
                    " ('remoteusers','0'), " .
                    " ('debug', '0'), " .
                    " ('enable_comment', '0'), " .
                    " ('enable_contact', '0'), " .
                    " ('enable_emailstory', '0'), " .
                    " ('enable_forum', '0'), " .
                    " ('enable_registration', '0'), " .
                    " ('enable_story', '0'), " .
                    " ('gfxDriver', '2'), " .
                    " ('gfxFormat', 'jpg'), " .
                    " ('gfxPath', '');";

    DB_query($SQL_DEFAULTS,1);

    // Register the plugin with Geeklog
    COM_errorLog("Registering $pi_name plugin with Geeklog", 1);
    DB_delete($_TABLES['plugins'],'pi_name','captcha');
    DB_query("INSERT INTO {$_TABLES['plugins']} (pi_name, pi_version, pi_gl_version, pi_homepage, pi_enabled) "
        . "VALUES ('$pi_name', '$pi_version', '$gl_version', '$pi_url', 1)");

    if (DB_error()) {
        COM_errorLog("Failure registering plugin with Geeklog");
        plugin_uninstall_captcha();
        return false;
        exit;
    }

    // Create initial log entry
    CAPTCHA_errorLog("CAPTCHA Plugin Successfully Installed");

    COM_errorLog("Successfully installed the $pi_name Plugin!",1);
    return true;
}

/*
* Main Function
*/

$action = COM_applyFilter($_POST['action']);

$display = COM_siteHeader();
$T = new Template($_CONF['path'] . 'plugins/captcha/templates');
$T->set_file('install', 'install.thtml');
$T->set_var('install_header', $LANG_CP00['install_header']);
$T->set_var('img',$_CONF['site_url'] . '/captcha/captcha.png');
$T->set_var('cgiurl', $_CONF['site_admin_url'] . '/plugins/captcha/install.php');
$T->set_var('admin_url', $_CONF['site_admin_url'] . '/plugins/captcha/index.php');

if ($action == 'install') {
    if (plugin_install_captcha()) {
        $installMsg = sprintf($LANG_CP00['install_success'],$_CONF['site_admin_url'] . '/plugins/captcha/index.php');
        $T->set_var('installmsg1',$installMsg);
    } else {
       	echo COM_refresh ($_CONF['site_admin_url'] . '/plugins.php?msg=72');
    }
} else if ($action == "uninstall") {
   plugin_uninstall_captcha('installed');
   $T->set_var('installmsg1',$LANG_CP00['uninstall_msg']);
}

if (DB_count($_TABLES['plugins'], 'pi_name', 'captcha') == 0) {
    $T->set_var('installmsg2', $LANG_CP00['uninstalled']);
    $T->set_var('readme', $LANG_CP00['readme']);
    $T->set_var('btnmsg', $LANG_CP00['install']);
    $T->set_var('action','install');

    $gl_version = VERSION;
    $php_version = phpversion();

    $glver = sprintf($LANG_CP00['geeklog_check'],$gl_version);
    $phpver = sprintf($LANG_CP00['php_check'],$php_version);
    $T->set_var(array(
        'lang_overview'     => $LANG_CP00['overview'],
        'lang_details'      => $LANG_CP00['details'],
        'cp_requirements'   => $LANG_CP00['preinstall_check'],
        'gl_version'        => $glver,
        'php_version'       => $phpver,
        'install_doc'       => $LANG_CP00['preinstall_confirm'],
    ));
} else {
    echo COM_refresh($_CONF['site_url'] . '/index.php?msg=1&amp;plugin=captcha');
    exit;
}
$T->parse('output','install');
$display .= $T->finish($T->get_var('output'));
$display .= COM_siteFooter(true);

echo $display;

?>
