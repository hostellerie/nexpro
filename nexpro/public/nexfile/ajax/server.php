<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v3.0.0 for the nexPro Portal Server                        |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | server.php                                                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Blaine Lang            - Blaine DOT Lang AT nextide DOT ca                |
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

include ('../../lib-common.php');
include ('../library.php');

$mytimer = new timerobject();
$mytimer->startTimer();
$charset = COM_getCharset();

// Code added to handle the issue with the default $_COOKIE array being sent by the Flash Image uploader
// We can sent the cookies in the post form data and then extract and filter the data to rebuild the COOKIE array
// Also now need this to support Geeklog 1.6.1 that enables HTTP only cookie support.
// Javascript no longer has access to the gl_session id in the cookie - issue only apparent in the YUI upload form
if ((!isset($_USER['uid']) AND isset($_POST['cookie_session']))) {

    $_COOKIE[$_CONF['cookie_session']] = COM_applyFilter($_POST['cookie_session']);

    // Have a valid session id now from the COOKIE - ReInitialize the session data
    if (isset($_COOKIE[$_CONF['cookie_session']])) {
        $_USER = SESS_sessionCheck();
        if ($_USER['uid'] > 0)  {
            $_GROUPS = SEC_getUserGroups ($_USER['uid']);
            // Global array of current user permissions [read,edit]
            $_RIGHTS = explode(',', SEC_getUserPermissions());
        }
    }

}

//set up the user
if($_USER['uid'] < 2) {
    $uid = 0;
} else {
    $uid = $_USER['uid'];
}
$error = 'NULL';

$filter = new sanitizer();
$op = $filter->getCleanData('char',$_REQUEST['op']);
$filter->initFilter();  // Reset Filter
$firephp = FirePHP::getInstance(true);
$firephp->group('Nexfile - AJAX Server');

if (isset($_REQUEST['pending'])) {
    $logmessage = "op:$op, user: $uid, cid: {$_POST['cid']}, pending request count: {$_REQUEST['pending']}";
} else {
    $logmessage = "op:$op, user: $uid, reportmode: {$_GET['reportmode']}";
}
$firephp->log($logmessage);
//COM_errorLog ($logmessage);


$data = array();

function firelogmsg($message) {
    global $firephp,$mytimer;
    $exectime = $mytimer->stopTimer();
    $firephp->log("$message - time:$exectime");
}


