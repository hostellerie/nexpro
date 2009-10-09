<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.2 for the nexPro Portal Server                          |
// | Sept 14, 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | config.php                                                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2009 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Eric de la Chevrotiere - Eric.delaChevrotiere@nextide.ca                  |
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


/*******************************************************************************/
/*  Do not Edit anything below this line                                       */
/******************************************************************************/

$_TABLES['formDefinitions']       = $_DB_table_prefix . 'nxform_definitions';
$_TABLES['formFields']            = $_DB_table_prefix . 'nxform_fields';
$_TABLES['formResults']           = $_DB_table_prefix . 'nxform_results';
$_TABLES['formResData']           = $_DB_table_prefix . 'nxform_resdata';
$_TABLES['formResText']           = $_DB_table_prefix . 'nxform_restext';
$_TABLES['formResultsTmp']        = $_DB_table_prefix . 'nxform_results_tmp';
$_TABLES['formResDataTmp']        = $_DB_table_prefix . 'nxform_resdata_tmp';
$_TABLES['formResTextTmp']        = $_DB_table_prefix . 'nxform_restext_tmp';


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

?>