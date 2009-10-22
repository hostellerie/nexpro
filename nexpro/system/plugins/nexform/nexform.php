<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.2 for the nexPro Portal Server                          |
// | Sept 14, 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexform.php                                                               |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2008 by the following authors:                         |
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


// Plugin information
$CONF_FE=array();
$CONF_FE['pi_display_name'] = 'nexform';
$CONF_FE['pi_name']         = 'nexform';
$CONF_FE['gl_version']      = '1.6.1';
$CONF_FE['version']         = '2.2.0';
$CONF_FE['pi_url']          = 'http://www.nextide.ca/';


define('FE_CHMOD_FILES', '0755');  // Needs to be a string for the upload class use.


// Custom Field type - mapping to template names to be used
//   custX          type of field, where X is an incremental number
//      form        form template
//      record      record template
//      print       print templates to use for the main container and records
//      javascript  javascript template

$CONF_FE['customfieldmap'] = array (
    'cust1' => array (
        'form'       => 'mycustomform_detail.thtml',
        'record'     => 'mycustomform_record.thtml',
        'print'      => 'mycustomform_print.thtml,mycustomform_print_record.thtml',
        'javascript' => 'mycustomform_js.thtml'
        )
);


/* Do not change anything below this line  */

$CONF_FE['fieldtypes'] = array (
    'text'       =>  array('txt_frm','Text'),
    'mtxt'       =>  array('mtxt_frm','Multiple Text Field'),
    'date1'      =>  array('da1_ftm','Date'),
    'date2'      =>  array('da2_frm','Date with popup Calendar'),
    'datetime'   =>  array('time_frm','Date/Time with popup Calendar'),
    'passwd'     =>  array('pwd_frm','Password'),
    'select'     =>  array('sel_frm','Select dropdown list'),
    'checkbox'   =>  array('chk_frm','Checkbox'),
    'multicheck' =>  array('mchk_frm','Multiple Checkboxes'),
    'textarea1'  =>  array('ta1_frm','Textarea'),
    'textarea2'  =>  array('ta2_frm','Textarea with Editor'),
    'radio'      =>  array('rad_frm','Radio Option'),
    'file'       =>  array('file_frm','File Upload'),
    'mfile'      =>  array('mfile_frm','Multiple File Upload'),
    'dynamic'    =>  array('dynm_frm','Dynamic Form'),
    'captcha'    =>  array('captcha_frm','Captcha'),
    'heading'    =>  array('heading','Form Heading'),
    'submit'     =>  array('sub_frm','Submit Button'),
    'cancel'     =>  array('btn_frm','Cancel Button'),
    'hidden'     =>  array('hid_frm','Hidden')
);

$CONF_FE['postmethods'] = array (
    'dbsave'            => 'Save to Database',
    'email'             => 'Email Results',
    'email+dbsave'      => 'Email and Save Results',
    'posturl'           => 'Post to URL'
);


$_TABLES['nxform_definitions']       = $_DB_table_prefix . 'nxform_definitions';
$_TABLES['nxform_fields']            = $_DB_table_prefix . 'nxform_fields';
$_TABLES['nxform_results']           = $_DB_table_prefix . 'nxform_results';
$_TABLES['nxform_resdata']           = $_DB_table_prefix . 'nxform_resdata';
$_TABLES['nxform_restext']           = $_DB_table_prefix . 'nxform_restext';
$_TABLES['nxform_results_tmp']       = $_DB_table_prefix . 'nxform_results_tmp';
$_TABLES['nxform_resdata_tmp']       = $_DB_table_prefix . 'nxform_resdata_tmp';
$_TABLES['nxform_restext_tmp']       = $_DB_table_prefix . 'nxform_restext_tmp';


if (!isset($CONF_FE['post_url'])) {
    require_once $_CONF['path_system'] . 'classes/config.class.php';
    $nexform_config = config::get_instance();
    $CONF_FE_2 = $nexform_config->get_config('nexform');
}
if(is_array($CONF_FE_2)) $CONF_FE=@array_merge($CONF_FE_2,$CONF_FE);
?>