<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v2.2.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
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

$langfile = $_CONF['path'] . 'plugins/nexfile/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    include_once ($langfile);
} else {
    include_once ($_CONF['path'] . 'plugins/nexfile/language/english.php');
}

include_once ($_CONF['path'] . 'plugins/nexfile/autouninstall.php');

/*
* Check and see if we need to load the plugin configuration
*/
if (!isset($_FMCONF['storage_path'])) {
    require_once $_CONF['path_system'] . 'classes/config.class.php';

    $nexfile_config = config::get_instance();
    $_FMCONF = $nexfile_config->get_config('nexfile');
}

require_once ($_CONF['path'] . 'plugins/nexfile/nexfile.php');


/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getmenuitems_nexfile()
{
    global $_TABLES,$_CONF, $_FMCONF,$LANG_FM00;

    if ( SEC_inGroup($_FMCONF['access_mode']) ) {
        $menuitems[$LANG_FM00['headerlabel']] ="$_CONF[site_url]/nexfile/index.php";
        return $menuitems;
    }
}


/**
* Returns the URL of the plugin's
*
* @return   string      URL of the icon
*
*/
function plugin_geticon_nexfile($icon='')
{
    global $_CONF,$_FMCONF;
    $iconurl = '';

    if (array_key_exists($icon,$_FMCONF['icons'])) {
        $iconurl = $_FMCONF['imagesurl'] . $_FMCONF['icons'][$icon];
    }
    if (empty($iconurl)) {
        $iconurl = $_CONF['layout_url'] .'/nexfile/images/admin/nexfile.gif';
    }
    return $iconurl;
}


function plugin_autotags_nexfile($op,$content='',$tag='') {
    global $_CONF,$_TABLES;

    if ($op == 'tagname' ) {
        return 'file';
    } elseif ($op == 'parse') {
       if (DB_count($_TABLES['nxfile_files'],'fid',$tag['parm1']) == 1) {
           $filelink = '<a href="'.$_CONF['site_url'].'/nexfile/download.php?op=download&fid='.$tag['parm1'].'">'.$tag['parm2'].'</a>';
           $retval = str_replace($tag['tagstr'],$filelink,$content);
           return $retval;
        } else {
           return $content;
        }
    }
}



/**
* Geeklog is asking us to provide any new items that show up in the type drop-down
* on search.php.  Let's let users search the Filelistings in the Filemgmt Plugin
*
*/

function plugin_searchtypes_nexfile()
{
    global $LANG_FM00;

    $tmp['nexfile'] = $LANG_FM00['searchlabel'];
    return $tmp;
}


/**
* This searches for faqs matching the user query and returns an object
* back to search.php where it will be formated and printed.
*
* @param    string  $query      Keywords user is looking for
* @param    date    $datestart  Start date to get results for (not used)
* @param    date    $dateend    End date to get results for (not used)
* @param    string  $topic      The topic they were searching in (not used)
* @param    string  $type       Type of items they are searching, or 'all' (deprecated)
* @param    int     $author     Get all results by this author (not used)
* @param    string  $keyType    search key type: 'all', 'phrase', 'any'
* @param    int     $page       page number of current search (deprecated)
* @param    int     $perpage    number of results per page (deprecated)
* @return   object              search result object
*
*/
function plugin_dopluginsearch_nexfile($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_TABLES, $LANG_FM00;

	if(!COM_isAnonUser()) {
		// This cached setting will really only benefit when there are many thousand access records like portal23
		// User setting (all users) is cleared each time a folder permission is updated.
		// But this library is also included for all AJAX requests
		$data = DB_getItem($_TABLES['nxfile_usersettings'],'allowable_view_folders',"uid={$_USER['uid']}");
		if (empty($data)) {
			$allowableViewFolders = fm_getAllowableCategories('view',false);
			$data = serialize($allowableViewFolders);
			if (!DB_count($_TABLES['nxfile_usersettings'],'uid',$_USER['uid'])) {
			   DB_query("INSERT INTO {$_TABLES['nxfile_usersettings']} (uid,allowable_view_folders) VALUES ({$_USER['uid']},'$data')");
			} else {
				DB_query("UPDATE {$_TABLES['nxfile_usersettings']} set allowable_view_folders = '$data' WHERE uid={$_USER['uid']}");
			}
		} else {
			$allowableViewFolders = unserialize($data);
		}

	} else {
		$allowableViewFolders = fm_getAllowableCategories('view',false);
		$allowableViewFoldersSql = implode(',',$allowableViewFolders);  // Format to use for SQL statement - test for allowable categories
	}

    // Make sure the query is SQL safe
    $query = trim(addslashes($query));

	$sql  = "SELECT a.fid AS id, a.title, b.description, ";
	$sql .= "CONCAT ('/nexfile/index.php?fid=', a.fid) as url ";
	$sql .= "FROM {$_TABLES['nxfile_files']} a ";
	$sql .= "LEFT JOIN {$_TABLES['nxfile_filedetail']} b on a.fid=b.fid ";
	$sql .= "WHERE 1=1 ";
    if (!empty($allowableViewFoldersSql)) {
        $sql .= "AND file.cid in ($allowableViewFoldersSql) ";
    }

    if (!empty($datestart) && !empty($dateend))
    {
        $delim = substr($datestart, 4, 1);
        if (!empty($delim))
        {
            $DS = explode($delim, $datestart);
            $DE = explode($delim, $dateend);
            $startdate = mktime(0,0,0,$DS[1],$DS[2],$DS[0]);
            $enddate = mktime(23,59,59,$DE[1],$DE[2],$DE[0]);
            $sql .= "AND (a.date BETWEEN '$startdate' AND '$enddate') ";
        }
    }
    if (!empty($author)) {
        $sql .= "AND (a.submitter = '$author') ";
    }

    $search = new SearchCriteria('nexfile', $LANG_FM00['searchlabel']);
	$columns = array('title' => 'a.title', 'b.description', 'a.fname');
	list($sql,$ftsql) = $search->buildSearchSQL($keyType, $query, $columns, $sql);
    $search->setSQL($sql);
    $search->setFTSQL($ftsql);
    $search->setRank(4);

    return $search;
}

/**
* Returns any formatted links to files that have been refereced by the GL-Auto Tag
*/
function plugin_replacetags_nexfile($content,$tag)
{
    global $_TABLES,$_CONF;

    print_r($tag);
    if (DB_count($_TABLES['nxfile_files'],'id',$tag['parm1']) == 1) {
        $filelink = '<a href="'.$_CONF['site_url'].'/nexfile/download.php?op=download&fid='.$tag['parm1'].'">'.$tag['parm2'].'</a>';
        $retval = str_replace($tag['tagstr'],$filelink,$content);
        return $retval;
    } else {
        return $content;
    }
}

/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_nexfile() {
    global $_FMCONF;
    return $_FMCONF['version'];
}

/**
* Called by the plugin Editor when a plugin's state changes
*/
function plugin_pluginstatechange_nexfile($plugin,$state) {
    global $_TABLES;

    // The nexfile plugin is dependant on the plugin nexpro being installed and enabled
    if ($plugin == 'nexpro') {
        if ($state == 'disabled' OR $state == 'uninstalled') {
            // Disable this menu as it depends on the nexpro plugin
            DB_query("UPDATE {$_TABLES['plugins']} SET pi_enabled = 0 WHERE pi_name='nexfile'");
        }
    }
}


