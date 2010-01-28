<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFlow Plugin v3.1.1 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Blaine Lang            - Blaine DOT Lang AT nextide DOT ca                |
// | Randy Kolenko          - Randy DOT Kolenko AT nextide DOT ca              |
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

$langfile = $_CONF['path'] . 'plugins/nexflow/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    include_once ($langfile);
} else {
    include_once ($_CONF['path'] . 'plugins/nexflow/language/english.php');
}

require_once ($_CONF['path'] . 'plugins/nexflow/nexflow.class.php');
require_once ($_CONF['path'] . 'plugins/nexflow/library.php');
include_once ($_CONF['path'] . 'plugins/nexflow/autouninstall.php');



require_once ($_CONF['path'] . 'plugins/nexflow/nexflow.php');

if (isset($CONF_NF['max_uploadfile_size'])) {
    $CONF_NF['max_uploadfile_size'] = $CONF_NF['max_uploadfile_size'] * 1024 * 1024;
}


/* Include the base nextide provided workflow functions as well as all custom function library files */
$customFunctionsDir = $_CONF['path'] .'plugins/nexflow/custom_functions/';

if (is_dir($customFunctionsDir)) {
   if ($dh = opendir($customFunctionsDir)) {
       while (($file = readdir($dh)) !== false) {
           if (filetype($customFunctionsDir . $file) == 'file' && strpos($file, '.php')) {
                include_once ($customFunctionsDir . $file);
           }
       }
       closedir($dh);
   }
}



/* Used to run the nexflow Orchestrator manually by the plugin
* Set the site config.php parm for the interval to something like 20, 30 or 60 seconds
* This function will then be called to complete all possible workflow queue tasks
*/
function plugin_runScheduledTask_nexflow() {
    global $CONF_NF;

    /* Check and see if we need to run the workflow Orchesrtator or if it's auto-scheduled by the Server */
    if (!$CONF_NF['orchestrator_using_cron']) {
        $processlist = array();

        $nfclass = new nexflow();
        $nfclass->clean_queue();

        //$nfclass->set_debug(true);
        $retval = nf_getListofUncompletedTasks(&$processlist);
        $taskcount = $retval['count'];
        $processlist = $retval['list'];
        $i = 1;

        // Loop thru the workflow orchestrato while we have new un-completed tasks
        while($taskcount > 0){
            if ($nfclass->_debug ) {
                COM_errorLog("Orchestrator: Loop:$i, $taskcount un-completed tasks found");
            }
            $nfclass->clean_queue();
            $retval = nf_getListofUncompletedTasks(&$processlist);
            $taskcount = $retval['count'];
            $processlist = $retval['list'];
            $i++;
        }
    }
}

function plugin_getuseroption_nexflow() {
    global $_CONF, $_USER, $CONF_NF, $LANG_NF00;

    if ( $_USER['uid'] > 1 AND !$CONF_NF['taskconsolepermrequired']) {
       return array ($LANG_NF00['useradminmenu'], $_CONF['site_url'] . '/nexflow/index.php');
    } elseif (SEC_hasRights('nexflow.user')) {
       return array ($LANG_NF00['useradminmenu'], $_CONF['site_url'] . '/nexflow/index.php');
    }
}



function plugin_getadminoption_nexflow() {
    global $_CONF, $_TABLES, $LANG_NF00;
    if(SEC_hasRights('nexflow.admin')){
        return array ($LANG_NF00['adminmenu'], $_CONF['site_admin_url'] . '/plugins/nexflow/templates.php');
    }
}


/**
* This will put an option for nexfile admin in the command and control block on moderation.php
*
*/
function plugin_cclabel_nexflow()
{
    global $_CONF,$LANG_NF00;
    if (SEC_hasRights('nexflow.admin')) {
        return array($LANG_NF00['adminmenu'],$_CONF['site_admin_url'] . "/plugins/nexflow/templates.php",plugin_geticon_nexflow());
    }
}

/**
* Returns the URL of the plugin's icon
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_nexflow()
{
    global $_CONF;

    return $_CONF['layout_url'] .'/nexflow/images/admin/nexflow.gif';
}




/**
* Called if user is created - Adds a default userpref record which is used when member edits account preferences
*/
function plugin_user_create_nexflow ($uid)
{
    global $_TABLES;
    DB_query ("INSERT INTO {$_TABLES['nf_useraway']} (uid) VALUES ('{$uid}')");
}


/**
* Called if admin deletes a user - remove all user messages as well
*/
function plugin_user_delete_nexflow ($uid)
{
    global $_TABLES;

    // Delete the user preference record
    DB_query ("DELETE FROM {$_TABLES['nf_useraway']} WHERE uid = '{$uid}'");

}

