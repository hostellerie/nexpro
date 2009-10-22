<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v3.0.0 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexfile.php                                                                |
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


// Plugin information
$_FMCONF=array();
$_FMCONF['pi_display_name'] = 'nexfile';
$_FMCONF['pi_name']         = 'nexfile';
$_FMCONF['gl_version']      = '1.6.1';
$_FMCONF['version']         = '3.0.0';
$_FMCONF['pi_url']          = 'http://www.nextide.ca/';


// Should not need to change this setting but we have made it a define
// Used to create unique filename when using the upload/replace functionality
$_FMCONF['upload_prefix_character_count']   =   18;
$_FMCONF['download_chunk_rate']             =   8192;  //set to 8k download chunks.

/* Should not need to adjust these offset which are used to adjust the left side padding of the filelisting display
 * They have been setup as variables in case a site or theme requires some tweaking
 * Integer values will be used as a pixel value offset or multiplier in the templates
*/
$_FMCONF['paddingsize'] = 10;
$_FMCONF['filedescriptionOffset'] = 50;


// Permissions used when category directories are auto created and files uploaded
define('FM_CHMOD_FILES', 0666);
define('FM_CHMOD_DIRS',  0777);

define ('unapprovedstatus', 0);
define ('approvedstatus', 1);
define ('lockedstatus', 2);

$_FMCONF['notificationTypes'] = array(
    1   => 'New File Added',
    2   => 'New File Approved',
    3   => 'New File Declined',
    4   => 'File Changed',
    5   => 'Broadcast'
);

/* Settings for location and names of icons to use for the plugin.
 * These are not defined in the online config manager because
 * you may want to move these settings to the theme's functions.php to over-ride them per theme
*/
$_FMCONF['imagesurl'] = $_CONF['layout_url'] .'/nexfile/images/';
$_FMCONF['icons'] = array(
    'favorite-on'    => 'staron-16x16.gif',
    'favorite-off'   => 'staroff-16x16.gif'
);


// Database names - should never be changed
$_TABLES['nxfile_access']               = $_DB_table_prefix .'nxfile_access';
$_TABLES['nxfile_categories']           = $_DB_table_prefix .'nxfile_categories';
$_TABLES['nxfile_files']                = $_DB_table_prefix .'nxfile_files';
$_TABLES['nxfile_filedetail']           = $_DB_table_prefix .'nxfile_filedetail';
$_TABLES['nxfile_fileversions']         = $_DB_table_prefix .'nxfile_fileversions';
$_TABLES['nxfile_notifications']        = $_DB_table_prefix .'nxfile_notifications';
$_TABLES['nxfile_filesubmissions']      = $_DB_table_prefix .'nxfile_filesubmissions';
$_TABLES['nxfile_recentfolders']        = $_DB_table_prefix .'nxfile_recentfolders';
$_TABLES['nxfile_downloads']            = $_DB_table_prefix .'nxfile_downloads';
$_TABLES['nxfile_favorites']            = $_DB_table_prefix .'nxfile_favorites';
$_TABLES['nxfile_usersettings']         = $_DB_table_prefix .'nxfile_usersettings';
$_TABLES['nxfile_notificationlog']      = $_DB_table_prefix .'nxfile_notificationlog';
$_TABLES['nxfile_import_queue']         = $_DB_table_prefix .'nxfile_import_queue';
$_TABLES['nxfile_export_queue']         = $_DB_table_prefix .'nxfile_export_queue';

$_TABLES['auditlog']                = $_DB_table_prefix .'auditlog';

?>