function plugin_upgrade_nexfile() {
    global $_TABLES,$_FMCONF;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexfile'");
    if (intval($curversion) < 3) {
        if (upgrade_300() == 0 )  {
            DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '3.0.0', `pi_gl_version` = '1.6.0' WHERE `pi_name` = 'nexfile' LIMIT 1");
        }
    }

    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexfile'") == '3.0.0') {
        return 11;
    } else {
        return 12;
    }
}


/* Plugin API hook - presently only supporting nexProject
*  Need to support a request to delete a file that have been added for a project - via the nexproject UI
*  Need to delete the folder assigned to a project if the project is deleted or if the plugin is uninstalled
*/
function plugin_itemdeleted_nexfile($id,$type) {
    global $_CONF,$_TABLES;

   if ($type == 'nexproject_fileitem') {
        nexdoc_deletefile($id);
   } elseif ($type == 'nexproject_filefolder') {
        fm_delCategory($cid);
   }

}



//  ------- GENERAL nexfile FUNCTIONS --------


function nexfile_statusMessage($message='',$url='',$action='') {
    global $_CONF,$_USER,$LANG_nexfile,$_FMCONF;
    $retval .= COM_siteHeader();
    $retval .= COM_startBlock($LANG_nexfile['msg47']);
    $retval .= "<font class='alert'>$message</font>";
    $retval .= "<br><p>";
    if ($action != "") {
        $retval .= $action;
        $retval .= "<p />";
    }elseif ($_FMCONF['msgdelay'] == "0") {
        $retval .= sprintf($LANG_nexfile['msg48'],$LANG_nexfile['msg49'],$url);
        $retval .= "<p />";
    } else {
        if ($url == "") {
        $url = "#";
    }
    $retval .= '<script language="javascript1.1">';
        $retval .= 'window.setTimeout("window.location.href=\'' .$url. '\'",' .$_FMCONF['msgdelay']. ');';
        $retval .= '<!-- window.location.replace("'.$url. '");';
        $retval .= '// --> </script>';
        $retval .= sprintf($LANG_nexfile['msg50'],$url);
        $retval .= "<p />";
    }
    $retval .= COM_endBlock();
    $retval .= COM_siteFooter();
    echo $retval;
}


/**
* Returns a formatted listbox of categories user has access to
* Able to pass in the Rights that user needs to have as a test
* Takes either a single Right or an array of Rights
*
* @param        string              $selected     Will make this item the selected item in the listbox
* @param        string|array        $rights       Rights to check (admin,view,upload,upload_dir,upload_ver,approval)
* @param        string              $cid          Parent category to start at and then recursively check
* @param        string              $level        Used by this function as it calls itself to control the ident formatting
* @param        string              $selectlist   Used by this function to be able to append to the formatted select list
* @return       string                            Return a formatted HTML Select listbox of categories
*/

function fm_recursiveCatList($selected='',$rights='view',$cid='0',$level='1',$selectlist='') {
    global $_TABLES;
    $query = DB_QUERY("SELECT cid,pid,name FROM {$_TABLES['nxfile_categories']} WHERE PID='$cid' ORDER BY CID");
    while ( list($cid,$pid,$name,$description) = DB_fetchARRAY($query)) {
        // Check if user has access to this category
        if (fm_getPermission($cid,$rights)) {
            // Check and see if this category has any sub categories - where a category record has this cid as it's parent
            if (DB_COUNT($_TABLES['nxfile_categories'], 'pid', $cid) > 0)  {
                $selectlist .= '<option value="' . $cid;
                $indent = '';
                if ($level > 1) {
                    for ($i=2; $i<= $level; $i++) {
                        $indent .= "--";
                    }
                }
                if ($name == $selected) {
                    $selectlist .= '" Selected>' .$indent .$name . '</option>' . LB;
                } else {
                    $selectlist .= '">' . $indent .$name . '</option>' . LB;
                }
                $selectlist = fm_recursiveCatList($selected,$rights,$cid,$level+1,$selectlist);
            } else {
                $indent = '';
                if ($level > 1) {
                    for ($i=2; $i<= $level; $i++) {
                        $indent .= "--";
                    }
                }
                $selectlist .= '<option value="' . $cid;
                if ($name == $selected) {
                    $selectlist .= '" Selected>' . $indent . $name . '</option>' . LB;
                } else {
                    $selectlist .= '">' . $indent . $name . '</option>' . LB;
                }
            }
        }
    }
    return $selectlist;
}


/**
* Returns a formatted listbox of categories user has Upload Access
* First checks for View access so that delegated admin can be just for sub-categories
* @TODO: Combine this function and fm_recursiveCatAdmin() into a single function
* @param        string              $selected     Will make this item the selected item in the listbox
* @param        string              $cid          Parent category to start at and then recursively check
* @param        string              $level        Used by this function as it calls itself to control the ident formatting
* @param        string              $selectlist   Used by this function to be able to append to the formatted select list
* @return       string                            Return a formatted HTML Select listbox of categories
*/
function fm_recursiveCatAddFileList($selected='',$cid='0',$level='1',$selectlist='') {
    global $_TABLES;
    $query = DB_QUERY("SELECT cid,pid,name FROM {$_TABLES['nxfile_categories']} WHERE PID='$cid' ORDER BY CID");

    while ( list($cid,$pid,$name,$description) = DB_fetchARRAY($query)) {
        // Check if user has access to this category
        if (fm_getPermission($cid,'view')) {
            // Check and see if this category has any sub categories - where a category record has this cid as it's parent
            if (DB_COUNT($_TABLES['nxfile_categories'], 'pid', $cid) > 0)  {
                $indent = '';
                if ($level > 1) {
                    for ($i=2; $i< $level; $i++) {
                        $indent .= "--";
                    }
                }
                if (fm_getPermission($cid,array ('admin','upload','upload_dir'))) {
                    $selectlist .= '<option value="' . $cid;
                    if ($name == $selected) {
                        $selectlist .= '" selected="selected">' .$indent .$name . '</option>' . LB;
                    } else {
                        $selectlist .= '">' . $indent .$name . '</option>' . LB;
                    }
                }
                $selectlist = fm_recursiveCatAddFileList($selected,$cid,$level+1,$selectlist);
            } else {
                $indent = '';
                if ($level > 1) {
                    for ($i=2; $i<= $level; $i++) {
                        $indent .= "--";
                    }
                }
                if (fm_getPermission($cid,array ('admin','upload','upload_dir'))) {
                    $selectlist .= '<option value="' . $cid;
                    if ($name == $selected) {
                        $selectlist .= '" selected="selected">' . $indent . $name . '</option>' . LB;
                    } else {
                        $selectlist .= '">' . $indent . $name . '</option>' . LB;
                    }
                }
            }
        }
    }

    return $selectlist;
}

