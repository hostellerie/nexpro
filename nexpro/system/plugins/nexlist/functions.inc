<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexList Plugin v2.2.0 for the nexPro Portal Server                        |
// | Sept. 25, 2009                                                            |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2009 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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

$langfile = $_CONF['path'] . 'plugins/nexlist/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile))
{
    include_once ($langfile);
}
else
{
    include_once ($_CONF['path'] . 'plugins/nexlist/language/english.php');
}

require_once ($_CONF['path'] . 'plugins/nexlist/nexlist.php');
require_once ($_CONF['path'] . 'plugins/nexlist/autouninstall.php');
require_once ($_CONF['path'] . 'plugins/nexlist/library.php');
require_once ($_CONF['path'] . 'plugins/nexlist/custom.php');

/**
* returns the administrative option for this plugin
*
*/
function plugin_getadminoption_nexlist()
{
    global $_TABLES, $_CONF,$LANG_LL01;

    if (SEC_hasRights('nexlist.edit')) {
        $numrecords = DB_count($_TABLES['nexlist']);
        return array($LANG_LL01['adminmenutitle'], $_CONF['site_admin_url'] . '/plugins/nexlist/index.php', $numrecords);
    }

}

/**
* returns the administrative option for this plugin
*
*/
function plugin_cclabel_nexlist()
{
    global $_TABLES, $_CONF,$LANG_LL01;

    if (SEC_hasRights('nexlist.edit')) {
        return array($LANG_LL01['adminmenupanel'],"{$_CONF['site_admin_url']}/plugins/nexlist/index.php",plugin_geticon_nexlist());
    }

}

/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_nexlist()
{
    global $_CONF;

    return $_CONF['layout_url'] . '/nexlist/images/admin/nexlist.gif';
}



/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getHeaderCode_nexlist()
{
    global $_CONF, $CONF_LL;

    $code ='';
    return $code;
}


/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexlist() {
    global $CONF_LL;
    return $CONF_LL['version'];
}

/* Example use showing the $tag array structure for the nexlist autotag function
*  [list:13,0]            - returns the selectbox html for list #13 - field 0
*  [list:13,0,79]         - returns the selectbox html for list #13 - field 0 and item 79 selected
*  [list:13,0,79,read]    - returns a single value from list #13 - item 79
*
*  Use the last autotag parm when your form has multiple selectbox dropdowns as in the editlist admin page.
*  [list:13,0,79,edit,5]  - returns the selectbox html for list #13 - field 0 and item 79 selected for row5
*
*  [alist:13,0]             - returns an array of list options for list #13, field 0
*/

function plugin_autotags_nexlist($op,$content='',$tag='') {
    global $_CONF,$_TABLES;
    if ($op == 'tagname' ) {
        // Supports standard formatted HTML list or return an array for the list values
        return array('list','alist');
    } elseif ($op == 'parse') {
        $parms = explode(',',$tag['parm1']);
        $listid = $parms[0];
        if (DB_count($_TABLES['nexlist'],'id',$listid) == 1) {
            if (isset($parms[1])) {
                $fieldnum = $parms[1];
            } else {
                $fieldnum = 0;
            }
            if (isset($parms[2])) {
                $selected = $parms[2];
            } else {
                $selected = 0;
            }
            if (isset($parms[3])) {
                $mode = $parms[3];
            } else {
                $mode = 'edit';
            }
            if (isset($parms[4])) {
                $varname  = $parms[4];  // Used to create the edit select fieldname
            } else {
                $varname = 'nexlistfield';
            }
            if ($CONF_LL['debug']) {
                COM_errorLog("functions.inc => listid: $listid, fieldnum:$fieldnum, selected:$selected,mode:$mode");
            }
            if ($tag['tag'] == 'alist') {
                return nexlistOptionList( 'alist', $varname, $listid, $fieldnum,$selected);
            } else {
                $listHTML = nexlistOptionList( $mode, $varname, $listid, $fieldnum,$selected);
                $retval = str_replace($tag['tagstr'],$listHTML,$content);
                return $retval;
            }
        } else {
            return $content;
        }
    }
}



function plugin_upgrade_nexlist() {
    global $_TABLES,$CONF_LL;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexlist'");

    switch ($curversion) {
    case "2.1.0":
    case "2.1.1":
        if (nexlist_upgrade_220() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.2.0', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexlist' LIMIT 1");
        }
        break;
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexlist'") == $CONF_LL['version']) {
        return 11;
    } else {
        return 12;
    }
}


/*
 * Nexlist function that will call out to all plugins to test if they are using this List Item
 * This function is called before a List Item is deleted
 * Pass in the item id and return a message if a plugin function is using the list item record.
 * Plugins need to create a function called nexlist_delitem_{plugin_name}
 * Plugin function needs to accept two parms - listid and item id
 * Plugin function can use the listid to quickly see if list is used
 * Plugins should return a string message or '' if not using the list item
 *
 * Note: Currently it aborts and returns if one plugin reports item is being used
*/
function nexlist_checkItemDependencies($id) {
    global $_TABLES,$_PLUGINS;

    $retval = '';
    $listid = DB_getItem($_TABLES['nexlistitems'],'lid',"id=$id");
    if ($listid > 0 AND $id > 0) {
        foreach ($_PLUGINS as $pi_name) {
            $function = 'nexlist_delitem_' . $pi_name;
            if (function_exists($function)) {
                $retval = $function($listid,$id);
                if ($retval != '') break;
            }
        }
    } else {
        $retval = "Invalid List or list id";
    }
    return $retval;
}


/*
 * Nexlist function that will call out to all plugins to test if they are using this List
 * This function is called before a List is deleted
 * Pass in the list id and return a message if a plugin function is using the list.
 * Plugins need to create a function called nexlist_deldef_{plugin_name}
 * Plugin function needs to accept one parm - listid
 * Plugins should return a string message or '' if not using the list item
 *
 * Note: Currently it aborts and returns if one plugin reports item is being used
*/
function nexlist_checkListDependencies($listid) {
    global $_TABLES,$_PLUGINS;

    $retval = '';
    if ($listid > 0) {
        foreach ($_PLUGINS as $pi_name) {
            $function = 'nexlist_deldef_' . $pi_name;
            if (function_exists($function)) {
                $retval = $function($listid);
                if ($retval != '') break;
            }
        }
    } else {
        $retval = "Invalid List";
    }
    return $retval;
}

/**
* Called by the plugin Editor when a plugin's state changes
*/
function plugin_pluginstatechange_nexlist($plugin,$state) {
    global $_TABLES;

    // The nexproject plugin is dependant on the plugin nexpro being installed and enabled
    if ($plugin == 'nexpro') {
        if ($state == 'disabled' OR $state == 'uninstalled') {
            // Disable this menu as it depends on the nexpro plugin
            DB_query("UPDATE {$_TABLES['plugins']} SET pi_enabled = 0 WHERE pi_name='nexlist'");
        }
    }
}

/**
* Provide URL of a documentation file
*
* @param    string  $file   documentation file being requested, e.g. 'config'
* @return   mixed           URL or false when not available
*
*/
function plugin_getdocumentationurl_nexlist($file)
{
    global $_CONF;

    static $docurl;

    switch ($file) {
    case 'index':
    case 'config':
        if (isset($docurl)) {
            $retval = $docurl;
        } else {
            $retval = 'http://www.nextide.ca/dokuwiki/doku.php/core_plugins_nexlist#configuration';
        }
        break;

    default:
        $retval = false;
        break;
    }

    return $retval;
}

?>