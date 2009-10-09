<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexTime Plugin v1.2.0 for the nexPro Portal Server                        |
// | Date: Sept. 23, 2009                                                      |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | upgrade.inc                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2009 by the following authors:                         |
// | Ted Clark              - Support@nextide.ca                               |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
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



function upgrade_120() {
    global $_TABLES, $CONF_NEXTIME, $_CONF;

    $base_path = $_CONF['path'] . 'plugins/nextime/';
    require_once $_CONF['path_system'] . 'classes/config.class.php';
    //we have to update the online config with all values here to propagate them accross properly.
    @include_once $base_path . 'config.php';
    require_once $base_path . 'install_defaults.php';
    $c = config::get_instance();
    if (!$c->group_exists('nextime')) {
      if(!plugin_initconfig_nextime()){
        COM_errorLog("ERROR! - There has been an error migrating to the online configuration.",1);
        return -1;
      }else{

        $c->add('supervisor_group_id', $CONF_NEXTIME['supervisor_group_id'],'text',0, 0, 0, 110, true, 'nextime');

	    $c->add('nxtime_list', NULL, 'fieldset', 0, 1, NULL, 0, true, 'nextime');

	    $c->add('nexlist_timesheet_tasks', $CONF_NEXTIME['nexlist_timesheet_tasks'],'text',0, 1, 0, 120, true, 'nextime');
	    $c->add('nexlist_nextime_activities', $CONF_NEXTIME['nexlist_nextime_activities'],'text',0, 1, 0, 130, true, 'nextime');
	    $c->add('nexlist_nextime_projects', $CONF_NEXTIME['nexlist_nextime_projects'],'text',0, 1, 0, 140, true, 'nextime');
	    $c->add('nexlist_employee_to_supervisor', $CONF_NEXTIME['nexlist_employee_to_supervisor'],'text',0, 1, 0, 150, true, 'nextime');
	    $c->add('nexlist_user_locations', $CONF_NEXTIME['nexlist_user_locations'],'text',0, 1, 0, 160, true, 'nextime');
	    $c->add('nexlist_manager_to_supervisor', $CONF_NEXTIME['nexlist_manager_to_supervisor'],'text',0, 1, 0, 170, true, 'nextime');
	    $c->add('nexlist_employee_to_delegate', $CONF_NEXTIME['nexlist_employee_to_delegate'],'text',0, 1, 0, 180, true, 'nextime');

        COM_errorLog("Success - Completed nexTime plugin version 1.2.0 update",1);
        return 0;
      }
    }else{
      COM_errorLog("Success - Completed nexTime plugin version 1.2.0 update",1);
      return 0;
    }

}


?>