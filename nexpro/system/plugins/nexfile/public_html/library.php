<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v3.0.2 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | library.php                                                               |
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

// this file can't be used on its own
if (strpos ($_SERVER['PHP_SELF'], 'library.php') !== false) {
    die ('This file can not be used on its own.');
}

$paddingsize = $_FMCONF['paddingsize'];
$filedescriptionOffset = $_FMCONF['filedescriptionOffset'];

require_once( $_CONF['path_system'] . 'nexpro/classes/tagcloud.class.php' );
$actionurl = "{$_CONF['site_url']}/nexfile/index.php";
$validReportModes = array (
    'latestfiles',
    'notifications',
    'lockedfiles',
    'downloads',
    'flaggedfiles',
    'unread',
    'myfiles',
    'approvals',
    'incoming',
    'searchtags',
    'search');
if (isset($customNexfileReportModes) AND count($customNexfileReportModes) > 0) {
    $validReportModes = array_merge($validReportModes,$customNexfileReportModes);
}

$ajaxBackgroundMode = false;    // Set true to activate the background AJAX driven file listing mode
$lastModifiedFolderDate = 0;    // Used in nexdoc_displayFolderListing() to determine the most recent data and bubble the date up for all parents.
$selectedTopLevelFolder = 0;    // Current top level folder - used to reset the last modified folder date when displaying folders

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

/* Rendering the files in a folder can be a 3 pass process to not delay the loading of the main user interface for very large folders
   The inital pass (pass 1) is used when the UI and folder listing is first being generated for the user
   Primary objective is to render all the folders and some initial files. If there are more files in the folder, then display a note
   AJAX requests will be queue for all folders with more files to display
   If there are folders with a large number of files say > 100, then don't limit the record count in pass 2
   Users will be presented with a link to click on for these folders to get rest of files.
   These large folders may not even be of interest to the user or contain old files so let's not take up processing time unless it's needed.
*/
$lastRenderedFiles = array();
$lastRenderedFolder = 0;
$recordCountPass1 = 2;        // Number of intial files to show when generating folder file listing
$recordCountPass2 = 10;      // Number of files to return to the AJAX get filelisting routine

if($_USER['uid'] < 2) {
    $uid = 0;
} else {
    $uid = $_USER['uid'];
}

/* Recursive Function to display folder listing */
function nexdoc_displayFolderListing($template,$id=0,$reportmode='',$level=0,$folderprefix='',$rowid=1) {
   // COM_errorLog("nexdoc_displayFolderListing - folder:$id, level $level");
    global $_CONF,$_TABLES,$actionurl,$allowableViewFolders,$paddingsize;
    global $allowableViewFoldersSql,$lastModifiedFolderDate,$selectedTopLevelFolder;

    if ($id > 0 AND !in_array($id,$allowableViewFolders)) {
        COM_errorLog("No view access to category $id");
        return;
    }
    $template->set_var('imgset', "{$_CONF['layout_url']}/nexfile/images");
    $retval = '';

    $level++;
    if ($level == 1) {
        $retval .= nexdoc_displayFileListing($template,$id,$reportmode,$level,$folder_number);
    }

    $sql = '';
    if (function_exists('nexfile_customReportFoldersSQL')) {
        $sql = trim(nexfile_customReportFoldersSQL($id,$reportmode));
    }
    if ($id > 0 OR !empty($sql)) {
        // Show any subfolders and check and see if this is a custom report

        if (empty($sql)) {
            $sql  = "SELECT DISTINCT cid,pid,name,description,folderorder FROM {$_TABLES['nxfile_categories']} WHERE pid=$id ";
            if (!empty($allowableViewFoldersSql)) {
                $sql .= "AND cid in ($allowableViewFoldersSql) ";
            }
            $sql .= "ORDER BY folderorder";
        }
        $resFolders = DB_QUERY($sql);
        if (DB_numRows($resFolders) > 0) {
            $i = $rowid;
            while ( list($folderId,$pid,$folderName,$folderDesc,$order) = DB_fetchARRAY($resFolders)) {
                if (empty($folderprefix)) {
                    $formattedFolderNumber = $i;
                } else {
                    $formattedFolderNumber = "{$folderprefix}.{$i}";
                }
                if ($pid == $selectedTopLevelFolder) $lastModifiedFolderDate = 0;
                //COM_errorLog("nexdoc_displayFolderListing - cid:$folderId, pid: $pid");
                $subfolderlisting = nexdoc_displayFileListing($template,$folderId,$reportmode,$level,$formattedFolderNumber);

                if (DB_count($_TABLES['nxfile_categories'],'pid',$folderId)) {
                    // Show any sub-subfolders - calling this function again recursively
                    $subfolderlisting .= nexdoc_displayFolderListing($template,$folderId,$reportmode,$level,$formattedFolderNumber);
                }
                $template->set_var('padding_right',0);
                $template->set_var('folder_desc_padding_left',23 + (($level) * 30) );   // Version 3.0 - not presently used
                $template->set_var('folder_id',$folderId);
                $template->set_var('parent_folder_id',$pid);
                $template->set_var('folder_name',$folderName);
                $template->set_var('folder_description',$folderDesc);
                $template->set_var('folder_link',"{$_CONF['site_url']}/nexfile/index.php?cid={$folderId}");
                $template->set_var('folder_contents',$subfolderlisting);
                $template->set_var('folder_number',"{$formattedFolderNumber}.0");
                if ($lastModifiedFolderDate > 0) {
                    $template->set_var('last_modified_date', strftime($_CONF['shortdate'],$lastModifiedFolderDate));
                } else {
                    $template->set_var('last_modified_date', '');
                }
                //nexdoc_formatNotificationIcon($template,0,$folderId);

                // For the checkall files - need to set the inline files
                // and can't be done in nexdoc_displayFileListing since a folder can have subfolders
                // and template var in parent folder is being over-written
                $resFiles = DB_QUERY("SELECT fid from {$_TABLES['nxfile_files']} WHERE cid=$folderId");
                $files = array();
                while ($A = DB_fetchArray($resFiles)) {
                    $files[] = $A['fid'];
                }
                $template->set_var('folder_files',implode(',',$files));
                if (fm_getPermission($folderId,'admin')) {
                    if ($order == 10) {
                        $template->set_var('hide_moveup','none');
                    } else {
                        $template->set_var('hide_moveup','');
                    }
                    if ($order < $maxorder) {
                        $template->set_var('hide_movedown','');
                    } else {
                        $template->set_var('hide_movedown','none');
                    }
                    $template->parse('onhover_move_options','movefolder');
                } else {
                    $template->set_var('onhover_move_options','');
                }
                $template->set_var('folder_padding_left',($level * $paddingsize) );
                $template->parse('folderlisting','subfolder');
                $retval .= $template->get_var('folderlisting');
                $i++;
            }
        } elseif ($level == 1) {
            $retval .= "<div id=\"subfolder{$GLOBALS['lastRenderedFiles'][0][0]}_rec{$GLOBALS['lastRenderedFiles'][0][1]}_bottom\">";
        }
    }

    return $retval;
}


function nexdoc_displayFileListing($template,$cid=0,$reportmode='',$level,$foldernumber) {
    global $_CONF,$_TABLES,$_FMCONF,$uid,$recordCountPass1,$recordCountPass2;
    global $allowableViewFoldersSql,$LANG_nexfile,$validReportModes,$lastModifiedFolderDate,$lastRenderedFolder;
    global $paddingsize,$filedescriptionOffset,$ajaxBackgroundMode;

    $folderAdmin = false;
    $i = 1;
    $template->set_var('more_records_message','');
    $template->set_var('foldernumber',$foldernumber);
    $template->set_var('level',$level);

    $tagcloud = new nexfileTagCloud();
    // Show any files under this folder

    $sql = nexdoc_getFileListingSQL($cid,$reportmode);
    if ($cid > 0) {
        $template->set_var('showfolder','none');
        $folderAdmin = fm_getPermission($cid,'admin');
    }

    if ($reportmode == 'getmorefolderdata') {
        if (isset($_POST['pass2']) AND $_POST['pass2'] == 1) {
            $i = $recordCountPass1 + 1;
        } else {
            $i = $recordCountPass2 + 1;
        }
    } elseif ($reportmode != 'getallfiles') {
        if ($lastRenderedFolder == $cid) {
            $i = $recordCountPass1 + 1;
        }
    }

    $files = array();   // Needed to not display duplicate files in cases like the downloads report, see note above
    $resFiles = DB_query($sql);
    $numrows = DB_numRows($resFiles);
    if ($numrows > 0) {
        // Show any files in this directory
        $break = false;
        $template->set_var('show_ownername','none');
        if (in_array($reportmode,$validReportModes)) {
            $template->set_var('show_foldername','');
            if($reportmode == 'incoming') {
                $template->set_var('show_ownername','');
            }
        } else {
            $template->set_var('show_foldername','none');
        }
        while ( list($fid,$subfolderId,$title,$fname,$date,$version,$submitter,$status,$description,$category,$pid,$changedby_uid,$fsize) = DB_fetchARRAY($resFiles)) {
            if (!in_array($fid,$files)) {
                $tags = $tagcloud->get_itemtags($fid);
                $template->set_var('padding_left',($level * $paddingsize) + $paddingsize );
                $template->set_var('file_desc_padding_left',$filedescriptionOffset + ($level * $paddingsize) );
                if ($status == 2) {
                    $template->set_var('showlock','');
                } else  {
                    $template->set_var('showlock','none');
                }
                $template->set_var('details_link_parms', "?fid=$fid");
                $template->set_var('fid',$fid);
                $template->set_var('filesize',fm_formatFileSize($fsize));
                $template->set_var('file_name',$title);
                $template->set_var('modified_date', strftime($_CONF['shortdate'],$date));
                if ($i == 1 AND $date > $lastModifiedFolderDate) {
                    $lastModifiedFolderDate = $date;
                }
                $template->set_var('folder_link',"{$_CONF['site_url']}/nexfile/index.php?cid={$subfolderId}");
                $template->set_var('folder_name',$category);
                $template->set_var('file_number',"$foldernumber.$i");
                $template->set_var('subfolder_id',$subfolderId);

                if ($status > 0) {
                    nexdoc_formatFavoriteImage($template,$fid);
                    nexdoc_formatNotificationIcon($template,$fid,$data['cid']);
                    nexdoc_formatfiletags($template,$tags);
                }

                //$description = htmlspecialchars($description,ENT_QUOTES,$charset);
                $template->set_var('file_description',nl2br($description));
                if ($reportmode == 'approvals') {
                    $submitterName = DB_getItem($_TABLES['users'],'username',"uid=$submitter");
                    $template->set_var('action_link',"<a href=\"{$_CONF['site_url']}/users.php?mode=profile&uid={$submitter}\">{$submitterName}</a>");
                } elseif ($reportmode == 'incoming') {
                    if ($status == lockedstatus) {
                        $template->parse('download_action_link','download_disabled');
                        $template->set_var('edit_action_link','');
                    } else {
                        $template->set_var('download_link',"{$_CONF['site_url']}/nexfile/download.php?op=incoming&fid={$fid}");
                        $template->parse('download_action_link','download_action');
                        $template->set_var('edit_link',"{$_CONF['site_url']}/nexfile/download.php?op=editfile&fid={$fid}");
                        $template->parse('edit_action_link','editfile_action');
                    }

                } else {
                    if ($cid == 0 AND !empty($reportmode)) {
                        $folderAdmin = fm_getPermission($subfolderId,'admin');
                    }
                    if (!$folderAdmin AND $status == lockedstatus AND $changedby_uid != $uid ) {  // File locked but not by user
                        $statuser = DB_getItem($_TABLES['users'],'username', "uid=$changedby_uid");
                        $template->set_var ('download_title', sprintf($LANG_nexfile['msg08'],$statuser));
                        $template->parse('download_action_link','download_disabled');
                        $template->set_var('edit_action_link','');
                    } else {
                        $template->set_var('download_link',"{$_CONF['site_url']}/nexfile/download.php?op=download&fid={$fid}");
                        $template->parse('download_action_link','download_action');
                        if ($uid > 1) {
                            $template->set_var('edit_link',"{$_CONF['site_url']}/nexfile/download.php?op=editfile&fid={$fid}");
                            $template->parse('edit_action_link','editfile_action');
                        } else {
                            $template->set_var('edit_action_link','');
                        }
                    }
                }
                $pos = strrpos($fname,'.') + 1;
                $ext = strtolower(substr($fname, $pos));

                if (array_key_exists($ext, $_FMCONF['iconlib'] )) {
                    $icon = $_FMCONF['imagesurl'] . key($_FMCONF['iconlib'][$ext]);
                } else {
                    $icon = $_FMCONF['imagesurl'] . key($_FMCONF['iconlib']['none']);
                }
                $template->set_var('extension_icon',$icon);

                if ($ajaxBackgroundMode == true AND $i >= $recordCountPass1) {
                    $break = true;
                    $template->set_var('message_padding',100 + ($level * $paddingsize));
                    $GLOBALS['lastRenderedFiles'][] = array($cid,$fid,$foldernumber,$level);
                    $template->parse('more_records_message','moredata_msg');
                } elseif ($reportmode == 'getmoredata'  AND $i >= $recordCountPass2) {
                    $break = true;
                    $template->set_var('message_padding',100 + ($level * $paddingsize));
                    if ($numrows > ($recordCountPass2 - $recordCountPass1)) {
                        $template->parse('more_records_message','loadfolder_msg');
                    }
                }

                if ($reportmode == 'incoming') {
                    if ($i == 1) {
                        $template->parse('filelisting','incoming_rec');
                    } else {
                        $template->parse('filelisting','incoming_rec',true);
                    }
                } else {
                    if ($i == 1) {
                        $template->parse('filelisting','filelisting_rec');
                    } else {
                        $template->parse('filelisting','filelisting_rec',true);
                    }
                }
                $files[] = $fid;
                $i++;

                if ($break) {
                    break;
                }
            }
        }

    } else {
        $template->set_var('folder_desc_padding_left',60 + ($level * 20));   // Version 3.0 - not presently used
        $template->set_var('folder_id',$cid);
        $template->set_var('last_modified_date','');
        if ($level == 1) {
            $template->set_var('filelisting','');
        } else {
            $template->parse('filelisting','emptyfolder');
        }
    }

    return $template->finish ($template->get_var('filelisting'));

}

