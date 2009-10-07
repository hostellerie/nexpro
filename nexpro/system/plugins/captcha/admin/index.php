<?php
// +---------------------------------------------------------------------------+
// | CAPTCHA v3 Plugin                                                         |
// +---------------------------------------------------------------------------+
// | $Id: index.php,v 1.2 2007/09/12 18:02:49 eric Exp $|
// | Admin Interface to CAPTCHA Plugin.                                        |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007 by the following authors:                              |
// |                                                                           |
// | Author: mevans@ecsnet.com                                                 |
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

require_once('../../../lib-common.php');

function CP_array_sort($array, $key) {
    for ($i=0;$i<sizeof($array);$i++) {
        $sort_values[$i] = $array[$i][$key];
    }
    asort($sort_values);
    reset($sort_values);
    while (list($arr_key, $arr_val) = each($sort_values)) {
        $sorted_arr[] = $array[$arr_key];
    }
    return $sorted_arr;
}


// Only let admin users access this page
if (!SEC_inGroup('Root')) {
    // Someone is trying to illegally access this page
    COM_errorLog("Someone has tried to illegally access the CAPTCHA Administration page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: " . $_SERVER['REMOTE_ADDR'],1);
    $display  = COM_siteHeader();
    $display .= COM_startBlock($LANG27[12]);
    $display .= $LANG27[12];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
}

$msg = '';

if ( isset($_POST['mode']) ) {
    $mode = $_POST['mode'];
} else {
    $mode = '';
}

if ( $mode == $LANG_CP00['cancel'] && !empty($LANG_CP00['cancel']) ) {
    header('Location:' . $_CONF['site_admin_url'] . '/moderation.php');
    exit;
}

if ( $mode == $LANG_CP00['save'] && !empty($LANG_CP00['save']) ) {
    $settings['anonymous_only']         = $_POST['anononly'] == 'on' ? 1 : 0;
    $settings['remoteusers']            = $_POST['remoteusers'] == 'on' ? 1 : 0;
    $settings['enable_comment']         = $_POST['comment'] == 'on' ? 1 : 0;
    $settings['enable_story']           = $_POST['story'] == 'on' ? 1 : 0;
    $settings['enable_registration']    = $_POST['registration'] == 'on' ? 1 : 0;
    $settings['enable_contact']         = $_POST['contact'] == 'on' ? 1 : 0;
    $settings['enable_emailstory']      = $_POST['emailstory'] == 'on' ? 1 : 0;
    $settings['enable_forum']           = $_POST['forum'] == 'on' ? 1 : 0;
    $settings['enable_mediagallery']    = $_POST['mediagallery'] == 'on' ? 1: 0;

    $settings['gfxDriver']              = COM_applyFilter($_POST['gfxdriver']);
    $settings['gfxFormat']              = COM_applyFilter($_POST['gfxformat']);
    $settings['gfxPath']                = addslashes($_POST['gfxpath']);
    $settings['debug']                  = $_POST['debug'] == 'on' ? 1 : 0;
    $settings['imageset']               = COM_applyFilter($_POST['imageset']);

    foreach($settings AS $option => $value ) {
        $value = addslashes($value);
        DB_save($_TABLES['cp_config'],"config_name,config_value","'$option','$value'");
        $_CP_CONF[$option] = stripslashes($value);
    }
    $msg = $LANG_CP00['success'];
}


$display = '';
$display = COM_siteHeader();

$T = new Template($_CONF['path'] . 'plugins/captcha/templates');
$T->set_file (array ('admin' => 'admin.thtml'));

$imageset = array();
$i = 0;
$directory = $_CONF['path'] . 'plugins/captcha/images/static/';

$dh = @opendir($directory);
while ( ( $file = @readdir($dh) ) != false ) {
    if ( $file == '..' || $file == '.' ) {
        continue;
    }
    $imagedir = $directory . $file;
    if (@is_dir($imagedir)) {
        if ( file_exists($imagedir . '/' . 'imageset.inc') ) {
            include ( $imagedir . '/' . 'imageset.inc');
            $imageset[$i]['dir'] = $file;
            $imageset[$i]['name'] = $staticimageset['name'];
            $i++;
        }
    }
}
@closedir($dh);

$sImageSet = CP_array_sort($imageset,'name');
$set_select = '<select name="imageset" id="imageset">';
for ( $i=0; $i < count($sImageSet); $i++ ) {
    $set_select .= '<option value="' . $sImageSet[$i]['dir'] . '"' . ($_CP_CONF['imageset'] == $sImageSet[$i]['dir'] ? ' SELECTED ': '') .'>' . $sImageSet[$i]['name'] .  '</option>';
}
$set_select .= '</select>';

