<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.1.0 for the nexPro Portal Server                     |
// | Sept. 20, 2009                                                            |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | debug.php                                                                 |
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
if ($_PRJCONF['debug']) {
    // Debug Code to show variables
    if (!empty($_POST)) {
        echo "HTTP_POST_VARS:<br>";
        var_dump($_POST);
        echo "<hr>";
    }
    if (!empty($_GET)) {
        echo "HTTP_GET_VARS:<br>";
        var_dump($_GET);
        echo "<hr>";
    }
    if (!empty($_FILES)) {
        echo "HTTP_POST_FILES:<br>";
        var_dump($_FILES);
        echo "<hr>";
    }
}

?>