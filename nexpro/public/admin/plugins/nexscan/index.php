<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexScan Plugin v1.0.0 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Eric de la Chevrotiere - Eric DOT delaChevrotiere AT nextide DOT ca       |
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

require_once ('../../../lib-common.php');

if (!SEC_inGroup('Root')) {
    echo COM_siteHeader();
    echo COM_startBlock($LANG_NEXPRO['access_denied']);
    echo "You must be in the admin group to run this tool!";
    echo COM_endBlock();
    echo COM_siteFooter();
    exit;
}

//
// Function Definitions
//////////////////////////////////////////////////

function main_display() {
    global $_CONF, $_TABLES, $CONF_NS, $LANG_NS02, $_USER;
    $retval = '';

    $retval .= COM_startBlock($LANG_NS02['css_scanner']);

    $p = new Template($_CONF['path'] . 'plugins/nexscan/templates');
    $p->set_file('css_scanner', 'css_scanner.thtml');

    NXCOM_set_common_vars($p);
    NXCOM_set_language_vars($p, $LANG_NS02);

    //housekeeping
    DB_query("DELETE FROM {$_TABLES['nxscan_cssscan']}");
    DB_query("DELETE FROM {$_TABLES['nxscan_options']} WHERE name=''");

    $options = array();
    $file_types = (is_array($_REQUEST['file_types'])) ? $_REQUEST['file_types']:array ('.php', '.inc', '.thtml');
    $scan_dirs = ($_REQUEST['scan_dirs'] != '') ? $_REQUEST['scan_dirs']:$_CONF['path'] . "\r\n" . $_CONF['path_html'];
    $orphans_only = ($_REQUEST['orphans_only'] != '') ? $_REQUEST['orphans_only']:0;
    $css_file_arr = (is_array($_REQUEST['css_files'])) ? $_REQUEST['css_files']:array();

    $css_files = get_all_css_files();
    $css_files_input = '<br><h3>' . $LANG_NS02['layout_css'] . '</h3>';
    $label_flag = false;
    $i = 0;
    foreach ($css_files as $css_file) {
        if ($label_flag == false && (strpos($css_file, $_CONF['path_html'] . 'layout') === false)) {
            $label_flag = true;
            $css_files_input .= '<br><h3>' . $LANG_NS02['other_css'] . '</h3>';
        }
        $css_file = str_replace('\\', '/', $css_file);
        $css_files_input .= "<input type=\"checkbox\" id=\"css_files$i\" name=\"css_files[]\" value=\"$css_file\" checked=\"checked\">$css_file<br>";
        $i++;
    }

    $p->set_var('css_files', $css_files_input);
    $p->set_var('scan_dirs', $scan_dirs);
    $p->set_var('orphans_checked', ($orphans_only == 1) ? 'checked="checked"':'');

    $type_checkboxes = '';
    foreach ($CONF_NS['types_to_scan'] as $type) {
        if (in_array($type, $file_types)) {
            $type_checkboxes .= "<input type=\"checkbox\" name=\"file_types[]\" value=\"$type\" checked=\"checked\">$type&nbsp;&nbsp;&nbsp;";
        }
        else {
            $type_checkboxes .= "<input type=\"checkbox\" name=\"file_types[]\" value=\"$type\">$type&nbsp;&nbsp;&nbsp;";
        }
    }
    $p->set_var('file_types', $type_checkboxes);

    $res = DB_query("SELECT scan_id, name FROM {$_TABLES['nxscan_options']} WHERE user_id={$_USER['uid']}");
    $prev_scans = '';
    $i = 0;
    while ($A = DB_fetchArray($res)) {
        $row = ($i++ % 2) + 1;
        $prev_scans .= "<tr class=\"pluginRow$row\"><td width=\"1\"><a href=\"{$_CONF['site_admin_url']}/plugins/nexscan/index.php?op=delete&sid={$A['scan_id']}\"><img src=\"{$_CONF['layout_url']}/images/deleteitem.png\"></a></td><td><a href=\"{$_CONF['site_admin_url']}/plugins/nexscan/index.php?op=scan&sid={$A['scan_id']}\">{$A['name']}</a></td></tr>";
    }
    if ($i > 0) {
        $p->set_var('previous_scans', $prev_scans);
    }
    else {
        $p->set_var('previous_scans', "<tr><td>{$LANG_NS02['no_previous_scans']}</td></tr>");
    }


    $p->parse('output', 'css_scanner');
    $retval .= $p->finish($p->get_var('output'));

    $retval .= COM_endBlock();

    return $retval;
}

