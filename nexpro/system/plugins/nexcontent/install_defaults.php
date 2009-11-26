<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexContent Plugin v2.3.0 for the nexPro Portal Server                     |
// | October 9, 2009                                                           |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
// | Initial Installation Defaults used when loading the online configuration  |
// | records. These settings are only used during the initial installation     |
// | and not referenced any more once the plugin is installed.                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2009 by the following authors:                         |
// | Blaine Lang            - Blaine.Lang@nextide.ca                           |
// | Randy Kolenko          - Randy.Kolenko@nextide.ca                         |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

global $CONF_SE_DEFAULT;;
$CONF_SE_DEFAULT = array();


$CONF_SE_DEFAULT['debug'] = false;

/* Setup Paths and URLs */
$CONF_SE_DEFAULT['public_url']   = $_CONF['site_url'] .'/nc';
$CONF_SE_DEFAULT['uploadpath']   = $_CONF['path_html'] . 'nc/images/';

// Relative Directory to $_CONF['site_url'] where the Editor Image Library store
$CONF_SE_DEFAULT['imagelibrary']  = '/nc/library';

// +---------------------------------------------------------------------------+
// | Set defaults values for Pages served by nexcontent                        |
// | Default values will be used if set. Settings can be defined per page      |
// | in the editor.                                                            |
// +---------------------------------------------------------------------------+

/* Default Page Title if set to use for all pages */
/* Will override the page title set by Geeklog for any nexcontent pages if set */
$CONF_SE_DEFAULT['pagetitle']  = 'Page Title';

/* Default value if set to use for all page META Tag "Description" value */
/* Can be 200 - 250 words */
$CONF_SE_DEFAULT['meta_description']  = '';

/* Default value to be used for page META Tag "Keywords" value*/
/* List of comma separated keywords - 20 to 25 words is normal
/* Note: Not many search engines use this tag anymore or place much empahsis on it */
$CONF_SE_DEFAULT['meta_keywords']  = '';

/* NOT USED YET: Default Page favicon */
$CONF_SE_DEFAULT['favicon'] = $CONF_SE_DEFAULT['public_url'] .'/images/favion.ico';


// +---------------------------------------------------------------------------+
// | Miscelaneous settings                                                     |
// +---------------------------------------------------------------------------+


/* Number of images that can be uploaded at one time to a content category page */
$CONF_SE_DEFAULT['max_num_images'] = 10;

$CONF_SE_DEFAULT['convert_tool'] = 'gd';

/* Enable Breadcrumbs */
$CONF_SE_DEFAULT['breadcrumbs'] = true;

/* Character to be used in the breadcrumb URL listing as the separator */
$CONF_SE_DEFAULT['breadcrumb_separator'] = '>&nbsp;';


$CONF_SE_DEFAULT['max_uploadfile_size'] = 20;

$CONF_SE_DEFAULT['max_upload_width'] = 370;
$CONF_SE_DEFAULT['max_upload_height'] = 300;
$CONF_SE_DEFAULT['image_quality'] = 90;
$CONF_SE_DEFAULT['auto_thumbnail_dimension'] = 75;
$CONF_SE_DEFAULT['auto_thumbnail_resize_type'] = 1;
$CONF_SE_DEFAULT['auto_thumbnail_quality'] = 90;

// +---------------------------------------------------------------------------+
// | hould users be able to upload images from nexcontent                      |
// | or use only the FCK uploader                                              |
// +---------------------------------------------------------------------------+
$CONF_SE_DEFAULT['loadImageUploader']   = false;


$CONF_SE_DEFAULT['allowableImageTypes']    = array(
    'image/bmp'     => '.bmp, .ico',
    'image/gif'     => '.gif',
    'image/pjpeg'   => '.jpg, .jpeg',
    'image/jpeg'    => '.jpg, .jpeg',
    'image/png'     => '.png'
);