/**
* Returns a formatted listbox of categories user has ADMIN Access
* First checks for View access so that delegated admin can be just for sub-categories
*
* @param        string              $selected     Will make this item the selected item in the listbox
* @param        string              $cid          Parent category to start at and then recursively check
* @param        string              $level        Used by this function as it calls itself to control the ident formatting
* @param        string              $selectlist   Used by this function to be able to append to the formatted select list
* @param        string              $restricted   Used if you do not want to show this categories subfolders
* @return       string                            Return a formatted HTML Select listbox of categories
*/
function fm_recursiveCatAdmin($selected='',$cid='0',$level='1',$selectlist='',$restricted='') {
    global $_TABLES;
    $query = DB_QUERY("SELECT cid,pid,name FROM {$_TABLES['nxfile_categories']} WHERE PID='$cid' ORDER BY CID");
    while ( list($cid,$pid,$name,$description) = DB_fetchARRAY($query)) {
        // Check if user has access to this category
        if ($cid != $restricted AND fm_getPermission($cid,'view')) {
            // Check and see if this category has any sub categories - where a category record has this cid as it's parent
            if (DB_COUNT($_TABLES['nxfile_categories'], 'pid', $cid) > 0)  {
                if ($level > 1) {
                    for ($i=2; $i<= $level; $i++) {
                        $indent .= "-";
                    }
                }
                if (fm_getPermission($cid,'admin')) {
                    if ($indent != '') $name = " $name";
                    $selectlist .= '<option value="' . $cid;
                    if (trim($name) == $selected) {
                        $selectlist .= '" selected="selected">' .$indent .$name . '</option>' . LB;
                    } else {
                        $selectlist .= '">' . $indent .$name . '</option>' . LB;
                    }
                }
                $selectlist = fm_recursiveCatAdmin($selected,$cid,$level+1,$selectlist,$restricted);
            } else {
                $indent = '';
                if ($level > 1) {
                    for ($i=2; $i<= $level; $i++) {
                        $indent .= "-";
                    }
                }
                if (fm_getPermission($cid,'admin')) {
                    if ($indent != '') $name = " $name";
                    $selectlist .= '<option value="' . $cid;
                    if (trim($name) == $selected) {
                        $selectlist .= '" selected="selected">' . $indent . $name . '</option>' . LB;
                    } else {
                        $selectlist .= '">' . $indent . $name . '</option>' . LB;
                    }
                }
            }
        }
    }

    return $selectlist;
}


