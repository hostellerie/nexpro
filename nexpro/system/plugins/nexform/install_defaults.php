<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.2.0 for the nexPro Portal Server                        |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Blaine Lang            - Blaine DOT Lang AT nextide DOT ca                |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

global $CONF_FE_DEFAULT;
$CONF_FE_DEFAULT = array();

$CONF_FE_DEFAULT['debug'] = false;         // Set to true to enable extra logging to error.log in form rendering and posting functions

$CONF_FE_DEFAULT['post_url']        = $_CONF['site_url'] .'/nxfrm';
$CONF_FE_DEFAULT['public_url']      = $_CONF['site_url'] .'/nxfrm';
$CONF_FE_DEFAULT['uploadpath']      = $_CONF['path_html'] . 'nxfrm/data/';
$CONF_FE_DEFAULT['downloadURL']     = $_CONF['site_url'] . '/nxfrm/data';
$CONF_FE_DEFAULT['image_url']       = $_CONF['layout_url'] . '/nexform/images';
$CONF_FE_DEFAULT['export_dir']      = $_CONF['path_html'] . 'admin/plugins/nexform/export/';

/* When editing a template - these variables are required  and checked that they are not removed */
$CONF_FE_DEFAULT['mandatory_template_vars'] = array('form_id','form_action','form_handler');

/* When adding new fields - should the mandatory Option be set by default */
$CONF_FE_DEFAULT['field_mandatory_default'] = false;

/* Set any fixed field options you want to have applied and used for all forms */
$CONF_FE_DEFAULT['defaultattributes'] = array (
    'text'       =>  array('size=60' => 1),
    'file'       =>  array('size=40' => 1),
    'textarea1'  =>  array('rows=10 cols=50' => 1)
);

/* Define custom template files to be used to render the form - in the {theme_dir}/nexform directory */
$CONF_FE_DEFAULT['templates'] = array (
    'default'   => array('defaultform.thtml' => 1),
    'minimal'   => array('minimalform.thtml' => 1)
);

$CONF_FE_DEFAULT['fieldstyles'] = array (
    1     => array('Default' => 1, 'frm_label1' => 1),
    2     => array('Bold' => 1, 'frm_label2' => 1),
    3     => array('Normal' => 1, 'frm_label3' => 1),
    4     => array('Highlighted' => 1, 'frm_label3' => 1),
    5     => array('Large Heading' => 1, 'frm_label3' => 1),
    6     => array('Medium Heading1' => 1, 'frm_label3' => 1),
    7     => array('Small Heading' => 1, 'frm_label3' => 1),
    8     => array('Boxed Look' => 1, 'frm_label3' => 1),
);


/* Default field formatting options. Just enter the integer value - spacing in pixel */
$CONF_FE_DEFAULT['field_defaultspacing'] = '0';         // values like: 40, 50 will be converted to a style="width:xx%;"
$CONF_FE_DEFAULT['field_defaultrightpadding'] = '5';    // Default right padding to use for the form label/field pair */
$CONF_FE_DEFAULT['field_defaultlabelpadding'] = '5';    // Default padding between the label and field */

/* Set Limit of the number of summary fields to show in result listing tool */
$CONF_FE_DEFAULT['result_summary_fields'] = 5;

/* FCK Editor Toolbar to use in TextArea Field Settings
 * Load Javascrript for for Toolbar to work - set to true if for some reason GL or no other plugin is loading
*/
$CONF_FE_DEFAULT['load_editor']  = false;
$CONF_FE_DEFAULT['fckeditor_toolbar'] = 'editor-toolbar1';    // Toolbar to use in the forms with textarea2 fields


$CONF_FE_DEFAULT['max_uploadimage_width']    = '2100';
$CONF_FE_DEFAULT['max_uploadimage_height']   = '1600';
$CONF_FE_DEFAULT['max_uploadfile_size']      = '6553600';     // 6.400 MB