function nexdoc_formatFavoriteImage($template,$fid) {
    global $_TABLES,$uid;
    if ($uid > 1 AND $fid >= 1) {
        if (DB_count($_TABLES['nxfile_favorites'],array('uid','fid'),array($uid,$fid))) {
            $template->set_var('favorite_status_image',plugin_geticon_nexfile('favorite-on'));
            $template->set_var('LANG_favorite_status','Click to clear favorite');
        } else {
            $template->set_var('favorite_status_image',plugin_geticon_nexfile('favorite-off'));
            $template->set_var('LANG_favorite_status','Click to mark item as a favorite');
        }
    } else {
        $template->set_var('show_favorite','none');
    }
}


function nexdoc_formatNotificationIcon($template,$fid,$cid=0) {
    global $_TABLES,$_USER,$uid,$imgset;

    if (!isset($uid)) {
        if($_USER['uid'] < 2) {
            $uid = 0;
        } else {
            $uid = $_USER['uid'];
        }
    }

    // Check and see if user has subscribed to this file
    $direct = DB_count($_TABLES['nxfile_notifications'], array('fid','uid'), array($fid,$uid));
    $indirect = DB_count($_TABLES['nxfile_notifications'], array ('fid','cid','uid'), array(0,$cid,$uid));
    if ($fid == 0 AND $cid > 0) {
        if ($indirect) {
            $template->set_var('folder_notification_status','Notification Enabled - Click to change');
            $template->set_var('folder_notification_status_image','email-green.gif');
        } else {
            $template->set_var('folder_notification_status','Notification Disabled - Click to change');
            $template->set_var('folder_notification_status_image','email-regular.gif');
        }
    } else {
        if ($direct or $indirect) {
            $template->set_var('notification_status','Notification Enabled - Click to change');
            $template->set_var('notification_status_image','email-green.gif');
        } else {
            $template->set_var('notification_status','Notification Disabled - Click to change');
            $template->set_var('notification_status_image','email-regular.gif');
        }
    }

}


function nexdoc_formatfiletags ($template,$tags,$query='') {
    global $actionurl;

    $template->set_var('action_url',$actionurl);
    if (!empty($tags)) {
        $atags = explode(',',$tags);
        $asearchtags = explode(',',stripslashes($query));

        $i = 1;
        $stags = array();   // used to rebuild $tags - space delimited list of search tags
        foreach ($atags as $tag) {
            $tag = trim($tag);  // added to handle extra space thats added when removing a tag - thats between 2 other tags
            if (!empty($tag)) {
                if (!empty($query)) {
                    $template->set_var('searchtag', $query . ',' . $tag);
                } else {
                    $template->set_var('searchtag', $tag);
                }
                $template->set_var('desctag',stripslashes($tag));
                if ($i > 1) {
                    if (in_array($tag,$asearchtags)) {
                        $template->parse('tags','tag_rec',true);
                    } else {
                        $template->parse('tags','tag_link',true);
                    }
                } else {
                    if (in_array($tag,$asearchtags)) {
                        $template->parse('tags','tag_rec');
                    } else {
                        $template->parse('tags','tag_link');
                    }
                }
                $i++;
                $stags[] = $tag;
            }
        }
    } else {
        $template->set_var('tags',$tags);
    }
    $x = $template->get_var('tags');
    return $x;

}


function nexdoc_formatHeader($cid=0,$reportmode='') {
    global $_CONF;

    $tpl = new Template($_CONF['path_layout'] . 'nexfile');
    if ($reportmode == 'incoming') {
        $tpl->set_file('header','incoming_header.thtml');
    } else {
        $tpl->set_file('header','filelisting_header.thtml');
    }
    if($cid > 0) {
        $tpl->set_var('showfolder','none');
    } else {
        $tpl->set_var('showfolderexpandlink','none');
    }

    if ($reportmode == 'approvals') {
        $tpl->set_var('LANG_actionheading','Submitter');
    } else {
        $tpl->set_var('LANG_actionheading','Actions');
    }

    if($reportmode == 'incoming') {
        $tpl->set_var('show_ownername','none');
    }

    $tpl->set_var('LANG_dateheading','Modified');

    PLG_templateSetVars('nexfile_listingheader',$tpl);

    $tpl->parse ('output', 'header');
    return $tpl->finish ($tpl->get_var('output'));
}


function nexdoc_getCategoryOptions($cid) {
    global $_TABLES,$LANG_FM02;

    $category_options = '';
    if ($cid > 0) {
        $selected = DB_getItem($_TABLES['nxfile_categories'],'name',"cid=$cid");
    } else {
        $selected = '';
    }
    $category_options = fm_recursiveCatAdmin($selected,'0','1');

    if (SEC_hasRights('nexfile.admin')) {
        $category_options = LB . '<option value="0">'.$LANG_FM02['TOP_CAT'].'</option>' . LB . $category_options;
    } elseif (!fm_getPermission($pid,array ('admin'))) {
        $category_options = LB . '<option value="'.$pid.'" SELECTED=selected>'.$selected.'</option>' . LB . $category_options;
    }
    return $category_options;
}


