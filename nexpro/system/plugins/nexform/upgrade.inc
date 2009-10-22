<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.2 for the nexPro Portal Server                          |
// | Sept 14, 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | upgrade.inc                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
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

function nexform_upgrade_201() {
    global $_TABLES;

    COM_errorLog("Success - Completed nexForm plugin version 2.0.1 update",1);
    return 0;
}

function nexform_upgrade_22() {
    global $_CONF,$_TABLES;
    require_once $_CONF['path_system'] . 'classes/config.class.php';
    $plugin_path = $_CONF['path'] . 'plugins/nexform/';
    require_once $plugin_path . 'install_defaults.php';
    if (file_exists($plugin_path . 'config.php')) {
        global $_DB_table_prefix, $CONF_FE;
        require_once $plugin_path . 'config.php';
    }
    if (plugin_initconfig_nexform()) {
        return 0;
    } else {
        COM_errorLog('nexform_upgrade_22: There was an error upgrading the nexform plugin');
        return 1;
    }

}



?>