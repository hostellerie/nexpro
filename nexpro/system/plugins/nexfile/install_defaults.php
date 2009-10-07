<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
// | Initial Installation Defaults used when loading the online configuration  |
// | records. These settings are only used during the initial installation     |
// | and not referenced any more once the plugin is installed.                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
// | Eric de la Chevrotiere - Eric.delaChevrotiere@nextide.ca                  |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

global $_FMCONF_DEFAULT;
$_FMCONF_DEFAULT = array();

$_FMCONF_DEFAULT['debug']               = false;    // Set to true to enable extra logging to error.log in form rendering and posting functions
$_FMCONF_DEFAULT['access_mode']         = 13;       // Group to test if user has access before showing menu link. Can be group_id or group_name
$_FMCONF_DEFAULT['storage_path']        = $_CONF['path_data'] . 'nexfile/';
$_FMCONF_DEFAULT['maxfilesize']         = 8;       // Max Size in MB to allow uploaded
$_FMCONF_DEFAULT['numlatestfiles']      = 30;
$_FMCONF_DEFAULT['dateformat']          = '%a %d %b %I:%M%p';
$_FMCONF_DEFAULT['defOwnerRights']      = array('view','upload','upload_direct','upload_ver','approval','admin');
$_FMCONF_DEFAULT['defCatGroupRights']   = array('view','upload','upload_ver');
$_FMCONF_DEFAULT['shownewlimit']        = '10';
$_FMCONF_DEFAULT['downloadchunkrate']   = '8192';
$_FMCONF_DEFAULT['nolimitUploadGroups'] = array('Root');

// Array of group's to create permission records as a default
$_FMCONF_DEFAULT['defCatGroup']         = array('Logged-in Users','Root');

// By Default Core GL groups are not included in the Group Listing to set folder perms - add them here if desired.
$_FMCONF_DEFAULT['includeCoreGroups']   = array('All Users','Logged-in Users');
// Exclude these groups from the selection of available groups
$_FMCONF_DEFAULT['excludeGroups']       = array('nexfile Admin','forum Admin', 'messenger Admin','faqman Admin', 'Static Page Admin');

$_FMCONF_DEFAULT['email_enabled']       = true;
$_FMCONF_DEFAULT['from_email']          = 'Nexfile Distribution List';
$_FMCONF_DEFAULT['notify_newfile']      = true;
$_FMCONF_DEFAULT['notify_changedfile']  = true;
$_FMCONF_DEFAULT['allow_broadcasts']    = true;