function nexdoc_displayActiveFolder($cid=0,$reportmode='') {
    global $_CONF,$uid,$_TABLES,$_FMCONF,$LANG_FM12,$validReportModes;

    $tpl = new Template($_CONF['path_layout'] . 'nexfile');
    $tpl->set_file(array(
        'activefolder_container'    =>  'activefolder_container.thtml',
        'selectedcategory'          =>  'activefolder_selectedcategory.thtml',
        'toplevelcategory'          =>  'activefolder_toplevelcategory.thtml',
        'reportingview'             =>  'activefolder_reportingview.thtml',
        'activefolder_admin'        =>  'activefolder_admin.thtml',
        'activefolder_display'      =>  'activefolder_nonadmin.thtml',
        'activefolder_anonymous'    =>  'activefolder_anonymous.thtml',
        'breadcrumb_link'           =>  'folder_breadcrumb_record.thtml',
        'ajaxstatus'                =>  'ajaxstatus_div.thtml',
        'ajaxactivity'              =>  'ajaxactivity_div.thtml',
        'uploadbutton'              =>  'uploadfile_button.thtml'
        ));

    $tpl->set_var('site_url',$_CONF['site_url']);
    $tpl->set_var('layout_url',$_CONF['layout_url']);
    $tpl->set_var('show_activefolder','none');
    $tpl->set_var('show_reportmodeheader','none');
    $tpl->set_var('show_nonadmin','none');
    $tpl->set_var('show_folderheader','block');
    $tpl->set_var('show_breadcrumbs','block');

    if ($cid == 0) {
        if (in_array($reportmode,$validReportModes)) {
            $tpl->set_var('report_heading',$LANG_FM12[$reportmode]);
            $tpl->parse('ajaxstatus_reportmode','ajaxstatus');
            $tpl->parse('ajaxactivity_reportmode','ajaxactivity');
            $tpl->set_var('show_reportmodeheader','');
        } else {
            $tpl->parse('ajaxstatus_workspacemode','ajaxstatus');
            $tpl->parse('ajaxactivity_workspacemode','ajaxactivity');
        }
        $tpl->parse('selected_category_div','reportingview');
    } else {
        $tpl->set_var('cid',$cid);

        // Folder Stats
        $list = array();
        array_push($list,$cid);
        fm_getRecursiveCatIDs ($list,$cid,'view');
        $tpl->set_var('folder_count',count($list));
        $numfiles = 0;
        foreach ($list as $folderid) {
            $q = DB_query("SELECT count(fid) FROM {$_TABLES['nxfile_files']} WHERE cid=$folderid");
            list($x) = DB_fetchArray($q);
            $numfiles = $numfiles + $x;
        }
        $tpl->set_var('file_count',$numfiles);

        $query = DB_QUERY("SELECT pid,name,description from {$_TABLES['nxfile_categories']} WHERE cid='$cid'");
        list($pid,$activeFolderName,$folderDescription) = DB_fetchArray($query);
        if ($pid == 0) {
            $tpl->set_var('show_parentfolder','none');    // used in activefolder_admin.thtml only
        }

        $tpl->set_var('active_category_id',$cid);
        $tpl->set_var('active_folder_name',$activeFolderName);
        $tpl->set_var('folder_description',$folderDescription);

        $categoryOptions .= nexdoc_recursiveAccessOptions(array('admin'),$pid);
        $tpl->set_var('activefolder_options',$categoryOptions);
        $query = DB_query("SELECT cid_newfiles,cid_changes FROM {$_TABLES['nxfile_notifications']} WHERE cid=$cid and uid={$uid}");
        if (DB_numRows($query) == 1) {
            list ($fileadded,$filechanged) = DB_fetchArray($query);
            if ($fileadded == 1) $tpl->set_var('chk_fileadded',"CHECKED=checked");
            if ($filechanged == 1) $tpl->set_var('chk_filechanged',"CHECKED=checked");
        } else {
            if ($_FMCONF['notify_newfile'] == 1) $tpl->set_var('chk_fileadded',"CHECKED=checked");
            if ($_FMCONF['notify_changedfile'] == 1) $tpl->set_var('chk_filechanged',"CHECKED=checked");
        }
        if (fm_getPermission($cid,'admin')) {
            if (function_exists('nexfile_customFolderFields')) {
                nexfile_customFolderFields($tpl,'edit',$cid);
            }
            $tpl->parse('active_folder','activefolder_admin');
        } elseif ($uid > 1) {
            $tpl->set_var('show_nonadmin','');
            $tpl->parse('active_folder','activefolder_display');
        } else {
            $tpl->set_var('show_nonadmin','');
            $tpl->parse('active_folder','activefolder_anonymous');
        }

        // Display the folder breadcrumb trail
        if ($pid != 0) {
            $parent = $pid;
            $rootfolder = $cid;
            while ($parent != 0) {  // Determine the rootfolder
                $rootfolder = $parent;
                $parent = DB_getItem($_TABLES['nxfile_categories'],'pid',"cid=$parent");
            }
            $tpl->set_var('catid',$rootfolder);
            $tpl->set_var('padding_left',0);
            $tpl->set_var('folder_name', DB_getItem($_TABLES['nxfile_categories'],'name',"cid=$rootfolder"));
            $tpl->parse('folder_breadcrumb_links','breadcrumb_link');
            if ($rootfolder != $pid) {
                $query = DB_QUERY("SELECT cid,name,description from {$_TABLES['nxfile_categories']} WHERE cid='$pid' ");
                list($catid,$folderName,$folderDescription) = DB_fetchArray($query);
                $tpl->set_var('catid',$catid);
                $tpl->set_var('padding_left',5);
                $tpl->set_var('folder_action_url',"{$_CONF['site_url']}/nexfile/index.php?cid=$catid");
                $tpl->set_var('folder_name',$folderName);
                $tpl->parse('folder_breadcrumb_links','breadcrumb_link',true);
            }

            // Check if user's role allows upload and if they have permission
            if (fm_getPermission($cid,'admin') OR (in_array($role,array(1,2,3)) AND fm_getPermission($cid,'upload_dir'))) {
                $tpl->parse('upload_button','uploadbutton');
            }
            $tpl->parse('ajaxstatus_breadcrumbmode','ajaxstatus');
            $tpl->parse('ajaxactivity_breadcrumbmode','ajaxactivity');
            $tpl->set_var('show_activefolder','block');
            $tpl->set_var('show_folderheader','none');
            $tpl->parse('selected_category_div','selectedcategory');

        }  else {
            if (fm_getPermission($cid,'admin')) {
                $tpl->set_var('show_breadcrumbs','none');
                $tpl->set_var('show_activefolder','block');
                $tpl->parse('ajaxstatus_foldermode','ajaxstatus');
                $tpl->parse('ajaxactivity_foldermode','ajaxactivity');
                $tpl->parse('selected_category_div','selectedcategory');
            } else {
                $tpl->parse('ajaxstatus_workspacemode','ajaxstatus');
                $tpl->parse('ajaxactivity_workspacemode','ajaxactivity');
                $tpl->parse('selected_category_div','toplevelcategory');
            }

        }
    }

    $tpl->parse ('output', 'activefolder_container');
    return $tpl->finish ($tpl->get_var('output'));
}




function nexdoc_movefile($fid,$newcid) {
    global $_CONF,$_TABLES,$_USER,$_FMCONF;

    $filemoved = false;
    if ($newcid > 0) {
        $query = DB_query("SELECT fname,cid,version,submitter FROM {$_TABLES['nxfile_files']} WHERE fid={$fid}");
        list ($fname,$orginalCid,$curVersion,$submitter) = DB_fetchArray($query);
        if ($submitter == $_USER['uid'] OR fm_getPermission($newcid,'admin')) {
            if ($newcid !== $orginalCid) {
                // Check if there is more then 1 reference to this file in this category
                $fnameDB = addslashes($fname);  // Need to add slashes for DB call to not fail
                if (DB_count($_TABLES['nxfile_files'], array('cid','fname'), array("$orginalCid","$fname")) > 1) {
                    COM_errorLog("Checking for duplicate file - $orginalCid,$fname > yes");
                    $dupfile_inuse = true;
                } else {
                    COM_errorLog("Checking for duplicate file - $orginalCid,$fname > no");
                    $dupfile_inuse = false;
                }

                /* Need to move the file */
                $query2 = DB_query("SELECT id,fname FROM {$_TABLES['nxfile_fileversions']} WHERE fid={$fid}");
                while (list ($fileid,$vname) = DB_fetchArray($query2)) {
                    $vname = stripslashes($vname);
                    $sourcefile = $_FMCONF['storage_path'] . "{$orginalCid}/{$vname}";
                    if ( file_exists($sourcefile) )  {
                        COM_errorLog("Checking if file $sourcefile exists - true");
                        $targetfile = $_FMCONF['storage_path'] . "{$newcid}/{$vname}";
                        // If there is more then 1 reference to this file in this category
                        if ($dupfile_inuse) {
                            @copy($sourcefile,$targetfile);
                        } else {
                            if (file_exists($targetfile)) {
                                @unlink($sourcefile);
                            } else {
                                @rename($sourcefile,$targetfile);
                            }
                        }
                        $filemoved = true;
                    } else {
                        COM_errorLog("Checking if file $sourcefile exists - false");
                    }
                }
                if ($filemoved) {    // At least one file moved - so now update record
                    DB_query("UPDATE {$_TABLES['nxfile_files']} SET cid ='$newcid' WHERE fid={$fid}");
                }
            }
        } else {
            COM_errorLog("User {$_USER['username']} does not have access to move file: $fid {$fname} to category: $newcid");
        }
    }
    return $filemoved;

}