$T->set_var(array(
    'site_admin_url'            => $_CONF['site_admin_url'],
    'site_url'                  => $_CONF['site_url'],
    'anonchecked'               => ($_CP_CONF['anonymous_only'] ? ' CHECKED=CHECKED' : ''),
    'remotechecked'             => ($_CP_CONF['remoteusers'] ? ' CHECKED=CHECKED' : ''),
    'commentchecked'            => ($_CP_CONF['enable_comment'] ? ' CHECKED=CHECKED' : ''),
    'storychecked'              => ($_CP_CONF['enable_story'] ? ' CHECKED=CHECKED' : ''),
    'registrationchecked'       => ($_CP_CONF['enable_registration'] ? ' CHECKED=CHECKED' : ''),
    'contactchecked'            => ($_CP_CONF['enable_contact'] ? ' CHECKED=CHECKED' : ''),
    'emailstorychecked'         => ($_CP_CONF['enable_emailstory'] ? ' CHECKED=CHECKED' : ''),
    'forumchecked'              => ($_CP_CONF['enable_forum'] ? ' CHECKED=CHECKED' : ''),
    'mediagallerychecked'       => ($_CP_CONF['enable_mediagallery'] ? ' CHECKED=CHECKED' : ''),
    'gdselected'                => ($_CP_CONF['gfxDriver'] == 0 ? ' SELECTED=SELECTED' : ''),
    'imselected'                => ($_CP_CONF['gfxDriver'] == 1 ? ' SELECTED=SELECTED' : ''),
    'noneselected'              => ($_CP_CONF['gfxDriver'] == 2 ? ' SELECTED=SELECTED' : ''),

    'jpgselected'               => ($_CP_CONF['gfxFormat'] == 'jpg' ? ' SELECTED=SELECTED' : ''),
    'pngselected'               => ($_CP_CONF['gfxFormat'] == 'png' ? ' SELECTED=SELECTED' : ''),

    'gfxpath'                   => $_CP_CONF['gfxPath'],

    'debugchecked'              => ($_CP_CONF['debug'] ? ' CHECKED=CHECKED' : ''),

    'lang_overview'             => sprintf($LANG_CP00['captcha_info'], 'http://www.gllabs.org/wiki/doku.php?id=captcha:start'),
    'lang_view_logfile'         => $LANG_CP00['view_logfile'],
    'lang_admin'                => $LANG_CP00['admin'],
    'lang_settings'             => $LANG_CP00['enabled_header'],
    'lang_anonymous_only'       => $LANG_CP00['anonymous_only'],
    'lang_enable_comment'       => $LANG_CP00['enable_comment'],
    'lang_enable_story'         => $LANG_CP00['enable_story'],
    'lang_enable_registration'  => $LANG_CP00['enable_registration'],
    'lang_enable_contact'       => $LANG_CP00['enable_contact'],
    'lang_enable_emailstory'    => $LANG_CP00['enable_emailstory'],
    'lang_enable_forum'         => $LANG_CP00['enable_forum'],
    'lang_enable_mediagallery'  => $LANG_CP00['enable_mediagallery'],
    'lang_save'                 => $LANG_CP00['save'],
    'lang_cancel'               => $LANG_CP00['cancel'],
    'lang_gfx_driver'           => $LANG_CP00['gfx_driver'],
    'lang_gfx_format'           => $LANG_CP00['gfx_format'],
    'lang_convert_path'         => $LANG_CP00['convert_path'],
    'lang_gd_libs'              => $LANG_CP00['gd_libs'],
    'lang_imagemagick'          => $LANG_CP00['imagemagick'],
    'lang_static_images'        => $LANG_CP00['static_images'],
    'lang_debug'                => $LANG_CP00['debug'],
    'lang_configuration'        => $LANG_CP00['configuration'],
    'lang_integration'          => $LANG_CP00['integration'],
    'lang_imageset'             => $LANG_CP00['image_set'],
    'lang_remoteusers'          => $LANG_CP00['remoteusers'],
    'selectImageSet'            => $set_select,
    'lang_msg'                  => $msg,
    'version'                   => $_CP_CONF['version'],
    's_form_action'             => $_CONF['site_admin_url'] . '/plugins/captcha/index.php',
));


$T->parse('output', 'admin');
$display .= $T->finish($T->get_var('output'));
$display .= COM_siteFooter();
echo $display;
exit;

?>