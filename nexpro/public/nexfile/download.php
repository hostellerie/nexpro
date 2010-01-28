<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v3.0.2 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | download.php                                                              |
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

include ('../lib-common.php');

$fid = COM_applyfilter($_GET['fid'],true);
$op = COM_applyfilter($_GET['op']);

COM_errorLog("Download.php - op:$op, uid:{$_USER['uid']}, fid:$fid");

if ($op == 'incoming') {
    if (!DB_count($_TABLES['nxfile_import_queue'],'id',$fid)) {
        echo COM_refresh($_CONF['site_url'] . '?msg=1&plugin=nexfile');
        exit();
    }
}

if ($op == 'download') {
    if (!DB_count($_TABLES['nxfile_files'],'fid',$fid)) {
        echo COM_refresh($_CONF['site_url'] . '?msg=1&plugin=nexfile');
        exit();
    }

    include_once($_CONF['path_system'] . 'classes/downloader.class.php');
    $version = COM_applyBasicFilter($_GET['version'],true);
    if ($version > 0) {
        $query = DB_query("SELECT fname,ftype FROM {$_TABLES['nxfile_fileversions']} WHERE fid={$fid} AND version={$version}");
        list($fname,$ftype) = DB_fetchARRAY($query);
        $cid = DB_getItem($_TABLES['nxfile_files'], "cid", "fid={$fid}");
    } else {
        $query = DB_query("SELECT cid,fname,ftype,mimetype FROM {$_TABLES['nxfile_files']} WHERE fid=$fid");
        list($cid,$fname,$ftype,$mimetype) = DB_fetchARRAY($query);
    }

    // Make sure user has access
    if (!fm_getPermission($cid, 'view')) {
        echo COM_refresh($_CONF['site_url'] . '?msg=1&plugin=nexfile');
        exit();
    }

    if ($ftype == "file") {
        $directory = $_FMCONF['storage_path'] . $cid . '/';
        $logfile = $_CONF['path'] .'logs/error.log';
        $pos = strrpos($fname,'.') + 1;
        $ext = substr($fname, $pos);

        // Open this file and stream it to the end user

        $download = new downloader();
        if (!empty($mimetype)) {
            $download->addAvailableExtensions(array($ext => $mimetype));
        }

        $download->setLogFile($logfile);
        $download->setLogging(true);
        $download->setPath($directory);
        $download->downloadFile($fname);
        DB_query("UPDATE {$_TABLES['nxfile_filedetail']} SET hits = hits +1 WHERE fid='$fid' ");
        DB_query("INSERT INTO {$_TABLES['nxfile_downloads']} (uid,fid,remote_ip,date) VALUES ('{$_USER['uid']}',$fid,'{$_SERVER['REMOTE_ADDR']}',NOW() )");
        if ($download->areErrors()) {
            $err = $download->printWarnings();
            $err .= "\n" . $download->printErrors();
            COM_errorLog("nexFile: Download error for user: {$_USER['uid']} - file: $fname. $err");
            return false;
       }


    }

} elseif ($_GET['op'] == "incoming") {

    if (!DB_count($_TABLES['nxfile_import_queue'],'id',$fid)) {
        echo COM_refresh($_CONF['site_url'] . '?msg=1&plugin=nexfile');
        exit();
    }

    include_once($_CONF['path_system'] . 'classes/downloader.class.php');
    $query = DB_query("SELECT * FROM {$_TABLES['nxfile_import_queue']} WHERE id=$fid");
    $A = DB_fetchARRAY($query);

    // Make sure user has access
    if (SEC_inGroup('Root') OR (!COM_isAnonUser() AND $A['uid'] == $_USER['uid'])) {
        $directory = $_FMCONF['storage_path'] . 'queue/';
        $logfile = $_CONF['path'] .'logs/error.log';
        $pos = strrpos($fname,'.') + 1;
        $ext = strtolower(substr($fname, $pos));

        $download = new downloader();
        //$download->addAvailableExtensions(array('pdf' => 'application/pdf'));

        $download->setLogFile($logfile);
        $download->setLogging(true);
        $download->setPath($directory);
        $download->downloadFile($A['queue_filename']);
        if ($download->areErrors()) {
            $err = $download->printWarnings();
            $err .= "\n" . $download->printErrors();
            COM_errorLog("nexFile: Download error for user: {$_USER['uid']} - file: $fname. $err");
            return false;
        }

    } else {
        echo COM_refresh($_CONF['site_url'] . '?msg=1&plugin=nexfile');
        exit();
    }

} elseif ($op == 'editfile') {
    if (!DB_count($_TABLES['nxfile_files'],'fid',$fid)) {
        echo COM_refresh($_CONF['site_url'] . '?msg=1&plugin=nexfile');
        exit();
    }
    $filename = DB_getItem($_TABLES['nxfile_files'],"fname","fid='{$fid}'");
    $cid = DB_getItem($_TABLES['nxfile_files'],"cid","fid='{$fid}'");
    // Make sure user has access
    if (!fm_getPermission($cid, 'view')) {
        echo COM_refresh($_CONF['site_url'] . '?msg=1&plugin=nexfile');
        exit();
    }

    $extension = str_replace(".","",strrchr($filename,"."));

    $file = "{$_FMCONF['storage_path']}{$cid}/{$filename}";
    $mime = '';
    foreach ($_FMCONF['allowable_file_types'] as $mimetype => $extensions) {
        if (array_key_exists(".{$extension}",$extensions)) {
            $mime = $mimetype;
            break;
        }
    }
    if (!empty($mime)) {
        // Add the file to the outgoing queue - so we can find it's match and replace it in-place when it's updated via the desktop agent
        $newfilename = nxfile_generateEditFileName($fid);
        $t = time();
        $uid = intval($_USER['uid']);
        $sql  = "INSERT INTO {$_TABLES['nxfile_export_queue']} (orig_filename,token,extension,timestamp,uid,fid) values (";
        $sql .= "'{$filename}',";
        $sql .= "'{$newfilename[1]}',";
        $sql .= "'{$extension}',";
        $sql .= "'{$t}',";
        $sql .= "'{$uid}',";
        $sql .= "'{$fid}'";
        $sql .= ")";
        DB_query($sql);
        // Change file status to locked - being edited
        DB_query("UPDATE {$_TABLES['nxfile_files']} SET status = 2, status_changedby_uid = {$_USER['uid']} WHERE fid='$fid' ");

        // Open this file and stream it to the end user
        $fp = fopen($file,"r");
        // Send out the headers and then the file in chunks
        header("Content-type: {$mime}");
        header("Content-disposition: attachment;filename={$newfilename[0]}");
        while(!feof($fp)){
            $data = fread($fp,$_FMCONF['downloadchunkrate']);
            echo $data;
        }
        exit;
    } else {
        COM_errorLog("MIME type for file $filename ($fid) could not be determined");
    }

} elseif ($_GET['op'] == "chksubmission") {
    if (!DB_count($_TABLES['nxfile_files'],'fid',$fid)) {
        echo COM_refresh($_CONF['site_url'] . '?msg=1&plugin=nexfile');
        exit();
    }
    $cid = DB_getItem($_TABLES['nxfile_files'], "cid", "fid={$fid}");

    // make sure user has access
    if (!fm_getPermission($cid, 'admin')) {
        echo COM_siteHeader();
        echo COM_startBlock('Access Denied');
        echo 'You do not have access rights to this file.  Your attempt has been logged.';
        echo COM_endBlock();
        echo COM_siteFooter();
    }

    if (DB_count($_TABLES['nxfile_filesubmissions'],'id',$fid) > 0) {
        include_once($_CONF['path_system'] . 'classes/downloader.class.php');
        $query = DB_query("SELECT cid,ftype,fname,tempname FROM {$_TABLES['nxfile_filesubmissions']} WHERE id=$fid");
        list($cid,$ftype,$fname,$tname) = DB_fetchARRAY($query);

        $directory = $_FMCONF['storage_path'] . $cid . '/submissions/';
        $logfile = $_CONF['path'] .'logs/error.log';

        if ($ftype == "file") {

            $pos = strrpos($tname,'.') + 1;
            $ext = strtolower(substr($tname, $pos));

            $download = new downloader();
            $download->_setAvailableExtensions ($_FMCONF['downloadfiletypes']);
            $download->setAllowedExtensions ($_FMCONF['downloadfiletypes']);
            $download->setLogFile($logfile);
            $download->setLogging(true);
            $download->setPath($directory);
            $download->downloadFile($tname);
            DB_query("UPDATE {$_TABLES['nxfile_filedetail']} SET hits = hits +1 WHERE fid='$fid' ");
            if ($download->areErrors()) {
                echo $LANG_FMERR['download1'];
                echo $download->printWarnings();
                echo $download->printErrors();
                return false;
            }
        } else {
            $url = $fname;
            if ($fd = fopen ($url, "rb")) {
                $pos = strrpos($url,"/") + 1;
                $fname = substr($url,$pos);
                if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
                    $fname = preg_replace('/\./', '%2e', $fname, substr_count($fname, '.') - 1);
                }
                DB_query("UPDATE {$_TABLES['nxfile_filedetail']} SET hits = hits +1 WHERE fid='$fid' ");
                header("Cache-Control:");
                header("Pragma:");
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"{$fname}\"");
                header("Content-length:");
                fpassthru($fd);
                fclose ($fd);
                DB_query("UPDATE {$_TABLES['nxfile_filedetails']} SET downloads=downloads+1 WHERE fid='$fid' ");
            }
        }
    } else {
        echo $LANG_FMERR['download4'];
        exit();
    }