/**
* Initialize plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $CONF_NEXPRO if available (e.g. from
* an old config.php), uses $_NEXPRO_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_nexcontent()
{
    global $CONF_SE, $CONF_SE_DEFAULT;

    if (is_array($CONF_SE) && (count($CONF_SE) > 1)) {
        $CONF_SE_DEFAULT = array_merge($CONF_SE_DEFAULT, $CONF_SE);
    }

    $c = config::get_instance();
    if (!$c->group_exists('nexcontent')) {

      $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'nexcontent');
      $c->add('nc_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'nexcontent');
      $c->add('debug', $CONF_SE_DEFAULT['debug'],'select',
                0, 0, 0, 10, true, 'nexcontent');
      $c->add('public_url', $CONF_SE_DEFAULT['public_url'],
              'text', 0, 0, 0, 20, true, 'nexcontent');
      $c->add('uploadpath', $CONF_SE_DEFAULT['uploadpath'],
              'text', 0, 0, 0, 30, true, 'nexcontent');
      $c->add('imagelibrary', $CONF_SE_DEFAULT['imagelibrary'],
              'text', 0, 0, 0, 40, true, 'nexcontent');

      $c->add('pagetitle', $CONF_SE_DEFAULT['pagetitle'],
              'text', 0, 0, 0, 50, true, 'nexcontent');
      $c->add('meta_description', $CONF_SE_DEFAULT['meta_description'],
              'text', 0, 0, 0, 60, true, 'nexcontent');
      $c->add('meta_keywords', $CONF_SE_DEFAULT['meta_keywords'],
              'text', 0, 0, 0, 70, true, 'nexcontent');
      $c->add('favicon', $CONF_SE_DEFAULT['favicon'],
              'text', 0, 0, 0, 80, true, 'nexcontent');
     $c->add('max_num_images', $CONF_SE_DEFAULT['max_num_images'],
              'text', 0, 0, 0, 90, true, 'nexcontent');
     $c->add('loadImageUploader', $CONF_SE_DEFAULT['loadImageUploader'],
              'select', 0, 0, 0, 100, true, 'nexcontent');
     $c->add('convert_tool', $CONF_SE_DEFAULT['convert_tool'],
              'text', 0, 0, 0, 110, true, 'nexcontent');
     $c->add('breadcrumbs', $CONF_SE_DEFAULT['breadcrumbs'],
              'select', 0, 0, 0, 120, true, 'nexcontent');
     $c->add('breadcrumb_separator', $CONF_SE_DEFAULT['breadcrumb_separator'],
              'text', 0, 0, 0, 130, true, 'nexcontent');
     $c->add('max_uploadfile_size', $CONF_SE_DEFAULT['max_uploadfile_size'],
              'text', 0, 0, 0, 140, true, 'nexcontent');
     $c->add('max_upload_width', $CONF_SE_DEFAULT['max_upload_width'],
              'text', 0, 0, 0, 150, true, 'nexcontent');
     $c->add('max_upload_height', $CONF_SE_DEFAULT['max_upload_height'],
              'text', 0, 0, 0, 160, true, 'nexcontent');
     $c->add('image_quality', $CONF_SE_DEFAULT['image_quality'],
              'text', 0, 0, 0, 170, true, 'nexcontent');
     $c->add('auto_thumbnail_dimension', $CONF_SE_DEFAULT['auto_thumbnail_dimension'],
              'text', 0, 0, 0, 180, true, 'nexcontent');
     $c->add('auto_thumbnail_resize_type', $CONF_SE_DEFAULT['auto_thumbnail_resize_type'],
              'select', 0, 0, 2, 190, true, 'nexcontent');
     $c->add('auto_thumbnail_quality', $CONF_SE_DEFAULT['auto_thumbnail_quality'],
              'text', 0, 0, 0, 200, true, 'nexcontent');
     $c->add('allowableImageTypes', $CONF_SE_DEFAULT['allowableImageTypes'],
                '*text', 0, 0, NULL, 210, true, 'nexcontent');
    }

    return true;
}



?>