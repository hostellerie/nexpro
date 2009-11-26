<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin for Geeklog - The Ultimate Weblog                   |
// | Release date: Oct 30,2007                                                 |
// +---------------------------------------------------------------------------+
// | lib-uploadfiles.php   library of functions for uploading files            |
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


function gf_check4files($id,$tempfile=false) {
    global $_FILES,$_CONF,$_TABLES,$_USER,$CONF_FORUM,$LANG_GF00;
    global $_FM_TABLES,$CONF_FORUM,$filemgmt_FileStore;

    $filelinks = '';
    $uploadfile = $_FILES['file_forum'];
    $cid = COM_applyFilter($_POST['filemgmtcat'],true);
    $use_filestore = false;
    if ($uploadfile['name'] != '' ) {
        if ($_POST['chk_usefilemgmt'] == 1 AND $cid > 0) {
            $filename = $uploadfile['name'];
            $pos = strrpos($uploadfile['name'],'.') + 1;
            $ext = strtolower(substr($uploadfile['name'], $pos));
            $use_filestore = true;
        } else {
            $uploadfilename =  ppRandomFilename();
            $pos = strrpos($uploadfile['name'],'.') + 1;
            $ext = strtolower(substr($uploadfile['name'], $pos));
            $filename = "{$uploadfilename}.{$ext}";
            //COM_errorlog("Forum file upload: Original file: {$uploadfile['name']} and new filename: $filename");
        }
        if ($use_filestore) {
            if ($CONF_FORUM['FM_PLUGIN'] == 'filemgmt') {
                $filestore_path = $filemgmt_FileStore;
            } elseif ($CONF_FORUM['FM_PLUGIN'] == 'nexfile') {
                $filestore_path = $_CONF['path_html'] . 'nexfile/data/'.$cid. '/';
            } else {
                $filestore_path = $CONF_FORUM['uploadpath'];
            }
        } else {
            $filestore_path = $CONF_FORUM['uploadpath'];
        }
        if ( gf_uploadfile($filename,$uploadfile,$CONF_FORUM['allowablefiletypes'],$filestore_path) ) {
            if (array_key_exists($uploadfile['type'],$CONF_FORUM['inlineimageypes']) AND function_exists(MG_resizeImage)) {
                if ($_POST['chk_usefilemgmt'] == 1) {
                    $srcImage = "{$filemgmt_FileStore}{$filename}";
                    $destImage = "{$CONF_FORUM['uploadpath']}/tn/{$filename}";
                } else {
                    $srcImage = "{$CONF_FORUM['uploadpath']}/{$filename}";
                    $destImage = "{$CONF_FORUM['uploadpath']}/tn/{$uploadfilename}.{$ext}";
                }

                $ret = MG_resizeImage($srcImage,$destImage,$CONF_FORUM['inlineimage_height'],$CONF_FORUM['inlineimage_width']);
            }

            // Store both the created filename and the real file source filename
            $realfilename = $filename;
            $filename = "$filename:{$uploadfile['name']}";
            if ($tempfile) {
                $temp = 1;
            } else {
                $temp = 0;
            }
            if ($use_filestore) {
                // Check and see if nexfile or the filemgmt plugin is being used
                if ($CONF_FORUM['FM_PLUGIN'] == 'nexfile') {
                    DB_query("INSERT INTO {$_TABLES['fm_files']} (cid,fname,title,version,ftype,size,submitter,status,date)
                        VALUES ('$cid','$realfilename','$realfilename','1','file','{$uploadfile['size']}','{$_USER['uid']}','1',UNIX_TIMESTAMP())");
                    $fid = DB_insertId();
                    DB_query("INSERT INTO {$_TABLES['fm_detail']} (fid,description,platform,hits,rating,votes,comments)
                        VALUES ('$fid','','','0','0','0','0')");
                    DB_query("INSERT INTO {$_TABLES['fm_versions']} (fid,fname,ftype,version,size,notes,date,uid,status)
                        VALUES ('$fid','$realfilename','file','1','{$uploadfile['size']}','',UNIX_TIMESTAMP(),'{$_USER['uid']}','1')");
                    DB_query("INSERT INTO {$_TABLES['gf_attachments']} (topic_id,repository_id,filename,tempfile)
                        VALUES ('$id',$fid,'$filename',$temp)");

                } elseif ($CONF_FORUM['FM_PLUGIN'] == 'filemgmt') {
                    $sql = "INSERT INTO {$_FM_TABLES['filemgmt_filedetail']} (cid, title, url, size, submitter, status,date ) ";
                    $sql .= "VALUES ('$cid', '$realfilename', '$realfilename', '{$uploadfile['size']}', '{$_USER['uid']}', 1, UNIX_TIMESTAMP())";
                    DB_query($sql);
                    $newid = DB_insertID();
                    DB_query("INSERT INTO {$_TABLES['gf_attachments']} (topic_id,repository_id,filename,tempfile)
                        VALUES ('$id',$newid,'$filename',$temp)");
                    $description = ppPrepareForDB($_POST['filemgmt_desc']);
                    DB_query("INSERT INTO {$_FM_TABLES['filemgmt_filedesc']} (lid, description) VALUES ($newid, '$description')");
                }

            } else {
                DB_query("INSERT INTO {$_TABLES['gf_attachments']} (topic_id,filename,tempfile)
                    VALUES ('$id','$filename',$temp)");
            }

        } else {
            COM_errorlog("upload error:" . $GLOBALS['gf_errmsg']);
            $errmsg = $GLOBALS['gf_errmsg'];
        }
    }

    if (!$tempfile AND $_POST['uniqueid'] > 0 AND DB_COUNT($_TABLES['gf_topic'],'id',$id)) {
        DB_query("UPDATE {$_TABLES['gf_attachments']} SET topic_id=$id, tempfile=0 WHERE topic_id={$_POST['uniqueid']}");
    }

    return $filelinks;

}


function gf_uploadfile($filename,&$upload_file,$allowablefiletypes,$filestore_path) {
    global $_FILES,$_CONF,$_TABLES,$CONF_FORUM,$LANG_GF00;

    include_once($_CONF['path_system'] . 'classes/upload.class.php');
    $upload = new upload();
    $upload->setPath($filestore_path);
    $upload->setLogging(true);
    $upload->setAutomaticResize(false);
    $upload->setAllowedMimeTypes($allowablefiletypes);
    // Set max dimensions as well in case user is uploading a full size image
    $upload->setMaxDimensions ($CONF_FORUM['max_uploadimage_width'], $CONF_FORUM['max_uploadimage_height']);
    $upload->setMaxFileSize($CONF_FORUM['max_uploadfile_size']);

    if (strlen($upload_file['name']) > 0) {
        $upload->setFileNames($filename);
        $upload->setPerms( $CONF_FORUM['fileperms'] );

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
            $errmsg = "Forum Upload Attachment Error:" . $upload->printErrors(false);
            COM_errorlog($errmsg);
            $GLOBALS['gf_errmsg'] = $LANG_GF00['uploaderr'] .':<BR>' . $upload->printErrors(false);
            return false;
        }
        return true;

    } else {
        return false;
    }

    return false;

}

?>