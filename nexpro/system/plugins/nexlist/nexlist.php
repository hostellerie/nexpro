<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexList Plugin v2.2.0 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexlist.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
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

require_once $_CONF['path_system'] . 'classes/config.class.php';

$CONF_LL=array();
$CONF_LL['version'] = '2.2.0';
$CONF_LL['pi_name'] = 'nexlist';
$CONF_LL['pi_display_name'] = 'nexList';
$CONF_LL['pi_gl_version'] = '1.6.1';


$_TABLES['nexlist']        = $_DB_table_prefix . 'nxlist';
$_TABLES['nexlistfields']  = $_DB_table_prefix . 'nxlist_fields';
$_TABLES['nexlistitems']   = $_DB_table_prefix . 'nxlist_items';

$nexlist_config = config::get_instance();
$CONF_LL_2 = $nexlist_config->get_config('nexlist');
if(is_array($CONF_LL_2)) {
    $CONF_LL=@array_merge($CONF_LL_2,$CONF_LL);

}

?>