function plugin_profilevariablesedit_nexflow($uid, &$template)
{
    global $_CONF, $_TABLES, $LANG_NF01;

    $p = new Template($_CONF['path_layout'] . '/nexflow/userprefs');
    $p->set_file ('prefs', 'awaysettings_edit.thtml');
    $p->set_var('layout_url',$_CONF['layout_url']);

    $query = DB_query ("SELECT away_start,away_return,reassign_uid,reason,is_active FROM {$_TABLES['nf_useraway']} WHERE uid = $uid");
    list ($datestart,$datereturn,$reassign_uid,$reason,$is_active) = DB_fetchArray ($query);

    if ($datestart == 0) $datestart = time();
    if ($datereturn == 0) $datereturn = time();
    if ($is_active) {
        $p->set_var('chk_awayactive', 'CHECKED');
    } else {
        $p->set_var('chk_awayactive', '');
    }

    $p->set_var ('away_sdate', strftime('%m-%d-%Y',$datestart));
    $p->set_var ('away_rdate',  strftime('%m-%d-%Y',$datereturn));
    $p->set_var ('away_reassignoptions', nf_listUsers($reassign_uid));
    $p->set_var ('away_reason', $reason);

    $p->set_var('lang_awaytitle', $LANG_NF01['title']);
    $p->set_var ('lang_awaylabel', $LANG_NF01['label']);
    $p->set_var ('lang_awayfrom', $LANG_NF01['from']);
    $p->set_var ('lang_awayto', $LANG_NF01['to']);
    $p->set_var ('lang_awayreason', $LANG_NF01['reason']);
    $p->set_var ('lang_selectowner', $LANG_NF01['selectowner']);
    $p->set_var ('lang_awayowner', $LANG_NF01['owner']);
    $p->set_var ('lang_awayactive', $LANG_NF01['active']);
    $template->set_var('out_of_office_fields',$p->finish($p->parse ('output', 'prefs')));

}

function plugin_profilevariablesdisplay_nexflow($uid, &$template)
{
    global $_CONF,$_TABLES, $LANG_NF01;

    $query = DB_query ("SELECT away_start,away_return,reassign_uid,reason,is_active FROM {$_TABLES['nf_useraway']} WHERE uid = $uid");
    list ($datestart,$datereturn,$reassign_uid,$reason,$is_active) = DB_fetchArray ($query);

    if ($is_active == 1 AND time() > $datestart AND time() < $datereturn) {
        $dispStartDate = COM_getUserDateTimeFormat($datestart);
        $dispReturnDate = COM_getUserDateTimeFormat($datereturn);
        $p = new Template($_CONF['path_layout'] . '/nexflow/userprefs');
        $p->set_file ('prefs', 'awaysettings_view.thtml');
        $p->set_var ('away_sdate', $dispStartDate[0]);
        $p->set_var ('away_rdate',  $dispReturnDate[0]);
        $p->set_var ('away_reason', nl2br($reason));

        $p->set_var('lang_awaytitle', $LANG_NF01['title']);
        $p->set_var ('lang_awaylabel', $LANG_NF01['label']);
        $p->set_var ('lang_awayfrom', $LANG_NF01['from']);
        $p->set_var ('lang_awayto', $LANG_NF01['to']);
        $p->set_var ('lang_awayreason', $LANG_NF01['reason']);
        $p->set_var ('lang_selectowner', $LANG_NF01['selectowner']);
        $p->set_var ('lang_awayowner', $LANG_NF01['owner']);
        $p->parse ('output', 'prefs');
        $template->set_var('out_of_office_message', $p->finish($p->get_var('output')));

    }
}

function plugin_profileextrassave_nexflow()
{
    global $_USER, $_TABLES;
    $uid = $_USER['uid'];

    $startdate = COM_applyFilter(ppConvertDate($_POST['away_sdate']),true);
    $returndate = COM_applyFilter(ppConvertDate($_POST['away_rdate']),true);
    $reassignuid = COM_applyFilter($_POST['away_ressignuid'],true);
    $reason =  addslashes(COM_checkWords(COM_checkHTML(COM_killJS($_POST['away_reason']))));
    $is_active = COM_applyFilter($_POST['away_chkstatus'],true);

    if (DB_count($_TABLES['nf_useraway'],'uid',$uid) == 0) {
        $sql = "INSERT INTO {$_TABLES['nf_useraway']} (uid,away_start,away_return,reassign_uid,reason,is_active) VALUES ";
        $sql .= "($uid,$startdate,$returndate,$reassignuid,'$reason',$is_active) ";

    } else {
        $sql  = "UPDATE {$_TABLES['nf_useraway']} SET away_start=$startdate,away_return=$returndate, ";
        $sql .= "reassign_uid=$reassignuid,reason='$reason',is_active=$is_active WHERE uid=$uid";
    }
    DB_query($sql);

}


