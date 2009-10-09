<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexContent Plugin v2.3.0 for the nexPro Portal Server                     |
// | October 9, 2009                                                           |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | config.php                                                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2009 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
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


//This parameter is used during the initial installation of the plugin to ensure that the
//nexcontent images directory in the public path is writable.
$CONF_SE_PREREQUISITE['uploadpath'] = $_CONF['path_html'] . 'nc/images/';

// Permissions that will be used for directories used to store uploaded images.
$CONF_SE['imagedir_perms'] = (int) 0755;

// Permissions that will be used for uploaded images.
$CONF_SE['image_perms'] = (int) 0755;

$CONF_SE['menuoptions'] = array (
    0   => 'None',
    1   => 'Header Menu',
    2   => 'Block Menu',
    3   => 'Same as Parent',
    4   => 'New Block',
    5   => 'Single Block'
);


$_TABLES['nexcontent']               = $_DB_table_prefix . 'nxcontent';
$_TABLES['nexcontent_pages']         = $_DB_table_prefix . 'nxcontent_pages';
$_TABLES['nexcontent_images']        = $_DB_table_prefix . 'nxcontent_images';


?>