function get_all_css_files() {
    global $_CONF;

    $css_files = array();
    $themes = @scandir($_CONF['path_html'] . 'layout');
    foreach ($themes as $theme) {
        if ($theme != '..' && $theme != '.') {
            $dir = $_CONF['path_html'] . 'layout/' . $theme;
            $css_files = recursive_get_all_css_files($dir, $css_files);
        }
    }
    $css_files = recursive_get_all_css_files($_CONF['path_html'], $css_files);

    return $css_files;
}

function recursive_get_all_css_files(&$dir, &$css_files) {
    $files = @scandir($dir);
    if (is_array($files)) {
        foreach ($files as $file) {
            $fullname = $dir . '/' . $file;
            $fullname = str_replace('//', '/', $fullname);

            if ($file != '..' && $file != '.') {
                if (is_dir($fullname)) {
                    $css_files = recursive_get_all_css_files($fullname, $css_files);
                }
                else {
                    //make sure file is the right type
                    $pos = strrpos($file, '.');
                    if (stripos($file, '.css', $pos)) {
                        $css_files[$fullname] = $fullname;
                    }
                }
            }
        }
    }
    return $css_files;
}

function scan_css($sid=0) {
    global $_CONF, $_TABLES, $LANG_NS02, $_USER;

    $retval = '';
    $retval .= COM_startBlock($LANG_NS02['css_scanner']);

    if ($sid == 0) {
        $css_files_to_scan = $_REQUEST['css_files'];
        $file_types = $_REQUEST['file_types'];
        $scan_dir_text = str_replace('\\', '/', $_REQUEST['scan_dirs']);
        $orphans_only = ($_REQUEST['orphans_only'] != '') ? $_REQUEST['orphans_only']:0;
        $fuzzy_filter = ($_REQUEST['fuzzy_filter'] != '') ? $_REQUEST['fuzzy_filter']:0;
        $name = $_REQUEST['scan_name'];

        if (!is_array($css_files_to_scan) || !is_array($file_types) || $scan_dir_text == '') {
            $retval .= COM_startBlock($LANG_NS02['error']);
            $retval .= $LANG_NS02['missing_parameters'];
            $retval .= COM_endBlock();
            return $retval;
        }

        $scan_dirs = explode("\r\n", $scan_dir_text);

        //now scan each directory
        foreach ($file_types as $key=>$type) {
            $file_types[$key] = $type;
        }

        $scan_id = DB_getItem($_TABLES['nxscan_options'], 'scan_id', "1=1 ORDER BY scan_id DESC LIMIT 1") + 1;
        $file_filter = addslashes(serialize($file_types));
        $directories_to_scan = addslashes(serialize($scan_dirs));
        $css_files_to_scan = addslashes(serialize($css_files_to_scan));
        $name = NXCOM_filterText($name);
        DB_query("INSERT INTO {$_TABLES['nxscan_options']} (scan_id, user_id, name, css_files_to_scan,
        directories_to_scan, file_filter, only_show_unused, fuzzy_filter) VALUES
        ($scan_id, {$_USER['uid']}, '$name', '$css_files_to_scan', '$directories_to_scan', '$file_filter',
        $orphans_only, $fuzzy_filter)");
    }
    else {
        $scan_id = $sid;
        DB_query("DELETE FROM {$_TABLES['nxscan_cssscan']} WHERE scan_id=$scan_id");
    }

    //now generate the report
    $p = new Template($_CONF['path'] . 'plugins/nexscan/templates');
    $p->set_file('css_scanner_report', 'css_scanner_report.thtml');

    NXCOM_set_common_vars($p);
    NXCOM_set_language_vars($p, $LANG_NS02);

    $p->set_var('scan_id', $scan_id);

    $p->parse('output', 'css_scanner_report');
    $retval .= $p->finish($p->get_var('output'));
    $retval .= COM_endBlock();
    return $retval;
}

