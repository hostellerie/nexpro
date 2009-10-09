<?php
    require_once ('../lib-common.php');

    $id = COM_applyFilter($_GET['id'], true);
    $sql = "SELECT filename,repository_id FROM {$_TABLES['gf_attachments']} WHERE id=$id;";
    $res = DB_query($sql);
    $A = DB_fetchArray($res);

    if ($A === FALSE) {
        echo "Error: Cannot Display Selected File";
        COM_errorLog("Error: Cannot Display Selected File");
        exit;
    }

    if ($A['repository_id'] > 0) {
        if ($CONF_FORUM['FM_PLUGIN'] == 'filemgmt') {
            echo COM_refresh("{$_CONF['site_url']}/filemgmt/visit.php?lid={$A['repository_id']}");
        } elseif ($CONF_FORUM['FM_PLUGIN'] == 'nexfile') {
            echo COM_refresh("{$_CONF['site_url']}/nexfile/download.php?op=download&fid={$A['repository_id']}");
        } else {
            COM_errorLog("Forum Error: Invalid request to download file");
        }
        exit;
    }

    $filedata = explode(':', $A['filename']);
    $filename = $filedata[0];
    $realname = $filedata[1];
    $filepath = "{$CONF_FORUM['uploadpath']}/$filename";

    if ($fd = fopen ($filepath, "rb")) {
        header("Content-type: application/octet-stream");
        header("Content-Disposition: inline; filename=\"{$realname}\"");
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        fpassthru($fd);
        fclose ($fd);
    } else {
        echo "Error: Cannot Display Selected File, $realname";
        COM_errorLog("Error: Cannot Display Selected File, $realname");
    }
?>