/**
* Will upload a new file or image using the Geeklog upload class
*/
function fm_uploadfile($directory,$filename) {
    global $_FILES,$_CONF,$_TABLES,$_FMCONF,$LANG_FMERR;

    include_once($_CONF['path_system'] . 'classes/upload.class.php');
    $upload = new upload();
    $upload->setLogging(true);
    // Check if user uploading file has no-mime type restriction enabled
    foreach ($_FMCONF['nolimitUploadGroups'] as $group) {
        if (SEC_inGroup($group)) {
            $upload->setIgnoreMimeCheck(true);
            break;
        }
    }

    $upload->setAutomaticResize(false);
    $upload->setAllowedMimeTypes($_FMCONF['allowable_file_types']);

    // Set max dimensions as well in case user is uploading full size images
    //$upload->setMaxDimensions ($_FMCONF['max_uploadimage_width'], $_FMCONF['max_uploadimage_height']);
    $upload->setMaxFileSize($_FMCONF['maxfilesize'] * 1024 * 1024);


    if (!$upload->setPath($directory)) {
        $GLOBALS['fm_errmsg'] = $upload->printErrors(false);
        return false;
    }
    if ($upload->numFiles() == 1) {
        $curfile = current($_FILES);
        if (strlen($curfile['name']) > 0) {
            $upload->setFileNames($filename);
            $upload->setPerms( $_FMCONF['fileperms'] );
            reset($_FILES);
            $upload->uploadFiles();
            if ($upload->areErrors()) {
                $GLOBALS['fm_errmsg'] = $upload->printErrors(false);
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
    return false;

}


/**
* Function to verify the URL being referenced is valid
*/
function fm_verifyURL($url) {
    global $LANG_FMERR;
    if (!preg_match('/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\//i', $url, $m)) {
        $GLOBALS['fm_errmsg'] = $LANG_FMERR['err5'];
        return false;
    } else {
      $proto=$m[1];
      $hostname=strtolower($m[2]);
      $port=$m[3];
    }
    if (!@fsockopen("$hostname", '80', $errno, $errstr, 30)) {
        $GLOBALS['fm_errmsg'] = sprintf($LANG_FMERR['err6'], $hostname);
        return false;
    }
    if (!@fopen("$url", 'r')) {
        $GLOBALS['fm_errmsg'] =  sprintf($LANG_FMERR['err6'], $url);
        return false;
    } else {
        return true;
    }
}


function fm_preparefordb($str,$htmlallowed=false,$htmlfilter=false,$censor=false) {

    if ($censor) {
        $str = COM_checkWords($str);
    }
    if($htmlallowed) {
        if(!get_magic_quotes_gpc() ) {
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
        if(get_magic_quotes_gpc() ) {
            $str = htmlspecialchars($str);
        } else {
            $str = addslashes(htmlspecialchars($str));
        }
    $str = str_replace("'", "&rsquo;",$str);
    }
    return $str;
}

// Callback Function for the array walk function below to apply the data filters to clean any posted data
function fm_cleanfield(&$field) {
    if (gettype($field) == "string" ) {
        $field = fm_preparefordb($field);
    }
}

// Function to clean any posted data
function fm_cleandata($postarray) {
    array_walk($postarray,'fm_cleanfield');
    return $postarray;
}

function fm_formatFileSize($size) {
    $size = intval($size);
    if ($size/1000000 > 1) {
        $size = round($size/1000000,2) . " MB";
    } elseif ($size/1000 > 1) {
        $size = round($size/1000,2) . " KB";
    } else {
        $size = round($size,2) . " Bytes";
    }
    return $size;
}



/* Function to check if user has a particular right or permission to a category
*  Checks the access table for the user and any groups they belong to
*  Takes either a single Right or an array of Rights
*
* @param        string          $cid            Category to check user access for
* @param        string|array    $rights         Rights to check (admin,view,upload,upload_dir,upload_ver,approval)
* @param        Boolean         $adminOverRide  Set to false, to ignore if user is in the Admin group and check for absolute perms
* @return       Boolean                         Returns True if user has one of the requested access rights else FALSE
*/
function fm_getPermission($cid,$rights,$userid=0,$adminOverRide=true) {
    global $_TABLES,$_USER,$_CONF;
    if ($cid == '') {
        return false;
    }

    // If user is an admin - they should have access to all rights on all categories
    if ($userid == 0) {
        if(empty($_USER['uid']) OR $_USER['uid'] == 1 ) {
            $uid = 1;
        } else {
            $uid = $_USER['uid'];
        }
    }
    else {
        $uid = $userid;
    }

    if ($adminOverRide AND SEC_inGroup('nexfile Admin', $uid)) {
        return true;
    }
    else {
        // Check user access records
       $query = DB_query("SELECT view,upload,upload_direct,upload_ver,approval,admin from {$_TABLES['nxfile_access']} WHERE catid=$cid AND uid=$uid");
       while (list ($view,$upload,$upload_dir,$upload_ver,$approval,$admin) = DB_fetchArray($query)) {
            if (is_array($rights)) {
                foreach ($rights as $key) {      // Field name above needs to match access right name
                    if ($$key == 1)  {
                        return true;
                    }
                }
            } elseif ($$rights == 1) {
                return true;
            }
       }

        // For each group that the user is a member of - check if they have the right
        $groups = SEC_getUserGroups();
        foreach ($groups as $groupid) {
            $query = DB_query("SELECT view,upload,upload_direct,upload_ver,approval,admin from {$_TABLES['nxfile_access']} WHERE catid=$cid AND grp_id=$groupid");
            while (list ($view,$upload,$upload_dir,$upload_ver,$approval,$admin) = DB_fetchArray($query)) {
                if (is_array($rights)) {
                    // If any of the required permissions set - return true
                    foreach ($rights as $key) {
                        if ($$key == 1)  {      // Field name above needs to match access right name
                            return true;
                        }
                    }
                } elseif ($$rights == 1) {
                    return true;
                }
            }
       }
    }
    return false;
}

/**
* Create a log entry in the audit table, recording the user, time, script name and logentry message
* Function will prepare the logentry for DB insert - example quotes escaped
**/
function fm_updateAuditLog($logentry) {
    global $_USER,$_TABLES,$HTTP_SERVER_VARS;
    if (isset($_USER['uid']) AND $_USER['uid'] != "") {
        $userid = $_USER['uid'];
    } else {
        $userid = 1;
    }
    $date = time();
    $logentry = fm_preparefordb($logentry);
    DB_query("INSERT INTO {$_TABLES['auditlog']} (uid,date,script,logentry) VALUES ('$userid', '$date','{$HTTP_SERVER_VARS['SCRIPT_NAME']}', '$logentry' )");
    return true;
}

function fm_autoCreateNotifications($fid, $cid) {
    global $_TABLES;

    $ausers = array();
    $query = DB_query("SELECT uid FROM {$_TABLES['users']}");
    while ($A = DB_fetchArray($query)) {
        $exceptions = DB_count($_TABLES['nxfile_notifications'], array ('fid','cid','uid'), array ($fid,0,$A['uid']));
        if (fm_getPermission($cid,'view',$A['uid']) AND $A['uid'] > 1 AND $exceptions == 0) {
            $ausers[] = $A['uid'];
        }
    }

    $date = time();
    foreach ($ausers as $nuid) {
        if (!DB_count($_TABLES['nxfile_notifications'], array('fid','uid'), array($fid,$nuid) ) ) {
            DB_query("INSERT INTO {$_TABLES['nxfile_notifications']} (fid,cid,uid,date) VALUES ('$fid','$cid','$nuid','$date')");
        }
    }
}


/**
* Send out notifications to all users that have subscribed to this file or file category
* Will check user preferences for notification if Messenger Plugin is installed
* @param        string      $id        Key used to retrieve details depending on message type
* @param        string      $type      Message type - 1:New file notification, 2: Submission Approval, 3: Submission Deleted
* @return       Boolean     Returns True if atleast 1 message was sent out
**/
function fm_sendNotification($id,$type=1) {
    global $_CONF,$_TABLES,$_USER, $REMOTE_ADDR, $_FMCONF,$LANG_FM10;

    $target_users = array();

    switch ( $type ) {
        case "1":    // New File added where $id = file id. Send to all subscribed users
            $sql = "SELECT file.fid,file.fname,file.cid,file.submitter,category.name FROM "
                 . "{$_TABLES['nxfile_files']} file, {$_TABLES['nxfile_categories']} category "
                 . "WHERE file.cid=category.cid and file.fid={$id}";

            $query = DB_query($sql);
            list($fid,$fname,$cid,$submitter,$catname) = DB_fetchARRAY($query);
            if ($cid > 0) {
                $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['nxfile_notifications']} WHERE cid_newfiles = 1 AND cid={$cid}");
            } else {
                $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['nxfile_notifications']} WHERE cid_newfiles = 1 AND fid={$fid}");
            }
            break;

        case "2":    // File submission being approved by admin where $id = file id. Send only to user
           $sql = "SELECT file.fid,file.fname,file.cid,file.submitter,category.name FROM {$_TABLES['nxfile_files']} file, "
                . "{$_TABLES['nxfile_categories']} category WHERE file.cid=category.cid and file.fid={$id}";

            $query = DB_query($sql);
            list($fid,$fname,$cid,$submitter,$catname) = DB_fetchARRAY($query);
            // Just need to create this SQL record for this user - to fake out logic below
            $target_users[] = $submitter;
            break;

        case "3":    // File submission being declined by admin where $id = new submission record id. Send only to user
            $fname = DB_getItem($_TABLES['nxfile_filesubmissions'],"fname", "id=$id");
            $submitter = DB_getItem($_TABLES['nxfile_filesubmissions'],"submitter", "id=$id");
            // Just need to create this SQL record for this user - to fake out logic below
            $target_users[] = $submitter;
            $target_username = DB_getItem($_TABLES['users'],'username',"uid=$submitter");
            break;

        case "4":    // File changed where $id = file id. Send to all subscribed users
            $sql = "SELECT file.fid,file.fname,file.cid,file.submitter,category.name FROM "
                 . "{$_TABLES['nxfile_files']} file, {$_TABLES['nxfile_categories']} category "
                 . "WHERE file.cid=category.cid and file.fid={$id}";

            $query = DB_query($sql);
            list($fid,$fname,$cid,$submitter,$catname) = DB_fetchARRAY($query);
            if ($cid > 0) {
                $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['nxfile_notifications']} WHERE cid_changes = 1 AND (fid={$fid} OR cid={$cid})");
            } else {
                $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['nxfile_notifications']} WHERE fid={$fid} AND cid_changes = 1");
            }
            break;
    }

    if ($type == 1 OR $type == 4) {
        if ($_FMCONF['notify_newfile'] == 1) {  // Site default to notify all users on new files
            $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['users']} WHERE status = " . USER_ACCOUNT_ACTIVE);
            while ( list($uid) = DB_fetchARRAY($queryNotifyUsers)) {
                if (DB_count($_TABLES['nxfile_usersettings'],array('uid','notify_newfile',array($uid,0))))  {
                    $personalSetting = false;   // Found user setting to not be notified
                } else {
                    $personalSetting = true;
                }
                $personalException = DB_count($_TABLES['nxfile_notifications'], array ('cid','uid','cid_newfiles'), array ($cid,$uid,1));

                // Only want to notify users that don't have setting disabled or exception record
                if ($personalSetting == TRUE AND $personalException == FALSE AND $uid != $submitter) {
                    // Now just test that this user has view access to this folder
                    if (fm_getPermission($cid,'view')) {
                        $target_users[] = $uid;
                    }
                }
            }

        } else {
            $sql = "SELECT a.uid FROM {$_TABLES['nxfile_usersettings']} a, "
                 . "LEFT JOIN {$_TABLES['users']} b on b.uid=a.uid "
                 . "WHERE a.notify_newfile = 1 and b.status=" . USER_ACCOUNT_ACTIVE;
            $queryNotifyUsers = DB_query($sql);
            while ( list($uid) = DB_fetchARRAY($queryNotifyUsers)) {
                $personalException = DB_count($_TABLES['nxfile_notifications'], array ('fid','ignore_filechanges'), array ($id,1));
                // Only want to notify users that don't have setting disabled or exception record
                if ($personalException === FALSE) {
                    $target_users[] = $uid;
                }
            }
        }

    }

    if (count($target_users) > 0) {

        if ($type == 2 OR $type == 3 ) {    // Only send to user that submitted the file
            $subject = "{$_CONF['site_name']} - {$LANG_FM10[$type]['SUBJECT']}";
            $submitter_name = DB_getItem($_TABLES['users'],"username", "uid=$submitter");
            $message .= sprintf($LANG_FM10[$type]['LINE1'], $submitter_name,$catname);
            $message .= sprintf($LANG_FM10[$type]['LINE2'], $fname, "{$_CONF['site_url']}/nexfile/index.php?cid={$cid}");
            //$message .= $LANG_FM10[$type]['LINE3'];
            $message .= $LANG_FM10[$type]['LINE4'] . $LANG_FM10[$type]['ADMIN']."\n";
            $message .= $_CONF[site_url] . "\n";

            // Sort the array so that we can check for duplicate user notification records
            sort($target_users);
            reset($target_users);
            $lastuser = '';
            $distribution = array();

            /* Send out Notifications to all users on distribution
             * Use the Bcc feature of COM_mail (added June/2009)
             * To send to complete distribution as one email and not loop thru distribution sending individual emails
            */
            foreach ($target_users as $target_uid) {
                if ($target_uid != $lastuser) {
                    $query = DB_query("SELECT username,email FROM {$_TABLES['users']} WHERE uid=$target_uid");
                    list ($username,$email) = DB_fetchArray($query);
                    if (!empty($email)) {
                        $distribution[] = $email;
                        $sql = "INSERT INTO {$_TABLES['nxfile_notificationlog']} (target_uid,submitter_uid,notification_type,fid,cid,datetime) "
                             . "VALUES ($target_uid,$submitter,$type,$fid,$cid,UNIX_TIMESTAMP() )";
                        DB_query($sql);
                    }
                    $lastuser = $target_uid;
                }
            }
            return fm_sendEmail($distribution,$subject,$message);

        } else {
            $query = DB_query("SELECT username,email FROM {$_TABLES['users']} WHERE uid=$submitter");
            list ($username,$to) = DB_fetchArray($query);
            $subject = "{$_CONF['site_name']} - {$LANG_FM10[$type]['SUBJECT']}";
            $message  = $LANG_FM10[$type]['HELLO'] .' '. $username . ",\n\n";
            $message .= sprintf($LANG_FM10[$type]['LINE1'], $fname);
            $message .= $LANG_FM10[$type]['LINE2'];
            $message .= $LANG_FM10[$type]['LINE3'] . $LANG_FM10[$type]['ADMIN']."\n";
            $message .= $_CONF[site_url] . "\n";
            if ($_FMCONF['email_enabled']) {
                COM_mail($to, $subject, $message);
                $type = "Nexfile";
                $logentry = $type ."," .$to ."," .$subject;
                fm_logNotification($logentry);
                $sql = "INSERT INTO {$_TABLES['nxfile_notificationlog']} (target_uid,submitter_uid,notification_type,datetime) "
                     . "VALUES ($submitter,$submitter,$type,UNIX_TIMESTAMP() )";
                DB_query($sql);
                return true;
            } else {
                COM_errorLog("Nexfile - fm_sendEmail Warning: Email Disabled");
                return false;
            }

        }


    } else {
        return false;
    }
}

