<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Release date: Sept 12,2008                                                |
// +---------------------------------------------------------------------------+
// | getattachment.php   Script for downloading Attachment from nexflow task   |
// +---------------------------------------------------------------------------+
// | Plugin Author:   blaine.lang@nextde.ca                                    |
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

require_once ('../lib-common.php');

if (!isset($_USER['uid']) OR $_USER['uid'] < 2)
{
    die ('You need to be logged in');
}

$id = COM_applyFilter($_GET['id'], true);
$query = DB_query("SELECT filename FROM {$_TABLES['nfproject_attachments']} WHERE id=$id;");
$A = DB_fetchArray($query);

if (DB_numRows($query) != 1) {
    COM_errorLog("Error: Cannot Display Selected File");
    die ('Error: Cannot located file record');
}

$filedata = explode(':', $A['filename']);
$filename = $filedata[0];
$realname = $filedata[1];
$filepath = "{$CONF_NF['uploadpath']}/$filename";

if ($fd = fopen ($filepath, "rb")) {
    header("Content-type: application/octet-stream");
    header("Content-Disposition: inline; filename=\"{$realname}\"");
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    fpassthru($fd);
    fclose ($fd);
} else {
    COM_errorLog("Error: Cannot Display Selected File, $realname");
    die ("Error: Cannot retrieve File: $realname");
}


?>