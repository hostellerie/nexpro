<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexContent Plugin v2.2.0 for the nexPro Portal Server                     |
// | Sept. 16, 2009                                                            |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | upgrade.inc                                                               |
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



function upgrade_220() {
    global $_TABLES;

    COM_errorLog("Success - Completed nexContent plugin version 2.2.0 update",1);
    return 0;
}


function upgrade_23() {
    global $_CONF,$_TABLES;
    require_once $_CONF['path_system'] . 'classes/config.class.php';
    $plugin_path = $_CONF['path'] . 'plugins/nexcontent/';
    require_once $plugin_path . 'install_defaults.php';
    if (file_exists($plugin_path . 'config.php')) {
        global $_DB_table_prefix, $CONF_SE;
        require_once $plugin_path . 'config.php';
    }
    if (plugin_initconfig_nexcontent()) {
        return 0;
    } else {
        COM_errorLog('upgrade_23: There was an error upgrading the nexcontent plugin');
        return 1;
    }

}



?>