/* Function to handle sending of Email Notification
*  Uses the Blind Copy 'Bcc' feature of COM_mail added to Geeklog 1.6
*
* @param        array          $distribution    Array of user email addresses
* @param        string         $subject         Email subject
* @param        string         $message         Email message
* @return       Boolean        Returns True if email enabled and distribution list contains atleast 1 element in array
*/
function fm_sendEmail($distribution,$subject,$message) {
    global $_USER,$_CONF,$_TABLES, $_FMCONF;

    if ($_FMCONF['email_enabled']) {
        if (is_array($distribution) AND count($distribtion >= 1)) {
            $bccDistributionList = implode(',',$distribution);
            $to = "{$_FMCONF['from_email']} <{$_CONF['noreply_mail']}>";
            COM_mail($to, $subject, $message,'',$false,0,'',$bccDistributionList);

            // Log notification for admin viewing and tracking
            $type = "Nexfile";
            $logentry = $type ."," .$bccDistributionList ."," .$subject;
            fm_logNotification($logentry);
            return true;
        } else {
            COM_errorLog("Nexfile - fm_sendEmail Error: Empty distribution");
            return false;
        }
    } else {
        COM_errorLog("Nexfile - fm_sendEmail Warning: Email Disabled");
        return false;
    }
}

function fm_logNotification($logentry) {
    global $_CONF;
    $timestamp = strftime( "%b %d %H:%M" );
    $logfile = $_CONF['path_log'] . 'notifiction.log';

    if( !$file = fopen( $logfile, a )) {
        $retval .= $LANG01[33] . ' ' . $logfile . ' (' . $timestamp . ')<br>' . LB;
    } else {
        fputs( $file, "$timestamp,$logentry \n" );
    }
}



function nexdoc_deletefile($fid) {
    global $_CONF,$_TABLES,$_USER,$_FMCONF;

    if ($_USER['uid'] > 1 AND DB_count($_TABLES['nxfile_files'],'fid',$fid)) {

        // Check if user is the owner or has category admin rights
        $query = DB_query("SELECT cid,title,version,submitter,size FROM {$_TABLES['nxfile_files']} WHERE fid=$fid");
        list ($cid,$title,$version,$submitter,$fsize) = DB_fetchArray($query);
        if ($submitter == $_USER['uid'] OR fm_getPermission($cid,'admin')) {
            // Update site master tags table
            require_once( $_CONF['path_system'] . 'classes/tagcloud.class.php' );
            $tagcloud = new nexfileTagCloud();
            $tagcloud->clear_tags($fid);

            // Need to check there are no other repository entries in this category for the same filename
            $fname = DB_getItem($_TABLES['nxfile_fileversions'],"fname","fid=$fid AND version=$version");
            $fname = addslashes($fname);  // In case stored filename has quotes in it;
            if (DB_count($_TABLES['nxfile_files'], array('cid','fname'), array("$cid","$fname")) == 1) {
                $ret = @unlink($_FMCONF['storage_path'] . "$cid/$fname");
                if (!$ret) {
                    COM_errorLog("Attempted to unlink file but failed - path: {$_FMCONF['storage_path']}$cid/$fname");
                } else {
                    COM_errorLog("Successfully deleted file: {$_FMCONF['storage_path']}$cid/$fname");
                }
                fm_updateAuditLog("Delete File id: $fid, Version: $delversion. File: $fname. Single reference - file deleted");
            } else {
                COM_errorLog("Delete file failed - no matching record. Cid: $cid, fname: $fname");
                fm_updateAuditLog("Delete File id: $fid, Version: $delversion. File: $fname. Other references - not deleted");
            }
            DB_query("DELETE FROM {$_TABLES['nxfile_fileversions']} WHERE fid={$fid}");
            fm_updateAuditLog("Delete File and versions for fid: $fid , Main file records deleted");
            DB_query("DELETE FROM {$_TABLES['nxfile_files']} WHERE fid={$fid}");
            DB_query("DELETE FROM {$_TABLES['nxfile_filedetail']} WHERE fid={$fid}");
            DB_query("DELETE FROM {$_TABLES['nxfile_notifications']} WHERE fid={$fid}");
            return true;
        } else {
            COM_errorLog("Unable to delete file. User: {$_USER['uid']}, file: $fid and Folder: $cid");
            $GLOBALS['alertMsg'] = 'No permission to remove selected file(s)';
            return false;
        }

    } else {
        return false;
    }

}