/**
* Initialize plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $CONF_NEXPRO if available (e.g. from
* an old config.php), uses $_NEXPRO_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_nexfile()
{
    global $_CONF, $_FMCONF, $_FMCONF_DEFAULT;
    @include_once ($_CONF['path'] . 'plugins/nexfile/config.php');

    unset($_FMCONF['defCatGroup']);     // New format - can not merge
    if (!empty($_FMCONF['excludeGroups'])) {
        $_FMCONF['excludeGroups'] = explode(',',$_FMCONF['excludeGroups']);
    }

    if (is_array($_FMCONF) && (count($_FMCONF) > 1)) {
        $_FMCONF_DEFAULT = array_merge($_FMCONF_DEFAULT, $_FMCONF);
    }

    $c = config::get_instance();
    if (!$c->group_exists('nexfile')) {

        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'nexfile');
        $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'nexfile');
        $c->add('debug', $_FMCONF_DEFAULT['debug'],
                'select', 0, 0, 0, 5, true, 'nexfile');
        $c->add('access_mode',$_FMCONF_DEFAULT['access_mode'] ,
                'select',0,0,20,10,TRUE,'nexfile');
        $c->add('maxfilesize', $_FMCONF_DEFAULT['maxfilesize'],
                'text', 0, 0, 1, 20, true, 'nexfile');
        $c->add('storage_path', $_FMCONF_DEFAULT['storage_path'],
                'text', 0, 0, 1, 30, true, 'nexfile');
        $c->add('numlatestfiles', $_FMCONF_DEFAULT['numlatestfiles'],
                'text', 0, 0, 1, 40, true, 'nexfile');
        $c->add('shownewlimit', $_FMCONF_DEFAULT['shownewlimit'],
                'text', 0, 0, 1, 50, true, 'nexfile');
        $c->add('downloadchunkrate', $_FMCONF_DEFAULT['downloadchunkrate'],
                'text', 0, 0, 1, 55, true, 'nexfile');
        $c->add('dateformat', $_FMCONF_DEFAULT['dateformat'],
                'text', 0, 0, 1, 60, true, 'nexfile');
        $c->add('nolimitUploadGroups', $_FMCONF_DEFAULT['nolimitUploadGroups'],'%text',0,0,NULL,70,TRUE,'nexfile');

        $c->add('allowable_file_types',array(
            'application/x-gzip-compressed'     => array('.tar.gz' => 1,'.tgz' => 1),
            'application/x-zip-compressed'      => array('.zip' => 1),
            'application/x-tar'                 => array('.tar' => 1),
            'text/plain'                        => array('.php' => 1,'.txt' => 1),
            'text/html'                         => array('.html' => 1,'.htm' => 1),
            'image/bmp'                         => array('.bmp' => 1,'.ico' => 1),
            'image/gif'                         => array('.gif' => 1),
            'image/png'                         => array('.png' => 1),
            'image/pjpeg'                       => array('.jpg' => 1,'.jpeg' => 1),
            'image/jpeg'                        => array('.jpg' => 1,'.jpeg' => 1),
            'audio/mpeg'                        => array('.mp3' => 1),
            'audio/wav'                         => array('.wav' => 1),
            'application/pdf'                   => array('.pdf' => 1),
            'application/x-shockwave-flash'     => array('.swf' => 1),
            'application/msword'                => array('.doc' => 1),
            'application/vnd.ms-msexcel'        => array('.xls' => 1),
            'application/vnd.ms-powerpoint'     => array('.ppt' => 1),
            'application/vnd.ms-project'        => array('.mpp' => 1),
            'application/vnd.vision'            => array('.vsd' => 1),
            'application/octet-stream'          => array('.vsd' => 1,'.fla' => 1, '.psd' => 1, '.pdf' => 1, '.jpg' => 1, '.png' => 1, '.doc' => 1, '.xls' => 1)),
            '**placeholder',0,0,NULL,80,TRUE,'nexfile');

        $c->add('iconlib',array(
            'php'       => array('php.gif' => 1),
            'phps'      => array('php.gif' => 1),
            'bmp'       => array('bmp.gif' => 1),
            'gif'       => array('gif.gif' => 1),
            'jpg'       => array('jpg.gif' => 1),
            'html'      => array('htm.gif' => 1),
            'htm'       => array('htm.gif' => 1),
            'mov'       => array('mov.gif' => 1),
            'mp3'       => array('mp3.gif' => 1),
            'pdf'       => array('pdf.gif' => 1),
            'ppt'       => array('ppt.gif' => 1),
            'mht'       => array('mht.gif' => 1),
            'tar'       => array('tar.gif' => 1),
            'gz'        => array('zip.gif' => 1),
            'txt'       => array('txt.gif' => 1),
            'doc'       => array('doc.gif' => 1),
            'xls'       => array('xls.gif' => 1),
            'mpp'       => array('mpp.gif' => 1),
            'exe'       => array('exe.gif' => 1),
            'swf'       => array('swf.gif' => 1),
            'vsd'       => array('vsd.gif' => 1),
            'none'      => array('none.gif' => 1)),
            '**placeholder',0,0,NULL,90,TRUE,'nexfile');

        $c->add('fs_perms', NULL, 'fieldset', 0, 1, NULL, 0, true, 'nexfile');
        $c->add('excludeGroups',$_FMCONF_DEFAULT['excludeGroups'],'%text',0,1,0,100,true,'nexfile');
        $c->add('includeCoreGroups',$_FMCONF_DEFAULT['includeCoreGroups'],'%text',0,1,0,110,true,'nexfile');
        $c->add('includeCoreGroups',$_FMCONF_DEFAULT['includeCoreGroups'],'%text',0,1,0,120,true,'nexfile');

        $c->add('defOwnerRights', $_FMCONF_DEFAULT['defOwnerRights'],'%text',0,1,NULL,130,TRUE,'nexfile');
        $c->add('defCatGroupRights', $_FMCONF_DEFAULT['defCatGroupRights'],'%text',0,1,NULL,140,TRUE,'nexfile');
        $c->add('defCatGroup', $_FMCONF_DEFAULT['defCatGroup'],'%text',0,1,NULL,150,TRUE,'nexfile');

        $c->add('fs_usersettings', NULL, 'fieldset', 0, 2, NULL, 0, true, 'nexfile');
        $c->add('email_enabled',$_FMCONF_DEFAULT['email_enabled'],'select',0,2,0,200,true,'nexfile');
        $c->add('from_email',$_FMCONF_DEFAULT['from_email'],'text',0,2,0,210,true,'nexfile');
        $c->add('notify_newfile',$_FMCONF_DEFAULT['notify_newfile'],'select',0,2,0,220,true,'nexfile');
        $c->add('notify_changedfile',$_FMCONF_DEFAULT['notify_changedfile'],'select',0,2,0,230,true,'nexfile');
        $c->add('allow_broadcasts',$_FMCONF_DEFAULT['allow_broadcasts'],'select',0,2,0,240,true,'nexfile');

    }

    return true;
}



?>