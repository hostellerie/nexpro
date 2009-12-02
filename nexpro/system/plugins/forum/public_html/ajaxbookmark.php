<?php
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin for Geeklog - The Ultimate Weblog                   |
// | Release date: Oct 30,2007                                                 |
// +---------------------------------------------------------------------------+
// | ajaxbookmark.php - AJAX Server update functions for bookmark feature      |
// +---------------------------------------------------------------------------+
// | Plugin Author:   blaine@portalparts.com, www.portalparts.com              |
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

include ('../lib-common.php');
require_once ($_CONF['path_html'] . 'forum/include/gf_format.php');
$id = COM_applyFilter($_GET['id'],true);

if ($_USER['uid'] > 1 AND $id >= 1) {
    if (DB_count($_TABLES['gf_bookmarks'],array('uid','topic_id'),array($_USER['uid'],$id))) {
        $bookmarkimg = '<img src="'.gf_getImage('star_off_sm').'" TITLE="'.$LANG_GF02['msg203'].'">';
        DB_query("DELETE FROM {$_TABLES['gf_bookmarks']} WHERE uid={$_USER['uid']} AND topic_id=$id");
    } elseif (DB_count($_TABLES['gf_bookmarks'],array('uid','pid'),array($_USER['uid'],$id))) {
        $bookmarkimg = '<img src="'.gf_getImage('star_off_sm').'" TITLE="'.$LANG_GF02['msg203'].'">';
        DB_query("DELETE FROM {$_TABLES['gf_bookmarks']} WHERE uid={$_USER['uid']} AND pid=$id");
    } else {
        $bookmarkimg = '<img src="'.gf_getImage('star_on_sm').'" TITLE="'.$LANG_GF02['msg204'].'">';
        $pid = DB_getItem($_TABLES['gf_topic'],'pid',"id=$id");
        DB_query("INSERT INTO {$_TABLES['gf_bookmarks']} (uid,topic_id,pid) VALUES ({$_USER['uid']},$id,$pid)");
    }
    $html = '<a href="#" onClick="ajax_toggleForumBookmark('.$id.');return false;">'.$bookmarkimg.'</a>';
    $html = htmlentities ($html);

    $retval = "<result>";
    $retval .= "<topic>$id</topic>";
    $retval .= "<html>$html</html>";
    $retval .= "</result>";

    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("content-type: text/xml");

    print $retval;
}


?>