/**
* Initialize plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $CONF_FE if available (e.g. from
* an old config.php), uses $CONF_FE_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_nexform()
{
    global $CONF_FE, $CONF_FE_DEFAULT;
    @include_once ($_CONF['path'] . 'plugins/nexform/config.php');

    if (is_array($CONF_FE) && (count($CONF_FE) > 1)) {
        $CONF_FE_DEFAULT = array_merge($CONF_FE_DEFAULT, $CONF_FE);
    }

    $c = config::get_instance();
    if (!$c->group_exists('nexform')) {

        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'nexform');
        $c->add('fs_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'nexform');
        $c->add('fs_layout', NULL, 'fieldset', 0, 1, NULL, 0, true, 'nexform');
        $c->add('fs_attachments', NULL, 'fieldset', 0, 2, NULL, 0, true, 'nexform');

        $c->add('debug', $CONF_FE_DEFAULT['debug'],
                'select', 0, 0, 0, 5, true, 'nexform');
        $c->add('post_url', $CONF_FE_DEFAULT['post_url'],
                'text', 0, 0, NULL, 10, true, 'nexform');
        $c->add('public_url', $CONF_FE_DEFAULT['public_url'],
                'text', 0, 0, NULL, 20, true, 'nexform');
        $c->add('image_url', $CONF_FE_DEFAULT['image_url'],
                'text', 0, 0, NULL, 30, true, 'nexform');
        $c->add('export_dir', $CONF_FE_DEFAULT['export_dir'],
                'text', 0, 0, NULL, 40, true, 'nexform');
        $c->add('load_editor',$CONF_FE_DEFAULT['load_editor'],
                'select',0,0,0,50,true,'nexform');
        $c->add('fckeditor_toolbar', $CONF_FE_DEFAULT['fckeditor_toolbar'],
                'text', 0, 0, NULL, 60, true, 'nexform');
        $c->add('result_summary_fields', $CONF_FE_DEFAULT['result_summary_fields'],
                'text', 0, 0, NULL, 70, true, 'nexform');

        $c->add('field_mandatory_default',$CONF_FE_DEFAULT['field_mandatory_default'],
                'select',0,1,0,100,true,'nexform');
        $c->add('defaultattributes',$CONF_FE_DEFAULT['defaultattributes'],'**placeholder',0,1,NULL,110,TRUE,'nexform');

        $c->add('fieldstyles',$CONF_FE_DEFAULT['fieldstyles'],'**placeholder',0,1,NULL,120,TRUE,'nexform');
        $c->add('templates',$CONF_FE_DEFAULT['templates'],'**placeholder',0,1,NULL,130,TRUE,'nexform');

        $c->add('field_defaultspacing', $CONF_FE_DEFAULT['field_defaultspacing'],
                'text', 0, 1, NULL, 140, true, 'nexform');
        $c->add('field_defaultrightpadding', $CONF_FE_DEFAULT['field_defaultrightpadding'],
                'text', 0, 1, NULL, 150, true, 'nexform');
        $c->add('field_defaultlabelpadding', $CONF_FE_DEFAULT['field_defaultlabelpadding'],
                'text', 0, 1, NULL, 160, true, 'nexform');


        $c->add('uploadpath', $CONF_FE_DEFAULT['uploadpath'],
                'text', 0, 2, NULL, 200, true, 'nexform');
        $c->add('downloadURL', $CONF_FE_DEFAULT['downloadURL'],
                'text', 0, 2, NULL, 210, true, 'nexform');
        $c->add('max_uploadimage_width', $CONF_FE_DEFAULT['max_uploadimage_width'],
                'text', 0, 2, NULL, 220, true, 'nexform');
        $c->add('max_uploadimage_height', $CONF_FE_DEFAULT['max_uploadimage_height'],
                'text', 0, 2, NULL, 230, true, 'nexform');
        $c->add('max_uploadfile_size', $CONF_FE_DEFAULT['max_uploadfile_size'],
                'text', 0, 2, NULL, 240, true, 'nexform');

        $c->add('allowablefiletypes',array(
            'application/x-gzip-compressed'     => array('.tar.gz' => 1,'.tgz' => 1),
            'application/x-zip-compressed'      => array('.zip' => 1),
            'application/x-tar'                 => array('.tar' => 1),
            'text/plain'                        => array('.php' => 1,'.txt' => 1),
            'text/html'                         => array('.html' => 1,'.htm' => 1),
            'image/bmp'                         => array('.bmp' => 1,'.ico' => 1),
            'image/gif'                         => array('.gif' => 1),
            'image/png'                         => array('.png' => 1),
            'image/pjpeg'                       => array('.jpg' => 1,'.jpeg' => 1),
            'image/jpeg'                        => array('.jpg' => 1,'.jpeg' => 1),
            'audio/mpeg'                        => array('.mp3' => 1),
            'audio/wav'                         => array('.wav' => 1),
            'application/pdf'                   => array('.pdf' => 1),
            'application/x-shockwave-flash'     => array('.swf' => 1),
            'application/msword'                => array('.doc' => 1),
            'application/vnd.ms-msexcel'        => array('.xls' => 1),
            'application/vnd.ms-powerpoint'     => array('.ppt' => 1),
            'application/vnd.ms-project'        => array('.mpp' => 1),
            'application/vnd.vision'            => array('.vsd' => 1),
            'application/octet-stream'          => array('.vsd' => 1,'.fla' => 1, '.psd' => 1, '.pdf' => 1, '.jpg' => 1, '.png' => 1, '.doc' => 1, '.xls' => 1)),
            '**placeholder',0,2,NULL,250,TRUE,'nexform');


    }

    return true;
}



?>