function plugin_getHeaderCode_nexflow() {
    global $_CONF;

    $code  = "\n\n<!-- Begin nexflow  -->\n";
    $code .= '<script language="javascript" src="' . $_CONF['site_url'] . '/nexflow/include/header.js" type="text/javascript"></script>';
    $code .= '<script type="text/javascript" src="' . $_CONF['site_url'] . '/nexflow/include/wz_jsgraphics.js"></script>';
    $code .= "\n<!-- End nexflow -->\n\n";
    return $code;

}

/**
* Called by the plugin Editor when a plugin's state changes
*/
function plugin_pluginstatechange_nexflow($plugin,$state) {
    global $_TABLES;

    // The nexflow plugin is dependant on the plugins nexpro and nexflow being installed and enabled
    if ($plugin == 'nexpro' || $plugin == 'nexform') {
        if ($state == 'disabled' OR $state == 'uninstalled') {
            // Disable this menu as it depends on the nexpro plugin
            DB_query("UPDATE {$_TABLES['plugins']} SET pi_enabled = 0 WHERE pi_name='nexflow'");
        }
    }
}



/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexflow() {
    global $CONF_NF;
    return $CONF_NF['version'];
}



function plugin_upgrade_nexflow() {
    global $_TABLES,$CONF_NF;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexflow'");

    switch ($curversion) {
    case "2.0.0":
        if (nexflow_upgrade_300() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '3.1.0', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexflow' LIMIT 1");
        }
        break;
    case "3.0.0":
        if (nexflow_upgrade_310() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '3.1.0', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexflow' LIMIT 1");
        }
        break;
    case "3.1.0":
        DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '3.1.1', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexflow' LIMIT 1");
        break;
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexflow'") == '3.1.1') {
        return true;
        COM_errorlog("nexFlow was updated successfully.");
    } else {
        return false;
        COM_errorlog("nexFlow was not updated to verion 3.1.0.  Please check any errors before retrying.");
    }
}

// function to test permissions on directories
function plugin_chkPermissions_nexflow(&$template) {
    global $CONF_NF;

    if (!$file = @fopen ($CONF_NF['export_dir'] . 'test.txt', 'w')) {
        $msg = '<font color="#ff0000">Could not write to <b>' . $CONF_NF['export_dir'] . '</b>. Please make sure this directory exists and is set to <b>chmod 755</b>.</font>';
        $template->set_var('message', $msg);
        $template->parse('output', 'warning', true);
    } else {
        fclose ($file);
        unlink ($CONF_NF['export_dir'] . 'test.txt');
    }

    return $display;
}

/* Function call my lookuplist admin - when deleting a list item
 * Gives us a chance to see if item is being used and abort the delete
 * Custom workflow functions that use lookuplists should append any lists used
 * to the Nexflow common lookuplist array $NF_LISTS
*/
function nexlist_delitem_nexflow($listid,$id) {
    global $_TABLES,$NF_LISTS;

    $retval = '';
    // Check and see if this lookuplist is used
    if (in_array($listid,$NF_LISTS)) {
        /* Add logic here or in the custom function located in the function nf_chkLookupListsbeforeDelete()
         * This function is located in the file plugins/nexflow/custom_functions/custom_batch_functions.php
        */
        $retval = nf_chkLookupListsbeforeDelete($listid, $id);
    }
    return $retval;
}

/* Function call my lookuplist admin - when deleting a Lookup List Definition
 * Gives us a chance to see if list is being used and abort the delete
*/
function nexlist_deldef_nexflow($listid) {
    global $_TABLES,$NF_LISTS;

    $retval = '';
    // Check and see if this lookuplist is used
    if (in_array($listid,$NF_LISTS)) {
        $retval = 'Nexflow custom function is using this list';
    }
    return $retval;
}

/**
* Logs messages to nfchange.log
* Writes a nexflow related message to an error log.
* @param        string      $logentry       Text to log to error log
* @return       nothing
*/
function nf_changeLog( $logentry )
{
    global $_CONF, $LANG01;

    $retval = '';
    if( !empty( $logentry ))   {
        $timestamp = strftime( '%c' );
        $logfile = $_CONF['path_log'] . 'nfchange.log';
        if($file = fopen( $logfile, 'a' )) {
            fputs( $file, "$timestamp - $logentry \n" );
        }
    }
}

?>