/* Function to updated permission records for a list of users or list of groups
*  Checks the access table for the user and any groups they belong to
*  Takes either a single Right or an array of Rights
*
* @param        string         $catid          Category ID to update permission rights
* @param        array          $accessrights   Array of access rights to add (admin,view,upload,upload_dir,upload_ver,approval)
* @param        string|array   $users          String of userid or Array of users to add access rights
* @param        string|array   $groups         String of group id or Array of groups to add access rights.
*                                              Ver 3.0+ requires that this be a group name but earlier version used group id
*                                              For backward capability, logic checks for an intval
* @return       Boolean     Returns True if update was successful else FALSE
*/
function fm_updateCatPerms($catid,$accessrights,$users,$groups) {
    global $_TABLES;

    if ($users != "" AND !is_array($users)) {
        $users = array($users);
    }
    if ($groups != ""  AND !is_array($groups)) {
        $groups = array($groups);
    }

        if (!empty($accessrights)) {
            if (in_array("view", $accessrights)) {
                $view = 1;
            } else {
                $view = 0;
            }
            if (in_array("upload", $accessrights)) {
                $upload = 1;
            } else {
                $upload = 0;
            }
            if (in_array("approval", $accessrights)) {
                $approval = 1;
            } else {
                $approval = 0;
            }
            if (in_array("upload_direct", $accessrights)) {
                $direct = 1;
            } else {
                $direct = 0;
            }
            if (in_array("admin", $accessrights)) {
                $admin = 1;
            } else {
                $admin = 0;
            }
            if (in_array("upload_ver", $accessrights)) {
                $versions = 1;
            } else {
                $versions = 0;
            }

            if (!empty($users)) {
                foreach($users as $uid ) {
                    $uid = COM_applyFilter($uid);
                    $query = DB_query("SELECT accid FROM {$_TABLES['nxfile_access']} WHERE catid={$catid} AND uid=$uid");
                    if (DB_numRows($query) == 0) {
                        DB_query("INSERT INTO {$_TABLES['nxfile_access']} (catid,uid,grp_id,view,upload,upload_direct,upload_ver,approval,admin) VALUES ('{$catid}','$uid','0','$view','$upload','$direct','$versions','$approval','$admin')");
                        fm_updateAuditLog("Added new User Permission record for: $uid, Category: {$catid}");
                    } else {
                        DB_query("UPDATE {$_TABLES['nxfile_access']} SET view=$view, upload=$upload, upload_direct=$direct, upload_ver=$versions, approval=$approval, admin=$admin WHERE catid=$catid AND uid=$uid");
                        fm_updateAuditLog("Updated User Permission record for: $uid, Category: {$catid}");
                    }
                }
            }

            if (!empty($groups)) {
                foreach($groups as $group) {
                    if (is_numeric($group)) {
                        $grp_id = COM_applyFilter($group);
                    } else {
                        $group = COM_applyFilter($group);
                        $grp_id = DB_getItem($_TABLES['groups'],'grp_id',"grp_name='$group'");
                    }
                    if (DB_count($_TABLES['groups'],'grp_id',$grp_id)) {
                        $query = DB_query("SELECT accid FROM {$_TABLES['nxfile_access']} WHERE catid={$catid} AND grp_id=$grp_id");
                        if (DB_numRows($query) == 0) {
                            DB_query("INSERT INTO {$_TABLES['nxfile_access']} (catid,uid,grp_id,view,upload,upload_direct,upload_ver,approval,admin) VALUES ('{$catid}','0','$grp_id','$view','$upload','$direct','$versions','$approval','$admin')");
                            fm_updateAuditLog("Added new Group: $grp_id, Permission record for Category: {$catid}");
                        } else {
                            DB_query("UPDATE {$_TABLES['nxfile_access']} SET view=$view, upload=$upload, upload_direct=$direct, upload_ver=$versions, approval=$approval, admin=$admin WHERE catid=$catid AND grp_id=$grp_id");
                            fm_updateAuditLog("Updated perms for Group: $grp_id, Permission record for Category: {$catid}");
                        }
                    }
                }
            }

            /* May need to review this - and clear only those users that have been updated later.
               But determining the users in updated groups and sorting out duplicates from the individual user perms
               and only updating them may take more processing then simply clearing all.
               The users setting will be updated the next time they use the application - public/nexfile/library.php
               Distributing the load to update the cached setting.
               This cached setting will really only benefit when there are many thousand access records like portal23
            */
            DB_query("UPDATE {$_TABLES['nxfile_usersettings']} set allowable_view_folders = ''");

            return true;

        } else {
            return false;
        }

}



/* Function to return and array of subcatories
*  Recursive function calls itself building the list
*
* @param        array      $list        Array of categories
* @param        string     $cid         Category to lookup
* @param        string     $perms       Permissions to check if user has access to catgegory
*/
function fm_getRecursiveCatIDs (&$list,$cid,$perms) {
    global $_CONF,$_TABLES,$_FMCONF;
    $query = DB_QUERY("SELECT cid,pid FROM {$_TABLES['nxfile_categories']} WHERE PID='$cid' ORDER BY CID");
    while ( list($cid,$pid) = DB_fetchARRAY($query)) {
        // Check and see if this category has any sub categories - where a category record has this cid as it's parent
        if (DB_COUNT($_TABLES['nxfile_categories'], 'pid', $cid) > 0) {
            if (fm_getPermission($cid, $perms)) {
                array_push($list,$cid);
                fm_getRecursiveCatIDs($list, $cid,$perms);
            }
        } else {
            if (fm_getPermission($cid,$perms)) {
                array_push($list,$cid);
            }
        }
    }
    return $list;
}


/* Function to delete a filemgmt pro category
*  Checks the access table for the user and any groups they belong to
*  Takes either a single Right or an array of Rights
*
* @param        string     $catid       Category ID to delete
* @param        boolean    $bypass      Optional to Bypass category admin security check
* @return       array      Returns an empty with success (0 for sucess OR 1 for error) and error message
*/
function fm_delCategory($catid,$bypass=false) {
    global $_TABLES,$_CONF,$_FMCONF,$LANG_FMERR,$LANG_nexfile;

    if ($bypass OR fm_getPermission($catid,'admin')) {

        /* Build an array of all linked categories under this category the user has admin access to */
        $list = array();
        array_push($list,$catid);
        fm_getRecursiveCatIDs ($list,$catid,'admin');
        foreach ($list as $cid) {
            $catdir = $_FMCONF['storage_path'] . $cid;
            if (file_exists($catdir)) {
                /* Recursively delete all files and folders */
                ppRmdir($catdir);
            }
            DB_query("DELETE FROM {$_TABLES['nxfile_categories']} WHERE cid='{$cid}'");
            DB_query("DELETE FROM {$_TABLES['nxfile_access']} WHERE catid='{$cid}'");
            DB_query("DELETE FROM {$_TABLES['nxfile_recentfolders']} WHERE cid='{$cid}'");
            DB_query("DELETE FROM {$_TABLES['nxfile_notifications']} WHERE cid='{$cid}'");
            $query = DB_query("SELECT fid FROM {$_TABLES['nxfile_files']} WHERE cid='{$cid}'");
            while (list ($fid) = DB_fetchArray($query))  {
                nexdoc_deletefile($fid);
                PLG_itemDeleted($fid, 'nexfile_item');
            }
            PLG_itemDeleted($cid, 'nexfile_folder');
        }
        fm_updateAuditLog(sprintf($LANG_nexfile['msg57'], $catid));
        return true;

    } else {
        return false;
    }
}



function fm_createCatFolder($path) {
  if (@is_dir($path)) {
    @chmod($path, FM_CHMOD_DIRS);
    return true;
  }
  else {
    $oldumask = umask(0);
    $result = @mkdir($path, FM_CHMOD_DIRS);
    umask($oldumask);
    if (!@is_dir($path) || !$result) {
      $result = @mkdir($path, 0755);
      @chmod($path, FM_CHMOD_DIRS);
    }
    return $result;
  }
}


