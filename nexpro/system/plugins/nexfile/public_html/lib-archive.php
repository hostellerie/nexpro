<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexFile Plugin v3.0.2 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | lib-archive.php                                                           |
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

// this file can't be used on its own
if (strpos ($_SERVER['PHP_SELF'], 'lib-archive.php') !== false) {
    die ('This file can not be used on its own.');
}

function  nexdoc_createArchiveFromFiles($rootfolder,$fileitems) {
    global $_CONF,$_TABLES,$_FMCONF;

    $archiveDirectory = "{$_FMCONF['storage_path']}tmp/";
    $zipfilename = ppRandomFilename(6) . '.zip';

    // Check if user is in a folder or workspace
    if ($rootfolder == 0) {
        $zipfilesonly = true; // Add only the files and not the directory structure
    } else {
        $zipfilesonly = false;
    }

    if (file_exists("{$archiveDirectory}{$zipfilename}")) {
        @unlink("{$archiveDirectory}{$zipfilename}");
        //COM_errorLog("Creating archive {$archiveDirectory}{$zipfilename} - removing existing file");
    } else {
        //COM_errorLog("Creating archive {$archiveDirectory}{$zipfilename}");
    }
    $zip = new ZipArchive;
    $zipOpenResult = $zip->open("{$archiveDirectory}{$zipfilename}",ZIPARCHIVE::CREATE);
    if ( $zipOpenResult === TRUE ) {
        /* If user is inside a workspace or directory then we need to process
         * list of files from parent folder down and add any needed folders to archive
         * $fileitems will contain just file id's - checking a folder will just add files to hidden form field
        */
        $filesAdded = array();
        $fileitems = str_replace(',,',',',$fileitems);  // Remove an empty items
        $sql  = "SELECT a.cid,a.fid,a.fname,b.pid,b.name as folder FROM {$_TABLES['nxfile_files']} a ";
        $sql .= "LEFT JOIN {$_TABLES['nxfile_categories']} b on b.cid=a.cid ";
        $sql .= "WHERE fid in ($fileitems)";
        $query = DB_query($sql);
        $pfolders = array();  // Array of parent folders that I will need to create folders for in archive
        $files = array();
        while ($A = DB_fetchArray($query)) {
            // Check that user has access to this files category
            if (fm_getPermission($A['cid'],'view')) {
                if ($zipfilesonly) {
                    $sourcefile = $_FMCONF['storage_path'] . "{$A['cid']}/{$A['fname']}";
                    if (file_exists($sourcefile)) {
                        //COM_errorLog("$i: Adding file ({$A['fid']}): $sourcefile ");
                        $zip->addFile($sourcefile, $A['fname']);
                        $filesAdded[] = $A['fid'];
                    }
                } else {
                    $files[$A['fid']] = array('fname' => $A['fname'], 'cid' => $A['cid'], 'folder' => $A['folder']);
                    $pid = 0;   // Reset variable - will be set below
                    if ($A['pid'] ==  $rootfolder AND !array_key_exists($A['cid'],$pfolders)) {
                        $pid = $A['cid'];
                        $pfolders[$pid] = array();
                    } elseif ($A['cid'] != $rootfolder) {
                        if ($A['pid'] == $rootfolder) {
                            $pid = $A['cid'];
                        } else {
                            $parent = $A['pid'];
                            $lastparent = 0;
                            while ($parent != $rootfolder AND $parent != 0) {
                                $lastparent = $parent;
                                $parent = DB_getItem($_TABLES['nxfile_categories'],'pid',"cid=$parent");
                            }
                            $pid = $lastparent;
                        }
                        if ($pid > 0 AND $pid !=  $rootfolder AND !array_key_exists($pid,$pfolders)) {
                            $pfolders[$pid] = array();
                        }
                    }
                    if ($pid != 0) {
                        // Build an array of parent or top level folders and associated files
                        array_push($pfolders[$pid],$A['fid']);
                    }

                    // Add any files now to the archive that are in the Root Folder
                    if ($A['cid'] == $rootfolder) {
                        $sourcefile = $_FMCONF['storage_path'] . "{$rootfolder}/{$A['fname']}";
                        if (file_exists($sourcefile)) {
                            //COM_errorLog("$i: Adding file ({$A['fid']}): $sourcefile ");
                            $zip->addFile($sourcefile, $A['fname']);
                            $filesAdded[] = $A['fid'];
                        }
                    }
                }

            }  else {
                COM_errorLog("Archive: User does not have access to category: {$A['cid']} - File: {$A['fid']} skipped");
            }
        }

        /* If we have to create directories in the archive, then use the array organized
         * by top level folder or category id that includes all files user has access
        */
        foreach ($pfolders as $folder => $masterFileList) {
            $filesAdded = nexdoc_archiveAddFolder($zip,$folder,$masterFileList);
            $missingFiles = array_diff($masterFileList,$filesAdded);
            if (!empty($missingFiles)) {
                $filesAdded = nexdoc_archiveAddParentFromFiles($zip,$masterFileList,$filesAdded,$folder);
            }
        }
        $zip->close();
        //COM_errorLog("Completed {$archiveDirectory}{$zipfilename}, filesize: " . filesize("{$archiveDirectory}{$zipfilename}"));

        include_once($_CONF['path_system'] . 'classes/downloader.class.php');
        $download = new downloader();
        $download->setLogging(false);
        $download->_setAvailableExtensions( array('zip' => 'application/x-zip-compresseed') );
        $download->setAllowedExtensions( array('zip' => 'application/x-zip-compresseed') );
        $download->setPath($archiveDirectory);
        $download->downloadFile($zipfilename);
        if ($download->areErrors()) {
            $err = $download->printWarnings();
            $err .= "\n" . $download->printErrors();
            COM_errorLog("nexFile: Download error for user: {$_USER['uid']} - file: {$archiveDirectory}{$zipfilename}, Err => $err");
        }

    } else {
        COM_errorLog("Failed to create {$archiveDirectory}{$zipfilename}, Err => $zipOpenResult");
    }

}