function scan_file($scan_id, $scan_num) {
    global $_CONF, $_TABLES;

    $scan_files = DB_getItem($_TABLES['nxscan_options'], 'css_files_to_scan', "scan_id=$scan_id");
    $scan_files = unserialize($scan_files);

    if (count($scan_files) <= $scan_num) {
        $value = -1;
    }
    else {
        $value = htmlentities(scan_css_file($scan_id, $scan_files[$scan_num], $scan_num));
    }

    $retval = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
    $retval .= "<result>\n";
    $retval .= "<retval>$value</retval>\n";
    $retval .= "</result>\n";

    return $retval;
}

function prepare_css($buffer) {
    $buffer = str_replace("\r", '', $buffer);
    $buffer = str_replace("\n", '', $buffer);
    $buffer = str_replace(';', '', $buffer);
    
    $newcss = 'hello';  //hello will be replaced, just needs to be there so php recognizes as a string
                        //instead of an array, due to the $newcss[$cur++] method which is much faster than .=
    $len = strlen($buffer);
    $in_comment = false;
    $in_definition = false;
    for ($i = 0, $cur = 0; $i < $len; $i++) {
        if ($buffer[$i] == '/' && $buffer[$i+1] == '*') {
            $in_comment = true;
        }
        else if ($buffer[$i] == '/' && $buffer[$i-1] == '*') {
            $in_comment = false;
        }
        else if ($buffer[$i] == '{' && $in_comment == false) {
            $newcss[$cur++] = '{';
            $in_definition = true;
        }
        else if ($buffer[$i] == '}' && $in_comment == false) {
            $newcss[$cur++] = '}';
            $in_definition = false;
        }
        else if ($in_comment == false && $in_definition == false) {
            $newcss[$cur++] = $buffer[$i];
        }
    }
    
    return $newcss;
}

