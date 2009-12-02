<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexfile Plugin v3.0.0 for the nexPro Portal Server                        |
// | Sept 30, 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | upload.php     - Windows Desktop agent handler                            |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2009 by the following authors:                         |
// | Randy Kolenko          - randy DOT kolenko AT nextide DOT ca              |
// | Blaine Lang            - blaine DOT lang AT nextide DOT ca                |
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

$username = COM_applyFilter($_POST['username']);
$password = COM_applyFilter($_POST['password']);
$op       = COM_applyFilter($_POST['op']);

if (!file_exists("{$_FMCONF['storage_path']}queue")) {
    @mkdir("{$_FMCONF['storage_path']}queue");
    if (!file_exists("{$_FMCONF['storage_path']}queue")) {
        COM_errorLog("Nexfile Error: Directory for upload queue does not exist - {$_FMCONF['storage_path']}queue");
        die();
    }
}


function nxfile_downloadFile($fid){
    global $_TABLES,$_CONF,$_FMCONF, $_USER;

    $filename = DB_getItem($_TABLES['nxfile_files'],"fname","fid='{$fid}'");
    $cat = DB_getItem($_TABLES['nxfile_files'],"cid","fid='{$fid}'");
    $extension = str_replace(".","",strrchr($filename,"."));

    $file = "{$_FMCONF['storage_path']}{$cat}/{$filename}";
    $mime = $_FMCONF['downloadfiletypes'][$extension];

    //we have to jam this in the outgoing queue
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

    //we need to open this file and stream it to the end user.
    $fp = fopen($file,"r");
    //spit out the headers here
    header("Content-type: {$mime}");
    header("Content-disposition: attachment;filename={$newfilename[0]}");

    while(!feof($fp)){
        $data = fread($fp,$_FMCONF['downloadchunkrate']);
        echo $data;
    }

}


function nxfile_uploadFile($fileArray, $username='', $password=''){
    global $_TABLES,$_CONF,$_FMCONF, $_USER;

    $isopen = COM_applyFilter($_POST['isopen'],true);   // Sent by the NexFile Desktop Agent and if value == 1 then the file is still open.

    $uid = intval(DB_getItem($_TABLES['users'],"uid","username='$username' AND passwd='$password'"));
    $fullname = DB_getItem($_TABLES['users'],"fullname","uid=$uid");
    $filename = $fileArray['name'];
    $filesize = intval($fileArray['size']);
    $mimetype = $fileArray['type'];
    $outputInformation = '';
    //format is ....{t..token...}.extension if its an actual upload
    $matchesArray = array();
    preg_match_all("|{[^}]+t}|",$filename,$matchesArray);

    // Check that $matchesArray[0][0] contains valid data - should contain the token
    if($matchesArray[0][0] != '' && isset($matchesArray[0][0])) {

        $token = str_replace("{","",$matchesArray[0][0]);
        $token = str_replace("t}","",$token);
        $fid = DB_getItem($_TABLES['nxfile_export_queue'],"fid","token='{$token}'");

        //using the fid and token, we align this to the export table and ensure this is a valid upload!

        $sql = "SELECT id, orig_filename,extension,timestamp,fid FROM {$_TABLES['nxfile_export_queue']} WHERE token='{$token}' ";
        $res = DB_query($sql);
        $nrows = DB_numRows($res);
        if($nrows > 0) {
            list($id,$orig_filename,$extension,$timestamp,$fid)=DB_fetchArray($res);
            $cat = DB_getItem($_TABLES['nxfile_files'],"cid","fid='{$fid}'");
            $outputInformation .= "Upload of a file that matches the export table.\n ";
            // Update the repository with the new file - PHP/Windows will not rename a file if it exists
            // Rename is atomic and fast vs copy and unlink as there is a chance someone may be trying to download the file
            if (@rename($fileArray['tmp_name'],"{$_FMCONF['storage_path']}{$cat}/{$orig_filename}") == FALSE) {
                @copy($fileArray['tmp_name'],"{$_FMCONF['storage_path']}{$cat}/{$orig_filename}");
                @unlink($fileArray['tmp_name']);
            }
            // Update information in the repository
            DB_query("UPDATE {$_TABLES['nxfile_files']} SET status='1', status_changedby_uid='$uid' WHERE fid=$fid");

        } else {
            $outputInformation .=  "Upload of a file that attempted to match the export table but failed.\n";
            $outputInformation .=  "This file will be placed into the upload queue.\n";
            $moved = @rename($fileArray['tmp_name'],"{$_FMCONF['storage_path']}queue/{$fileArray['name']}");
            if($moved){
            // Update the incoming queue.
                $t=time();
                $fileArray['name'] = (!get_magic_quotes_gpc()) ? addslashes($fileArray['name']) : $fileArray['name'];
                $tempfilename=substr($fileArray['name'],$_FMCONF['upload_prefix_character_count']);
                $description = "Uploaded by $fullname on " . date("F j, Y, g:i a") . ', via the Nexfile desktop agent';
                $sql  = "INSERT INTO {$_TABLES['nxfile_import_queue']} (orig_filename,queue_filename,timestamp,uid,size,mimetype,description ) ";
                $sql .= "values ('{$tempfilename}','{$fileArray['name']}','{$t}', $uid,$filesize,'$mimetype','$description')";
                DB_query($sql);
	        } else {
	            $outputInformation .= "Sorry, there is already a file with that name in the queue";
	        }
        }

    } else {

        // Dump this into the upload queue for processing later
        $outputInformation .=  "Upload of a file to the queue.\n";
        $moved = @rename($fileArray['tmp_name'],"{$_FMCONF['storage_path']}queue/{$fileArray['name']}");
        if($moved){
            //now we update the incoming queue.
            $description = "Uploaded by $fullname on " . date("F j, Y, g:i a") . ', via the Nexfile desktop agent';
            $t = time();
            $fileArray['name'] = (!get_magic_quotes_gpc()) ? addslashes($fileArray['name']) : $fileArray['name'];
            $tempfilename = substr($fileArray['name'],$_FMCONF['upload_prefix_character_count']);
            $sql  = "INSERT INTO {$_TABLES['nxfile_import_queue']} (orig_filename,queue_filename,timestamp,uid,size,mimetype,description ) ";
            $sql .= "values ('{$tempfilename}','{$fileArray['name']}','{$t}', $uid,$filesize,'$mimetype','$description')";
            DB_query($sql);
        } else {
            $outputInformation .= "Sorry, there is already a file with that name in the queue";
        }
    }

    $outputInformation .=  ("File: {$fileArray['name']} has been uploaded...\n" );
    return $outputInformation;

}


function nxfile_testuser($username,$password){
    global $_TABLES;
    if(DB_getItem($_TABLES['users'],'uid',"username='$username' AND passwd='$password'") > 0) {
        return true;
    } else {
        return false;
    }
}

switch($op) {
    case 'testconnection':
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("content-type: application/xml");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
        echo "<data>";
        if(nxfile_testuser($username,$password)){
            echo "<status>success</status>";
            echo "<statusid>1</statusid>";
        } else {
            echo "<status>fail</status>";
            echo "<statusid>0</statusid>";
        }
        echo "</data>";
        break;

    default:
        if(nxfile_testuser($username,$password)){
            com_errorlog(nxfile_uploadFile($_FILES['file'],$username,$password)); //fetch the file
        }
        break;
}


?>