/* Move a file from the incoming Queue area to a repository category */
function nexdoc_moveQueuefile($id,$newcid) {
    global $_CONF,$_TABLES,$_USER,$_FMCONF;

    $filemoved = false;
    if ($newcid > 0) {
        $query = DB_query("SELECT orig_filename,queue_filename,timestamp,uid,size,mimetype FROM {$_TABLES['nxfile_import_queue']} WHERE id={$id}");
        list ($fname,$qname,$date,$submitter,$filesize,$mimetype) = DB_fetchArray($query);
        $sourcefile = $_FMCONF['storage_path'] . "queue/{$qname}";
        $targetfile = $_FMCONF['storage_path'] . "{$newcid}/{$fname}";

        if (!empty($qname) AND !empty($fname) AND file_exists($sourcefile)) {
            if ($submitter == $_USER['uid'] OR fm_getPermission($newcid,'admin')) {
                /* Need to move the file  */
                $pos = strrpos($fname,'.') + 1;
                $fileExtension = substr($fname, $pos);

                $ret = @rename($sourcefile,$targetfile);
                if ($ret AND file_exists($targetfile)) {
                    @unlink($sourcefile);
                    $filemoved = true;
                } elseif (file_exists($targetfile)) {
                    COM_errorLog("Move failed - file of same name exists - $sourcefile");
                    // Let's give the new file a random name and try the move again - add the numerical MonthDayHourSecond
                    $targetfile = $_FMCONF['storage_path'] . "{$newcid}/{$fname}-" . date('mdHms');
                    COM_errorLog("Attempting to move with a random name - $targetfile");
                    $ret = @rename($sourcefile,$targetfile);
                    if ($ret AND file_exists($targetfile)) {
                        @unlink($sourcefile);
                        $filemoved = true;
                    } else {
                        COM_errorLog("Move with random filename also failed");
                    }
                }
                if ($filemoved) {    // File successfully moved - create new records
                    // Set status of file to 1 - online
                    $fname = addslashes($fname);
                    $qname = addslashes($qname);
                    $sql =  "INSERT INTO {$_TABLES['nxfile_files']} (cid,fname,title,version,ftype,size,mimetype,extension,submitter,status,date) ";
                    $sql .= "VALUES ({$newcid},'$fname','{$fname}','1','file',";
                    $sql .= "'$filesize','$mimetype','$fileExtension',$submitter,1,'$date')";
                    DB_query($sql);

                    $fid = DB_insertId();  // New File ID
                    DB_query("INSERT INTO {$_TABLES['nxfile_filedetail']} (fid,description,hits,rating,votes,comments)
                        VALUES ('$fid','File uploaded with no description','0','0','0','0')");
                    DB_query("INSERT INTO {$_TABLES['nxfile_fileversions']} (fid,fname,ftype,version,notes,size,date,uid,status)
                        VALUES ('$fid','$fname','file','1','','$filesize','$date','$submitter','1')");

                    // Optionally add notification records and send out notifications to all users with view access to this new file
                    if (DB_getItem($_TABLES['nxfile_categories'], 'auto_create_notifications', "cid={$newcid}") == 1) {
                        fm_autoCreateNotifications($fid, $newcid);
                    }
                    // Send out notifications of update
                    if ($_POST['notification'] == 1) {
                        fm_sendNotification($fid);
                    }
                    fm_updateAuditLog("Direct upload of File ID: $fid, in Category: {$newcid}");

                    // Remove the incoming queue file
                    DB_query("DELETE FROM  {$_TABLES['nxfile_import_queue']} WHERE id={$id}");
                }  else {
                    $GLOBALS['fm_errmsg'] = 'Error moving file';
                }

            } else {
                COM_errorLog("User {$_USER['username']} does not have access to move file: $fid {$fname} to category: $newcid");
            }

        } else {
            $GLOBALS['fm_errmsg'] = "Error moving file - source file $gname missing";
            COM_errorLog("Nexfile: {$GLOBALS['fm_errmsg']}");
        }
    }

    return $filemoved;
}




/* AJAX Server common functions */


function nexdocsrv_getFileListingSummary($fid) {
    global $_CONF,$_TABLES,$_FMCONF,$paddingsize;

    $tagcloud = new nexfileTagCloud();
    // Show any files under this folder
    $sql = "SELECT file.fid as fid,file.cid,file.title,file.fname,file.date,file.version,file.submitter,file.status,";
    $sql .= "detail.description,category.name,category.pid ";
    $sql .= "FROM {$_TABLES['nxfile_files']} file ";
    $sql .= "LEFT JOIN {$_TABLES['nxfile_filedetail']} detail ON detail.fid=file.fid ";
    $sql .= "LEFT JOIN {$_TABLES['nxfile_categories']} category ON file.cid=category.cid ";
    if ($cid > 0) {
        $sql .= "WHERE file.cid=$cid ORDER BY date DESC";
        $template->set_var('showfolder','none');
    } else {
        $sql .= "ORDER BY file.date DESC ";
    }

    $resFiles = DB_query($sql);
    if (DB_numRows($resFiles) > 0) {
        // Show any files in this directory
        $i = 1;
        while ( list($fid,$subfolderId,$title,$fname,$date,$version,$submitter,$status,$description,$category,$pid) = DB_fetchARRAY($resFiles)) {
            $tags = $tagcloud->get_itemtags($fid);
            $template->set_var('padding_left',($level * $paddingsize) );
            $template->set_var('file_desc_padding_left',60 + ($level * 20) );

            $template->set_var('details_link_parms', "?fid=$fid");

            $template->set_var('fid',$fid);
            $template->set_var('file_name',$title);
            $template->set_var('folder_link',"{$_CONF['site_url']}/nexfile/index.php?cid={$subfolderId}");
            $template->set_var('folder_name',$category);
            $template->set_var('subfolder_id',$subfolderId);
            //$template->set_var('parent_folder_id',$pid);
            $template->set_var('tags',nexdoc_formatfiletags($tags));
            $template->set_var('file_description',nl2br($description));
            $template->set_var('download_link',"{$_CONF['site_url']}/nexfile/download.php?op=download&fid={$fid}");
            $template->parse('download_action_link','download_action');
            if ($uid > 1) {
                $template->set_var('edit_link',"{$_CONF['site_url']}/nexfile/download.php?op=editfile&fid={$fid}");
                $template->parse('edit_action_link','editfile_action');
            } else {
                $template->set_var('edit_action_link','');
            }
            $pos = strrpos($fname,'.') + 1;
            $ext = strtolower(substr($fname, $pos));
            if (array_key_exists($ext, $_FMCONF['iconlib'] )) {
                $icon = $_FMCONF['imagesurl'] . key($_FMCONF['iconlib'][$ext]);
            } else {
                $icon = $_FMCONF['imagesurl'] . key($_FMCONF['iconlib']['none']);
            }
            $template->set_var('extension_icon',$icon);
            if ($i == 1) {
                $template->parse('filelisting','filelisting_rec');
            } else {
                $template->parse('filelisting','filelisting_rec',true);
            }
            $i++;
        }

    } else {
        $template->set_var('folder_desc_padding_left',60 + ($level * 20));   // Version 3.0 - not presently used
        $template->parse('filelisting','emptyfolder');
    }

    return $template->finish ($template->get_var('filelisting'));

}


function nexdocsrv_filedetails($fid,$reportmode='') {
    global $_CONF,$_TABLES,$_FMCONF,$LANG_FM02,$LANG_nexfile,$actionurl;

    $tagcloud = new nexfileTagCloud();
    $page = new Template($_CONF['path_layout'] . 'nexfile');
    $page->set_file (array (
        'page'      =>  'filedetail.thtml',
        'versions'  =>  'filedetail_versions.thtml'
        ));

    if ($reportmode == 'approvals') {
        $sql = "SELECT file.cid,file.title,file.fname,file.date,file.version,file.size, ";
        $sql .= "file.description,file.submitter,file.status,file.version_note as notes,tags ";
        $sql .= "FROM {$_TABLES['nxfile_filesubmissions']} file ";
        $sql .= "WHERE file.id=$fid ";
    } else {
        $sql  = "SELECT file.cid, file.title, file.fname, file.date, file.version, file.size, ";
        $sql .= "detail.description, file.submitter, file.status, v.notes, '' as tags ";
        $sql .= "FROM {$_TABLES['nxfile_files']} file ";
        $sql .= "LEFT JOIN {$_TABLES['nxfile_filedetail']} detail ON file.fid=detail.fid ";
        $sql .= "LEFT JOIN {$_TABLES['nxfile_fileversions']} v ON v.fid=file.fid ";
        $sql .= "WHERE file.fid=$fid ORDER BY v.version DESC LIMIT 1";
    }

    $query = DB_query($sql);
    if (DB_numRows($query) > 0) {

        list($cid,$title,$fname,$date,$curVersion,$size,$description,$submitter,$status,$curVerNotes,$tags) = DB_fetchARRAY($query);
        if ($reportmode != 'approvals') {
            $tags = $tagcloud->get_itemtags($fid);
        }
        $shortdate = strftime($_CONF['shortdate'],$date);
        $size = fm_formatFileSize($size);
        $pos = strrpos($fname,'.') + 1;
        $ext = strtolower(substr($fname, $pos));
        if (array_key_exists($ext, $_FMCONF['iconlib'] )) {
            $icon = $_FMCONF['imagesurl'] . key($_FMCONF['iconlib'][$ext]);
        } else {
            $icon = $_FMCONF['imagesurl'] . key($_FMCONF['iconlib']['none']);
        }
        /* @TODO: Find out why status is not 0 and it's looking for a version */
        //if ($status == 0) {
            $author = DB_getItem($_TABLES['users'], "username", "uid=$submitter");
            $fullname = DB_getItem($_TABLES['users'], "fullname", "uid=$submitter");
        //} else {
        //    $curAuthorUid = DB_getItem($_TABLES['nxfile_fileversions'], "uid", "fid='$fid' AND version='$curVersion'");
        //    $author = DB_getItem($_TABLES['users'], "username", "uid='$curAuthorUid'");
        //    $fullname = DB_getItem($_TABLES['users'], "fullname", "uid=$curAuthorUid");
        //}
        $catname = DB_getItem($_TABLES['nxfile_categories'], "name", "cid=$cid");

        $page->set_var ('site_url',$_CONF['site_url']);
        $page->set_var ('layout_url',$_CONF['layout_url']);
        $page->set_var ('action_url',$actionurl);
        $page->set_var ('imgset', "{$_CONF['site_url']}/nexfile/images");
        $page->set_var ('heading',$heading);
        $page->set_var ('fid',$fid);
        $page->set_var ('shortdate',$shortdate);
        $page->set_var ('fname',$fname);
        $page->set_var ('current_version','(V'.$curVersion.')');
        $page->set_var ('filetitle',$title);
        $page->set_var ('author',"{$fullname}&nbsp;&nbsp;&nbsp;({$author})");
        $page->set_var ('description',nl2br($description));
        $page->set_var ('tags',$tags);
        $page->set_var ('catname',$catname);
        $page->set_var ('fileicon',$icon);
        $page->set_var ('size',$size);
        $page->set_var ('LANG_TAGS','Tags');
        $page->set_var ('LANG_SIZE',$LANG_FM02['SIZE']);
        $page->set_var ('LANG_AUTHOR',$LANG_FM02['AUTHOR']);
        $page->set_var ('LANG_CAT',$LANG_FM02['CAT']);
        $page->set_var ('LANG_DESCRIPTION',$LANG_FM02['DESCRIPTION']);
        $page->set_var ('LANG_VERSION_NOTE',$LANG_FM02['VERSION_NOTE']);
        $page->set_var ('LANG_DOWNLOAD',$LANG_FM02['DOWNLOAD']);
        $page->set_var ('LANG_DOWNLOAD_MESSAGE',$LANG_nexfile['msg61']);
        $page->set_var ('LANG_LINK_MESSAGE',$LANG_nexfile['msg65']);
        $page->set_var ('LANG_LASTUPDATED',$LANG_nexfile['msg62']);
        $page->set_var ('current_ver_note',nl2br($curVerNotes));

        if ($status == unapprovedstatus) {
            $statUser = DB_getItem($_TABLES['users'], "username", "uid=$submitter");
            $page->set_var ('status_image', '<img src="'.$_FMCONF['imagesurl'].'padlock.gif">');
            $page->set_var ('statusmessage', '* '. $LANG_nexfile['msg43']);
        } elseif ($status == lockedstatus) {
            $statUserUid = DB_getItem($_TABLES['nxfile_files'], "status_changedby_uid", "fid=$fid");
            $statUser = DB_getItem($_TABLES['users'], "username", "uid=$statUserUid");
            $page->set_var ('status_image', '<img src="'.$_FMCONF['imagesurl'].'padlock.gif">');
            $page->set_var ('statusmessage', '* '. sprintf($LANG_nexfile['msg08'],$statUser));
            $page->set_var ('LANG_DOWNLOAD_MESSAGE', sprintf($LANG_nexfile['msg08'],$statUser));
            $page->set_var('disable_download','onClick="return false;"');
        } else {
            $page->set_var('show_statusmsg','none');
            $page->set_var ('status_image','&nbsp;');
            $page->set_var ('statusmessage', '&nbsp;');
        }

        $query = DB_query("SELECT fname,version,notes,size,date,uid
                    FROM {$_TABLES['nxfile_fileversions']}
                    WHERE fid=$fid AND version < $curVersion ORDER by version DESC");

        $cssid = 1;
        while ( list($fname,$file_version,$ver_note,$ver_size,$ver_date,$submitter) = DB_fetchARRAY($query)) {
            $ver_shortdate = strftime($_CONF['shortdate'],$ver_date);
            $ver_longdate = COM_getUserDateTimeFormat($ver_date);
            $ver_longdate = $longdate[0];
            $ver_author = DB_getItem($_TABLES['users'], "username", "uid=$submitter");
            $ver_size = intval($ver_size);

            if ($ver_size/1000000 > 1) {
                $ver_size = round($ver_size/1048576,2) . " MB";
            } elseif ($ver_size/1000 > 1) {
                $ver_size = round($ver_size/1024,2) . " KB";
            } else {
                $ver_size = round($ver_size,2) .$LANG_FM02['BYTES'];
            }

            $pos = strrpos($fname,'.') + 1;
            $ext = strtolower(substr($fname, $pos));
            if (array_key_exists($ext, $_FMCONF['iconlib'] )) {
                $icon = $_FMCONF['imagesurl'] . key($_FMCONF['iconlib'][$ext]);
            } else {
                $icon = $_FMCONF['imagesurl'] . key($_FMCONF['iconlib']['none']);
            }
            $page->set_var ('vname',$fname);
            $page->set_var ('ver_shortdate',$ver_shortdate);
            $page->set_var ('ver_author',$ver_author);
            $page->set_var ('ver_size',$ver_size);
            $page->set_var ('ver_fileicon',$icon);
            $page->set_var ('file_versionnum','(V'.$file_version.')');
            $page->set_var ('file_version',$file_version);
            $page->set_var ('edit_version_note',$ver_note);
            $page->set_var ('version_note',nl2br($ver_note));
            if (fm_getPermission($cid,'admin')) {
                $page->set_var ('link_edit','<a href="'.$_SERVER['PHP_SELF']. '?op=editfile&fid='.$fid.'&version='.$file_version.'">' .$LANG_FM02['EDIT']. '</a>');
                $page->set_var ('link_delete','<a href="'.$_SERVER['PHP_SELF']. '?op=deletefile&fid='.$fid.'&version='.$file_version.'">' .$LANG_FM02['DELETE']. '</a>');
            }
            $page->set_var('cssid',$cssid);
            $cssid = ($cssid == 1) ? 2 : 1;
            $page->parse('version_records','versions',true);

        }
        $page->parse ('output', 'page');
        $retval = $page->finish ($page->get_var('output'));
    } else {
        $retval = "<p class=\"pluginAlert\">Error: nexdocsrv_filedetails($fid) - No file found.</p>";
    }
    return $retval;
}


function nexdocsrv_folderperms($cid) {
    global $_CONF,$_TABLES,$_FMCONF,$LANG_FM02,$LANG_nexfile,$actionurl;

    $tpl = new Template($_CONF['path_layout'] . 'nexfile');
    $tpl->set_file(array(
        'folderperms'           =>  'folderperms.thtml',
        'perms_record'          =>  'folderperms_record.thtml'
        ));

    $tpl->set_var('catid',$cid);
    $tpl->set_var('user_options',NXCOM_listUsers());
    $tpl->set_var('group_options',nexdoc_getGroupOptions());
    $sql = "SELECT accid,uid,grp_id,view,upload,upload_direct,upload_ver,approval,admin ";
    $sql .= "FROM {$_TABLES['nxfile_access']} WHERE uid > 0 AND catid = {$cid}";
    $query = DB_query($sql);
    while ( list($accid,$acc_uid,$acc_grpid,$acc_view,$acc_upload,$acc_uploaddirect,$acc_uploadver,$acc_approval,$acc_admin) = DB_fetchARRAY($query)) {
        $username = DB_getItem($_TABLES['users'], "username", "uid=$acc_uid");
        $view = ($acc_view) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $upload = ($acc_upload) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $uploaddir = ($acc_uploaddirect) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $uploadver = ($acc_uploadver) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $approve = ($acc_approval) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $admin = ($acc_admin) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $tpl->set_var(array(
            'accid'             => $accid,
            'username'          => $username,
            'view_perm'         => $view,
            'upload_perm'       => $upload,
            'uploaddir_perm'    => $uploaddir,
            'uploadver_perm'    => $uploadver,
            'approve_perm'      => $approve,
            'admin_perm'        => $admin));
        $tpl->parse('user_perm_records','perms_record',true);
    }

    $sql = "SELECT accid,uid,grp_id,view,upload,upload_direct,upload_ver,approval,admin ";
    $sql .= "FROM {$_TABLES['nxfile_access']} WHERE grp_id > 0 AND catid = {$cid}";
    $query = DB_query($sql);
    while ( list($accid,$acc_grpd,$acc_grpid,$acc_view,$acc_upload,$acc_uploaddirect,$acc_uploadver,$acc_approval,$acc_admin) = DB_fetchARRAY($query)) {
        $groupname = DB_getItem($_TABLES['groups'], "grp_name", "grp_id=$acc_grpid");
        $view = ($acc_view) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $upload = ($acc_upload) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $uploaddir = ($acc_uploaddirect) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $uploadver = ($acc_uploadver) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $approve = ($acc_approval) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $admin = ($acc_admin) ? $LANG_FM02['YES'] : $LANG_FM02['NO'];
        $tpl->set_var(array(
            'accid'             => $accid,
            'username'          => $groupname,
            'view_perm'         => $view,
            'upload_perm'       => $upload,
            'uploaddir_perm'    => $uploaddir,
            'uploadver_perm'    => $uploadver,
            'approve_perm'      => $approve,
            'admin_perm'        => $admin));
        $tpl->parse('group_perm_records','perms_record',true);
    }
    $tpl->parse('folder_perms_panel','folderperms');
    return $tpl->finish ($tpl->get_var('folder_perms_panel'));
}


/* Generate Left Side Navigation code which is used to create the YUI menu's in the AJAX handler javascript */
function nexdocsrv_generateLeftSideNavigation($data='') {
    global $_CONF,$_TABLES,$uid,$_FMCONF;

    if (empty($data)) $data = array();

    $approvals = fm_getSubmissionCnt();

    $data['reports'] = array();
    $data['topfolders'] = array();
    $data['reports'][] = array(
        'name' => "Latest&nbsp;Files",
        'link' => "reportmode=latestfiles",
        'parent' => 'allitems',
        'icon' => 'icon-filelisting');
    if ($uid > 1) {
        $data['reports'][] = array(
            'name' => "Notifications",
            'link' => "reportmode=notifications",
            'parent' => 'allitems',
            'icon' => 'icon-fileowned');
        $data['reports'][] = array(
            'name' => "Owned&nbsp;by&nbsp;me",
            'link' => "reportmode=myfiles",
            'parent' => 'allitems',
            'icon' => 'icon-fileowned');
        $data['reports'][] = array(
            'name' => "Downloaded&nbsp;by&nbsp;me",
            'link' => "reportmode=downloads",
            'parent' => 'allitems',
            'icon' => 'icon-fileowned');
        $data['reports'][] = array(
            'name' => "Unread&nbsp;Files",
            'link' => "reportmode=unread",
            'parent' => 'allitems',
            'icon' => 'icon-fileowned');
        $data['reports'][] = array(
            'name' => "Locked&nbsp;by&nbsp;me",
            'link' => "reportmode=lockedfiles",
            'parent' => 'allitems',
            'icon' => 'icon-filelocked');
        $data['reports'][] = array(
            'name' => "Flagged&nbsp;by&nbsp;me",
            'link' => "reportmode=flaggedfiles",
            'parent' => 'allitems',
            'icon' => 'icon-fileflagged');
    }
    if ($approvals > 0) {
        $approvals = "&nbsp;($approvals)";
        $data['reports'][] = array(
            'name' => "Waiting&nbsp;approval{$approvals}",
            'link' => "reportmode=approvals",
            'parent' => 'allitems',
            'icon' => 'icon-fileowned');
    }

    if (!SEC_hasRights('nexfile Admin')) {
            $incoming = DB_count($_TABLES['nxfile_import_queue']);
    } else {
        $incoming = DB_count($_TABLES['nxfile_import_queue']);
    }

    if ($incoming > 0) {
        $incoming_msg = "&nbsp;($incoming)";
            $data['reports'][] = array(
            'name' => "Incoming&nbsp;Files{$incoming_msg}",
            'link' => "reportmode=incoming",
            'parent' => 'allitems',
            'icon' => 'icon-fileowned');
    }

    // Setup the Most Recent folders for this user
    if ($uid > 1) {
        $sql  = "SELECT a.id,a.cid,b.name FROM {$_TABLES['nxfile_recentfolders']} a ";
        $sql .= "LEFT JOIN {$_TABLES['nxfile_categories']} b ON b.cid=a.cid WHERE uid=$uid ORDER BY id";
        $query = DB_query($sql);
        if (DB_numRows($query) > 0) {
            while ($A = DB_fetchArray($query)) {
                $data['recentfolders'][] = array(
                    'name' => "{$A['name']}",
                    'link' => "cid={$A['cid']}",
                    'icon' => 'icon-allfolders');
            }
        }
    }

    $query = DB_QUERY("SELECT cid,pid,name,description from {$_TABLES['nxfile_categories']} WHERE pid='0' ORDER BY CID");
    while ( list($categoryId,$pid,$name,$description) = DB_fetchARRAY($query)) {
        if (fm_getPermission($categoryId,'view')) {
            $data['topfolders'][] = array(
                'name' => $name,
                'link' => "cid=$categoryId",
                'parent' => 'allfolders',
                'icon' => 'icon-allfolders');
        }
    }

	if (function_exists(nexfile_customLeftsideNavigation)) {
		$data = nexfile_customLeftsideNavigation($data);
	}

    return $data;

}

function nexdocsrv_generateFileListing($cid=0,$reportmode='',$level=0,$rowid=1) {
    global $_CONF,$selectedTopLevelFolder;

    $selectedTopLevelFolder = $cid;
    $tpl = new Template($_CONF['path_layout'] . 'nexfile');
    $tpl->set_file(array(
        'filedetails'           =>  'filedetails.thtml',
        'subfolder'             =>  'filelisting_subfolder_record.thtml',
        'emptyfolder'           =>  'filelisting_emptyfolder.thtml',
        'filelisting_rec'       =>  'filelisting_record.thtml',
        'moredata_msg'          =>  'loading_more_records_message.thtml',
        'incoming_rec'          =>  'incoming_record.thtml',
        'folderlisting_rec'     =>  'leftnav_folder_record.thtml',
        'movefolder'            =>  'folder_onhover_move.thtml',
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
    $retval = nexdoc_displayFolderListing($tpl,$cid,$reportmode,$level,'',$rowid);
    return $retval;

}


function nexdocsrv_generateSearchListing($cid,$reportmode='') {
    global $_CONF;

    $tpl = new Template($_CONF['path_layout'] . 'nexfile');
    $tpl->set_file(array(
        'filedetails'           =>  'filedetails.thtml',
        'subfolder'             =>  'filelisting_subfolder_record.thtml',
        'emptyfolder'           =>  'filelisting_emptyfolder.thtml',
        'filelisting_rec'       =>  'filelisting_record.thtml',
        'folderlisting_rec'     =>  'leftnav_folder_record.thtml',
        'movefolder'            =>  'folder_onhover_move.thtml',
        'tag_link'              =>  'taglink_record.thtml',
        'tag_rec'               =>  'tagdesc_record.thtml',
        'download_action'       =>  'download_link.thtml',
        'editfile_action'       =>  'editfile_link.thtml'
        ));

    $tpl->set_var('site_url', $_CONF['site_url']);
    $tpl->set_var('layout_url', $_CONF['layout_url']);
    $tpl->set_var('imgset', "{$_CONF['layout_url']}/nexfile/images");
    $retval = nexdoc_displayFolderListing($tpl,$cid,$reportmode);
    return $retval;

}


function nexdoc_displaySearchListing($query) {
    global $_CONF,$_TABLES,$_FMCONF,$allowableViewFoldersSql,$paddingsize,$filedescriptionOffset;

    $tpl = new Template($_CONF['path_layout'] . 'nexfile');
    $tpl->set_file(array(
        'filedetails'           =>  'filedetails.thtml',
        'subfolder'             =>  'filelisting_subfolder_record.thtml',
        'emptyfolder'           =>  'filelisting_emptyfolder.thtml',
        'filelisting_rec'       =>  'filelisting_record.thtml',
        'folderlisting_rec'     =>  'leftnav_folder_record.thtml',
        'movefolder'            =>  'folder_onhover_move.thtml',
        'tag_link'              =>  'taglink_record.thtml',
        'tag_rec'               =>  'tagdesc_record.thtml',
        'download_action'       =>  'download_link.thtml',
        'editfile_action'       =>  'editfile_link.thtml'
        ));

    $tpl->set_var('layout_url', $_CONF['layout_url']);
    $tpl->set_var('imgset', "{$_CONF['layout_url']}/nexfile/images");
    $query = addslashes($query);
    $sql = "SELECT file.fid as fid,file.cid,file.title,file.fname,file.date,file.version,file.submitter,file.status,";
    $sql .= "detail.description,category.name,category.pid ";
    $sql .= "FROM {$_TABLES['nxfile_files']} file ";
    $sql .= "LEFT JOIN {$_TABLES['nxfile_filedetail']} detail ON detail.fid=file.fid ";
    $sql .= "LEFT JOIN {$_TABLES['nxfile_categories']} category ON file.cid=category.cid ";
	$sql .= "WHERE 1=1 ";
    if (!empty($allowableViewFoldersSql)) {
        $sql .= "AND file.cid in ($allowableViewFoldersSql) ";
    }
    $sql .= "AND (file.title LIKE '%$query%' OR detail.description LIKE '%$query%') ";
    $sql .= "ORDER BY file.date DESC ";


    $query = DB_query($sql);
    $numrows = DB_numRows($query);
    if ($numrows > 0) {
        $firephp = FirePHP::getInstance(true);
        $firephp->log("Search found: $numrows records");
        // Show any files in this directory
        $i = 1;
        while ( list($fid,$subfolderId,$title,$fname,$date,$version,$submitter,$status,$description,$category,$pid) = DB_fetchARRAY($query)) {
            $tpl->set_var('padding_left',(2 * $paddingsize) );
            $tpl->set_var('file_desc_padding_left',$filedescriptionOffset);
            if ($status == 2) {
                $tpl->set_var('showlock','');
            } else  {
                $tpl->set_var('showlock','none');
            }
            $tpl->set_var('details_link_parms', "?fid=$fid");
            $tpl->set_var('fid',$fid);
            $tpl->set_var('file_name',$title);
            $tpl->set_var('modified_date', strftime($_CONF['shortdate'],$date));
            $tpl->set_var('folder_link',"{$_CONF['site_url']}/nexfile/index.php?cid={$subfolderId}");
            $tpl->set_var('folder_name',$category);
            $tpl->set_var('subfolder_id',$subfolderId);
            $tpl->set_var('file_description',nl2br($description));
            $tpl->set_var('download_link',"{$_CONF['site_url']}/nexfile/download.php?op=download&fid={$fid}");
            $tpl->parse('download_action_link','download_action');
            if ($uid > 1) {
                $tpl->set_var('edit_link',"{$_CONF['site_url']}/nexfile/download.php?op=editfile&fid={$fid}");
                $tpl->parse('edit_action_link','editfile_action');
            } else {
                $tpl->set_var('edit_action_link','');
            }
            $pos = strrpos($fname,'.') + 1;
            $ext = strtolower(substr($fname, $pos));
            if (array_key_exists($ext, $_FMCONF['iconlib'] )) {
                $icon = $_FMCONF['imagesurl'] . key($_FMCONF['iconlib'][$ext]);
            } else {
                $icon = $_FMCONF['imagesurl'] . key($_FMCONF['iconlib']['none']);
            }
            $tpl->set_var('extension_icon',$icon);
            if ($i == 1) {
                $tpl->parse('filelisting','filelisting_rec');
            } else {
                $tpl->parse('filelisting','filelisting_rec',true);
            }
            $i++;
        }

    } else {
        $tpl->set_var('folder_desc_padding_left',$filedescriptionOffset);   // Version 3.0 - not presently used
        $tpl->parse('filelisting','emptyfolder');
    }

    return $tpl->finish ($tpl->get_var('filelisting'));
}


function nexdoc_displayTagSearchListing($query,&$data) {
    global $_CONF,$_TABLES,$_FMCONF,$allowableViewFoldersSql,$paddingsize,$filedescriptionOffset;

    $tpl = new Template($_CONF['path_layout'] . 'nexfile');
    $tpl->set_file(array(
        'filedetails'           =>  'filedetails.thtml',
        'subfolder'             =>  'filelisting_subfolder_record.thtml',
        'emptyfolder'           =>  'filelisting_emptyfolder.thtml',
        'filelisting_rec'       =>  'filelisting_record.thtml',
        'folderlisting_rec'     =>  'leftnav_folder_record.thtml',
        'movefolder'            =>  'folder_onhover_move.thtml',
        'tag_link'              =>  'taglink_record.thtml',
        'tag_rec'               =>  'tagdesc_record.thtml',
        'download_action'       =>  'download_link.thtml',
        'editfile_action'       =>  'editfile_link.thtml'
        ));
    $tpl->set_var('layout_url', $_CONF['layout_url']);
    $tpl->set_var('imgset', "{$_CONF['layout_url']}/nexfile/images");
    $sql = "SELECT file.fid as fid,file.cid,file.title,file.fname,file.date,file.version,file.submitter,file.status,";
    $sql .= "detail.description,category.name,category.pid ";
    $sql .= "FROM {$_TABLES['nxfile_files']} file ";
    $sql .= "LEFT JOIN {$_TABLES['nxfile_filedetail']} detail ON detail.fid=file.fid ";
    $sql .= "LEFT JOIN {$_TABLES['nxfile_categories']} category ON file.cid=category.cid ";
	$sql .= "WHERE 1=1 ";
    if (!empty($allowableViewFoldersSql)) {
        $sql .= "AND file.cid in ($allowableViewFoldersSql) ";
    }
    $tagcloud = new nexfileTagCloud();
    $itemids = $tagcloud->search($query);
    if ($itemids) $itemids = implode(',',$itemids);
    if (!empty($itemids)) {
        $sql .= "AND file.fid in ($itemids) ";
    }

    $sql .= "ORDER BY file.date DESC ";

    $resFiles = DB_query($sql);
    if (DB_numRows($resFiles) > 0) {
        // Show any files in this directory
        $i = 1;
        $query = stripslashes($query);
        while ( list($fid,$subfolderId,$title,$fname,$date,$version,$submitter,$status,$description,$category,$pid) = DB_fetchARRAY($resFiles)) {
            $tpl->set_var('padding_left',(2 * $paddingsize) );
            $tpl->set_var('file_desc_padding_left',$filedescriptionOffset);
            if ($status == 2) {
                $tpl->set_var('showlock','');
            } else  {
                $tpl->set_var('showlock','none');
            }
            $tpl->set_var('details_link_parms', "?fid=$fid");
            $tpl->set_var('fid',$fid);
            $tpl->set_var('file_name',$title);
            $tpl->set_var('modified_date', strftime($_CONF['shortdate'],$date));
            $tpl->set_var('folder_link',"{$_CONF['site_url']}/nexfile/index.php?cid={$subfolderId}");
            $tpl->set_var('folder_name',$category);
            $tpl->set_var('subfolder_id',$subfolderId);
            $tags = $tagcloud->get_itemtags($fid);
            nexdoc_formatfiletags($tpl,$tags,$query);
            nexdoc_formatFavoriteImage($tpl,$fid);
            $tpl->set_var('file_description',nl2br($description));
            $tpl->set_var('download_link',"{$_CONF['site_url']}/nexfile/download.php?op=download&fid={$fid}");
            $tpl->parse('download_action_link','download_action');
            if ($uid > 1) {
                $tpl->set_var('edit_link',"{$_CONF['site_url']}/nexfile/download.php?op=editfile&fid={$fid}");
                $tpl->parse('edit_action_link','editfile_action');
            } else {
                $tpl->set_var('edit_action_link','');
            }
            $pos = strrpos($fname,'.') + 1;
            $ext = strtolower(substr($fname, $pos));
            if (array_key_exists($ext, $_FMCONF['iconlib'] )) {
                $icon = $_FMCONF['imagesurl'] . key($_FMCONF['iconlib'][$ext]);
            } else {
                $icon = $_FMCONF['imagesurl'] . key($_FMCONF['iconlib']['none']);
            }
            $tpl->set_var('extension_icon',$icon);
            if ($i == 1) {
                $tpl->parse('filelisting','filelisting_rec');
            } else {
                $tpl->parse('filelisting','filelisting_rec',true);
            }
            $i++;
        }

    } else {
        $tpl->set_var('folder_desc_padding_left',$filedescriptionOffset);   // Version 3.0 - not presently used
        $tpl->parse('filelisting','emptyfolder');
    }

    $data['displayhtml'] = $tpl->finish ($tpl->get_var('filelisting'));
    $data['tagcloud'] = $tagcloud->displaycloud();
}


function nexdoc_getFileListingSQL($cid,$reportmode) {
    global $_USER,$_CONF,$_TABLES,$_FMCONF,$uid;
    global $allowableViewFoldersSql,$lastRenderedFolder,$recordCountPass1,$recordCountPass2;

    $sql = '';
    // Check and see if this is a custom report
    if (function_exists('nexfile_customReportFilesSQL')) {
        $sql = trim(nexfile_customReportFilesSQL($cid,$reportmode));
        if (!empty($sql)) return $sql;
    }

    $sql = "SELECT file.fid as fid,file.cid,file.title,file.fname,file.date,file.version,file.submitter,file.status,";
    $sql .= "detail.description,category.name,category.pid,status_changedby_uid as changedby_uid, size ";
    $sql .= "FROM {$_TABLES['nxfile_files']} file ";
    $sql .= "LEFT JOIN {$_TABLES['nxfile_filedetail']} detail ON detail.fid=file.fid ";
    $sql .= "LEFT JOIN {$_TABLES['nxfile_categories']} category ON file.cid=category.cid ";
    if ($reportmode == 'lockedfiles') {
        $sql .= "WHERE file.status=2 AND status_changedby_uid=$uid ORDER BY date DESC LIMIT {$_FMCONF['numlatestfiles']}";
    } elseif ($reportmode == 'downloads') {
        // Will return multiple records for same file as we capture download records each time a user downloads it
        $sql .= "LEFT JOIN {$_TABLES['nxfile_downloads']} downloads on downloads.fid=file.fid ";
        $sql .= "WHERE uid=$uid ";
        $sql .= "ORDER BY file.date DESC LIMIT {$_FMCONF['numlatestfiles']}";
    } elseif ($reportmode == 'unread') {
        $sql .= "LEFT OUTER JOIN {$_TABLES['nxfile_downloads']} downloads on downloads.fid=file.fid ";
        $sql .= "WHERE downloads.fid IS NULL ";
        if (empty($allowableViewFoldersSql)) {
            $sql .= "AND file.cid is NULL ";
        } else {
            $sql .= "AND file.cid in ($allowableViewFoldersSql) ";
        }
        $sql .= "ORDER BY file.date DESC LIMIT {$_FMCONF['numlatestfiles']}";

    } elseif ($reportmode == 'incoming') {
        $sql = "SELECT id as fid, 0 as cid, orig_filename as title,  queue_filename as fname, timestamp as date, 0 as version, ";
        $sql .= "uid as submitter, 0 as status, 'N/A' as description, 'Incoming Files' as name, 0 as pid, 0 as changedby_uid, size ";
        $sql .= "FROM {$_TABLES['nxfile_import_queue']} ";
        if (!SEC_inGroup('nexfile Admin')) {
            $sql .= "WHERE uid=$uid ";
        }
        $sql .= "ORDER BY date DESC ";

    } elseif ($reportmode == 'flaggedfiles') {
        $sql .= "LEFT JOIN {$_TABLES['nxfile_favorites']} favorites on favorites.fid=file.fid ";
        $sql .= "WHERE uid=$uid ";
    } elseif ($reportmode == 'myfiles') {
        $sql .= "WHERE file.submitter=$uid ORDER BY date DESC LIMIT {$_FMCONF['numlatestfiles']}";
    } elseif ($reportmode == 'mydownloads') {
        $sql .= "LEFT JOIN {$_TABLES['nxfile_downloads']} downloads on downloads.fid=file.fid ";
        $sql .= "WHERE downloads.uid=$uid ORDER BY file.date DESC LIMIT {$_FMCONF['numlatestfiles']}";
    } elseif ($reportmode == 'approvals') {
        // Determine if this user has any submitted files that they can approve
        $sql = "SELECT file.id,file.cid,file.title,file.fname,file.date,file.version,file.submitter,file.status,";
        $sql .= "file.description,category.name,category.pid,0 as changedby_uid, size ";
        $sql .= "FROM {$_TABLES['nxfile_filesubmissions']} file ";
        $sql .= "LEFT JOIN {$_TABLES['nxfile_categories']} category ON file.cid=category.cid ";
        if (!SEC_inGroup('nexfile Admin')) {
            $categories = fm_getAllowableCategories(array('approval','admin'));
            if (empty($categories)) {
                $sql .= "WHERE file.cid is NULL ";
            } else {
                $sql .= "WHERE file.cid in ($categories) ";
            }
        }
        $sql .= "ORDER BY file.date DESC ";

    } elseif ($cid > 0) {
        $sql .= "WHERE file.cid=$cid ORDER BY file.date DESC, file.fid DESC ";
        if ($reportmode == 'getmorefolderdata') {
            if (isset($_POST['pass2']) AND $_POST['pass2'] == 1) {
                $sql .= "LIMIT $recordCountPass1, 100000 ";
            } else {
                $sql .= "LIMIT $recordCountPass2, 100000 ";
            }
        } elseif ($reportmode != 'getallfiles') {
            // Set SQL query options for amount of data to return - used by the AJAX routine getmorefiledata to populate display in the background
            if ($lastRenderedFolder == $cid) {
                $recordCountPlusOne = $recordCountPass2 + 1;     // Add one - so that we can trigger the loop break condition in the main code below
                $sql .= "LIMIT $recordCountPass1, $recordCountPlusOne ";
            } else {
                $recordCountPlusOne = $recordCountPass1 + 1;     // Add one - so that we can trigger the loop break condition in the main code below
                $sql .= "LIMIT 0, $recordCountPlusOne ";
            }
        }

    } else {
        if (!SEC_inGroup('nexfile Admin')) {
            if (empty($allowableViewFoldersSql)) {
                $sql .= "WHERE file.cid is NULL ";
            } else {
                $sql .= "WHERE file.cid in ($allowableViewFoldersSql) ";
            }
        }
        $sql .= "ORDER BY file.date DESC LIMIT {$_FMCONF['numlatestfiles']}";
    }

    return $sql;

}


function nexdoc_updateFileSubscription($fid,$op='toggle') {
    global $_USER,$_CONF,$_TABLES,$_FMCONF;

    $retval = array('retcode' => '','subscribed' => '');
    if (isset($_USER) AND $_USER['uid'] > 1) {
        $uid = $_USER['uid'];
    } else {
        $retval['retcode'] = false;
        return $retval;
    }

    if (DB_count($_TABLES['nxfile_files'],'fid',$fid)) {    // Valid file and user
        $cid = DB_getItem($_TABLES['nxfile_files'],"cid","fid=$fid");
        // Check if user has an ignore file changes record or a subscribe to changes record for this file
        $direct = false;
        $ignorefilechanges = false;
        $query = DB_query("SELECT fid,ignore_filechanges FROM {$_TABLES['nxfile_notifications']} WHERE fid=$fid and uid=$uid");
        if (DB_numRows($query) == 1) {
            $A = DB_fetchArray($query);
            if ($A['ignore_filechanges'] == 1) {
                $ignorefilechanges = true;
            } else {
                $direct = true;
            }
        }
        $indirect = DB_count($_TABLES['nxfile_notifications'], array ('cid_changes','cid','uid'), array(1,$cid,$uid));
        if ($indirect AND $direct) {    // User may have subscribed to single file and the folder option was also set
            if ($op == 'toggle' or $op == 'remove') {
                DB_query("UPDATE {$_TABLES['nxfile_notifications']} set ignore_filechanges = 1 WHERE fid=$fid AND uid=$uid");
                $retval['subscribed'] = false;
            }
        } else if (($direct OR $indirect) AND !$ignorefilechanges) { // User is subscribed - so un-subscribe
            if ($op == 'toggle' or $op == 'remove') {
                $retval['subscribed'] = false;
                if ($direct > 0) {
                    DB_query("DELETE FROM {$_TABLES['nxfile_notifications']} WHERE fid=$fid AND uid=$uid");
                } else if ($indirect > 0) {
                    DB_query("INSERT INTO {$_TABLES['nxfile_notifications']} (fid,ignore_filechanges,uid,date) VALUES ($fid,1,'$uid', UNIX_TIMESTAMP() )");
                }
            }

        } else {    // User is not subscribed
            if ($op == 'toggle' OR $op == 'add') {
                $retval['subscribed'] = true;
                if ($ignorefilechanges) {
                    //delete the exception record
                    DB_query("UPDATE {$_TABLES['nxfile_notifications']} set ignore_filechanges = 0 WHERE fid=$fid AND uid=$uid");
                } else if (!$direct AND !$indirect) {
                    DB_query("INSERT INTO {$_TABLES['nxfile_notifications']} (fid,cid,uid,date) VALUES ($fid,0,$uid,UNIX_TIMESTAMP())");
                }
            }

        }
        $retval['retcode'] = true;

    } else {
        $retval['retcode'] = false;
    }

    return $retval;

}



function nexdoc_generateNotificationsReport() {
    global $_USER,$_CONF,$_TABLES,$_FMCONF,$LANG_FM12,$actionurl;

    $tpl = new Template($_CONF['path_layout'] . 'nexfile/reporting');
    $tpl->set_file(array(
        'page'                      =>  'reportview.thtml',
        'header'                    =>  'report_header.thtml',
        'filelisting_rec'           =>  'filenotification_record.thtml',
        'folderlisting_rec'         =>  'foldernotification_record.thtml',
        'log_rec'                   =>  'notificationlog_record.thtml'
        ));

    $tpl->set_var('site_url',$_CONF['site_url']);
    $sql = "SELECT a.id,a.fid,a.cid,a.date,cid_newfiles,cid_changes FROM {$_TABLES['nxfile_notifications']} a ";
    $sql .= "WHERE uid={$_USER['uid']} AND a.ignore_filechanges = 0 ORDER BY a.date DESC";
    $query = DB_query($sql);
    while ($A = DB_fetchArray($query)) {
        $tpl->set_var('recid',$A['id']);
        $tpl->set_var('fid',$A['fid']);
        $tpl->set_var('date',strftime($_CONF['shortdate'],$A['date']));
        if ($A['fid'] != 0) {
            $sql = "SELECT a.title,a.cid,b.name as folder FROM {$_TABLES['nxfile_files']} a ";
            $sql .= "LEFT JOIN {$_TABLES['nxfile_categories']} b ON b.cid = a.cid WHERE a.fid={$A['fid']} ";
            $q2 = DB_query($sql);
            list ($filename,$cid,$folder) = DB_fetchArray(DB_query($sql));
            $tpl->set_var('folderid',$cid);
            $tpl->set_var('filename',$filename);
            $tpl->set_var('foldername',$folder);
            $tpl->parse('filelisting_records','filelisting_rec',true);
        } elseif ($A['cid'] > 0) {
            $tpl->set_var('folderid',$A['cid']);
            if ($A['cid_newfiles'] == 1)  {
                $tpl->set_var('chk_newfiles','CHECKED=checked');
            } else {
                $tpl->set_var('chk_newfiles','');
            }
            if ($A['cid_changes'] == 1) {
                $tpl->set_var('chk_filechanges','CHECKED=checked');
            } else {
                $tpl->set_var('chk_filechanges','');
            }
            $folder = DB_getItem($_TABLES['nxfile_categories'],'name',"cid={$A['cid']}");
            $tpl->set_var('foldername',$folder);
            $tpl->parse('folderlisting_records','folderlisting_rec',true);
        }
    }

    if ($_FMCONF['notify_newfile']) {
        $tpl->set_var('chk_fileadded_on','CHECKED=checked');
    } else {
        $tpl->set_var('chk_fileadded_off','CHECKED=checked');
    }
    if ($_FMCONF['notify_changedfile']) {
        $tpl->set_var('chk_filechanged_on','CHECKED=checked');
    } else {
        $tpl->set_var('chk_filechanged_off','CHECKED=checked');
    }
    if ($_FMCONF['allow_broadcasts']) {
        $tpl->set_var('chk_broadcasts_on','CHECKED=checked');
    } else {
        $tpl->set_var('chk_broadcasts_off','CHECKED=checked');
    }

    $qsettings = DB_query("SELECT * FROM {$_TABLES['nxfile_usersettings']} WHERE uid={$_USER['uid']}");
    if (DB_numRows($qsettings) == 1) {
        $A = DB_fetchArray($qsettings);
        if ($A['notify_newfile'] == 1) {
            $tpl->set_var('chk_fileadded_off','');
            $tpl->set_var('chk_fileadded_on','CHECKED=checked');
        } else {
            $tpl->set_var('chk_fileadded_on','');
            $tpl->set_var('chk_fileadded_off','CHECKED=checked');
        }
        if ($A['notify_changedfile'] == 1) {
            $tpl->set_var('chk_filechanged_off','');
            $tpl->set_var('chk_filechanged_on','CHECKED=checked');
        } else {
            $tpl->set_var('chk_filechanged_on','');
            $tpl->set_var('chk_filechanged_off','CHECKED=checked');
        }
        if ($A['allow_broadcasts'] == 1) {
            $tpl->set_var('chk_broadcasts_off','');
            $tpl->set_var('chk_broadcasts_on','CHECKED=checked');
        } else {
            $tpl->set_var('chk_broadcasts_on','');
            $tpl->set_var('chk_broadcasts_off','CHECKED=checked');
        }
    }

    // Generate the user notification history - last 100 records
    $sql = "SELECT a.submitter_uid,a.notification_type,a.fid,b.fname,a.cid,c.name,a.datetime,d.username "
         . "FROM {$_TABLES['nxfile_notificationlog']} a "
         . "LEFT JOIN {$_TABLES['nxfile_files']} b ON b.fid=a.fid "
         . "LEFT JOIN {$_TABLES['nxfile_categories']} c ON c.cid=a.cid "
         . "LEFT JOIN {$_TABLES['users']} d ON d.uid=a.submitter_uid "
         . "WHERE a.target_uid={$_USER['uid']} "
         . "ORDER BY a.datetime DESC LIMIT 100";
    $query = DB_query($sql);
    $cssid = 1;
    while ($A = DB_fetchArray($query)) {
        $tpl->set_var('notification_type',$_FMCONF['notificationTypes'][$A['notification_type']]);
        $tpl->set_var('submitter_uid',$A['submitter_uid']);
        $tpl->set_var('submitter_name',$A['username']);
        $tpl->set_var('file_name',$A['fname']);
        $tpl->set_var('folder_name',$A['name']);
        $tpl->set_var('fid',$A['fid']);
        $tpl->set_var('cid',$A['cid']);
        $tpl->set_var('notification_date',strftime('%b %d %y, %I:%M',$A['datetime']));
        $tpl->parse('notification_history_records','log_rec',true);
        $tpl->set_var('cssid', $cssid);
        $cssid = ($cssid = 1) ? 2: 1;
    }

    $tpl->parse ('output', 'page');
    return $tpl->finish ($tpl->get_var('output'));

}


function nexdocsrv_getMoreActions($op) {
    global $_USER;
    $retval = '<option value="0">More Actions ...</option>';
    switch ($op) {
        case 'approvals':
            $retval .= '<option value="approvesubmissions">Approve selected Submissions</option>';
            $retval .= '<option value="deletesubmissions">Delete selected Submissions</option>';
            break;
        case 'incoming':
            $retval .= '<option value="delete">Delete selected files</option>';
            $retval .= '<option value="move">Move selected files</option>';
            break;
        case 'notifications':
            $retval .= '<option value="delete">Delete selected Notifications</option>';
            break;
        default:
            if (COM_isAnonUser()) {
                $retval .= '<option value="archive">Download as an archive</option>';
            } else {
                $retval .= '<option value="delete">Delete selected files</option>';
                $retval .= '<option value="move">Move selected files</option>';
                $retval .= '<option value="subscribe">Subscribe to update notifications</option>';
                $retval .= '<option value="archive">Download as an archive</option>';
                $retval .= '<option value="markfavorite">Mark Favorite</option>';
                $retval .= '<option value="clearfavorite">Clear Favorite</option>';
            }
            break;

    }
    return $retval;
}

function nexdocsrv_approveFileSubmission($id) {
    global $_TABLES,$_CONF,$_FMCONF;

    $query = DB_query("SELECT fid,cid,fname,tempname,title,description,tags,ftype,size,version,version_note,submitter,date,version_ctl,notify FROM {$_TABLES['nxfile_filesubmissions']} WHERE id=$id");
    list($fid,$cid,$fname,$tmpname,$title,$description,$tags,$ftype,$fsize,$version,$verNote,$submitter,$date,$versionmgmt,$notify) = DB_fetchARRAY($query);
    $data = array();
    // Check if there have been multiple submission requests for the same file and thus have same new version #
    if ($version == 1) {
        if ($ftype == 'file') {
            $curfile = "{$_FMCONF['storage_path']}{$cid}/submissions/$tmpname";
            $newfile = "{$_FMCONF['storage_path']}{$cid}/$fname";
            $rename =@rename ($curfile,$newfile);
        }
        DB_query("INSERT INTO {$_TABLES['nxfile_files']} (cid,fname,title,version,ftype,size,submitter,status,date,version_ctl)
            VALUES ('$cid','$fname','$title','1','$ftype','$fsize','$submitter',1,'$date','$versionmgmt')");
        $newfid = DB_insertId();
        DB_query("INSERT INTO {$_TABLES['nxfile_filedetail']} (fid,description,hits,rating,votes,comments)
            VALUES ('$newfid','$description',0,0,0,0)");
        DB_query("INSERT INTO {$_TABLES['nxfile_fileversions']} (fid,fname,ftype,version,notes,size,date,uid,status)
            VALUES ('$newfid','$uploadfilename','$ftype','1','$verNote','$fsize','$date','$submitter',1)");

    } else {
        // Need to rename the current versioned file
        if ($ftype == 'file') {
            $curfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/submissions/' .$tmpname;
            $newfile = $_CONF['path_html'] . 'nexfile/data/' .$cid. '/' .$fname;
            $rename = @rename ($curfile,$newfile);
        }

        DB_query("INSERT INTO {$_TABLES['nxfile_fileversions']} (fid,fname,ftype,version,notes,size,date,uid,status)
           VALUES ('$fid','$fname','$ftype','$version','$verNote','$fsize','$date','$submitter','1')");
        DB_query("UPDATE {$_TABLES['nxfile_files']} SET fname='$fname',version='$version', date='$date' WHERE fid=$fid");
        $newfid = $fid;
    }

    if ($newfid > 0) {
        $tagcloud = new nexfileTagCloud();
        // Update tags table and return tags formated as required
        $tagcloud->update_tags($newfid,$tags);

        // Send out notifications of approval
        fm_sendNotification($newfid,"2");
        DB_query("DELETE FROM {$_TABLES['nxfile_filesubmissions']} WHERE id=$id");

        // Optionally add notification records and send out notifications to all users with view access to this new file
        if (DB_getItem($_TABLES['nxfile_categories'], 'auto_create_notifications', "cid='$cid'") == 1) {
            fm_autoCreateNotifications($fid, $cid);
        }
        // Send out notifications of update to all subscribed users
        fm_sendNotification($newfid,"1");
        return true;
    } else {
        return false;
    }

}




/**
* Returns a formatted listbox of categories user has access
* First checks for View access so that delegated admin can be just for sub-categories
*
* @param        string|array        $perms        Single perm 'admin' or array of permissions as required by fm_getPermission()
* @param        int                 $selected     Will make this item the selected item in the listbox
* @param        string              $cid          Parent category to start at and then recursively check
* @param        string              $level        Used by this function as it calls itself to control the ident formatting
* @param        string              $selectlist   Used by this function to be able to append to the formatted select list
* @param        string              $restricted   Used if you do not want to show this categories subfolders
* @return       string                            Return a formatted HTML Select listbox of categories
*/
function nexdoc_recursiveAccessOptions($perms,$selected='',$cid='0',$level='1',$selectlist='',$restricted='') {
    global $_TABLES,$LANG_FM02;
    if (empty($selectlist) AND $level == 1) {
        if (SEC_hasRights('nexfile.admin')) {
            $selectlist = '<option value="0">'.$LANG_FM02['TOP_CAT'].'</option>' . LB;
        }
    }

    $query = DB_QUERY("SELECT cid,pid,name FROM {$_TABLES['nxfile_categories']} WHERE PID='$cid' ORDER BY CID");
    while ( list($cid,$pid,$name,$description) = DB_fetchARRAY($query)) {
        $indent = ' ';
        // Check if user has access to this category
        if ($cid != $restricted AND fm_getPermission($cid,'view')) {
            // Check and see if this category has any sub categories - where a category record has this cid as it's parent
            if (DB_COUNT($_TABLES['nxfile_categories'], 'pid', $cid) > 0)  {
                if ($level > 1) {
                    for ($i=2; $i<= $level; $i++) {
                        $indent .= "--";
                    }
                    $indent .= ' ';
                }
                if (fm_getPermission($cid,$perms)) {
                    if ($indent != '') $name = " $name";
                    $selectlist .= '<option value="' . $cid;
                    if ($cid == $selected) {
                        $selectlist .= '" selected="selected">' .$indent .$name . '</option>' . LB;
                    } else {
                        $selectlist .= '">' . $indent .$name . '</option>' . LB;
                    }
                }
                $selectlist = nexdoc_recursiveAccessOptions($perms,$selected,$cid,$level+1,$selectlist,$restricted);
            } else {
                if ($level > 1) {
                    for ($i=2; $i<= $level; $i++) {
                        $indent .= "--";
                    }
                    $indent .= ' ';
                }
                if (fm_getPermission($cid,$perms)) {
                    if ($indent != '') $name = " $name";
                    $selectlist .= '<option value="' . $cid;
                    if ($cid == $selected) {
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


function nexdoc_getGroupOptions() {
    global $_TABLES,$_FMCONF,$LANG_nexfile;

    $options = '';

    if (count($_FMCONF['excludeGroups']) > 0) {
        $excludeGroups = $_FMCONF['excludeGroups'];  // Don't want to alter the global value
        array_walk($excludeGroups, 'wrap_each');
        $excludeGroups = implode(',',$excludeGroups);
    } else {
        $excludeGroups = '';
    }

    if (count($_FMCONF['excludeGroups']) > 0) {
        $includeCoreGroups = $_FMCONF['includeCoreGroups'];   // Don't want to alter the global value
        array_walk($includeCoreGroups, 'wrap_each');
        $includeCoreGroups = implode(',',$includeCoreGroups);
    } else {
        $includeCoreGroups = '';
    }

    $sql = "SELECT grp_id,grp_name FROM {$_TABLES['groups']} ";
    $sql .= "WHERE (grp_gl_core = '0' ";
    if (!empty($excludeGroups)) {
        $sql .= "AND grp_name NOT IN ({$excludeGroups}) ";
    }
    $sql .= ' ) ';
    if (!empty($includeCoreGroups)) {
        $sql .= "OR ( grp_name in ($includeCoreGroups) ) ";
    }
    $sql .= 'ORDER BY grp_name';

    $query = DB_query( $sql );
    if (DB_numROWS($query) > 0 ) {
        while ( list($grp_id,$grp_name) = DB_fetchARRAY($query)) {
            $options .= '<option value="' . $grp_id . '">';
            $options .=  $grp_name . '</option>' . LB;
        }
    } else {
        $options = '<option value="0">'.$LANG_nexfile['msg36'].'</option>';
    }

    return $options;
}

?>