function scan_css_file($scan_id, $css_file, $scan_num) {
    global $_CONF, $_TABLES, $CONF_NS, $LANG_NS02, $_USER;
    $retval = '';

    //first parse the css file and build array of definitions
    $css_classes = array();
    $css_ids = array();

    $res = DB_query("SELECT * FROM {$_TABLES['nxscan_options']} WHERE scan_id=$scan_id");
    $options = DB_fetchArray($res);

    $scan_dirs = unserialize($options['directories_to_scan']);
    $file_types = unserialize($options['file_filter']);
    $orphans_only = $options['only_show_unused'];
    $fuzzy_filter = ($options['fuzzy_filter'] == 1) ? 1:0;

    $fp = fopen ($css_file, 'r');
    if ($fp !== NULL) {
        $length = filesize($css_file);
        if ($length > 0) {
            $buffer = fread($fp, $length);
        }
        fclose ($fp);
    }
    else {
        $retval .= COM_startBlock($LANG_NS02['error']);
        $retval .= $LANG_NS02['could_not_open_css_file'];
        $retval .= COM_endBlock();
        return $retval;
    }

    $buffer = prepare_css($buffer);    
    
    $definitions = explode('{}', $buffer);

    foreach ($definitions as $definition) {
        $definition = trim($definition);
        $subdefs = explode(',', $definition);

        foreach ($subdefs as $subdef) {
            $subdef = trim($subdef);
            if (($pos = strpos($subdef, ':')) !== false) {
                $subdef = substr($subdef, 0, $pos);
            }
            if (($pos = strpos($subdef, '[')) !== false) {
                $subdef = substr($subdef, 0, $pos);
            }

            //now find the class or id in the subdef
            if (($pos = strpos($subdef, '.')) !== false) {   //this is a class definition
                $def = substr($subdef, $pos + 1);   //+ 1 to lop off the .
                $defarr = explode(' ', $def);
                $def = $defarr[0];
                if ($fuzzy_filter == 0) {
                    $css_classes[$def] = array($def, $definition); //short def, full def
                }
                else {
                    if (!in_array(array($def, $definition), $css_classes)) {
                        $css_classes[] = array($def, $definition); //short def, full def
                    }
                }
            }
            else if (($pos = strpos($subdef, '#')) !== false) {   //this is a id definition
                $def = substr($subdef, $pos + 1);   //+ 1 to lop off the #
                $defarr = explode(' ', $def);
                $def = $defarr[0];
                if ($fuzzy_filter == 0) {
                    $css_ids[$def] = array ($def, $definition); //short def, full def
                }
                else {
                    if (!in_array(array($def, $definition), $css_ids)) {
                        $css_ids[] = array ($def, $definition); //short def, full def
                    }
                }
            }
            //else it is a semantic definition, and will always be used.
        }
    }
    if ((count($css_classes) + count($css_ids)) != 0) {
        foreach ($scan_dirs as $scan_dir) {
            $scan_dir = trim($scan_dir);
            if ($scan_dir != '') {
                $retval .= recursive_css_scandir($scan_dir, $css_classes, $css_ids, $file_types, $scan_id, $css_file);
            }
        }
    }

    //now generate the report
    $p = new Template($_CONF['path'] . 'plugins/nexscan/templates');
    $p->set_file('css_scanner_report_shell', 'css_scanner_report_shell.thtml');
    $p->set_file('css_scanner_report_class', 'css_scanner_report_class.thtml');
    $p->set_file('css_scanner_report_row', 'css_scanner_report_row.thtml');

    NXCOM_set_common_vars($p);
    NXCOM_set_language_vars($p, $LANG_NS02);

    $p->set_var('stylesheet_name', $css_file);
    $p->set_var('scan_num', $scan_num);
    $p->set_var('stylesheet_row', ($scan_num % 2) + 1);

    //filter out the duplicate classnames depentant on search type
    $css_classes_filtered = array();
    $css_ids_filtered = array();
    foreach ($css_classes as $entry) {
        $css_classes_filtered[$entry[$fuzzy_filter]] = $entry[$fuzzy_filter];
    }
    foreach ($css_ids as $entry) {
        $css_ids_filtered[$entry[$fuzzy_filter]] = $entry[$fuzzy_filter];
    }

    foreach ($css_classes_filtered as $class) {
        $p->set_var('classname', $class);
        $p->set_var('css_scanner_report_row_output', '');
        $field = ($fuzzy_filter == 1) ? 'fullname':'classname';
        $res = DB_query("SELECT found_in_file, line_number FROM {$_TABLES['nxscan_cssscan']} WHERE $field LIKE '{$class}' AND scan_id=$scan_id ORDER BY found_in_file ASC, line_number ASC");
        $i = 0;
        while (list ($found_in_file, $line_number) = DB_fetchArray($res)) {
            $p->set_var('row', ($i % 2) + 1);
            $p->set_var('found_in_file', $found_in_file);
            $p->set_var('line_number', $line_number);
            $p->set_var('filename', urlencode($found_in_file));

            $p->parse('css_scanner_report_row_output', 'css_scanner_report_row', true);
            $i++;
        }

        if (($orphans_only == 1 && $i == 0) || ($orphans_only != 1)) {
            $p->set_var('total_found', $i);
            $p->set_var('results', $p->get_var('css_scanner_report_row_output'));
            $p->set_var('bgcolor', ($i == 0) ? ' background-color: #F3B498;':' background-color: #FEF1B4;');
            $p->parse('css_scanner_report_class_output', 'css_scanner_report_class', true);
        }
    }
    $p->set_var('class_reports', $p->get_var('css_scanner_report_class_output'));

    $p->set_var('css_scanner_report_class_output', '');
    foreach ($css_ids_filtered as $class) {
        $p->set_var('classname', $class);
        $p->set_var('css_scanner_report_row_output', '');
        $field = ($fuzzy_filter == 1) ? 'fullname':'classname';
        $res = DB_query("SELECT found_in_file, line_number FROM {$_TABLES['nxscan_cssscan']} WHERE $field LIKE '{$class}' AND scan_id=$scan_id ORDER BY found_in_file ASC, line_number ASC");
        $i = 0;
        while (list ($found_in_file, $line_number) = DB_fetchArray($res)) {
            $p->set_var('row', ($i % 2) + 1);
            $p->set_var('found_in_file', $found_in_file);
            $p->set_var('line_number', $line_number);
            $p->set_var('filename', urlencode($found_in_file));

            $p->parse('css_scanner_report_row_output', 'css_scanner_report_row', true);
            $i++;
        }

        if (($orphans_only == 1 && $i == 0) || ($orphans_only != 1)) {
            $p->set_var('total_found', $i);
            $p->set_var('results', $p->get_var('css_scanner_report_row_output'));
            $p->set_var('bgcolor', ($i == 0) ? ' background-color: #F3B498;':' background-color: #FEF1B4;');

            $p->parse('css_scanner_report_class_output', 'css_scanner_report_class', true);
        }
    }
    $p->set_var('id_reports', $p->get_var('css_scanner_report_class_output'));

    $p->parse('output', 'css_scanner_report_shell');
    $retval = $p->finish($p->get_var('output'));

    return $retval;
}

