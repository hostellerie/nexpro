<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexScan v1.0.0 - CSS Scan Plugin for nexPro                               |
// | November 19, 2009                                                         |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexscan.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2008 by the following authors:                              |
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

$CONF_NS = array();
$CONF_NS['debug'] = false;
$CONF_NS['version'] = '1.0.0';
$CONF_NS['pi_name'] = 'nexscan';
$CONF_NS['pi_display_name'] = 'nexScan';
$CONF_NS['pi_gl_version'] = '1.6.0';

$CONF_NS['valid_wrap_chars'] = '\'\"\\ ';     //chars that are allowed to wrap the class def... ie class=\"cname\" or class=""
$CONF_NS['types_to_scan'] = array ('.php', '.inc', '.thtml', '.html', '.js');     //scan type options

$_TABLES['nxscan_cssscan']  = $_DB_table_prefix . 'nxscan_cssscan';
$_TABLES['nxscan_options']  = $_DB_table_prefix . 'nxscan_options';


require_once $_CONF['path_system'] . 'classes/config.class.php';
$nexscan_config = config::get_instance();
$CONF_NS_2 = $nexscan_config->get_config('nexscan');
if (is_array($CONF_NS_2)) $CONF_NS = @array_merge($CONF_NS_2, $CONF_NS);

?>
