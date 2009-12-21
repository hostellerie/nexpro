<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexScan Plugin v1.0.0 for the nexPro Portal Server                        |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Eric de la Chevrotiere - Eric DOT delaChevrotiere AT nextide DOT ca       |
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

$langfile = $_CONF['path'] . 'plugins/nexscan/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile))
{
    include_once ($langfile);
}
else
{
    include_once ($_CONF['path'] . 'plugins/nexscan/language/english.php');
}

require_once ($_CONF['path'] . 'plugins/nexscan/nexscan.php');
require_once ($_CONF['path'] . 'plugins/nexscan/autouninstall.php');


/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_nexscan()
{
    global $_CONF;

    return $_CONF['site_admin_url'] . '/plugins/nexscan/images/nexscan.gif';
}



/**
* returns the main option for this plugin

function plugin_getmenuitems_nexscan()
{
    global $_TABLES, $_CONF, $LANG_NS01;
    $menuitems = array();

    if (SEC_inGroup('Logged-in Users')) {
        $menuitems[$LANG_NS01['headermenu']] = $_CONF['site_url'] . '/nexscan/index.php';
    }

    return $menuitems;
}*/

/**
* returns the user option for this plugin

function plugin_getuseroption_nexscan()
{
    global $_TABLES, $_CONF, $LANG_NS01;

    if (SEC_inGroup('Logged-in Users')) {
        return array($LANG_NS01['useradmintitle'], $_CONF['site_admin_url'] . '/plugins/nexscan/index.php', '');
    }

}*/

/**
* returns the administrative option for this plugin
*/
function plugin_getadminoption_nexscan()
{
    global $_TABLES, $_CONF, $LANG_NS01;

    if (SEC_inGroup('Root')) {
        return array($LANG_NS01['adminmenutitle'], $_CONF['site_admin_url'] . '/plugins/nexscan/index.php');
    }

}

/**
* returns the administrative option for this plugin
*/
function plugin_cclabel_nexscan()
{
    global $_TABLES, $_CONF,$LANG_NS01;

    if (SEC_inGroup('Root')) {
        return array($LANG_NS01['adminmenutitle'],$_CONF['site_admin_url'] . "/plugins/nexscan/index.php",$_CONF['site_admin_url'] . '/plugins/nexscan/images/nexscan.gif');
    }

}

/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getHeaderCode_nexscan()
{
    global $_CONF;

    $code ='';
    return $code;
}


/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexscan() {
    global $CONF_NS;
    return $CONF_NS['version'];
}



?>