function recursive_css_scandir($dir, &$css_classes, &$css_ids, &$file_types, $scan_id, $css_file) {
    $retval = '';
    $files = @scandir($dir);
    if (is_array($files)) {
        foreach ($files as $file) {
            $fullname = $dir . '/' . $file;
            $fullname = str_replace('//', '/', $fullname);

            if ($file != '..' && $file != '.') {
                if (is_dir($fullname)) {
                    $retval .= recursive_css_scandir($fullname, $css_classes, $css_ids, $file_types, $scan_id, $css_file);
                }
                else {
                    //make sure file is the right type
                    foreach ($file_types as $type) {
                        $pos = strrpos($file, '.');
                        if (stripos($file, $type, $pos)) {
                            $retval .= scan_for_css_class($fullname, $css_classes, $css_ids, $scan_id, $css_file);
                            break;
                        }
                    }
                }
            }
        }
    }
    return $retval;
}

function scan_for_css_class($file, &$css_classes, &$css_ids, $scan_id, $css_file) {
    global $_TABLES, $CONF_NS, $_USER;
    $fp = fopen($file, 'r');

    if ($fp !== NULL) {
        $i = 1;
        while (!feof($fp)) {
            $line = fgets($fp);

            $pos = 0;
            if ( (($pos = stripos($line, 'class=', $pos)) !== false) || (($pos = stripos($line, 'class =', $pos)) !== false) ) {
                foreach ($css_classes as $class) {
                    if (($pos = stripos($line, $class[0], $pos)) !== false) {
                        $len = strlen($class[0]);
                        $chrstr = substr($line, $pos - 1, 1);
                        $chrend = substr($line, $pos + $len, 1);
                        if (strpos($CONF_NS['valid_wrap_chars'], $chrstr) && strpos($CONF_NS['valid_wrap_chars'], $chrend)) {
                            DB_query("INSERT INTO {$_TABLES['nxscan_cssscan']} (scan_id, user_id, css_file, classname, fullname, found_in_file, line_number, type) VALUES ($scan_id, {$_USER['uid']}, '$css_file', '{$class[0]}', '{$class[1]}', '$file', $i, 0)");
                        }
                    }
                }
            }

            $pos = 0;
            if ( (($pos = stripos($line, 'id=', $pos)) !== false) || (($pos = stripos($line, 'id =', $pos)) !== false) ) {
                foreach ($css_ids as $id) {
                    if (($pos = stripos($line, $id[0], $pos)) !== false) {
                        $len = strlen($id[0]);
                        $chr = substr($line, $pos + $len, 1);
                        if (strpos($CONF_NS['valid_wrap_chars'], $chr)) {
                            DB_query("INSERT INTO {$_TABLES['nxscan_cssscan']} (scan_id, user_id, css_file, classname, fullname, found_in_file, line_number, type) VALUES ($scan_id, {$_USER['uid']}, '$css_file', '{$id[0]}', '{$id[1]}', '$file', $i, 1)");
                        }
                    }
                }
            }

            $i++;
        }
    }
}