switch ($op) {

    case 'getfilelisting':
        $cid = $filter->getCleanData('int',$_GET['cid']);
        $reportmode = $filter->getCleanData('char',$_GET['reportmode']);
        if (empty($reportmode)) {
            $ajaxBackgroundMode = true;
        } elseif ($cid == 0 AND !in_array($reportmode,$validReportModes)) {
            $reportmode = 'latestfiles';
        }

        if ($reportmode == 'notifications') {
            $data['retcode'] = 200;
            $data['cid'] = $cid;
            $data['activefolder'] = nexdoc_displayActiveFolder($cid,$reportmode);
            $data['displayhtml'] = nexdoc_generateNotificationsReport();
            $data['header'] = nexdoc_formatHeader($cid,$reportmode);
            $data['moreactions'] = nexdocsrv_getMoreActions($reportmode);
        } elseif ($cid > 0 AND fm_getPermission($cid,'view')) {
            $data['retcode'] = 200;
            $data['cid'] = $cid;
            if ($uid > 1 AND $cid > 0 AND DB_count($_TABLES['nxfile_categories'],'cid',$cid)) {
            $pid = DB_getItem($_TABLES['nxfile_categories'],'pid',"cid=$cid");
                if ($pid > 0) {
                    if (DB_count($_TABLES['nxfile_recentfolders'],'uid',$uid) > 4) {
                        DB_query("DELETE FROM {$_TABLES['nxfile_recentfolders']} WHERE uid=$uid ORDER BY id ASC LIMIT 1");
                    }
                    if (DB_count($_TABLES['nxfile_recentfolders'],array('uid','cid'),array($uid,$cid)) == 0) {
                        DB_query("INSERT INTO {$_TABLES['nxfile_recentfolders']} (uid,cid) VALUES ($uid,$cid)");
                    }
                }
            }
            $data['displayhtml'] = nexdocsrv_generateFileListing($cid,$reportmode);
            if (is_array($lastRenderedFiles) AND count($lastRenderedFiles) > 0) {
                $data['lastrenderedfiles'] = serialize($lastRenderedFiles);
            }
            firelogmsg("Completed generating FileListing");
            $folderName = DB_getItem($_TABLES['nxfile_categories'],'name',"cid=$cid");
            $data['activefolder'] = nexdoc_displayActiveFolder($cid);
            $data['moreactions'] = nexdocsrv_getMoreActions($reportmode);
            $data['header'] = nexdoc_formatHeader($cid,$reportmode);
        } elseif ($cid == 0) {
            $data['retcode'] = 200;
            $data['cid'] = $cid;
            $data['displayhtml'] = nexdocsrv_generateFileListing($cid,$reportmode);
            $data['activefolder'] = nexdoc_displayActiveFolder($cid,$reportmode);
            $data['moreactions'] = nexdocsrv_getMoreActions($reportmode);
            $data['header'] = nexdoc_formatHeader($cid,$reportmode);

        }  else {
            $data['retcode'] = 401;
            $data['error'] = 'Error: No Access to Folder';
        }

        $retval = json_encode($data);
        firelogmsg("Completed generating AJAX return data");
        break;


    case 'getmorefiledata':
        $filter->cleanData('int',array('cid' => $_POST['cid'],'level' => $_POST['level'] ));
        $filter->cleanData('char',array('foldernumber' => $_POST['foldernumber']));
        $_CLEAN = $filter->normalize($filter->getDbData());
        $lastRenderedFolder = $_CLEAN['cid'];
        if ($_CLEAN['foldernumber'] == 'null') $_CLEAN['foldernumber'] = '';

        $retval = '<result>';
        $retval .= '<retcode>200</retcode>';

        $tpl = new Template($_CONF['path_layout'] . 'nexfile');
        $tpl->set_file(array(
            'filelisting_rec'       =>  'filelisting_record.thtml',
            'loadfolder_msg'        =>  'load_folder_message.thtml',
            'tag_link'              =>  'taglink_record.thtml',
            'tag_rec'               =>  'tagdesc_record.thtml',
            'upload_action'         =>  'upload_link.thtml',
            'download_action'       =>  'download_link.thtml',
            'download_disabled'     =>  'download_disabled_link.thtml',
            'editfile_action'       =>  'editfile_link.thtml'
            ));

        $tpl->set_var('site_url', $_CONF['site_url']);
        $tpl->set_var('layout_url', $_CONF['layout_url']);
        $tpl->set_var('imgset', "{$_CONF['layout_url']}/nexfile/images");

        $retval .= '<displayhtml>'.htmlspecialchars(nexdoc_displayFileListing($tpl,$_CLEAN['cid'],'getmoredata',$_CLEAN['level'],"{$_CLEAN['foldernumber']}"),ENT_QUOTES,$charset).'</displayhtml>';
        $retval .= '</result>';
        firelogmsg("Completed generating AJAX return data - cid: {$_CLEAN['cid']}");
        break;

    case 'getmorefolderdata':
        /* Need to use XML instead of JSON format for return data.
           It's taking up to 1500ms to interpret (eval) the JSON data into an object in the client code
           Parsing the XML is about 10ms
        */
        $filter->cleanData('int',array('cid' => $_POST['cid'],'level' => $_POST['level'] ));
        $filter->cleanData('char',array('foldernumber' => $_POST['foldernumber']));
        $_CLEAN = $filter->normalize($filter->getDbData());
        $retval = '<result>';
        $retval .= '<retcode>200</retcode>';

        $tpl = new Template($_CONF['path_layout'] . 'nexfile');
        $tpl->set_file(array(
            'filelisting_rec'       =>  'filelisting_record.thtml',
            'tag_link'              =>  'taglink_record.thtml',
            'tag_rec'               =>  'tagdesc_record.thtml',
            'upload_action'         =>  'upload_link.thtml',
            'download_action'       =>  'download_link.thtml',
            'download_disabled'     =>  'download_disabled_link.thtml',
            'editfile_action'       =>  'editfile_link.thtml'
            ));

        $tpl->set_var('site_url', $_CONF['site_url']);
        $tpl->set_var('layout_url', $_CONF['layout_url']);
        $tpl->set_var('imgset', "{$_CONF['layout_url']}/nexfile/images");

        $retval .= '<displayhtml>'.htmlspecialchars(nexdoc_displayFileListing($tpl,$_CLEAN['cid'],'getmorefolderdata',$_CLEAN['level'],"{$_CLEAN['foldernumber']}"),ENT_QUOTES,$charset).'</displayhtml>';
        $retval .= '</result>';
        firelogmsg("Completed generating AJAX return data - cid: {$_CLEAN['cid']}");
        break;

    case 'getleftnavigation':
        $data = nexdocsrv_generateLeftSideNavigation();
        $retval = json_encode($data);
        break;

    case 'getfolderlisting':
        $ajaxBackgroundMode = true;
        $cid = $filter->getCleanData('int',$_GET['cid']);
        $folderName = DB_getItem($_TABLES['nxfile_categories'],'name',"cid=$cid");
        $retval = '<result>';
        $retval .= '<retcode>200</retcode>';
        $retval .= '<cid>'.$cid.'</cid>';
        // Update recent folders for this user if logged in
        // Only save the latest 5 - if this category is not already one of users recent folders

        if ($uid > 1 AND $cid > 0 AND DB_count($_TABLES['nxfile_categories'],'cid',$cid)) {
            $pid = DB_getItem($_TABLES['nxfile_categories'],'pid',"cid=$cid");
            if ($pid > 0) {
                if (DB_count($_TABLES['nxfile_recentfolders'],'uid',$uid) > 4) {
                    DB_query("DELETE FROM {$_TABLES['nxfile_recentfolders']} WHERE uid=$uid ORDER BY id ASC LIMIT 1");
                }
                if (DB_count($_TABLES['nxfile_recentfolders'],array('uid','cid'),array($uid,$cid)) == 0) {
                    DB_query("INSERT INTO {$_TABLES['nxfile_recentfolders']} (uid,cid) VALUES ($uid,$cid)");
                }
            }
        }
        $retval .= '<displayhtml>' . htmlspecialchars(nexdocsrv_generateFileListing($cid,$reportmode),ENT_QUOTES,$charset).'</displayhtml>';
        firelogmsg("Completed generating FileListing");
        if (is_array($lastRenderedFiles) AND count($lastRenderedFiles) > 0) {
            $retval .= '<lastrenderedfiles>' . serialize($lastRenderedFiles) .'</lastrenderedfiles>';
        }
        $retval .= '<activefolder>' . htmlspecialchars(nexdoc_displayActiveFolder($cid),ENT_QUOTES,$charset).'</activefolder>';
        $retval .= '</result>';
        break;


    case 'rendernewfolderform':
        $cid = $filter->getCleanData('int',$_GET['cid']);
        $tpl = new Template($_CONF['path_layout'] . 'nexfile');
        $tpl->set_file('form','newfolder_form.thtml');
        $tpl->set_var('newfolder_options',nexdoc_recursiveAccessOptions('admin',$cid));
        if (function_exists('nexfile_customFolderFields')) {
            nexfile_customFolderFields($tpl,'add',$cid);
        }
        $tpl->parse ('output', 'form');
        $data['displayhtml'] = $tpl->finish ($tpl->get_var('output'));
        $retval = json_encode($data);
        break;


    case 'rendernewfilefolderptions':
        $cid = $filter->getCleanData('int',$_GET['cid']);
        $tpl = new Template($_CONF['path_layout'] . 'nexfile');
        $tpl->set_file('form','newfile_form.thtml');
        $tpl->set_var('newfile_category_options',nexdoc_recursiveAccessOptions(array('upload','upload_dir'),$cid));
        $tpl->parse ('output', 'form');
        $data['displayhtml'] = $tpl->finish ($tpl->get_var('output'));
        $retval = json_encode($data);
        break;


    case 'rendermoveform':
        $tpl = new Template($_CONF['path_layout'] . 'nexfile');
        $tpl->set_file('form','movefiles_form.thtml');
        $tpl->set_var('movefolder_options',nexdoc_recursiveAccessOptions('admin'));
        $tpl->parse ('output', 'form');
        $data['displayhtml'] = $tpl->finish ($tpl->get_var('output'));
        $retval = json_encode($data);
        break;

    case 'rendermovefileform':
        $tpl = new Template($_CONF['path_layout'] . 'nexfile');
        $tpl->set_file('form','movefile_form.thtml');
        $tpl->set_var('movefolder_options',nexdoc_recursiveAccessOptions('admin'));
        $tpl->parse ('output', 'form');
        $data['displayhtml'] = $tpl->finish ($tpl->get_var('output'));
        $retval = json_encode($data);
        break;


    case 'setfolderorder':
        $filter->cleanData('int',array('cid' => $_GET['cid'],'listingcid' => $_GET['listingcid']));
        $_CLEAN = $filter->normalize($filter->getDbData());
        if (fm_getPermission($_CLEAN['cid'],'admin')) {
            // Check and see if any subfolders don't yet have a order value - if so correct
            $maxorder = 0;
            $pid = DB_getItem($_TABLES['nxfile_categories'],'pid',"cid={$_CLEAN['cid']}");
            $maxquery = DB_query("SELECT folderorder FROM {$_TABLES['nxfile_categories']} WHERE pid=$pid ORDER BY folderorder ASC LIMIT 1");
            list($maxorder) = DB_fetchArray($maxquery);
            $nextFolderOrder = $maxorder + 10;
            $query = DB_query("SELECT cid,folderorder FROM {$_TABLES['nxfile_categories']} WHERE pid=$pid AND folderorder = 0");
            while (list($cid,$order) = DB_fetchArray($query))  {
                DB_query("UPDATE {$_TABLES['nxfile_categories']} SET folderorder = $nextFolderOrder WHERE cid = $cid");
                $nextFolderOrder += 10;
            }

            $itemquery = DB_query("SELECT * FROM {$_TABLES['nxfile_categories']} WHERE cid={$_CLEAN['cid']}");
            $retval = 0;
            if (DB_numRows($itemquery) == 1) {
                $A = DB_fetchArray($itemquery);
                if ($_GET['direction'] == 'down') {
                    $sql  = "SELECT folderorder FROM {$_TABLES['nxfile_categories']} WHERE pid={$A['pid']} ";
                    $sql .= "AND folderorder > {$A['folderorder']} ORDER BY folderorder ASC LIMIT 1";
                    $nextquery = DB_query($sql);
                    list($nextorder) = DB_fetchArray($nextquery);
                    if ($nextorder > $A['folderorder']) {
                        $folderorder = $nextorder + 5;
                    } else {
                        $folderorder = $A['folderorder'];
                    }
                    DB_query("UPDATE {$_TABLES['nxfile_categories']} SET folderorder = $folderorder WHERE cid = {$_CLEAN['cid']}");
                } elseif ($_GET['direction'] == 'up') {
                    $sql  = "SELECT folderorder FROM {$_TABLES['nxfile_categories']} WHERE pid={$A['pid']} ";
                    $sql .= "AND folderorder < {$A['folderorder']} ORDER BY folderorder DESC LIMIT 1";
                    $nextquery = DB_query($sql);
                    list($nextorder) = DB_fetchArray($nextquery);
                    $folderorder = $nextorder - 5;
                    if ($folderorder <= 0) $folderorder = 0;
                    DB_query("UPDATE {$_TABLES['nxfile_categories']} SET folderorder = $folderorder WHERE cid = {$_CLEAN['cid']}");
                }
            }

            /* Re-order any folders that may have just been moved */
            $query = DB_query("SELECT cid,folderorder from {$_TABLES['nxfile_categories']} WHERE pid=$pid ORDER BY folderorder");
            $folderOrder = 10;
            $stepNumber = 10;
            while ( list($cid,$order) = DB_fetchARRAY($query)) {
                if ($order != $folderOrder) {
                    DB_query("UPDATE {$_TABLES['nxfile_categories']} SET folderorder = $folderOrder WHERE cid = $cid");
                }
                $folderOrder += $stepNumber;
            }
            $data['retcode'] =  200;
            $data['displayhtml'] = nexdocsrv_generateFileListing($_CLEAN['listingcid']);
        } else {
            $data['retcode'] =  400;
        }

        $retval = json_encode($data);
        break;

    case 'updatefolder':
        $filter->cleanData('int',array(
            'cid' => $_POST['cid'],
            'catpid'  => $_POST['catpid'],
            'folderorder' => $_POST['folderorder'],
            'fileadded' => $_POST['fileadded_notify'],
            'filechanged' => $_POST['filechanged_notify']
            ));
        $filter->cleanData('text',array('catname' => $_POST['categoryname'],'catdesc' => $_POST['catdesc']));
        $_CLEAN = $filter->normalize($filter->getDbData());
        if ($_CLEAN['cid'] > 0 AND fm_getPermission($_CLEAN['cid'],'admin')) {
            $data['retcode'] =  200;
            $data['cid'] = $_CLEAN['cid'];
            DB_query("UPDATE {$_TABLES['nxfile_categories']} SET name='{$_CLEAN['catname']}', description='{$_CLEAN['catdesc']}' WHERE cid='{$_CLEAN['cid']}'");
            if (DB_getItem($_TABLES['fm_category'],'folderorder',"cid={$_CLEAN['cid']}") != $_CLEAN['folderorder']) {
                 DB_query("UPDATE {$_TABLES['nxfile_categories']} SET folderorder='{$_CLEAN['folderorder']}' WHERE cid='{$_CLEAN['cid']}'");
                /* Re-order any folders that may have just been moved */
                $query = DB_query("SELECT cid,folderorder from {$_TABLES['nxfile_categories']} WHERE pid={$_CLEAN['catpid']} ORDER BY folderorder");
                $folderOrder = 10;
                $stepNumber = 10;
                while ( list($cid,$order) = DB_fetchARRAY($query)) {
                    if ($order != $folderOrder) {
                        DB_query("UPDATE {$_TABLES['nxfile_categories']} SET folderorder = $folderOrder WHERE cid = $cid");
                    }
                    $folderOrder += $stepNumber;
                }
            }

            // Update the personal folder notifications for user
            if ($_CLEAN['filechanged'] == 1 OR $_CLEAN['fileadded'] == 1) {
                if (DB_count($_TABLES['nxfile_notifications'],array('cid','uid'),array($_CLEAN['cid'],$uid)) == 0) {
                    $sql  = "INSERT INTO {$_TABLES['nxfile_notifications']} (cid,cid_newfiles,cid_changes,uid,date) ";
                    $sql .= "VALUES ({$_CLEAN['cid']},{$_CLEAN['fileadded']},{$_CLEAN['filechanged']},$uid,UNIX_TIMESTAMP() )";
                    DB_query($sql);
                } else {
                    $sql  = "UPDATE {$_TABLES['nxfile_notifications']} set cid_newfiles = {$_CLEAN['fileadded']}, ";
                    $sql .= "cid_changes={$_CLEAN['filechanged']}, date=UNIX_TIMESTAMP() ";
                    $sql .= "WHERE uid=$uid and cid={$_CLEAN['cid']}";
                    DB_query($sql);
                }
            } else {
                DB_query("DELETE FROM {$_TABLES['nxfile_notifications']} WHERE uid=$uid and cid={$_CLEAN['cid']}");
            }

            // Now test if user has requested to change the folder's parent and if they have permission to this folder
            $pid = DB_getItem($_TABLES['nxfile_categories'],'pid',"cid={$_CLEAN['cid']}");
            if( $pid != $_CLEAN['catpid']) {
                if (fm_getPermission($_CLEAN['catpid'],'admin')) {
                    DB_query("UPDATE {$_TABLES['nxfile_categories']} SET pid='{$_CLEAN['catpid']}' WHERE cid='{$_CLEAN['cid']}'");
                }
            }

            PLG_itemSaved($_CLEAN['cid'], 'nexfile_folder_update');

        } else {
            $data['retcode'] = 500;
        }

        $retval = json_encode($data);
        break;

    case 'createfolder':
        $filter->cleanData('int',array('catparent' => $_POST['catparent'],'catinherit'  => $_POST['catinherit']));
        $filter->cleanData('text',array('catname' => $_POST['catname'],'catdesc'  => $_POST['catdesc']));
        $_CLEAN = $filter->getDbData();

        $catpid     = $_CLEAN['int']['catparent'];
        $catname    = $_CLEAN['text']['catname'];
        $catdesc    = $_CLEAN['text']['catdesc'];
        $catinherit = $_CLEAN['int']['catinherit'];

        if (empty($catname)) {
            $data['errmsg'] = 'Empty Folder Name';
            $data['retcode'] =  500;
        } elseif (fm_getPermission($catpid,'admin')) {
            if (PLG_itemPreSave('nexfile_folder_create',$_CLEAN)) {
                $catresult = fm_createCategory($catpid,$catname,$catdesc);
                if ($catresult['0'] > 0 ) {
                    $newcid = $catresult['0'];
                    if ($autonotify == 1) {     // Version 3.0 -- not presently being used
                        DB_query("UPDATE {$_TABLES['nxfile_categories']} set auto_create_notifications='1' WHERE cid='$newcid'");
                    }

                    PLG_itemSaved($newcid, 'nexfile_folder_create');

                    fm_updateAuditLog("New Category: $newcid created");
                    $data['retcode'] =  200;
                    $data['cid'] = $newcid;
                    if ($catpid == 0) {
                        $data['displaycid'] = $newcid;
                    } else {
                        $data['displaycid'] = $catpid;
                    }
                } else {
                    $data['retcode'] =  500;
                    $data['errmsg'] = $catresult['1'];
                    COM_errorLog("nexfile: Error creating new folder -> {$catresult['1']}");
                }
            } else {
                $data['errmsg'] = 'Missing Data or Improper Data';
                $data['retcode'] =  500;
            }

        } else {
            $data['errmsg'] = 'Insufficent Permissions';
            $data['retcode'] =  500;
        }
        $retval = json_encode($data);
        break;

    case 'deletefolder':
        $cid = $filter->getCleanData('int',$_POST['cid']);
        if ($cid > 0 AND DB_count($_TABLES['nxfile_categories'],'cid',$cid)) {
            if (fm_getPermission($cid,'admin')) {
                $pid = DB_getItem($_TABLES['nxfile_categories'],'pid',"cid=$cid");
                $parentFolder = DB_getItem($_TABLES['nxfile_categories'],'name',"cid=$pid");
                if (fm_delCategory($cid)) {
                    // Remove any recent folder records for this category
                    DB_query("DELETE FROM {$_TABLES['nxfile_recentfolders']} WHERE cid=$cid");
                    // Set the new active directory to the parent folder
                    $data['retcode'] =  200;
                    $data['activefolder'] = nexdoc_displayActiveFolder($pid);
                    $data['displayhtml'] = nexdocsrv_generateFileListing($pid);
                    $data = nexdocsrv_generateLeftSideNavigation($data);
                } else {
                    $data['retcode'] =  400;
                }
            } else {
                $data['retcode'] =  403;  // Forbidden
            }
        } else {
            $data['retcode'] =  404;  // Not Found
        }
        $retval = json_encode($data);
        break;

    case 'getfolderperms':
        $cid = $filter->getCleanData('int',$_GET['cid']);
        if ($cid > 0) {
            $data['html'] = nexdocsrv_folderperms($cid);
            $data['retcode'] = 200;
        } else {
            $data['retcode'] = 404;
        }
        $retval = json_encode($data);
        break;

    case 'delfolderperms':
        $id = $filter->getCleanData('int',$_GET['id']);
        if ($id > 0) {
            $cid = DB_getItem($_TABLES['nxfile_access'],"catid","accid={$id}");
            if (fm_getPermission($cid,'admin')) {
                DB_query("DELETE FROM {$_TABLES['nxfile_access']} WHERE accid={$id}");
                DB_query("UPDATE {$_TABLES['nxfile_usersettings']} set allowable_view_folders = ''");
                fm_updateAuditLog("Deleted Permission record: $id");
                $data['html'] = nexdocsrv_folderperms($cid);
                $data['retcode'] = 200;
            } else {
                $data['retcode'] = 403; // Forbidden
            }
        } else {
            $data['retcode'] = 404; // Not Found
        }
        $retval = json_encode($data);
        break;

    case 'addfolderperm':
        $cid = $filter->getCleanData('int',$_POST['catid']);
        if (fm_updateCatPerms(
            $cid,                          // Category ID
            $_POST['cb_access'],           // Array of permissions checked by user
            $_POST['selusers'],            // Array of site members
            $_POST['selgroups'])           // Array of groups
            )
        {
            $data['html'] = nexdocsrv_folderperms($cid);
            $data['retcode'] = 200;
        } else {
            $data['retcode'] = 403; // Forbidden
        }
        $retval = json_encode($data);
        break;

    case 'savefile':
        $textvars = array(
            'filetitle'     => $_POST['displayname'],
            'description'   => $_POST['description'],
            'vernote'       => $_POST['versionnote'],
            'tags'          => $_POST['tags']);

        $intvars = array(
            'cid'           => $_POST['category'],
            'notify'        => $_POST['notify']);

        $filter->setCheckhtml(false);  // Need to disable HTML filter or even new lines are removed
        $filter->cleanData('int',$intvars);
        $filter->cleanData('text',$textvars);
        $_CLEAN = $filter->normalize($filter->getDbData());
        $date = time();
        $uploadfilename = $filter->getCleanData('text',$_FILES['Filedata']['name']);
        //$uploadfilename = strtolower($uploadfilename);

        $pos = strrpos($uploadfilename,'.') + 1;
        $fileExtension = substr($uploadfilename, $pos);
        $filesize =  $filter->getCleanData('int',$_FILES['Filedata']['size']);
        $mimetype =  $filter->getCleanData('text',$_FILES['Filedata']['type']);

        $data['op'] = 'savefile';
        $data['message'] = '';
        $data['cid'] = $_CLEAN['cid'];

        if (DB_count($_TABLES['nxfile_files'], array('cid','fname'), array("{$_CLEAN['cid']}","$uploadfilename")) > 0) {
            $data['error'] = 'Duplicate File in this folder';
            $data['retcode'] = 400;
        } elseif (fm_getPermission($_CLEAN['cid'],'upload_dir') AND !empty($uploadfilename)) {
            if (empty($_CLEAN['filetitle'])) $_CLEAN['filetitle'] = $uploadfilename;
            $directory = "{$_FMCONF['storage_path']}{$_CLEAN['cid']}";
            if ( fm_uploadfile($directory,$uploadfilename) ) {
                if (!get_magic_quotes_gpc()) {
                    $uploadfilename = addslashes($uploadfilename);
                }
                // Set status of file to 1 - online
                $sql =  "INSERT INTO {$_TABLES['nxfile_files']} (cid,fname,title,version,ftype,size,mimetype,extension,submitter,status,date) ";
                $sql .= "VALUES ({$_CLEAN['cid']},'$uploadfilename','{$_CLEAN['filetitle']}','1','file',";
                $sql .= "'$filesize','$mimetype','$fileExtension',$uid,1,'$date')";
                DB_query($sql);

                $fid = DB_insertId();  // New File ID
                $tagcloud = new nexfileTagCloud();
                // Update tags table and return tags formated as required
                $tagcloud->update_tags($fid,$_POST['tags']);

                DB_query("INSERT INTO {$_TABLES['nxfile_filedetail']} (fid,description,hits,rating,votes,comments)
                    VALUES ('$fid','{$_CLEAN['description']}','0','0','0','0')");
                DB_query("INSERT INTO {$_TABLES['nxfile_fileversions']} (fid,fname,ftype,version,notes,size,date,uid,status)
                    VALUES ('$fid','$uploadfilename','file','1','{$_CLEAN['vernote']}','$filesize','$date','$uid','1')");

                PLG_itemSaved($fid, 'nexfile_filesaved');

                // Optionally add notification records and send out notifications to all users with view access to this new file
                if (DB_getItem($_TABLES['nxfile_categories'], 'auto_create_notifications', "cid={$_CLEAN['cid']}") == 1) {
                    fm_autoCreateNotifications($fid, $_CLEAN['cid']);
                }

                // Send out notifications of update
                if ($_POST['notify'] == "true") {
                    fm_sendNotification($fid);
                }
                fm_updateAuditLog("Direct upload of File ID: $fid, in Category: {$_CLEAN['cid']}");
                $data['error'] = 'File successfully uploaded';
                $data['retcode'] = 200;

            } else {
                $data['retcode'] = 400;
                // Needed to trim the last charcter off for some reason, as it was causing the JS success code to fail
                $data['error'] = str_replace(array('<BR>','<br>'),' ',$GLOBALS['fm_errmsg']);
                $data['error'] = substr($data['error'],0,strlen($data['error'])-1);
            }
       } elseif (fm_getPermission($_CLEAN['cid'],'upload') AND !empty($uploadfilename)) {

            $directory = "{$_FMCONF['storage_path']}{$_CLEAN['cid']}/submissions";

            // Generate random file name for newly submitted file to hide it until approved
            $charset = "abcdefghijklmnopqrstuvwxyz";
            for ($i=0; $i<12; $i++) $random_name .= $charset[(mt_rand(0,(strlen($charset)-1)))];
            $random_name .= '.' .$fileExtension;

            if ( fm_uploadfile($directory,$random_name) ) {
                if (!get_magic_quotes_gpc()) {
                    $uploadfilename = addslashes($uploadfilename);
                }

                // Status of file record will default to 0 -- not online
                $sql =  "INSERT INTO {$_TABLES['nxfile_filesubmissions']} ";
                $sql .= "(cid,fname,tempname,title,ftype,description,version_note,size,mimetype,extension,submitter,date,tags) ";
                $sql .= "VALUES ({$_CLEAN['cid']},'$uploadfilename','$random_name','{$_CLEAN['filetitle']}','file',";
                $sql .= "'{$_CLEAN['description']}','{$_CLEAN['vernote']}','$filesize','$mimetype','$fileExtension','$uid','$date','{$_CLEAN['tags']}')";
                DB_query($sql);
                $sid = DB_insertId();

                PLG_itemSaved($sid, 'nexfile_filesubmission');

                // Determine if any users that have upload.admin permission for this category
                // or nexfile admin rights should be notified of new file awaiting approval
                fm_sendAdminApprovalNofications($cid,$sid);
                fm_updateAuditLog("New upload submission, in Category: $cid");
                $data['message'] = "File successfully uploaded and you will be notified once it's approved";
                $data['error'] = 'File successfully uploaded';
                $data['retcode'] = 200;
            }


       } else {
           $data['retcode'] = 400;
           $data['error'] = 'Error: You do not have upload permission for that folder';
       }

        $retval = json_encode($data);
        break;

    case 'saveversion':
        $textvars = array(
            'vernote'       => $_POST['versionnote'],
            'fname'         => $_FILES['Filedata']['name'],
            'tags'          => $_POST['tags']);

        $intvars = array(
            'fid'           => $_POST['fid'],
            'fsize'         => $_FILES['Filedata']['size'],
            'notify'        => $_POST['notify']);

        $filter->setCheckhtml(false);  // Need to disable HTML filter or even new lines are removed
        $filter->cleanData('int',$intvars);
        $filter->cleanData('text',$textvars);
        $_CLEAN = $filter->normalize($filter->getDbData());
        $date = time();
        $uploadfilename = $_CLEAN['fname'];
        $fsize =  $_CLEAN['fsize'];

        $data['op'] = 'saveversion';

        if (!empty($_CLEAN['fid']) AND !empty($uploadfilename)) {
            $pos = strrpos($uploadfilename,'.') + 1;
            $newfilename = substr($uploadfilename,0,$pos);
            $fileExtension = strtolower(substr($uploadfilename, $pos+1));

            $query = DB_query("SELECT cid,fname,version,ftype FROM {$_TABLES['nxfile_files']} WHERE fid={$_CLEAN['fid']}");
            list($cid,$fname,$curVersion,$curftype) = DB_fetchARRAY($query);
            if ($curVersion < 1) {
                $curVersion = 1;
            }
            $newVersion = $curVersion + 1;
            $pos = strrpos($fname,'.');
            $oldFileExtension = strtolower(substr($fname, $pos+1));

            // Check for the filename having the version info as part of the filename - we will need to replace it
            $vpos = strrpos($fname,'_v'.$curVersion);
            if ($vpos > 0) {
                // Extract just the filename portion
                $filename = substr($fname,0,$vpos);
            } else {
                $filename = substr($fname,0,$pos);
            }

            if ($uploadfilename != '') {
                $pos2 = strrpos($uploadfilename, '.');
                $newFileExtension = strtolower(substr($uploadfilename, $pos2 + 1));
            }
            $curVerFname = strtolower($filename) . '_v' .$curVersion. '.' . $oldFileExtension;
            $newVerFname = strtolower($newfilename) . '_v' .$newVersion. '.' . $newFileExtension;

           if (fm_getPermission($cid,'upload_dir')) {
                if (empty($_CLEAN['filetitle'])) $_CLEAN['filetitle'] = $upndcname;
                $filesize =  $_FILES['Filedata']['size'];
                $directory = "{$_FMCONF['storage_path']}$cid";
                if ( fm_uploadfile($directory,$newVerFname) ) {

                    $curfile = "{$_CONF['path_html']}nexfile/data/{$cid}/{$fname}";
                    $newfile = "{$_CONF['path_html']}nexfile/data/{$cid}/{$curVerFname}";

                    if (!get_magic_quotes_gpc()) {
                        $fname = addslashes($fname);
                    }

                    // Need to check there are no other repository entries in this category for the same filename
                    if (DB_count($_TABLES['nxfile_files'], array('cid','fname'), array($cid,"$fname")) == 1) {
                        // Rename the current file so that it has a version number in the filename
                        @rename ($curfile,$newfile);
                    } else {
                        // Copy  the current file so that it has a version number in the filename
                        @copy ($curfile,$newfile);
                    }

                    if (!get_magic_quotes_gpc()) {
                        $newVerFname = addslashes($newVerFname);
                        $curVerFname = addslashes($curVerFname);
                    }
                    $sql = "INSERT INTO {$_TABLES['nxfile_fileversions']} (fid, fname, ftype, version, notes, size, date, uid, status) ";
                    $sql .= "VALUES ({$_CLEAN['fid']},'$newVerFname','file', '$newVersion', '{$_CLEAN['vernote']}', '$fsize', '$date', '$uid', '1')";
                    DB_query($sql);
                    $sql  = "UPDATE {$_TABLES['nxfile_files']} SET fname='$newVerFname',ftype='file',";
                    $sql .= "version='$newVersion',size='$fsize',date='$date' WHERE fid={$_CLEAN['fid']}";
                    DB_query($sql);
                    if ($curVersion == 1 AND $curftype != 'url') {
                        // The filename will have changed possibly if this was version 1 and it had not version suffix in the filename
                        DB_query("UPDATE {$_TABLES['nxfile_fileversions']} SET fname='$curVerFname' WHERE fid={$_CLEAN['fid']} and version={$curVersion}");
                    }

                    $tagcloud = new nexfileTagCloud();
                    // Update tags table and return tags formated as required
                    $tagcloud->update_tags($_CLEAN['fid'],$_POST['tags']);

                    // Send out notifications of update
                    // Optionally add notification records and send out notifications to all users with view access to this new file
                    if ($_POST['notify'] == 'true') {
                        fm_sendNotification($_CLEAN['fid']);
                    }
                    fm_updateAuditLog("New File Version - Local File - added for FID: {$_CLEAN['fid']}");
                    $data['error'] = 'File successfully uploaded';
                    $data['retcode'] = 200;
                    $data['fid'] = $_CLEAN['fid'];

                } else {
                    $data['retcode'] = 400;
                    $data['error'] = $GLOBALS['fm_errmsg'];

                }
           } else {
               $data['retcode'] = 400;
               $data['error'] = 'Error: You do not have upload permission for that folder';
           }

       } else {
           $data['retcode'] = 400;
           $data['error'] = 'Error: You do not have upload permission for that folder';
       }
       $retval = json_encode($data);
       break;

    case 'deleteversion':
        $intvars = array(
            'fid'           => $_GET['fid'],
            'version'       => $_GET['version']);

        $filter->cleanData('int',$intvars);
        $_CLEAN = $filter->normalize($filter->getDbData());

        if ($_CLEAN['fid'] > 0) {
            $query = DB_query("SELECT cid,version FROM {$_TABLES['nxfile_files']} WHERE fid={$_CLEAN['fid']}");
            list ($cid,$curVersion) = DB_fetchArray($query);
            $fname = DB_getItem($_TABLES['nxfile_fileversions'],"fname","fid={$_CLEAN['fid']} AND version={$_CLEAN['version']}");
            $fsize = DB_getItem($_TABLES['nxfile_fileversions'],"size","fid={$_CLEAN['fid']} AND version={$_CLEAN['version']}");
            $fname = addslashes($fname);  // In case stored filename has quotes in it;
            DB_query("DELETE FROM {$_TABLES['nxfile_fileversions']} WHERE fid={$_CLEAN['fid']} AND version={$_CLEAN['version']}");

            // Need to check there are no other repository entries in this category for the same filename
            if (DB_count($_TABLES['nxfile_files'], array('cid','fname'), array("$cid","$fname")) > 1) {
                fm_updateAuditLog("Delete File id: {$_CLEAN['fid']}, Version: $delversion. File: $fname. Other references - not deleted");
            } else {
                if (!empty($fname) AND file_exists("{$_FMCONF['storage_path']}{$cid}/$fname")) {
                    @unlink("{$_FMCONF['storage_path']}{$cid}/$fname");
                }
                fm_updateAuditLog("Delete File id: {$_CLEAN['fid']}, Version: $delversion. File: $fname. Single reference - file deleted");
            }
            // If there is at least 1 more version record on file then I may need to update current version
            if (DB_count($_TABLES['nxfile_fileversions'], "fid", "{$_CLEAN['fid']}") > 0) {
                if ($_CLEAN['version'] == $curVersion) {
                    // Retrieve most current version on record
                    $query = DB_query("SELECT fname,ftype,version,date FROM {$_TABLES['nxfile_fileversions']} WHERE fid={$_CLEAN['fid']} ORDER BY version DESC LIMIT 1");
                    list ($fname,$ftype,$version,$date) = DB_fetchArray($query);
                    DB_query("UPDATE {$_TABLES['nxfile_files']} SET fname='$fname',version='$version',ftype='$ftype', date='$date' WHERE fid={$_CLEAN['fid']}");
                }
            } else {
                fm_updateAuditLog("Delete File final version for fid: {$_CLEAN['fid']} , Main file records deleted");
                DB_query("DELETE FROM {$_TABLES['nxfile_files']} WHERE fid={$_CLEAN['fid']}");
                DB_query("DELETE FROM {$_TABLES['nxfile_filedetail']} WHERE fid={$_CLEAN['fid']}");
            }

            $data['retcode'] = 200;
            $data['fid'] = $_CLEAN['fid'];
            $data['displayhtml'] = nexdocsrv_filedetails($_CLEAN['fid']);
        } else {
            $data['retcode'] = 400;
        }
        $retval = json_encode($data);

        break;

    case 'refreshfiledetails':
        $fid = $filter->getCleanData('int',$_GET['fid']);

        if (DB_count($_TABLES['nxfile_files'],'fid',$fid)) {
            $data['retcode'] = 200;
            $data['fid'] = $fid;
            $data['displayhtml'] = nexdocsrv_filedetails($fid);
        } else {
            $data['retcode'] = 400;
        }
        $retval = json_encode($data);
        break;

    case 'updatenote':
        $intvars = array(
            'fid'           => $_POST['fid'],
            'version'       => $_POST['version']);

        $filter->setCheckhtml(false);  // Need to disable HTML filter or even new lines are removed
        $filter->cleanData('int',$intvars);
        $filter->cleanData('text',array('note' => $_POST['note']));
        $_CLEAN = $filter->normalize($filter->getDbData());

        if ($_CLEAN['fid'] > 0) {
            DB_query("UPDATE {$_TABLES['nxfile_fileversions']} SET notes='{$_CLEAN['note']}' WHERE fid={$_CLEAN['fid']} and version={$_CLEAN['version']}");
            fm_updateAuditLog("Updated File Version note FID: {$_CLEAN['fid']}");
            $data['retcode'] = 200;
            $data['fid'] = $_CLEAN['fid'];
            $data['displayhtml'] = nexdocsrv_filedetails($_CLEAN['fid']);
        } else {
            $data['retcode'] = 400;
        }
        $retval = json_encode($data);

        break;

    case 'updatefile':
        $textvars = array(
            'filetitle'     => $_POST['filetitle'],
            'description'   => $_POST['description'],
            'tags'          => $_POST['tags'],
            'approved'      => $_POST['approved'],
            'vernote'       => $_POST['version_note']);

        $intvars = array(
            'folder'        => $_POST['folder'],
            'fid'           => $_POST['id'],
            'version'       => $_POST['version']);

        $filter->setCheckhtml(false);  // Need to disable HTML filter or even new lines are removed
        $filter->cleanData('int',$intvars);
        $filter->cleanData('text',$textvars);
        $_CLEAN = $filter->normalize($filter->getDbData('','',false));
        $_WEB = $filter->normalize($filter->getWebData());

        $data['tagerror'] = '';

        $query = DB_query("SELECT fname,cid,version FROM {$_TABLES['nxfile_files']} WHERE fid={$_CLEAN['fid']}");
        list ($fname,$cid,$curVersion) = DB_fetchArray($query);

        $tagcloud = new nexfileTagCloud();
        if (fm_getPermission($cid,'view',0,false) AND !$tagcloud->update_tags($_CLEAN['fid'],$_POST['tags'])) {
            $data['tagerror'] = 'Tags not added - Group view perms requried';
        }

        $date = time();
        $filemoved = false;
        if ($_CLEAN['approved'] == 'false') {
            $sql = "UPDATE {$_TABLES['nxfile_filesubmissions']} SET title='{$_CLEAN['filetitle']}', description='{$_CLEAN['description']}', ";
            $sql .= "version_note='{$_CLEAN['vernote']}', cid={$_CLEAN['folder']}, tags='{$_CLEAN['tags']}' WHERE id={$_CLEAN['fid']} ";
            DB_query($sql);
            fm_updateAuditLog("Updated File Submission ID: {$_CLEAN['fid']}");
            $data['cid'] = $_CLEAN['folder'];
            $data['tags'] = $_CLEAN['tags'];
        } else {
            // Allow updating the category, title, description and image for the current version and primary file record
            if ($_CLEAN['version'] == $curVersion) {
                $newcid = $_CLEAN['folder'];
                DB_query("UPDATE {$_TABLES['nxfile_files']} SET title='{$_CLEAN['filetitle']}', date='$date' WHERE fid={$_CLEAN['fid']}");
                DB_query("UPDATE {$_TABLES['nxfile_filedetail']} SET description='{$_CLEAN['description']}' WHERE fid={$_CLEAN['fid']}");
                // Test if user has selected a different directory and if they have perms then move else return false;
                $filemoved = nexdoc_movefile($_CLEAN['fid'],$newcid);
                $data['cid'] = $newcid;
                // Return the tags for the AJAX handler to update the page. Use the cloud function to get tags so we are sure they were added
                $tpl = new Template($_CONF['path_layout'] . 'nexfile');
                $tpl->set_file(array(
                    'tag_link'              =>  'taglink_record.thtml',
                    'tag_rec'               =>  'tagdesc_record.thtml'
                    ));
                $tags = nexdoc_formatfiletags($tpl,$tagcloud->get_itemtags($_CLEAN['fid']));
                $data['tags'] = $tags;
            }
            DB_query("UPDATE {$_TABLES['nxfile_fileversions']} SET notes='{$_CLEAN['vernote']}' WHERE fid={$_CLEAN['fid']} and version={$_CLEAN['version']}");
            $data['tagcloud'] = $tagcloud->displaycloud();
            fm_updateAuditLog("Updated File FID: {$_CLEAN['fid']}");
        }
        $data['description'] = nl2br($_WEB['description']);
        $data['fid'] = $_CLEAN['fid'];
        $data['filename'] = $_WEB['filetitle'];
        $data['filemoved'] = $filemoved;
        $retval = json_encode($data);
        break;

    case 'deletefile':
        $fid = $filter->getCleanData('int',$_GET['fid']);
        $reportmode = $filter->getCleanData('char',$_GET['reportmode']);
        $listingFolder = $filter->getCleanData('int',$_GET['listingcid']);
        $data['fid'] = $fid;
        if ($reportmode == 'approvals') {
            $data['cid'] = DB_getItem($_TABLES['nxfile_filesubmissions'],'cid',"fid={$fid}");
        } else {
            $data['cid'] = DB_getItem($_TABLES['nxfile_files'],'cid',"fid={$fid}");
        }
        $message = '';

        if ($reportmode == 'approvals' AND fm_getPermission($data['cid'],'approval')) {

            $query = DB_query("SELECT cid,tempname,fname,notify FROM {$_TABLES['nxfile_filesubmissions']} WHERE id={$fid}");
            list ($cid,$tempname,$fname,$notify) = DB_fetchArray($query);
            if (!empty($tempname) AND file_exists("{$_FMCONF['storage_path']}{$cid}/submissions/$tempname")) {
                @unlink("{$_FMCONF['storage_path']}{$cid}/submissions/$tempname");
            }
            // Check for notification record for user
            if ($notify == 1) {
                fm_sendNotification($fid,"3");
            }
            DB_query("DELETE FROM {$_TABLES['nxfile_filesubmissions']} WHERE id={$fid}");
            $data['retcode'] = 200;
            $message = '<div class="pluginInfo aligncenter" style="width:100%;height:60px;padding-top:30px;">';
            $message .= 'File was sucessfully deleted. This message will clear in a couple seconds</div>';
            $data['displayhtml'] = nexdocsrv_generateFileListing($listingFolder,$reportmode);

        } elseif (nexdoc_deletefile($fid)) {   /* Includes security tests that user can delete this file */
            if (!in_array($reportmode,$validReportModes))  $ajaxBackgroundMode = true;
            $data['retcode'] = 200;
            $message = '<div class="pluginInfo aligncenter" style="width:100%;height:60px;padding-top:30px;">';
            $message .= 'File was sucessfully deleted. This message will clear in a couple seconds</div>';
            $data['displayhtml'] = nexdocsrv_generateFileListing($listingFolder,$reportmode);
            if (is_array($lastRenderedFiles) AND count($lastRenderedFiles) > 0) {
                $data['lastrenderedfiles'] = serialize($lastRenderedFiles);
            }
        } else {
            $data['retcode'] = 404;
        }

        $data['message'] = $message;
        $data['title'] = 'Delete Confirmation';
        $retval = json_encode($data);
        break;

    case 'deletecheckedfiles':
        if ($uid > 1) {
            $cid = $filter->getCleanData('int',$_POST['cid']);
            $reportmode = $filter->getCleanData('char',$_POST['reportmode']);
            $fileitems = $filter->getDbData('text',$_POST['checkeditems']);
            $files = explode(',',$fileitems);

            if (!empty($_POST['checkedfolders'])) {
                $folderitems = $filter->getDbData('text',$_POST['checkedfolders']);
                $folders = explode(',',$folderitems);
                foreach ($folders as $id) {
                    if ($_POST['reportmode'] == 'notifications') {
                        if ($id > 0 AND DB_count($_TABLES['nxfile_notifications'],array('id','uid'),array($id,$uid))) {
                              DB_query("DELETE FROM {$_TABLES['nxfile_notifications']} WHERE id={$id}");
                        }
                    } elseif ($id > 0 AND $_POST['multiaction'] == 'delete' AND fm_getPermission($id,'admin')) {
                        if (fm_delCategory($id)) {
                            // Remove any recent folder records for this category
                            DB_query("DELETE FROM {$_TABLES['nxfile_recentfolders']} WHERE cid=$id");
                        }
                    }
                }
                if (!in_array($reportmode,$validReportModes))  $ajaxBackgroundMode = true;
            }

            if ($_POST['reportmode'] == 'incoming') {
                foreach ($files as $id) {
                    if ($id > 0 AND DB_count($_TABLES['nxfile_import_queue'],'id',$id)) {
                        $query = DB_query("SELECT queue_filename as fname FROM {$_TABLES['nxfile_import_queue']} WHERE id={$id}");
                        list ($fname) = DB_fetchArray($query);
                        if (!empty($fname) AND file_exists($_FMCONF['storage_path'] . "queue/$fname")) {
                            @unlink($_FMCONF['storage_path'] . "queue/$fname");
                        }
                        DB_query("DELETE FROM {$_TABLES['nxfile_import_queue']} WHERE id={$id}");
                    }
                }
            } elseif ($_POST['reportmode'] == 'notifications') {
                foreach ($files as $id) {
                    if ($id > 0 AND DB_count($_TABLES['nxfile_notifications'],array('id','uid'),array($id,$uid))) {
                          DB_query("DELETE FROM {$_TABLES['nxfile_notifications']} WHERE id={$id}");
                    }
                }
            } else {
                foreach ($files as $id) {
                    if ($id > 0 ) {
                        nexdoc_deletefile($id);
                    }
                }
                if (!in_array($reportmode,$validReportModes))  $ajaxBackgroundMode = true;
            }

            $data['retcode'] = 200;
            $data['activefolder'] = nexdoc_displayActiveFolder($cid);
            $data['displayhtml'] = nexdocsrv_generateFileListing($cid,$_POST['reportmode']);
            if (is_array($lastRenderedFiles) AND count($lastRenderedFiles) > 0) {
                $data['lastrenderedfiles'] = serialize($lastRenderedFiles);
            }
        } else {
            $data['retcode'] = 500;
        }
        $retval = json_encode($data);
        break;

    case 'movecheckedfiles':
        $message = '';
        $cid = $filter->getCleanData('int',$_POST['cid']);
        $reportmode = $filter->getCleanData('char',$_POST['reportmode']);
        $fileitems = $filter->getDbData('text',$_POST['checkeditems']);
        $files = explode(',',$fileitems);

        $duplicates = 0;
        $movedfiles = 0;
        $newcid = $filter->getCleanData('int',$_POST['newcid']);
        if ($newcid > 0 AND $uid > 1 ) {
            foreach ($files as $id) {
                if ($id > 0 ) {
                    if ($reportmode == 'incoming') {
                        $fname = DB_getItem($_TABLES['nxfile_import_queue'],'orig_filename',"id={$id}");
                        $fnameDB = addslashes($fname);  // Need to add slashes for DB call to not fail
                        if (DB_count($_TABLES['nxfile_files'], array('cid','fname'), array("$newcid","$fnameDB")) > 0) {
                            $duplicates++;
                        } elseif (nexdoc_moveQueuefile($id,$newcid)) {
                            $movedfiles++;
                        }
                    } else {
                        $fname = DB_getItem($_TABLES['nxfile_files'],'fname',"fid=$id");
                        $fnameDB = addslashes($fname);  // Need to add slashes for DB call to not fail
                        if (DB_count($_TABLES['nxfile_files'], array('cid','fname'), array("$newcid","$fnameDB")) > 0) {
                            $duplicates++;
                        } elseif (nexdoc_movefile($id,$newcid)) {
                            $movedfiles++;
                        }
                    }
                }
            }
        }

        if ($movedfiles > 0) {
            $message = "Successfully moved $movedfiles files to this folder.";
            if ($duplicates > 0) {
                if ($duplicates == 1) {
                    $message .= "&nbsp;File could not be moved as it is a duplicate.";
                } else {
                    $message .= "&nbsp;$duplicates files could not be moved as they are duplicates.";
                }
            }
            $cid = $newcid;
        } elseif ($newcid == 0) {
           $message = 'Unable to move any files - Invalid new folder selected';
        } elseif ($duplicates > 0) {
            if ($duplicates == 1) {
                $message = "File could not be moved as it is a duplicate.";
            } else {
                $message = "$duplicates files could not be moved as they are duplicates.";
            }
        } else {
           $message = 'Unable to move any files - invalid folder or insufficient rights';
        }

        $data['retcode'] = 200;
        $data['cid'] = $cid;
        $data['movedfiles'] = $movedfiles;
        $data['message'] = $message;
        $data['activefolder'] = nexdoc_displayActiveFolder($cid);
        $data['displayhtml'] = nexdocsrv_generateFileListing($cid,$reportmode);
        $retval = json_encode($data);
        break;

    case 'approvefile':
        $id = $filter->getCleanData('int',$_GET['id']);
        if (nexdocsrv_approveFileSubmission($id)) {
            $data['displayhtml'] = nexdocsrv_generateFileListing(0,'approvals');
            $data = nexdocsrv_generateLeftSideNavigation($data);
            $data['retcode'] = 200;
        } else {
            $data['retcode'] = 400;
        }
        $retval = json_encode($data);

        break;

    case 'loadfiledetails':
        $fid = $filter->getCleanData('char',$_POST['id']);
        $reportmode = $filter->getCleanData('char',$_POST['reportmode']);

        $data['editperm'] = false;
        $data['deleteperm'] = false;
        $data['addperm'] = false;
        $data['lockperm'] = false;
        $data['notifyperm'] = false;
        $data['broadcastperm'] = false;

        $errmsg = '';
        $validfile = false;
        if ($reportmode == 'approvals') {
            if (DB_count($_TABLES['nxfile_filesubmissions'],'id',$fid)) {
                $firephp->log("File ID:$fid => Unapproved file, display fileie details");
                $firephp->dump('POST VARS',$_POST);
                $validfile = true;
                $sql = "SELECT file.id as fid,file.cid,file.title,file.fname,file.date,file.size,file.version,file.submitter,u.username, ";
                $sql .= "file.status,file.description,category.pid,category.name as folder,file.version_note as version_note,tags ";
                $sql .= "FROM {$_TABLES['nxfile_filesubmissions']} file ";
                $sql .= "LEFT JOIN {$_TABLES['nxfile_categories']} category ON file.cid=category.cid ";
                $sql .= "LEFT JOIN {$_TABLES['users']} u ON u.uid=file.submitter ";
                $sql .= "WHERE file.id=$fid ";
                $data = DB_fetchArray(DB_query($sql),false);
                $data['locked'] = false;
                $data['subscribed'] = false;
            }

        } else {
            if (DB_count($_TABLES['nxfile_files'],'fid',$fid)) {
                $validfile = true;
                $sql = "SELECT file.fid,file.cid,file.title,file.fname,file.date,file.size,file.version,file.submitter,u.username, ";
                $sql .= "file.status,detail.description,category.pid,category.name as folder,v.notes as version_note,file.status_changedby_uid ";
                $sql .= "FROM {$_TABLES['nxfile_files']} file ";
                $sql .= "LEFT JOIN {$_TABLES['nxfile_filedetail']} detail ON file.fid=detail.fid ";
                $sql .= "LEFT JOIN {$_TABLES['nxfile_categories']} category ON file.cid=category.cid ";
                $sql .= "LEFT JOIN {$_TABLES['nxfile_fileversions']} v ON v.fid=file.fid ";
                $sql .= "LEFT JOIN {$_TABLES['users']} u ON u.uid=file.submitter ";
                $sql .= "WHERE file.fid=$fid ORDER BY v.version DESC";
                $res = DB_fetchArray(DB_query($sql),false);
                $data = array_merge($data,$res);

                $tagcloud = new nexfileTagCloud();
                $data['tags'] = $tagcloud->get_itemtags($fid);

                // Check if file is locked
                if (($data['status']) == lockedstatus) {
                    $data['locked'] = true;
                } else {
                    $data['locked'] = false;
                }

                // Check and see if user has subscribed to this file
                $direct = false;
                $ignorefilechanges = false;
                // Check if user has an ignore file changes record or a subscribe to changes record for this file
                $query = DB_query("SELECT fid,ignore_filechanges FROM {$_TABLES['nxfile_notifications']} WHERE fid=$fid and uid=$uid");
                if (DB_numRows($query) == 1) {
                    $A = DB_fetchArray($query);
                    if ($A['ignore_filechanges'] == 1) {
                        $ignorefilechanges = true;
                    } else {
                        $direct = true;
                    }
                }
                $indirect = DB_count($_TABLES['nxfile_notifications'], array ('cid_changes','cid','uid'), array(1,$data['cid'],$uid));
                if (($direct or $indirect) AND !$ignorefilechanges) {
                    $data['subscribed'] = true;
                } else {
                    $data['subscribed'] = false;
                }
            }
        }

        if ($validfile) {
            $data['dispfolder'] = $data['folder'];
            $data['description'] = nl2br($data['description']);
            $data['version_note'] = nl2br($data['version_note']);
            $data['date'] = strftime('%b %d %Y %I:%M %p',$data['date']);
            $data['size'] = intval($data['size']);
            if ($data['size']/1000000 > 1) {
                $data['size'] = round($data['size']/1000000,2) . " MB";
            } elseif ($data['size']/1000 > 1) {
                $data['size'] = round($data['size']/1000,2) . " KB";
            } else {
                $data['size'] = round($data['size'],2) . " Bytes";
            }

            // Setup the folder option select HTML options
            $cid = intval($data['cid']);
            $folderoptions = fm_recursiveCatAdmin($cid,'0','1');
            if (fm_getPermission($data['cid'],'admin')) {
                $folderoptions = '<option value="0">Top Level</option>' . $folderoptions;
                $data['folderoptions'] = '<select name="folder" style="width:220px;">' . $folderoptions . '</select>';
            } else {
                $data['folderoptions'] = '<input type="text" name="folder" value="'.$data['folder']. '" READONLY />';
            }

            if (fm_getPermission($data['cid'],'admin')) {
                $data['downloadperm'] = true;
                $data['editperm'] = true;
                $data['deleteperm'] = true;
                $data['addperm'] = true;
                $data['lockperm'] = true;
                $data['notifyperm'] = true;
                $data['broadcastperm'] = true;
            } elseif ($data['locked']) {
                if ($data['status_changedby_uid'] == $uid) {
                    $data['lockperm'] = true;
                    $data['addperm'] = true;
                    if ($data['submitter'] == $uid) {
                        $data['deleteperm'] = true;
                    }
                } elseif ($data['status_changedby_uid'] > 0) {
                    if ($data['submitter'] == $uid) {
                        $data['lockperm'] = true;
                    } else {
                        $data['downloadperm'] = false;
                    }
                }
                $data['notifyperm'] = true;
            } elseif($uid > 1) {
                if ($data['submitter'] == $uid) {
                    $data['deleteperm'] = true;
                    $data['lockperm'] = true;
                }
                if (fm_getPermission($data['cid'],'upload_ver')) {
                    $data['addperm'] = true;
                }
                $data['notifyperm'] = true;
            }
            if (fm_getPermission($data['cid'],'view',0,false)) {
                $data['tagperms'] = true;   // Able to set or change tags
                if ($data['locked']) {
                    if ($data['submitter'] == $uid OR $data['status_changedby_uid'] == $uid) {
                        $data['downloadperm'] = true;
                    } else {
                        $data['downloadperm'] = false;
                    }
                } else {
                    $data['downloadperm'] = true;
                    if ($data['submitter'] == $uid) {
                        $data['editperm'] = true;
                    }
                }
            } else {
                $data['tagperms'] = false;
                $data['downloadperm'] = false;
            }

        } else {
            $errmsg = "Error - Invalid file: $fid";
        }
        $data['error'] =  $errmsg;
        $data['displayhtml'] = nexdocsrv_filedetails($fid,$reportmode);

        $retval = json_encode($data);
        break;

    case 'togglelock':
        $fid = $filter->getCleanData('int',$_GET['fid']);

        $data['error'] = '';
        $data['fid'] = $fid;
        if (DB_count($_TABLES['nxfile_files'],'fid',$fid)) {
            if (DB_getItem($_TABLES['nxfile_files'],'status',"fid=$fid") == 1) {
                fm_updateAuditLog("Lock File FID: $fid");
                DB_query("UPDATE {$_TABLES['nxfile_files']} SET status='2', status_changedby_uid='$uid' WHERE fid=$fid");
                $statUser = DB_getItem($_TABLES['users'], "username", "uid=$uid");
                $data['message'] =  'File Locked successfully';
                $data['locked_message'] = '* '. sprintf($LANG_nexfile['msg08'],$statUser);
                $data['locked'] = true;
            } else {
                fm_updateAuditLog("UnLock File FID: $fid");
                DB_query("UPDATE {$_TABLES['nxfile_files']} SET status='1', status_changedby_uid='$uid' WHERE fid=$fid");
                $data['message'] =  'File Un-Locked successfully';
                $data['locked'] = false;
            }
        } else {
            $data['error'] = 'Error locking file';
        }

        $retval = json_encode($data);
        break;

    case 'togglesubscribe':
        $fid = $filter->getCleanData('int',$_GET['fid']);
        $cid = $filter->getCleanData('int',$_GET['cid']);

        $data['error'] = '';
        $data['fid'] = $fid;
        $ret = nexdoc_updateFileSubscription($fid,'toggle');
        if ($ret['retcode'] === true) {
            $data['retcode'] = 200;
            if ($ret['subscribed'] === true) {
                $data['subscribed'] = true;
                $data['message'] = 'You will be notified of any new versions of this file';
                fm_updateAuditLog("Subscription record added for FID: $fid");
                $data['notifyicon'] = "{$_CONF['site_url']}/nexfile3/images/email-green.gif";
                $data['notifymsg'] = 'Notification Enabled - Click to change';
            } elseif ($ret['subscribed'] === false) {
                $data['subscribed'] = false;
                $data['message'] = 'You will not be notified of any new versions of this file';
                fm_updateAuditLog("Unsubscription record added for FID: $fid");
                $data['notifyicon'] = "{$_CONF['site_url']}/nexfile3/images/email-regular.gif";
                $data['notifymsg'] = 'Notification Disabled - Click to change';
            }
        } else {
            $data['error'] = 'Error accessing file record';
            $data['retcode'] = 404;
        }

        $retval = json_encode($data);
        break;

    case 'multisubscribe':
        if ($uid > 1 ) {
            $reportmode = $filter->getCleanData('char',$_POST['reportmode']);
            $fileitems = $filter->getDbData('text',$_POST['checkeditems']);
            $files = explode(',',$fileitems);
            foreach ($files as $fid) {
                nexdoc_updateFileSubscription($fid,'add');
            }
            $folderitems = $filter->getDbData('text',$_POST['checkedfolders']);
            $folders = explode(',',$folderitems);
            foreach ($folders as $cid) {
                if (DB_count($_TABLES['nxfile_notifications'],array('cid','uid'),array($cid,$uid)) == 0) {
                    $sql  = "INSERT INTO {$_TABLES['nxfile_notifications']} (cid,cid_newfiles,cid_changes,uid,date) ";
                    $sql .= "VALUES ({$cid},1,1,$uid,UNIX_TIMESTAMP() )";
                    DB_query($sql);
                }
            }
            $data['retcode'] =  200;
            $data = nexdocsrv_generateLeftSideNavigation($data);
            $data['displayhtml'] = nexdocsrv_generateFileListing(0,$reportmode);
        } else {
            $data['retcode'] = 500;
        }
        $retval = json_encode($data);
        break;

    case 'autocompletetag':
        $tagcloud = new nexfileTagCloud();
        $matches = $tagcloud->get_matchingtags($_GET['query']);
        $retval = implode("\n",$matches);

        break;

    case 'togglefavorite':
        $id = $filter->getCleanData('int',$_GET['id']);
        if ($_USER['uid'] > 1 AND $id >= 1) {
            if (DB_count($_TABLES['nxfile_favorites'],array('uid','fid'),array($_USER['uid'],$id))) {
                $data['favimgsrc'] = plugin_geticon_nexfile('favorite-off');
                DB_query("DELETE FROM {$_TABLES['nxfile_favorites']} WHERE uid={$_USER['uid']} AND fid=$id");
            } else {
                $data['favimgsrc'] = plugin_geticon_nexfile('favorite-on');
                DB_query("INSERT INTO {$_TABLES['nxfile_favorites']} (uid,fid) VALUES ({$_USER['uid']},$id)");
            }
            $data['retcode'] = 200;
        } else {
            $data['retcode'] = 400;
        }
        $retval = json_encode($data);
        break;

    case 'search':
        $query = $filter->getCleanData('text',$_GET['query']);
        if (!empty($query)) {
            $data['retcode'] = 200;
            $data['displayhtml'] = nexdoc_displaySearchListing($query);
            $data['header'] = nexdoc_formatHeader();
            $data['activefolder'] = nexdoc_displayActiveFolder(0,'search');
        } else {
            $data['retcode'] = 400;
        }

        $retval = json_encode($data);
        break;

    case 'searchtags':
        $tags = $filter->getDbData('text',$_GET['tags']);
        if (!empty($tags)) {
            $tpl = new Template($_CONF['path_layout'] . 'nexfile');
            $tpl->set_file('tagsearch_rec', 'tagsearchlink.thtml');
            $filter->initFilter();
            if (isset($_GET['removetag'])) {
                $_GET['removetag'] = stripslashes($_GET['removetag']);
                $removetag =  $filter->getDbData('text',$_GET['removetag']);
                $atags = explode(',',$tags);
                $key = array_search($removetag,$atags);
                if ($key !== false) {
                    unset($atags[$key]);
                }
                $tags = implode(',',$atags);
            } else {
                $removetag = '';
            }
            if (!empty($tags)) {
                $data['searchtags'] = stripslashes($tags);
                $atags = explode(',',$tags);
                if (count($atags) >= 1) {
                    foreach ($atags as $tag) {
                        $tag = trim($tag);  // added to handle extra space thats added when removing a tag - thats between 2 other tags
                        if (!empty($tag)) {
                            $tpl->set_var('searchtag',stripslashes($tag));
                            $tpl->parse('searchtags','tagsearch_rec',true);
                        }
                    }

                } else {
                    $tpl->parse('searchtags','tagsearch_rec');
                }

                $data['retcode'] =  200;
                $data['currentsearchtags'] = $tpl->get_var('searchtags');
                nexdoc_displayTagSearchListing($tags,$data);
                $data['header'] = nexdoc_formatHeader($cid);
                $data['activefolder'] = nexdoc_displayActiveFolder(0,'searchtags');
            } else {
                $data['retcode'] =  200;
                $data['currentsearchtags'] = '';
                $tagcloud = new nexfileTagCloud();
                $data['tagcloud'] = $tagcloud->displaycloud();
                $data['displayhtml'] = nexdocsrv_generateFileListing(0,'latestfiles');
                $data['header'] = nexdoc_formatHeader($cid);
                $data['activefolder'] = nexdoc_displayActiveFolder(0,'searchtags');
            }
        } else {
            $tagcloud = new nexfileTagCloud();
            $data['tagcloud'] = $tagcloud->displaycloud;
            $data['retcode'] =  203;    // Partial Information
        }
        $retval = json_encode($data);
        break;

    case 'markfavorite':
        if ($uid > 1 ) {
            $cid = $filter->getCleanData('int',$_POST['cid']);
            $reportmode = $filter->getCleanData('char',$_POST['reportmode']);
            $fileitems = $filter->getDbData('text',$_POST['checkeditems']);
            $files = explode(',',$fileitems);
            foreach ($files as $id) {
                if ($id > 0 AND !DB_count($_TABLES['nxfile_favorites'],array('uid','fid'),array($uid,$id))) {
                    DB_query("INSERT INTO {$_TABLES['nxfile_favorites']} (uid,fid) VALUES ($uid,$id)");
                }
            }

            $data['retcode'] =  200;
            $data['activefolder'] = nexdoc_displayActiveFolder($cid);
            $data['displayhtml'] = nexdocsrv_generateFileListing($cid,'flaggedfiles');
            $retval = json_encode($data);
        }
        break;

    case 'clearfavorite':
        if ($uid > 1 ) {
            $cid = $filter->getCleanData('int',$_POST['cid']);
            $reportmode = $filter->getCleanData('char',$_POST['reportmode']);
            $fileitems = $filter->getDbData('text',$_POST['checkeditems']);
            $files = explode(',',$fileitems);
            foreach ($files as $id) {
                if ($id > 0 AND DB_count($_TABLES['nxfile_favorites'],array('uid','fid'),array($uid,$id))) {
                    DB_query("DELETE FROM {$_TABLES['nxfile_favorites']} WHERE uid=$uid AND fid=$id");
                }
            }

            $data['retcode'] =  200;
            $data['activefolder'] = nexdoc_displayActiveFolder($cid);
            if ($cid > 0) {
                $reportmode = 'getallfiles';
            } else {
                $reportmode = 'flaggedfiles';
            }
            $data['displayhtml'] = nexdocsrv_generateFileListing($cid,$reportmode);
            $retval = json_encode($data);
        }
        break;

    case 'approvesubmissions':
        if ($uid > 1 ) {
            $reportmode = $filter->getCleanData('char',$_POST['reportmode']);
            $fileitems = $filter->getDbData('text',$_POST['checkeditems']);
            $files = explode(',',$fileitems);
            $approvedFiles = 0;
            foreach ($files as $id) {
                // Check if this is a valid submission record
                if ($id > 0 AND DB_count($_TABLES['nxfile_filesubmissions'],'id',$id)) {
                    // Verify that user has Admin Access to approve this file
                    $cid = DB_getItem($_TABLES['nxfile_filesubmissions'],'cid',"id=$id");
                    $firephp->log("Checking if user has admin approval");
                    if ($cid > 0 AND fm_getPermission($cid,array('admin','approval'),'',false)) {
                        $firephp->log("User has approval permission");
                        if (nexdocsrv_approveFileSubmission($id)) {
                            $approvedFiles++;
                        }
                    }
                }
            }
            if ($approvedFiles > 0) {
                $data['retcode'] =  200;
                $firephp->log("Approved $approvedFiles files");
                $data = nexdocsrv_generateLeftSideNavigation($data);
            } else {
                $data['retcode'] =  400;
            }

            $data['displayhtml'] = nexdocsrv_generateFileListing(0,$reportmode);
            $retval = json_encode($data);
        }
        break;

    case 'deletesubmissions':
        if ($uid > 1 ) {
            $reportmode = $filter->getCleanData('char',$_POST['reportmode']);
            $fileitems = $filter->getDbData('text',$_POST['checkeditems']);
            $files = explode(',',$fileitems);
            $deletedFiles = 0;
            foreach ($files as $id) {
                // Check if this is a valid submission record
                if ($id > 0 AND DB_count($_TABLES['nxfile_filesubmissions'],'id',$id)) {
                    // Verify that user has Admin Access to approve this file
                    $cid = DB_getItem($_TABLES['nxfile_filesubmissions'],'cid',"id=$id");
                    $firephp->log("Checking if user has admin approval");
                    if ($cid > 0 AND fm_getPermission($cid,array('admin','approval'),'',false)) {
                        $firephp->log("User has approval permission");
                        $query = DB_query("SELECT cid,tempname,fname,notify FROM {$_TABLES['nxfile_filesubmissions']} WHERE id={$id}");
                        list ($cid,$tempname,$fname,$notify) = DB_fetchArray($query);
                        if (!empty($tempname) AND file_exists("{$_FMCONF['storage_path']}{$cid}/submissions/$tempname")) {
                            @unlink("{$_FMCONF['storage_path']}{$cid}/submissions/$tempname");
                        }
                        // Check for notification record for user
                        if ($notify == 1) {
                            fm_sendNotification($id,"3");
                        }
                        DB_query("DELETE FROM {$_TABLES['nxfile_filesubmissions']} WHERE id={$id}");
                        $deletedFiles++;
                    }
                }
            }
            if ($deletedFiles > 0) {
                $data['retcode'] =  200;
                $firephp->log("Deleted $deletedFiles files");
                $data = nexdocsrv_generateLeftSideNavigation($data);
            } else {
                $data['retcode'] =  400;
            }

            $data['displayhtml'] = nexdocsrv_generateFileListing(0,$reportmode);
            $retval = json_encode($data);
        }
        break;

    case 'deletequeuefile':
        $fid = $filter->getCleanData('int',$_GET['fid']);
        $data['fid'] = $fid;
        $message = '';

        if (DB_count($_TABLES['nxfile_import_queue'],'id',$fid)) {
            $query = DB_query("SELECT queue_filename as fname FROM {$_TABLES['nxfile_import_queue']} WHERE id={$fid}");
            list ($fname) = DB_fetchArray($query);
            if (!empty($fname) AND file_exists("{$_FMCONF['storage_path']}queue/$fname")) {
                @unlink("{$_FMCONF['storage_path']}queue/$fname");
            }
            DB_query("DELETE FROM {$_TABLES['nxfile_import_queue']} WHERE id={$fid}");
            $data['retcode'] = 200;
            $data = nexdocsrv_generateLeftSideNavigation($data);
            $data['displayhtml'] = nexdocsrv_generateFileListing(0,'incoming');
        } else {
            $data['retcode'] = 500;
        }

        $retval = json_encode($data);
        break;

    case 'movequeuefile':
        $filter->cleanData('int',array('id' => $_POST['id'],'newcid' => $_POST['newcid']));
        $_CLEAN = $filter->normalize($filter->getDbData());
        $filename = DB_getItem($_TABLES['nxfile_import_queue'],'orig_filename',"id={$_CLEAN['id']}");
        if (file_exists("{$_FMCONF['storage_path']}{$_CLEAN['newcid']}/{$filename}")) {
            $data['retcode'] = 500;
            $data['errmsg'] = $LANG_FMERR['err18'];
        } elseif (nexdoc_moveQueuefile($_CLEAN['id'],$_CLEAN['newcid'])) {
            $data['retcode'] = 200;
            $data = nexdocsrv_generateLeftSideNavigation($data);
            $data['displayhtml'] = nexdocsrv_generateFileListing(0,'incoming');
        } else {
            $data['errmsg'] = $GLOBALS['fm_errmsg'];
            $data['retcode'] = 500;
        }
        $retval = json_encode($data);
        break;

    case 'deletenotification':
        $id = COM_applyFilter($_GET['id'],true);
        if ($uid > 1 AND $id > 0) {
            DB_query("DELETE FROM {$_TABLES['nxfile_notifications']} WHERE id=$id AND uid=$uid");
            $data['retcode'] = 200;
            $data['displayhtml'] = nexdoc_generateNotificationsReport();
        } else {
            $data['retcode'] = 500;
        }
        $retval = json_encode($data);
        break;

    case 'clearnotificationlog':
        DB_query("DELETE FROM {$_TABLES['nxfile_notificationlog']} WHERE target_uid=$uid");
        $data['retcode'] = 200;
        $retval = json_encode($data);
        break;

    case 'updatenotificationsettings':
        if ($uid > 1) {
            if (!DB_count($_TABLES['nxfile_usersettings'],'uid',$uid)) {
                DB_query("INSERT INTO {$_TABLES['nxfile_usersettings']} (uid) VALUES ({$uid})");
            }
            $filter->cleanData('int',array(
                'fileadded' => $_POST['fileadded_notify'],
                'filechanged' => $_POST['fileupdated_notify'],
                'broadcasts' => $_POST['admin_broadcasts']
                ));
            $_CLEAN = $filter->normalize($filter->getDbData());
            $sql = "UPDATE {$_TABLES['nxfile_usersettings']} SET notify_newfile={$_CLEAN['fileadded']}, ";
            $sql .= "notify_changedfile={$_CLEAN['filechanged']}, allow_broadcasts={$_CLEAN['broadcasts']} ";
            $sql .= "WHERE uid=$uid";
            DB_query($sql);
            $data['retcode'] = 200;
            $data['displayhtml'] = nexdoc_generateNotificationsReport();
        } else {
            $data['retcode'] = 500;
        }
        $retval = json_encode($data);
        break;

    case 'updatefoldersettings':
        $filter->cleanData('int',array(
            'cid' => $_POST['cid'],
            'fileadded' => $_POST['fileadded_notify'],
            'filechanged' => $_POST['filechanged_notify']
            ));
        $_CLEAN = $filter->normalize($filter->getDbData());
        if ($uid > 1 AND $_CLEAN['cid'] >= 1) {
            // Update the personal folder notifications for user
            if (DB_count($_TABLES['nxfile_notifications'],array('cid','uid'),array($_CLEAN['cid'],$uid)) == 0) {
                $sql  = "INSERT INTO {$_TABLES['nxfile_notifications']} (cid,cid_newfiles,cid_changes,uid,date) ";
                $sql .= "VALUES ({$_CLEAN['cid']},{$_CLEAN['fileadded']},{$_CLEAN['filechanged']},$uid,UNIX_TIMESTAMP() )";
                DB_query($sql);
            } else {
                $sql  = "UPDATE {$_TABLES['nxfile_notifications']} set cid_newfiles = {$_CLEAN['fileadded']}, ";
                $sql .= "cid_changes={$_CLEAN['filechanged']}, date=UNIX_TIMESTAMP() ";
                $sql .= "WHERE uid=$uid and cid={$_CLEAN['cid']}";
                DB_query($sql);
            }
            $data['retcode'] = 200;
            $data['displayhtml'] = nexdoc_generateNotificationsReport();
        } else {
            $data['retcode'] = 500;
        }

        $retval = json_encode($data);
        break;

    case 'broadcastalert':
        $fid = COM_applyFilter($_GET['fid'],true);
        $message = $filter->getDbData('text',$_GET['message']);
        $target_users = array();
        if ($_FMCONF['allow_broadcasts'] == 1) {  // Site default set to allow broadcast enabled
            $queryNotifyUsers = DB_query("SELECT uid FROM {$_TABLES['users']} WHERE status = " . USER_ACCOUNT_ACTIVE);
            while ( list($uid) = DB_fetchARRAY($queryNotifyUsers)) {
                if ($uid != $_USER['uid']) {
                    if (DB_count($_TABLES['nxfile_usersettings'],array('uid','allow_broadcasts',array($uid,0))))  {
                        $personalSetting = false;   // Found user setting to not be notified
                    } else {
                        $personalSetting = true;
                    }
                    // Only want to notify users that don't have setting disabled or exception record
                    if ($personalException == FALSE AND $uid != $_USER['uid']) {
                        $target_users[] = $uid;
                    }
                }
            }

        } else {
            $sql = "SELECT a.uid FROM {$_TABLES['nxfile_usersettings']} a, "
                 . "LEFT JOIN {$_TABLES['users']} b on b.uid=a.uid "
                 . "WHERE a.allow_broadcasts = 1 and b.status=" . USER_ACCOUNT_ACTIVE;
            $queryNotifyUsers = DB_query($sql);
            while ( list($uid) = DB_fetchARRAY($queryNotifyUsers)) {
                if ($uid != $_USER['uid']) {
                    $target_users[] = $uid;
                }
            }
        }

        /* Send out Notifications to all users on distribution
         * Use the Bcc feature of COM_mail (added June/2009)
         * To send to complete distribution as one email and not loop thru distribution sending individual emails
        */
        $lastuser = 0;
        $type = 5; // Notification message type - Broadcast message
        $sql = "SELECT file.title,file.cid,file.submitter,category.name FROM "
             . "{$_TABLES['nxfile_files']} file, {$_TABLES['nxfile_categories']} category "
             . "WHERE file.cid=category.cid and file.fid={$fid}";

        $query = DB_query($sql);
        list($filename,$cid,$submitter,$catname) = DB_fetchARRAY($query);
        foreach ($target_users as $target_uid) {
            // Check that user has view access to this folder
            if ($target_uid != $lastuser AND fm_getPermission($cid,'view')) {
                $query = DB_query("SELECT username,email FROM {$_TABLES['users']} WHERE uid=$target_uid");
                list ($username,$email) = DB_fetchArray($query);
                if (!empty($email)) {
                    $distribution[] = $email;
                    $sql = "INSERT INTO {$_TABLES['nxfile_notificationlog']} (target_uid,submitter_uid,notification_type,fid,cid,datetime) "
                         . "VALUES ($target_uid,{$_USER['uid']},$type,$fid,$cid,UNIX_TIMESTAMP() )";
                    DB_query($sql);
                }
                $lastuser = $target_uid;
            }
        }
        $subject = "{$_CONF['site_name']} - {$LANG_FM10[$type]['SUBJECT']}";
        $message .= "\n\n";
        $message .= sprintf($LANG_FM10[$type]['LINE1'], $filename,$catname,"{$_CONF['site_url']}/nexfile/index.php?cid={$cid}");
        $message .= $LANG_FM10[$type]['LINE2'];
        if (fm_sendEmail($distribution,$subject,$message)) {
            $data['retcode'] = 200;
            $data['count'] = count($distribution);
        } else {
            $data['retcode'] = 500;
        }

        $retval = json_encode($data);
        break;

    default:
        $errmsg = "Error - invalid operation: $op";
        $data['error'] =  $errmsg;
        $retval = json_encode($data);
        break;

}
$firephp->groupEnd();

if ($op != 'autocompletetag') {
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('content-type: application/xml',true);
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
}


echo $retval;
//$exectime = $mytimer->stopTimer();
//$firephp->log("End of AJAX Server processing - time:$exectime");

?>
