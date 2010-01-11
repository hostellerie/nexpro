<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.1.1 for the nexPro Portal Server                     |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | upgrade.inc                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Randy Kolenko          - Randy DOT Kolenko AT nextide DOT ca              |
// | Blaine Lang            - Blaine DOT Lang AT nextide DOT ca                |
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



function nexproject_upgrade_211() {
    global $_TABLES, $_PRJCONF, $_CONF,$_DB_table_prefix;

    $base_path = $_CONF['path'] . 'plugins/nexproject/';
    require_once $_CONF['path_system'] . 'classes/config.class.php';
    //we have to update the online config with all values here to propagate them accross properly.

    @include_once $base_path . 'config.php';
    require_once $base_path . 'install_defaults.php';
    $c = config::get_instance();
    if (!$c->group_exists('nexproject')) {
      if(!plugin_initconfig_nexproject()){
        COM_errorLog("ERROR! - There has been an error migrating to the online configuration.",1);
        return -1;
      }else{
        $c->add('prj_list', NULL, 'fieldset', 0, 1, NULL, 0, true, 'nexproject');
        $c->add('nexlist_locations', $_PRJCONF['nexlist_locations'],'text',0, 1, 0, 150, true, 'nexproject');
        $c->add('nexlist_departments', $_PRJCONF['nexlist_departments'],'text',0, 1, 0, 160, true, 'nexproject');
        $c->add('nexlist_category', $_PRJCONF['nexlist_category'],'text',0, 1, 0, 170, true, 'nexproject');
        $c->add('nexlist_objective', $_PRJCONF['nexlist_objective'],'text',0, 1, 0, 180, true, 'nexproject');
        $c->add('prj_file', NULL, 'fieldset', 0, 2, NULL, 0, true, 'nexproject');
        $c->add('nexfile_parent', $_PRJCONF['nexfile_parent'],'text',0, 2, 0, 190, true, 'nexproject');
        $c->add('prj_forum', NULL, 'fieldset', 0, 3, NULL, 0, true, 'nexproject');
        $c->add('forum_parent', $_PRJCONF['forum_parent'],'text',0, 3, 0, 200, true, 'nexproject');
        COM_errorLog("Success - Completed nexProject plugin version 2.0.3 update",1);
        return 0;
      }
    }else{
      COM_errorLog("Success - Completed nexProject plugin version 2.1.1 update",1);
      return 0;
    }

}


?>