function preview_file($filename, $class) {
    global $_CONF, $LANG_NS02;

    $fp = fopen($filename, 'r');
    if ($fp != '') {
        $display  = "<html><head><link rel=\"stylesheet\" type=\"text/css\" href=\"{$_CONF['site_admin_url']}/plugins/nexscan/css_scan.css\" title=\"css_scan\">";
        $display .= "<title>File Details</title></head><body><table class=\"file_detail\">";
        $display .= "<tr><td colspan=\"2\" class=\"file_header\">$filename<br>$class</td></tr>";
        $i = 1;
        while (!feof($fp)) {
            $line = fgets($fp);
            $line = htmlspecialchars($line);
            $line = str_ireplace(' ', '&nbsp;', $line);
            $line = str_ireplace($class, "<span class=\"highlight\">$class</span>", $line);
            $display .= "<tr><td class=\"line_number\">$i</td>";
            $style = ($i == 1) ? " style=\"  border-top: 1px solid #81A9E2;\"":'';
            $display .= "<td class=\"line_detail\"$style>$line</td></tr>";

            $i++;
        }
        $display .= "</table></body></html>";
    }
    else {
        $display  = COM_startBlock($LANG_NS02['error']);
        $display .= $LANG_NS02['invalid_file'] . ': ' . $filename;
        $display .= COM_endBlock();
    }

    return $display;
}

//
// Main Code
//////////////////////////////////////////////////

$retval = '';
$op = COM_applyFilter($_REQUEST['op']);
$showheader = true;

switch ($op) {
case 'scan':
    echo COM_siteHeader('none');
    $scan_id = intval ($_REQUEST['sid']);
    echo scan_css($scan_id);
    echo COM_siteFooter();
    break;

case 'scanfile':
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("content-type: application/xml");

    $scan_id  = intval ($_REQUEST['scan_id']);
    $scan_num = intval ($_REQUEST['scan_num']);
    echo scan_file($scan_id, $scan_num);
    $retval = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
    $retval .= "<result>\n";
    $retval .= "<retval>hello world</retval>\n";
    $retval .= "</result>\n";
	//echo $retval;
    exit;

case 'preview':
    $file = $_REQUEST['filename'];
    $class = $_REQUEST['class'];
    echo preview_file($file, $class);
    break;

case 'delete':
    $scan_id = intval($_REQUEST['sid']);
    DB_query("DELETE FROM {$_TABLES['nxscan_options']} WHERE scan_id=$scan_id AND user_id={$_USER['uid']}");
    DB_query("DELETE FROM {$_TABLES['nxscan_cssscan']} WHERE scan_id=$scan_id AND user_id={$_USER['uid']}");

    echo COM_siteHeader('none');
    echo main_display();
    echo COM_siteFooter();
    break;

default:
    echo COM_siteHeader('none');
    echo main_display();
    echo COM_siteFooter();
    break;
}

?>
