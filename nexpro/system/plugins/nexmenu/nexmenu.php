<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v3.2 for the nexPro Portal Server                          |
// | Oct 15, 2009                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexmenu.php                                                               |
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


// +---------------------------------------------------------------------------+
// | Plugin Config Parms - DO NOT EDIT BELOW THIS SECTION                      |
// +---------------------------------------------------------------------------+
$CONF_NEXMENU=array();
$CONF_NEXMENU['version']='3.2';
$CONF_NEXMENU['gl_version']='1.6.0';
$CONF_NEXMENU['pi_name']='nexmenu';
$CONF_NEXMENU['pi_display_name']='nexMenu';

$CONF_NEXMENU['dependent_plugins']=array(
    'nexpro'    => '2.1.0'
);


$_TABLES['nexmenu']             = $_DB_table_prefix . 'nxmenu';
$_TABLES['nexmenu_language']    = $_DB_table_prefix . 'nxmenu_language';
$_TABLES['nexmenu_config']      = $_DB_table_prefix . 'nxmenu_config';


/* Core Menu type options */
$CONF_NEXMENU['coremenu'] = array (
    1       => 'usermenu',
    2       => 'adminmenu',
    3       => 'topicmenu',
    4       => 'spmenu',
    5       => 'pluginmenu',
    6       => 'linksmenu'
);

// Only CSS Mode currently supports the 'headermenu' as one of the core menu options
if (!$CONF_NEXMENU['milonicmode']) {
    $CONF_NEXMENU['coremenu'][7] = 'headermenu';
    $LANG_NEXMENU03[7] = 'headermenu';
}

$CONF_NEXMENU['icons'] = array (
    1   => 'url_menuitem.gif',
    2   => 'url_menuitem.gif',
    3   => 'folder.gif',
    4   => 'core_menuitem.gif',
    5   => 'custom_menuitem.gif'
);


?>