function  nexdoc_createArchiveFromFolder($rootfolder) {
    global $_CONF,$_TABLES,$_FMCONF,$_USER;

    $archiveDirectory = "{$_FMCONF['storage_path']}tmp/";
    $zipfilename = ppRandomFilename(6) . '.zip';

    if (file_exists("{$archiveDirectory}{$zipfilename}")) {
        @unlink("{$archiveDirectory}{$zipfilename}");
        //COM_errorLog("Creating archive {$archiveDirectory}{$zipfilename} - removing existing file");
    } else {
        //COM_errorLog("Creating archive {$archiveDirectory}{$zipfilename}");
    }
    if (!fm_getPermission($rootfolder,'view')) {
        COM_errorLog("User: {$_USER['uid']} does not have view access to the root folder: $rootfolder");
        return '';
    }


    $zip = new ZipArchive;
    $zipOpenResult = $zip->open("{$archiveDirectory}{$zipfilename}",ZIPARCHIVE::CREATE);
    if ( $zipOpenResult === TRUE ) {
        /* If user is inside a workspace or directory then we need to process
         * list of files from parent folder down and add any needed folders to archive
         * $fileitems will contain just file id's - checking a folder will just add files to hidden form field
        */
        $filesAdded = array();
        $sql  = "SELECT a.cid,a.fid,a.fname,b.pid,b.name as folder FROM {$_TABLES['nxfile_files']} a ";
        $sql .= "LEFT JOIN {$_TABLES['nxfile_categories']} b on b.cid=a.cid ";
        $sql .= "WHERE a.cid=$rootfolder";
        $query = DB_query($sql);
        $pfolders = array();  // Array of parent folders that I will need to create folders for in archive
        $files = array();
        while ($A = DB_fetchArray($query)) {
                // Add any files now to the archive that are in the Root Folder
                $sourcefile = $_FMCONF['storage_path'] . "{$rootfolder}/{$A['fname']}";
                if (file_exists($sourcefile)) {
                    //COM_errorLog("$i: Adding file ({$A['fid']}): $sourcefile ");
                    $zip->addFile($sourcefile, $A['fname']);
                }
        }
        if (DB_count($_TABLES['nxfile_categories'],'pid',$cid)) {
            nexdoc_archiveAddParentFromFolder($zip,$rootfolder);
        }
        $zip->close();
        //COM_errorLog("Completed {$archiveDirectory}{$zipfilename}, filesize: " . filesize("{$archiveDirectory}{$zipfilename}"));

        include_once($_CONF['path_system'] . 'classes/downloader.class.php');
        $download = new downloader();
        $download->setLogging(false);
        $download->_setAvailableExtensions( array('zip' => 'application/x-zip-compresseed') );
        $download->setAllowedExtensions( array('zip' => 'application/x-zip-compresseed') );
        $download->setPath($archiveDirectory);
        $download->downloadFile($zipfilename);
        if ($download->areErrors()) {
            $err = $download->printWarnings();
            $err .= "\n" . $download->printErrors();
            COM_errorLog("nexFile: Download error for user: {$_USER['uid']} - file: {$archiveDirectory}{$zipfilename}, Err => $err");
        }

    } else {
        COM_errorLog("Failed to create {$archiveDirectory}{$zipfilename}, Err => $zipOpenResult");
    }

}




