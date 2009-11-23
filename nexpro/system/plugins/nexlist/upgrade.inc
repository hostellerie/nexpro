<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexList Plugin v2.1.1 for the nexPro Portal Server                        |
// | May 20, 2008                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | upgrade.inc                                                               |
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

function nexlist_upgrade_220() {
    global $_TABLES,$_CONF,$_DB_table_prefix;

    $base_path = $_CONF['path'] . 'plugins/nexlist/';
    require_once $_CONF['path_system'] . 'classes/config.class.php';
    //we have to update the online config with all values here to propagate them accross properly.
    require_once $base_path . 'install_defaults.php';
    require_once $base_path . 'config.php';
    $c = config::get_instance();
    if (!$c->group_exists('nexlist')) {
      if(!plugin_initconfig_nexlist()){
        COM_errorLog("ERROR! - There has been an error migrating to the online configuration.",1);
        return -1;
      }else{
        COM_errorLog("Success - Completed nexList plugin version 2.2.0 update",1);
        return 0;
      }
    }
}


?>