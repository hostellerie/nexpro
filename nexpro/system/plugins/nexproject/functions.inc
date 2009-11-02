<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.3 for the nexPro Portal Server                     |
// | Sept. 20, 2009                                                            |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2009 by the following authors:                         |
// | Ted Clark              - Support@nextide.ca                               |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
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

$langfile = $_CONF['path'] . 'plugins/nexproject/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    include_once ($langfile);
} else {
    include_once ($_CONF['path'] . 'plugins/nexproject/language/english.php');
}

require_once ($_CONF['path'] . 'plugins/nexproject/nexproject.php');
require_once ($_CONF['path'] . 'plugins/nexproject/autouninstall.php');
require_once ($_CONF['path_html'] .'/jscalendar/calendar.php');

/**
 * Returns the items for this plugin that should appear on the main menu
 */
function plugin_getmenuitems_nexproject()
{
    global $_TABLES, $_CONF;
    if (SEC_inGroup('Logged-in Users')) {
        $menuitems['All Projects'] = "{$_CONF['site_url']}/nexproject/projects.php";
        return $menuitems;
    }
}

/**
 * This will put an option for projects admin in the command and control block on moderation.php
 */
function plugin_cclabel_nexproject()
{
    global $_CONF;
    //there is no global/overall nexproject admin options for this plugin.  This is a stub function for any future requirements.

}


//returns the project icon for the project installation routine
function plugin_geticon_nexproject(){
    global $_CONF;

    return $_CONF['site_url'] . '/nexproject/images/nexproject.gif';

}


function plugin_getuseroption_nexproject()
{
    global $_TABLES, $_CONF;

    $menuitems = array(
        array('My Projects', "{$_CONF['site_url']}/nexproject/index.php"),
        array('Custom Project Filters', "{$_CONF['site_url']}/nexproject/filters.php")
    );
    return $menuitems;
    //return array('Custom Project Filters', $_CONF['site_url'] . '/nexproject/filters.php');
}



/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getHeaderCode_nexproject()
{
    global $_CONF, $_PRJCONF;

    $code ='';

    $code .= "\n<!-- Begin Javascript includes for Projects Plugin -->\n";
    $code .= "<script type=\"text/javascript\" src=\"".$_CONF['site_url'] ."/nexproject/javascript/nexproject.js\"></script>\n";
    $code .= "<link rel=\"stylesheet\" href=\"".$_CONF['site_url'] ."/layout/". $_CONF['theme'] ."/nexproject/stylesheet.css\"type=\"text/css\">\n";

    $code .= "<!-- END Javascript includes for Projects Plugin -->\n";
    return $code;
}



/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexproject() {
    global $_PRJCONF;
    return $_PRJCONF['version'];
}






function plugin_upgrade_nexproject() {
    global $_TABLES,$_PRJCONF;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexproject'");

    switch ($curversion) {
    case "2.0.0":
    case "2.0.1":
    case "2.0.2":
    case "2.0.3":
        if (nexproject_upgrade_210() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.1.0', `pi_gl_version` = '1.5.1' WHERE `pi_name` = 'nexproject' LIMIT 1");
        }
        break;
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexproject'") == $_PRJCONF['version']) {
        COM_errorlog("nexProject updated successfully.");
        return true;
    } else {
        COM_errorlog("nexProject was not updated to verion 2.1.0.  Please check any errors before retrying.");
        return false;
    }
}

function phpblock_projectFilter()
{
    global $_CONF, $_TABLES, $_USER, $strings;
    require_once($_CONF['path_system'] . 'nexpro/classes/TreeMenu.php');
    $retval = '';
    if ($_USER['uid'] > 1) {
        $menu = new HTML_TreeMenu();
        $baseurl = $_CONF['site_url'];
        $imagesdir = "$baseurl/nexproject/images";
        $folder_icon = "folder.gif";
        $msg = "";
        include ($_CONF['path_html'] . '/nexproject/filterbrowser.php');
    }
    return $retval;
}

function prg_updateAuditLog($logentry)
{
    if (!prj_nexFileExists()) {
        return false;
    }
    global $_USER, $_TABLES;
    if (isset($_USER['uid']) AND $_USER['uid'] != "") {
        $userid = $_USER['uid'];
    } else {
        $userid = 1;
    }
    $date = time();
    $logentry = prj_preparefordb($logentry);
    DB_query("INSERT INTO {$_TABLES['auditlog']} (uid,date,script,logentry) VALUES ('$userid', '$date','{$_SERVER['SCRIPT_NAME']}', '$logentry' )");
    return true;
}

function prj_preparefordb($str, $htmlallowed = false, $htmlfilter = false, $censor = false)
{
    if ($censor) {
        $str = COM_checkWords($str);
    }
    if ($htmlallowed) {
        if (!get_magic_quotes_gpc()) {
            if ($htmlfilter) {
                $str = addslashes(COM_checkHTML($str));
            } else {
                $str = addslashes($str);
            }
        } else {
            if ($htmlfilter) {
                // COM_checkHTML will strip slashes so I need to add them again
                $str = addslashes(COM_checkHTML($str));
            } else {
                $str = $str;
            }
        }
    } else {
        if (get_magic_quotes_gpc()) {
            $str = htmlspecialchars($str);
        } else {
            $str = addslashes(htmlspecialchars($str));
        }
        $str = str_replace("'", "&rsquo;", $str);
    }
    return $str;
}
// Callback Function for the array walk function below to apply the data filters to clean any posted data
function prj_cleanfield(&$field)
{
    if (gettype($field) == "string") {
        $field = prj_preparefordb($field);
    }
}
// Function to clean any posted data
function prj_cleandata($postarray)
{
    array_walk($postarray, 'prj_cleanfield');
    return $postarray;
}

