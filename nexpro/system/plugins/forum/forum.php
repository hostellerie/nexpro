<?php

// +--------------------------------------------------------------------------+
// | Geeklog Forums Plugin 3.1 for Geeklog - The Ultimate Weblog              |
// | Initial release date: Feb 7,2003                                         |
// +--------------------------------------------------------------------------+
// | config.php                                                               |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2009 by the following authors:                             |
// |                                                                          |
// | Blaine Lang                - blaine AT portalparts DOT com               |
// |                                                                          |
// | Authors: Blaine Lang       - blaine AT portalparts DOT com               |
// |                              www.portalparts.com                         |
// | Version 1.0 co-developer:    Matthew DeWyer, matt@mycws.com              |
// | Prototype & Concept :        Mr.GxBlock, www.gxblock.com                 |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | This program is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU General Public License              |
// | as published by the Free Software Foundation; either version 2           |
// | of the License, or (at your option) any later version.                   |
// |                                                                          |
// | This program is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with this program; if not, write to the Free Software Foundation,  |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.          |
// |                                                                          |
// +--------------------------------------------------------------------------+

if (strpos(strtolower($_SERVER['PHP_SELF']), 'forum.php') !== false) {
    die('This file can not be used on its own!');
}

global $_DB_table_prefix, $_TABLES;

$CONF_FORUM['imgset'] = $_CONF['layout_url'] .'/forum/image_set';
$CONF_FORUM['imgset_path'] = $_CONF['path_layout'] .'/forum/image_set';

/* The forum uses a number of icons and you may have a need to use a mixture of image types.
 * Enabling the $CONF_FORUM['autoimagetype'] feature will invoke a function that will first
 * check for an image of the type set in your themes function.php $_IMAGE_TYPE
 * If the icon of that image type is not found, then it will use an image of type
 * specified by the $CONF_FORUM['image_type_override'] setting.

 * Set $CONF_FORUM['autoimagetype'] to false to disable this feature and
 * only icons of type set by the themes $_IMAGE_TYPE setting will be used
*/
$CONF_FORUM['autoimagetype'] = true;
$CONF_FORUM['image_type_override'] = 'gif';

/*************************************************************************
*          Do not modify any settings below this area                    *
*************************************************************************/

$CONF_FORUM['version']            = '3.1.0';
$CONF_FORUM['pi_display_name']    = 'forum';
$CONF_FORUM['pi_name']            = 'forum';
$CONF_FORUM['gl_version']         = '1.6.1';
$CONF_FORUM['pi_url']             = 'http://www.portalparts.com/';

// Adding the Forum Plugin tables to $_TABLES array
$_TABLES['gf_userprefs']    = $_DB_table_prefix . 'gf_userprefs';
$_TABLES['gf_topic']        = $_DB_table_prefix . 'gf_topic';
$_TABLES['gf_categories']   = $_DB_table_prefix . 'gf_categories';
$_TABLES['gf_forums']       = $_DB_table_prefix . 'gf_forums';
$_TABLES['gf_settings']     = $_DB_table_prefix . 'gf_settings';
$_TABLES['gf_watch']        = $_DB_table_prefix . 'gf_watch';
$_TABLES['gf_moderators']   = $_DB_table_prefix . 'gf_moderators';
$_TABLES['gf_banned_ip']    = $_DB_table_prefix . 'gf_banned_ip';
$_TABLES['gf_log']          = $_DB_table_prefix . 'gf_log';
$_TABLES['gf_userinfo']     = $_DB_table_prefix . 'gf_userinfo';
$_TABLES['gf_attachments']  = $_DB_table_prefix . 'gf_attachments';
$_TABLES['gf_bookmarks']    = $_DB_table_prefix . 'gf_bookmarks';


?>