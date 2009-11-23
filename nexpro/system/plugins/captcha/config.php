<?php
// +---------------------------------------------------------------------------+
// | CAPTCHA v3 Plugin                                                         |
// +---------------------------------------------------------------------------+
// +---------------------------------------------------------------------------+
// | $Id: config.php,v 1.3 2007/09/12 18:02:49 eric Exp $|
// +---------------------------------------------------------------------------+
// | Copyright (C) 2005, 2006, 2007 by the following authors:                  |
// |                                                                           |
// | Mark R. Evans               -    mevans@ecsnet.com                        |
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

$_CP_CONF['expire'] = 900; // number of seconds to expire a session (900 = 15 min)

// Do not edit this file, all configuration options are available online

$_CP_CONF['version']   = '3.0.2';
$_TABLES['cp_config']               = $_DB_table_prefix . 'cp_config';
$_TABLES['cp_sessions']             = $_DB_table_prefix . 'cp_sessions';
?>