function prj_updateSession($pid,$lastop="") {
    global $_USER,$_TABLES, $_CONF;

    if(DB_count( $_TABLES['prj_session'], 'sess_id', $_COOKIE[$_CONF['cookie_session']] )  > 0) {
        DB_query("UPDATE {$_TABLES['prj_session']} SET pid = '$pid', lastop ='$lastop' WHERE sess_id = '{$_COOKIE[$_CONF['cookie_session']]}'");
        /* Delete any other records for this user */
        if($HTTP_COOKIES_VARS[$_CONF['cookie_session']]!=''){
            DB_query("DELETE FROM {$_TABLES['prj_session']} WHERE uid = '{$_USER['uid']}' AND sess_id <> '{$HTTP_COOKIES_VARS[$_CONF['cookie_session']]}'");
        }
    } else {
        /* Delete any other records for this user */
        DB_query("DELETE FROM {$_TABLES['prj_session']} WHERE uid = '{$_USER['uid']}'");

        $time=time();
        DB_query("INSERT INTO {$_TABLES['prj_session']} (sess_id,start_time, uid, pid)
            VALUES ('{$_COOKIE[$_CONF['cookie_session']]}', '$time' , '{$_USER['uid']}', '$pid')");
    }
}


function prj_getSessionProject()
{
    global $_TABLES, $_CONF;
    if (isset($_COOKIE[$_CONF['cookie_session']])) {
        $pid = DB_getItem($_TABLES['prj_session'], "pid" , "sess_id = {$_COOKIE[$_CONF['cookie_session']]}");
        if (isset($pid) AND (DB_count($_TABLES['prj_projects'], "pid", "$pid") > 0)) {
            Return $pid;
        }
    }
    Return 0;
}

/* Clear out the session record used to track which project the user is looking at
/* Delete the user's current session record and any older records */
function prj_clrSession()
{
    global $_TABLES, $_CONF;
    DB_query("DELETE FROM {$_TABLES['prj_session']} WHERE sess_id = '{$_COOKIE[$_CONF['cookie_session']]}'");
    DB_query("DELETE FROM {$_TABLES['prj_session']} WHERE start_time < " . (time() - $_CONF['whosonline_threshold']));
    Return true;
}

/* Used to show the ProjectBrowser Block and a limited set of blocks while on the main Projects page
*  Called by COM_siteHeader to format the leftblocks
*  @param    array      $showblocks     An array of blocks (using block name) as key to show
*/
function prj_showBlocks($showblocks) {
    global $_CONF, $_TABLES;
    $retval = '';
    foreach($showblocks as $block) {
        $sql = "SELECT bid, name,type,title,content,rdfurl,phpblockfn,help FROM {$_TABLES['blocks']} WHERE name='$block' and is_enabled = '1'";
        $result = DB_query($sql);
        if (DB_numRows($result) == 1) {
            $A = DB_fetchArray($result);
            $retval .= COM_formatBlock($A);
        }
    }
    return $retval;
}

/**
* Returns an array of ids of any sub-categories for the passed in category
* Called when adding new members to a project to grant the same access to the project file repository
*
* @param        string              $cid          Parent category to start at and then recursively check
* @param        string              $cid          Used by this function to be able to append to the formatted select list
* @return       string                            Return an array of category id's
*/
function prj_recursiveCatList($cid=0,$selectlist='') {
    global $_TABLES;
    $query = DB_QUERY("SELECT cid,pid,name FROM {$_TABLES['nxfile_categories']} WHERE pid = $cid ORDER BY cid");
    $selectlist[] = $cid;
    while ( list($cid,$pid,$name) = DB_fetchArray($query)) {
        // Check and see if this category has any sub categories - where a category record has this cid as it's parent
        if (DB_COUNT($_TABLES['nxfile_categories'], 'pid', $cid) > 0)  {
            $selectlist[] = $cid;
            $selectlist = prj_recursiveCatList($cid,$selectlist);
        } else {
            $selectlist[] = $cid;
        }
    }
    return $selectlist;
}




/**
* Provide URL of a documentation file
*
* @param    string  $file   documentation file being requested, e.g. 'config'
* @return   mixed           URL or false when not available
*
*/
function plugin_getdocumentationurl_nexproject($file)
{
    global $_CONF;

    static $docurl;

    switch ($file) {
    case 'index':
    case 'config':
        if (isset($docurl)) {
            $retval = $docurl;
        } else {
            $retval = 'http://www.nextide.ca/dokuwiki/doku.php/core_applications_nexproject#configuration1';
        }
        break;

    default:
        $retval = false;
        break;
    }

    return $retval;
}

/**
* Called by the plugin Editor when a plugin's state changes
*/
function plugin_pluginstatechange_nexproject($plugin,$state) {
    global $_TABLES;

    // The nexproject plugin is dependant on the plugin nexpro being installed and enabled
    if ($plugin == 'nexpro' || $plugin=='nexlist') {
        if ($state == 'disabled' OR $state == 'uninstalled') {
            // Disable this menu as it depends on the nexpro plugin
            DB_query("UPDATE {$_TABLES['plugins']} SET pi_enabled = 0 WHERE pi_name='nexproject'");
        }
    }
}
?>