function  nexdoc_archiveAddFolder($zip,$folder,$files=false,$zipfolder='') {
    global $_CONF,$_TABLES,$_FMCONF,$_USER;

    if (!fm_getPermission($folder,'view')) {
        COM_errorLog("User: {$_USER['uid']} does not have view access to the folder: $folder");
        return '';
    }
    $filesAdded = array();
    if ($files) {
        $fileitems = implode(',',$files);
    }
    $foldername = DB_getItem($_TABLES['nxfile_categories'],'name',"cid=$folder");
    if ($zipfolder != '' ) {
        $zipfoldername = $zipfolder . $foldername . '/';
    } else {
        $zipfoldername = $foldername . '/';
    }
    $sql  = "SELECT cid,fid,fname FROM {$_TABLES['nxfile_files']} ";
    $sql .= "WHERE cid=$folder ";
    if (!empty($fileitems)) {
        $sql .= "AND fid in ($fileitems)";
    }
    $query = DB_query($sql);
    if (DB_numRows($query) > 0) {
        // COM_errorLog("Adding zip folder ($folder): $foldername");
        $zip->addEmptyDir($zipfoldername);
        while ($A = DB_fetchArray($query)) {
            $sourcefile = "{$_FMCONF['storage_path']}{$folder}/{$A['fname']}";
            if (file_exists($sourcefile)) {
                // COM_errorLog("$i: Adding file $sourcefile > $zipfoldername . $fname");
                // COM_errorLog("$i: Adding file ({$A['fid']}): {$zipfoldername}{$A['fname']}");
                $zip->addFile($sourcefile, $zipfoldername . $A['fname']);
                $filesAdded[] = $A['fid'];
            }
        }
    }
    return $filesAdded;
}


function  nexdoc_archiveAddParentFromFiles($zip,$masterFileList,$filesAdded,$pid) {
    global $_CONF,$_TABLES;

    // COM_errorLog("nexdoc_archiveAddParentFromFiles - parent folder: $pid");
    $query = DB_query("SELECT cid FROM {$_TABLES['nxfile_categories']} where pid=$pid");
    while ($A = DB_fetchArray($query)) {
        $parentFolder = DB_getItem($_TABLES['nxfile_categories'],'name',"cid=$pid") . '/';
        $added = nexdoc_archiveAddFolder($zip,$A['cid'],$masterFileList,$parentFolder);
        $filesAdded = array_merge($filesAdded,$added);
        $missingFiles = array_diff($masterFileList,$filesAdded);
        if (!empty($missingFiles)) {
            $filesAdded = nexdoc_archiveAddParentFromFiles($zip,$masterFileList,$filesAdded,$A['cid']);
        }
    }
    return $filesAdded;

}

function  nexdoc_archiveAddParentFromFolder($zip,$pid) {
    global $_CONF,$_TABLES;

    //COM_errorLog("nexdoc_archiveAddParentFromFolder - parent folder: $pid");
    $query = DB_query("SELECT cid FROM {$_TABLES['nxfile_categories']} where pid=$pid");
    while ($A = DB_fetchArray($query)) {
        $parentFolder = DB_getItem($_TABLES['nxfile_categories'],'name',"cid=$pid") . '/';
        nexdoc_archiveAddFolder($zip,$A['cid']);
        if (DB_count($_TABLES['nxfile_categories'],'pid',$A['cid'])) {
            nexdoc_archiveAddParentFromFolder($zip,$A['cid']);
        }
    }

}




?>