<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.1.1 for the nexPro Portal Server                     |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | projectajaxrefresh.php                                                    |
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
require_once("../lib-common.php");
require_once($_CONF['path_html'] ."nexproject/includes/library.php");
require_once($_CONF['path_html'] ."nexproject/includes/lib-projects.php");
require_once($_CONF['path_system'] . 'classes/navbar.class.php');
require_once($_CONF['path_html']  . "/nexproject/includes/block.class.php");

$mode = COM_applyFilter($_REQUEST['op']);
$id = COM_applyFilter($_REQUEST['id'],true);       // Project ID


switch ($mode){

    case 'moveprojectlft':
        prj_moveProjectLeft($id);
        break;

    case 'moveprojectrht':
        prj_moveProjectRight($id);
        break;

    case 'moveprojectup':
        prj_moveProjectUp($id);
        break;

    case 'moveprojectdn':
        prj_moveProjectDown($id);
        break;

    case 'filter':
        echo $id;
        exit(0);
        break;
}

prj_displayMyProjects( $blockPage );

?>