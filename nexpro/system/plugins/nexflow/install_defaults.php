<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexflow Plugin v3.1 for the nexPro Portal Server                          |
// | Sept 21, 2009                                                             |
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

global $CONF_NF_DEFAULT;
$CONF_NF_DEFAULT = array();


$CONF_NF_DEFAULT['debug']                       = false;
$CONF_NF_DEFAULT['TaskConsole_URL']             = $_CONF['site_url'] . '/nexflow/index.php';
$CONF_NF_DEFAULT['RequestDetailLink_URL']       = $_CONF['site_url'] . '/nexflow/getproject.php';
$CONF_NF_DEFAULT['export_dir']                  = $_CONF['path_html'] . 'admin/plugins/nexflow/export/';
$CONF_NF_DEFAULT['email_notifications_enabled'] = false;

// Set to true if you have setup the nexflow Orchestrator to be exectuted in the background by the server
$CONF_NF_DEFAULT['orchestrator_using_cron']     = false;

// Enables Task Delete in the taskconsole - mytasks view
$CONF_NF_DEFAULT['allow_task_delete']           = false;

// Set to true if only logged in members can access 'All Requests' page
$CONF_NF_DEFAULT['allrequestsloginrequired']    = false;

// Set to true if only logged in members with nexflow.user right can access 'Task Console'
$CONF_NF_DEFAULT['taskconsolepermrequired']     = false;


$CONF_NF_DEFAULT['uploadpath']                  = $_CONF['path_html'] . 'nexflow/media';
$CONF_NF_DEFAULT['downloadURL']                 = $_CONF['site_url'] . '/nexflow/media';
$CONF_NF_DEFAULT['max_uploadfile_size']         = '12';     // 12 MB


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
function plugin_initconfig_nexflow()
{
    global $CONF_NF, $CONF_NF_DEFAULT;

    if (is_array($CONF_NF) && (count($CONF_NF) > 1)) {
        $CONF_NF_DEFAULT = array_merge($CONF_NF_DEFAULT, $CONF_NF);
    }

    $c = config::get_instance();
    if (!$c->group_exists('nexflow')) {

        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'nexflow');
        $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'nexflow');
        $c->add('fs_attachments', NULL, 'fieldset', 0, 1, NULL, 0, true, 'nexflow');

        $c->add('debug', $CONF_NF_DEFAULT['debug'],'select', 0, 0, 0, 5, true, 'nexflow');
        $c->add('TaskConsole_URL',$CONF_NF_DEFAULT['TaskConsole_URL'] ,
                'text',0,0,0,10,TRUE,'nexflow');
        $c->add('RequestDetailLink_URL',$CONF_NF_DEFAULT['RequestDetailLink_URL'] ,
                'text',0,0,0,20,TRUE,'nexflow');
        $c->add('export_dir',$CONF_NF_DEFAULT['export_dir'] ,
                'text',0,0,0,30,TRUE,'nexflow');
        $c->add('email_notifications_enabled', $CONF_NF_DEFAULT['email_notifications_enabled'],
            'select', 0, 0, 0, 40, true, 'nexflow');
        $c->add('orchestrator_using_cron', $CONF_NF_DEFAULT['orchestrator_using_cron'],
            'select', 0, 0, 0, 50, true, 'nexflow');
        $c->add('allow_task_delete', $CONF_NF_DEFAULT['allow_task_delete'],
            'select', 0, 0, 0, 60, true, 'nexflow');
        $c->add('allrequestsloginrequired', $CONF_NF_DEFAULT['allrequestsloginrequired'],
            'select', 0, 0, 0, 70, true, 'nexflow');
        $c->add('taskconsolepermrequired', $CONF_NF_DEFAULT['taskconsolepermrequired'],
            'select', 0, 0, 0, 80, true, 'nexflow');

        $c->add('uploadpath',$CONF_NF_DEFAULT['uploadpath'] ,
                'text',0,1,0,100,TRUE,'nexflow');
        $c->add('downloadURL',$CONF_NF_DEFAULT['downloadURL'] ,
                'text',0,1,0,110,TRUE,'nexflow');
        $c->add('max_uploadfile_size',$CONF_NF_DEFAULT['max_uploadfile_size'] ,
                'text',0,1,0,120,TRUE,'nexflow');

        $c->add('allowablefiletypes',array(
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
            '**placeholder',0,1,NULL,130,TRUE,'nexflow');


    }

    return true;
}



?>