// Check and see if any user has selected files to be downloaded as an archived
} elseif (isset($_POST['multiaction']) AND $_POST['multiaction'] == 'archive' AND !empty($_POST['checkeditems'])) {

    // delete any older zip archives that were created
    $archiveDirectory = "{$_FMCONF['storage_path']}tmp/";
    if (!file_exists($archiveDirectory)) {
        @mkdir($archiveDirectory);
    }
    $fd = opendir($archiveDirectory);
    while ((false !== ($file = @readdir($fd)))) {
        if ($file <> '.' && $file <> '..' && $file <> 'CVS' &&
                preg_match('/\.zip$/i', $file)) {
                    $ftimestamp = @fileatime("{$archiveDirectory}{$file}");
                    if ($ftimestamp < (time() - 600)) {
                        COM_errorLog("Nexfile: Remove tmp archive file : $file");
                        @unlink("{$archiveDirectory}{$file}");
                    }
        }
    }

    $filter = new sanitizer();
    $cid = $filter->getDbData('int',$_POST['cid']);
    $fileitems = $filter->getDbData('text',$_POST['checkeditems']);
    $files = explode(',',$fileitems);

    include('lib-archive.php');
    nexdoc_createArchiveFromFiles($cid,$fileitems);

} else {
    echo $LANG_FMERR['download4'];
    exit();
}


?>