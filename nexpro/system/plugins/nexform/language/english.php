<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexForm Plugin v2.2.0 for the nexPro Portal Server                        |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
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

$LANG_FE01 = array (
    'admin_only'        => 'Sorry Admins Only. If you are an Admin please login first.',
    'plugin'            => 'Plugin',
    'headermenu'        => '',
    'useradmintitle'    => '',
    'adminmenutitle'    => 'Form Management',
    'adminmenupanel'    => 'Form<br>Management',
    'access_denied'     => 'Access Denied',
    'access_denied_msg' => 'Only Root Users have Access to this Page.  Your user name and IP have been recorded.',
    'admin'             => 'Plugin Admin',
    'install_header'    => 'Install/Uninstall Plugin',
    'installed'         => 'The Plugin is now installed.<p><i>Enjoy,<br><a href="MAILTO:support@nextide.ca">Nextide Support</a></i>',
    'uninstalled'       => 'The Plugin is Not Installed',
    'install_success'   => 'Plugin installation was successful',
    'install_failed'    => 'Installation Failed -- See your error log to find out why.',
    'uninstall_msg'     => 'Plugin Successfully Uninstalled',
    'install'           => 'Install',
    'uninstall'         => 'UnInstall',
    'enabled'           => '<br>Plugin is installed and enabled.<br>Disable first if you want to De-Install it.<p>',
    'warning'           => 'Plugin De-Install Warning',
    'searchlabel'       => 'Forms',
    'searchresults'     => 'Form Search Results'

);
/* Admin Edit Fields Navbar Setup */
$LANG_FRM_ADMIN_NAVBAR = array (
    1   => 'Form Listing',
    2   => 'Create New Form',
    3   => 'Edit Form',
    4   => 'Field Listing',
    5   => 'Create New Field',
    6   => 'Edit Field',
    7   => 'Preview Form',
    8   => 'Edit Form Template',
    9   => 'Results Listing',
    10  => 'Result Detail',
    11  => 'Export to Excel',
    12  => 'Import Form Definition'

);

$CONF_FE['template_vars'] = array(
        'form_id'       => '* Unique Form ID',
        'form_action'   => '* URL to post form to - Do not edit',
        'form_handler'  => '* Action defined to handle posted form results like dbsave, email or custom',
        'submit'        => '* Form Submit Button - as defined in the form',
        'onsubmit'      => '  Javscript to execute on Form submission - if defined',
        'advancededitor'      => '  Javscript functions for HTML Advanced editor',
        'mfile_js_functions'  => '  Javscript functions required if Multi-File upload field type used',
        'introtext'     => '  Form Introduction',
        'labelx'        => '  Label for Field - label1 ... labelx for each form element',
        'fieldlx'       => '  Form Element - field1 ... fieldx for each form element',
        'cancel'        => '  Cancel button - if defined in the form',
        'msg_mandatory' => '  Message Note about  mandatory Fields'
    );


$LANG_FEMSG = array (
    1       => 'Select',         // Prompt that appears in select box'es in forms
    2       => 'Verification Error processing your request'     // Message Header for Captcha Verification Failure
);

$LANG_FE_ERR = array (
    'upload1'       => 'File Upload Error',
);


$PLG_nexform_MESSAGE1 = 'Thank you for your response';
$PLG_nexform_MESSAGE2 = 'nexform Editor - Unknown form handler Method';
$PLG_nexform_MESSAGE3 = 'Your record has been updated';
$PLG_nexform_MESSAGE10 = 'You must first install the nexpro plugin before installing any other nexpro related plugins.';
$PLG_nexform_MESSAGE11 = 'nexForm Plugin Upgrade completed - no errors';
$PLG_nexform_MESSAGE12 = 'nexForm Plugin Upgrade failed - check error.log';

// Localization of the Admin Configuration UI
$LANG_configsections['nexform'] = array(
    'label' => 'Nexform',
    'title' => 'Nexform Configuration'
);

$LANG_confignames['nexform'] = array(
    'debug'                     => 'Debug - set true for extra error.log detail',
    'post_url'                  => 'URL where form hander scripts are located. Other plugins like nexflow reference this setting',
    'public_url'                => 'URL where the plugins main public files are located',
    'uploadpath'                => 'Server directory to store attachments',
    'downloadURL'               => 'URL to uploaded form attachments',
    'defaultattributes'         => 'Set any default field options you want to have applied',
    'image_url'                 => 'URL to theme images',
    'export_dir'                => 'Directory for form export data',
    'field_mandatory_default'   => 'Adding new fields<br>mandatory option default',
    'load_editor'               => 'Enable the use of the FCKEditor',
    'fckeditor_toolbar'         => 'FCKEditor toolbar to use',
    'field_defaultspacing'      => 'Integer value to use as the default field/label width in pixels',
    'field_defaultrightpadding' => 'Default right padding to use for the form label/field pair',
    'field_defaultlabelpadding' => 'Default padding between the label and field',
    'result_summary_fields'     => 'Number of summary fields to show in result listing tool',
    'max_uploadimage_width'     => 'Maxiumum width in pixels to allow if upload file is an image',
    'max_uploadimage_height'    => 'Maxiumum heigth in pixels to allow if upload file is an image',
    'max_uploadfile_size'       => 'Maxiumum size in MB to allow if upload file is an image',
    'allowablefiletypes'        => 'Allowable file types allowed',
    'fieldstyles'               => 'Field Styles',
    'templates'                 => 'Form Template Options'
);

$LANG_configsubgroups['nexform'] = array(
    'sg_main'       => 'Main Settings'
);

$LANG_fs['nexform'] = array(
    'fs_main'           => 'Main Settings',
    'fs_layout'         => 'Form Layout Settings',
    'fs_attachments'    => 'Form Attachment Settings'
);

// Note: entries 0, 1 and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['nexform'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE),
    12 => array('No access' => 0, 'Read-Only' => 2, 'Read-Write' => 3),
    20 => array('Public Access' => 2, 'Site Members' => 13)
);

?>