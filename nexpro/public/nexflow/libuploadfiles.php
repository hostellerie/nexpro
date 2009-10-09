<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Release date: Sept 12,2008                                                |
// +---------------------------------------------------------------------------+
// | libuploadfiles.php   library of functions for uploading files             |
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

if (strpos ($_SERVER['PHP_SELF'], 'libuploads.php') !== false)
{
    die ('This file can not be used on its own.');
}


function nf_check4files($projectid,$taskid,$fieldname) {
    global $_FILES,$_CONF,$_TABLES,$_USER,$CONF_NF,$LANG_GF00;

    $errmsg = '';
    $uploadfile = $_FILES[$fieldname];

    // Check if there is a request to delete any attachments
    if (isset($_POST['chk_removeattachment'])) {
        foreach ($_POST['chk_removeattachment'] as $id) {
            $filename = DB_getItem($_TABLES['nfproject_attachments'],'filename',"id=$id");
            $parts = explode(':',$filename);
            COM_errorLog("{$CONF_NF['uploadpath']}/{$parts[0]}");
            DB_query("DELETE FROM {$_TABLES['nfproject_attachments']} WHERE id=$id");
            @unlink("{$CONF_NF['uploadpath']}/{$parts[0]}");
        }

    }

    if ($uploadfile['name'] != '' ) {
        $uploadfilename =  ppRandomFilename();
        $pos = strrpos($uploadfile['name'],'.') + 1;
        $ext = strtolower(substr($uploadfile['name'], $pos));
        $filename = "{$uploadfilename}.{$ext}";
        COM_errorlog("Workflow file upload: Original file: {$uploadfile['name']} and new filename: $filename");
        $filestore_path = $CONF_NF['uploadpath'];

        if ( nf_uploadfile($filename,$uploadfile,$CONF_NF['allowablefiletypes'],$filestore_path) ) {
            // Store both the created filename and the real file source filename
            $filename = "$filename:{$uploadfile['name']}";
            DB_query("INSERT INTO {$_TABLES['nfproject_attachments']} (project_id,task_id,fieldname,filename)
                    VALUES ($projectid,$taskid,'$fieldname','$filename')");
        } else {
            COM_errorlog("upload error:" . $GLOBALS['nf_errmsg']);
            $errmsg = $GLOBALS['nf_errmsg'];
        }
    }
    return $errmsg;

}


function nf_uploadfile($filename,&$upload_file,$allowablefiletypes,$filestore_path) {
    global $_FILES,$_CONF,$_TABLES,$CONF_NF,$LANG_GF00;

    include_once($_CONF['path_system'] . 'classes/upload.class.php');
    $upload = new upload();
    $upload->setPath($filestore_path);
    $upload->setLogging(true);
    $upload->setAutomaticResize(false);
    $upload->setAllowedMimeTypes($allowablefiletypes);
    $upload->setMaxFileSize($CONF_NF['max_uploadfile_size']);

    if (strlen($upload_file['name']) > 0) {
        $upload->setFileNames($filename);
        $upload->setPerms( $CONF_NF['fileperms'] );

        $upload->_currentFile = $upload_file;

        // Verify file meets size limitations
        if (!$upload->_fileSizeOk()) {
            $upload->_addError('File, ' . $upload->_currentFile['name'] . ', is bigger than the ' . $upload->_maxFileSize . ' byte limit');
        }

        // If all systems check, do the upload
        if ($upload->checkMimeType() AND $upload->_imageSizeOK() AND !$upload->areErrors()) {
            if ($upload->_copyFile()) {
                $upload->_uploadedFiles[] = $upload->_fileUploadDirectory . '/' . $upload->_getDestinationName();
            }
        }

        $upload->_currentFile = array();

        if ($upload->areErrors() AND !$upload->_continueOnError) {
            $errmsg = "Workflow Upload Attachment Error:" . $upload->printErrors(false);
            COM_errorlog($errmsg);
            $GLOBALS['nf_errmsg'] = $LANG_GF00['uploaderr'] .':<BR>' . $upload->printErrors(false);
            return false;
        }
        return true;

    } else {
        return false;
    }

    return false;

}


function nf_showAttachments($taskid=0,$projectid=0,$fieldname,$deloption=true) {
    global $_CONF,$_TABLES,$CONF_NF;

    $retval = '';
    $downloadlink = $_CONF['site_url'] . '/nexflow/getattachment.php?id=%s';
    if ($taskid > 0) {
        $query = DB_query("SELECT id,filename FROM {$_TABLES['nfproject_attachments']} WHERE task_id=$taskid AND fieldname = '$fieldname'");
    } elseif ($projectid > 0) {
        $query = DB_query("SELECT id,filename FROM {$_TABLES['nfproject_attachments']} WHERE project_id=$projectid AND fieldname = '$fieldname'");
    } else {
        return '';
    }

    if (DB_numRows($query) > 0) {
        $retval = '<table class="inlinetask_attachments" cellspacing="0" cellpadding="0" border="0">';
        while (list($id,$filename) = DB_fetchArray($query)) {
            $parts = explode(':',$filename);
            $retval .= '<tr>';
            $retval .= '<td><a href="'.sprintf($downloadlink,$id).'" target="_blank">' . $parts[1]. '</a></td>';
            if ($deloption) {
                $retval .= '<td><input type="checkbox" name="chk_removeattachment[]" value="'.$id.'">';
                $retval .= '<span style="padding-left:10px;position:relative;bottom:3px;">Check to remove file</span>';
                $retval .= '</td>';
            }
            $retval .= '</tr>';
        }
        $retval .= '</table>';
    }
    return $retval;

}

?>