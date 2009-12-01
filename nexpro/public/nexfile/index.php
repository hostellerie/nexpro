<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexfile Plugin v3.0.0 for the nexPro Portal Server                        |
// | Sept 30, 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2009 by the following authors:                         |
// | Blaine Lang            - blaine DOT lang AT nextide DOT ca                |
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


include ('../lib-common.php');
include ('library.php');

$filter = new sanitizer();
$filter->cleanData('int',array('cid' => $_REQUEST['cid'], 'fid' => $_GET['fid']));
$filter->cleanData('char',array('op' => $_REQUEST['op']));
$_CLEAN = $filter->getCleanData();
$cid = $_CLEAN['int']['cid'];
$fid = $_CLEAN['int']['fid'];

if ($fid > 0 AND empty($cid)) {
    $cid = DB_getItem($_TABLES['nxfile_files'],'cid',"fid=$fid AND status=1");
    $pid = DB_getItem($_TABLES['nxfile_categories'],'pid',"cid=$cid");
    if (empty($cid) OR !fm_getPermission($cid,'view') OR ($pid > 0 AND !fm_getPermission($pid,'view'))) {
        $fid = 0;
        $cid = 0;
    }
} elseif ($cid > 0) {
    if (!fm_getPermission($cid,'view')) {
        $cid = 0;
    }
}

$op = strtolower($_CLEAN['char']['op']);
$alertMsg = '';

if($_USER['uid'] < 2) {
    $uid = 0;
    $alertMsg = 'You are not logged in';
} else {
    $uid = $_USER['uid'];
}

if (!$file = @fopen ($_FMCONF['storage_path'] . 'test.txt', 'w')) {
    $alertMsg = "Unable to write to the file storage area: {$_FMCONF['storage_path']}";
}

if ($op =='downloadfolder') {
    if ($cid > 0 AND fm_getPermission($cid,'view')) {
            include('lib-archive.php');
            nexdoc_createArchiveFromFolder($cid);
    } else {
        COM_errorLog('Archive failed - invalid category or user does not have view access');
    }

}

echo COM_siteHeader('none');
$tpl = new Template($_CONF['path_layout'] . 'nexfile');
$tpl->set_file(array(
    'page'                      =>  'page.thtml',
    'header'                    =>  'filelisting_header.thtml',
    'toolbar'                   =>  'toolbar.thtml',
    'newfolderlink'             =>  'newfolder_link.thtml',
    'newfilelink'               =>  'newfile_link.thtml',
    'newfilediv'                =>  'newfile_div.thtml',
    'newfolderdiv'              =>  'newfolder_div.thtml',
    'movefilesdiv'              =>  'movefiles_div.thtml',
    'movequeuefile'             =>  'movefile_div.thtml',   // Used to move a single file - used for the incoming files mode
    'broadcast'                 =>  'broadcast_div.thtml',
    'filedetails'               =>  'filedetails.thtml',
    'subfolder'                 =>  'filelisting_subfolder_record.thtml',
    'emptyfolder'               =>  'filelisting_emptyfolder.thtml',
    'filelisting_rec'           =>  'filelisting_record.thtml',
    'tag_link'                  =>  'taglink_record.thtml',
    'tag_rec'                   =>  'tagdesc_record.thtml',
    'tagsearch_rec'             =>  'tagsearchlink.thtml',
    'tagcloud_rec'              =>  'tagcloud_record.thtml',
    'folderlisting_rec'         =>  'leftnav_folder_record.thtml',
    'movefolder'                =>  'folder_onhover_move.thtml'
    ));

$tpl->set_var('site_url', $_CONF['site_url']);
$tpl->set_var('layout_url', $_CONF['layout_url']);
$tpl->set_var('action_url', $actionurl);
$tpl->set_var('ajax_server_url', "{$_CONF['site_url']}/nexfile/ajax/server.php");
$tpl->set_var('actionurl_dir', "{$_CONF['site_url']}/nexfile");
$tpl->set_var('imgset', "{$_CONF['layout_url']}/nexfile/images");
$tpl->set_var('cookie_session',$_COOKIE[$_CONF['cookie_session']]);
$tpl->set_var('current_category', $cid);
$tpl->set_var('initialfid', $fid);
$tpl->set_var('initialcid', $cid);
$tpl->set_var('initialop', $op);                                      // Used with nexproject presently: to create new files
$tpl->set_var('initialparm', COM_applyFilter($_GET['parm'],true));    // Used with nexproject presently - expecting it to be an integer

if (in_array($op,$validReportModes)) {
    $tpl->set_var('report_option',$op); // Save in the form 'frmtoolbar' - will be used by AJAX and JS code
} elseif (in_array($_POST['reportmode'],$validReportModes)) {
    $tpl->set_var('report_option',$_POST['reportmode']);
} elseif ($cid > 0) {
    $tpl->set_var('report_option','');
} else {
    $tpl->set_var('report_option','latestfiles');
}

if (!empty($alertMsg)) {
    $tpl->set_var('show_alert','');
    $tpl->set_var('alert_message',$alertMsg);
} else {
    $tpl->set_var('show_alert','none');
}

$tpl->set_var('user_options',NXCOM_listUsers());
$tpl->set_var('group_options',nexdoc_getGroupOptions());

$tpl->set_var('newfolder_options',nexdoc_recursiveAccessOptions('admin'));
$tpl->set_var('movefolder_options',nexdoc_recursiveAccessOptions(array('upload_dir')));
$tpl->set_var('newfile_category_options',nexdoc_recursiveAccessOptions(array('upload','upload_dir')));
$adminFolders = nexdoc_recursiveAccessOptions('admin');

if ($adminFolders != '') {
    $tpl->set_var('newfolder_options',$adminFolders);
    $tpl->parse('newfolder_dialog','newfolderdiv');
    $tpl->parse('newfolder_menuitem','newfolderlink');
}
$uploadFolders = nexdoc_recursiveAccessOptions(array('upload','upload_dir'));
if ($uploadFolders != '') {
    $tpl->set_var('newfile_category_options',$uploadFolders);
    $tpl->parse('newfile_dialog','newfilediv');
    $tpl->parse('newfile_menuitem','newfilelink');
}

if (SEC_hasRights('nexfile.edit')) {
    $tpl->parse('folderadmin_link','folderadminlink');
}
$tagcloud = new nexfileTagCloud();
$tpl->set_var('tag_cloud',$tagcloud->displaycloud());

$tpl->parse('toolbar','toolbar');
$tpl->parse('filelisting_header','header');
$tpl->parse('file_details_panel','filedetails');
$tpl->parse('newfolder_dialog','newfolderdiv');
$tpl->parse('newfile_dialog','newfilediv');
$tpl->parse('movefiles_dialog','movefilesdiv');
$tpl->parse('moveQueuefile_dialog','movequeuefile');
$tpl->parse('broadcast_dialog','broadcast');

$tpl->parse ('output', 'page');
echo $tpl->finish ($tpl->get_var('output'));


echo COM_siteFooter();


?>