/* Function to create a filemgmt pro category
*  Checks the access table for the user and any groups they belong to
*  Takes either a single Right or an array of Rights
*
* @param        string     $catpid      Category Parent ID to create this folder under
* @param        string     $catname     Category Name
* @param        string     $catdesc     Optional Category Description
* @param        boolean    $bypass      Optional to Bypass category admin security check
* @return       array      Returns an array with the Category ID and any error message
*/
function fm_createCategory($catpid,$catname,$catdesc='',$bypass=false) {
    global $_USER,$_TABLES,$_CONF,$_FMCONF,$LANG_FMERR;

    $errmsg = '';
    // Verify user has admin access rights
    if ($bypass OR fm_getPermission($catpid,'admin')) {
        // Determine the max folder order and add the new folder to the bottom.
        $query = DB_query("SELECT max(folderorder) FROM {$_TABLES['nxfile_categories']} WHERE pid = $catpid");
        list ($maxorder) = DB_fetchArray($query);
        $maxorder = $maxorder + 10;
        DB_query("INSERT INTO {$_TABLES['nxfile_categories']} (pid,name,description,folderorder) VALUES ($catpid,'$catname','$catdesc',$maxorder)");
        $cid = DB_insertId();
        $catdir = $_FMCONF['storage_path'] . $cid;
        $error = 0;    // Check for error creating directories. If so - then delete new category record
        if (!file_exists($catdir)) {
            $catresult = fm_createCatFolder($catdir);
            if (!$catresult) {
                $errmsg .= sprintf($LANG_FMERR['err12'],$catdir);
                $error = 1;
            } else {
                $catinherit = COM_applyFilter($_POST['catinherit'],true);;
                if ($catpid > 0 AND $catinherit == 1) {
                    // Retrieve parent User access records - for each record create a new one for this category
                    $sql = "SELECT uid,view,upload,upload_direct,upload_ver,approval,admin FROM {$_TABLES['nxfile_access']} "
                         . "WHERE uid > 0 AND catid = {$catpid}";
                    $query1 = DB_query($sql);
                    while ( list($acc_uid,$acc_view,$acc_upload,$acc_uploaddirect,$acc_uploadver,$acc_approval,$acc_admin) = DB_fetchARRAY($query1)) {
                        $sql = "INSERT INTO {$_TABLES['nxfile_access']} "
                             . "(catid,uid,grp_id,view,upload,upload_direct,upload_ver,approval,admin) VALUES "
                             . "('$cid','$acc_uid',0,'$acc_view','$acc_upload','$acc_uploaddirect','$acc_uploadver','$acc_approval','$acc_admin')";
                        DB_query($sql);
                    }
                    // Retrieve parent Group access records - for each record create a new one for this category
                    $sql2 = "SELECT grp_id,view,upload,upload_direct,upload_ver,approval,admin "
                          . "FROM {$_TABLES['nxfile_access']} WHERE grp_id > 0 AND catid = {$catpid}";
                    $query2 = DB_query($sql2);
                    while ( list($acc_grpid,$acc_view,$acc_upload,$acc_uploaddirect,$acc_uploadver,$acc_approval,$acc_admin) = DB_fetchARRAY($query2)) {
                        $sql3 = "INSERT INTO {$_TABLES['nxfile_access']} "
                             . "(catid,uid,grp_id,view,upload,upload_direct,upload_ver,approval,admin) VALUES "
                             . "('$cid',0,'$acc_grpid','$acc_view','$acc_upload','$acc_uploaddirect','$acc_uploadver','$acc_approval','$acc_admin') ";
                        DB_query($sql3);
                    }

                } else {
                    // Create default permissions record for the user that created the category
                    fm_updateCatPerms($cid,$_FMCONF['defOwnerRights'],$_USER['uid'],"");
                    fm_updateCatPerms($cid,$_FMCONF['defCatGroupRights'],"",$_FMCONF['defCatGroup'] );
                }

                $catresult = fm_createCatFolder($catdir .'/submissions');
                if (!$catresult) {
                    $errmsg .= sprintf($LANG_FMERR['err14'],$catdir);
                    $error = 1;
                }
            }
        } else {
            $errmsg .= sprintf($LANG_FMERR['err12'],$catdir);
            $error = 1;
        }

        if ($error != 0 ) {
            DB_query("DELETE FROM {$_TABLES['nxfile_categories']} WHERE cid={$cid}");
            $cid = 0;
        }

        DB_query("UPDATE {$_TABLES['nxfile_usersettings']} set allowable_view_folders = ''");

    } else {
        $errmsg .= $LANG_FMERR['err16'];
    }

    return array ($cid,$errmsg);

}

function fm_getSubmissionCnt() {
    global $_TABLES;
    // Determine if this user has any submitted files that they can approve
    $query = DB_query("SELECT cid from {$_TABLES['nxfile_filesubmissions']}");
    $submissions = 0;
    while ( list($catid) = DB_fetchArray($query) ) {
        if (fm_getPermission($catid,'approval')) {
            $submissions++;
        }
    }
    return $submissions;
}


/* Notify Category Admin users that have full admin or upload-approval rights to category
   and have subscribed to category. Call for new files and new file version submissions
*/
function fm_sendAdminApprovalNofications($cid,$sid) {
    global $_CONF,$_TABLES,$_USER, $REMOTE_ADDR, $_FMCONF,$LANG_FM10;

    $q1 = DB_query("SELECT uid, grp_id FROM {$_TABLES['nxfile_access']} WHERE catid='$cid' AND (admin=1 OR approval=1) " );
    $notifyusers = array();
    while (list ($uid, $grpid) = DB_fetchArray($q1)) {
        if ($grpid > 0) {
            $q2 = DB_query("SELECT ug_uid FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id='$grpid' AND ug_uid IS NOT NULL");
            while (list ($ug_uid) = DB_fetchArray($q2)) {
                if (!in_array($ug_uid,$notifyusers)) {
                    $notifyusers[] = $ug_uid;
                }
            }
        } else {
            $notifyusers[] = $uid;
        }
    }

    foreach ($notifyusers as $notify_uid) {
        if (DB_COUNT($_TABLES['nxfile_notifications'],"uid","{$notify_uid}")) {
            $query = DB_query("SELECT file.fid,file.fname,file.cid,file.submitter,category.name FROM {$_TABLES['nxfile_filesubmissions']} file, {$_TABLES['nxfile_categories']} category WHERE file.cid=category.cid and file.id={$sid}");
            list($fid,$fname,$cid,$submitter,$catname) = DB_fetchARRAY($query);
            $subject = '' . $_CONF['site_name'] . ' ' .$LANG_FM10[4]['SUBJECT']. '';
            $submitter_name = DB_getItem($_TABLES['users'],"username", "uid=$submitter");
            $message  = $LANG_FM10[4]['HELLO'] .' '. $target_username . ",\n\n";
            $message .= sprintf($LANG_FM10[4]['LINE1'], $submitter_name,$catname);
            $message .= sprintf($LANG_FM10[4]['LINE2'], $fname, $_CONF['site_url'] . '/nexfile/index.php?op=notifications&cid=' .$cid);
            $message .= $LANG_FM10[4]['LINE3'];
            $message .= $LANG_FM10[4]['LINE4'] . $LANG_FM10[4]['ADMIN']."\n";
            $message .= $_CONF[site_url] . "\n";

            if (function_exists(messenger_send))  {
                $query = DB_query("SELECT notifications,sitepreference FROM {$_TABLES['messenger_userinfo']} WHERE uid={$target_user}");
                list($notifyoption,$sitepreference) = DB_fetchArray($query);
                if (DB_numRows($query) == 0 OR (DB_numRows($query) == 1 and $notifyoption == 1 and $sitepreference == 0)) {
                    msg_sendNotification($target_username,$subject,$message,"nexfile");
                } elseif (DB_numRows($query) == 1 and $notifyoption == 1 and $sitepreference == 1)  {
                    // Or wants all notifications via the messenger plugin
                    msg_send($target_username,$subject,nl2br($message));
                }
            } else {
                fm_sendEmail($target_username,$subject,$message);
            }
        }
    }
}

/**
* Return list of repository categories user has permission to access to be used in SQL statements
*
* @param   mixed   array or string    - permission(s) you want to test for
* @param   boolean return format      - if false, return an array
* @return  mixed - comma separated list of categories, or array
*/
function fm_getAllowableCategories ($perm='view', $returnstring=true)
{
    global $_TABLES,$_USER;

    if ($returnstring) {
        $retval = '';
    } else {
        $retval = array();
    }

    if (!is_array($perm)) {
        $perms[] = $perm;
    } else {
        $perms = $perm;
    }

    $groups = SEC_getUserGroups();
    $mygroups = array();
    foreach ($groups as $groupid) {
        $mygroups[] = $groupid;
    }
    $groups = implode(',',$mygroups);

    $sql = "SELECT catid FROM {$_TABLES['nxfile_access']} WHERE ( ";
    if (isset($_USER['uid']) AND $_USER['uid'] > 1) {
        $sql .= "uid={$_USER['uid']} OR ";
    }
    $sql .= " grp_id in ($groups)) AND ";
    $permstring = '';
    foreach($perms as $perm) {
        if (!empty($permstring)) {
            $permstring .= " OR $perm = 1";
        } else {
            $permstring .= "$perm = 1";
        }
    }
    $sql .= "( $permstring ) ";
    $query = DB_query($sql);
    if (DB_numRows($query) > 0) {
        $categories = array();
        while ($A = DB_fetchArray($query,false)) {
            $categories[] = $A['catid'];
        }
        if ($returnstring AND count($categories) > 0) {
            $retval = implode(',',$categories);
        } else {
            $retval = $categories;
        }
    }
    return $retval;
}



/**
* Get a list (actually an array) of all groups this group belongs to.
*
* @param   basegroup   int     id of group
* @return              array   array of all groups 'basegroup' belongs to
*
*/
function fm_getGroupList ($basegroup)
{
    global $_TABLES;

    $to_check = array ();
    array_push ($to_check, $basegroup);

    $groups = array ();

    while (sizeof ($to_check) > 0) {
        $thisgroup = array_pop ($to_check);
        if ($thisgroup > 0) {
            $result = DB_query ("SELECT ug_grp_id FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = $thisgroup");
            $numGroups = DB_numRows ($result);
            for ($i = 0; $i < $numGroups; $i++) {
                $A = DB_fetchArray ($result);
                if (!in_array ($A['ug_grp_id'], $groups)) {
                    if (!in_array ($A['ug_grp_id'], $to_check)) {
                        array_push ($to_check, $A['ug_grp_id']);
                    }
                }
            }
            $groups[] = $thisgroup;
        }
    }

    return $groups;
}


/**
* Get a list (actually an array) of all userss in this group or sub-groups.
*
* @param   basegroup   int     id of group
* @parm    users       Optional int or array of users that are already known
* @return              array   array of all users in group(s)
*
*/
function fm_getUserList ($basegroup,$users='')
{
    global $_TABLES;

    $groups = fm_getGroupList ($basegroup);
    $groupList = implode (',', $groups);
    $sql = 'SELECT DISTINCT uid ';
    $sql .= "FROM {$_TABLES['users']},{$_TABLES['group_assignments']} ";
    $sql .= "WHERE {$_TABLES['users']}.uid > 1 AND {$_TABLES['users']}.uid = ";
    $sql .= "{$_TABLES['group_assignments']}.ug_uid AND ({$_TABLES['group_assignments']}.ug_main_grp_id IN ({$groupList}))";
    $query = DB_query($sql);

    $ret_users = array();
    if ($users != '' AND is_array($users)) {
        $ret_users = $users;
    } else {
        $ret_users[] = $users;
    }
    while (list($uid) = DB_fetchArray($query)) {
        if (!in_array ($uid, $ret_users)) {
            array_push ($ret_users, $uid);
        }
    }
    return $ret_users;

}



/* Function to display the latest files in a block
*  Checks the access table for the user and any groups they belong to
*  Takes either a single Right or an array of Rights
*
* @return       string     Returns a HTML formatted table
*/
function phpblock_nexfile_latestfiles() {
    global $_TABLES,$_CONF,$_USER;

    // Get a list of groups this user is a member of
    $grp_array = SEC_getUserGroups();
    $grp_list = '';
    foreach ($grp_array as $key) {
        if ($grp_list != '') {
            $grp_list .= ",$key";
        } else {
            $grp_list = $key;
        }
    }

    // Now get a list of categories user as view access to
    $sql = "SELECT fid, title, catid, submitter,version,date FROM {$_TABLES['nxfile_access']} acc ";
    $sql .= "LEFT JOIN {$_TABLES['nxfile_files']} files ON acc.catid = files.cid ";
    $sql .= "WHERE view = '1' AND ( uid = '{$_USER['uid']}' OR grp_id ";
    $sql .= "IN ( $grp_list ) ) ";
    $sql .= "AND fid IS NOT NULL AND status = '0' ";
    $sql .= "GROUP BY fid ORDER BY date desc LIMIT 7";

    $query = DB_query($sql);
    $retval = '<table width="100%" border="0" cellpadding="1" cellspacing="1">';
    $i = 2;
    while( list( $fid,$fname,$catid,$uid,$version,$date ) = DB_fetchArray($query) ) {
        $description = str_replace('<br />','',DB_getItem($_TABLES['nxfile_filedetail'],'description',"fid='$fid'"));
        $author = DB_getItem($_TABLES['users'],'username',"uid='$uid'");
        $date = strftime('%b/%d/%y %I:%M&nbsp;%p',$date);
        $alttag = "Title: $fname\n";
        if(strlen ($fname) > 23) {
            $fname = substr ($fname, 0, 23);
            $fname .= '...';
        }
        $alttag .= "Description:$description\nAuthor:&nbsp;$author\nDate:&nbsp;$date\nVersion:&nbsp;$version";
        $retval .= "<tr><td class=\"plgAlt$i\"><img src=\"{$_CONF['layout_url']}/images/triangle-red.png\">&nbsp;<a href=\"{$_CONF['site_url']}/nexfile/details.php?fid=$fid\" TITLE=\"$alttag\">$fname</a></td></tr>";
        $i = ($i == 2) ? 1:2;
    }
    $retval .= "<tr><td class=\"plgAlt$i\" style=\"text-align:right;padding:5 10 5 0px;\"><a href=\"{$_CONF['site_url']}/nexfile/index.php\">View all Documents ...</a></td></tr>";
    $retval .= '</table>';

    return $retval;

}

function fm_getFilesize($url) {
    $i = 0;  //start count at -1 because we increment AT LEAST once
    if ($stream = @fopen($url, 'rb')) {
        do {
            $byte = fread($stream, 1);
            $i++;
        } while (!feof($stream));

        fclose($stream);
    }
    return $i;
}

//returns false on failure
//returns array on success.. [0] is filename, [1] is token
function nxfile_generateEditFileName($fid){
    global $_TABLES;

    $fid=intval($fid);
    if($fid==0) return false;
    $filename=DB_getItem($_TABLES['nxfile_files'],"fname","fid='{$fid}'");
    $extension=strrchr($filename,".");
    $pos=strpos($filename,".");

    // Generate an hash valie and append to filename to create a unique filename
    $length = 15;
    $filename=substr($filename,0,$pos);
    $hash = md5(uniqid(rand()));
    $token=substr($hash,0,$length);

    $newfilename=$filename.'{'.$token.'t}'.$extension;
    $newfilename=str_replace(" ","+",$newfilename);
    //echo $newfilename;
    $retarray=array();
    $retarray[0]=$newfilename;
    $retarray[1]=$token;
    return $retarray;
}



?>