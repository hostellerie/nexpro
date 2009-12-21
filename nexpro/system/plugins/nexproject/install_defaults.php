<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexProject Plugin v2.0.2 for the nexPro Portal Server                     |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Randy Kolenko          - Randy DOT Kolenko AT nextide DOT ca              |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

global $CONF_PRJ_DEFAULT;
$CONF_PRJ_DEFAULT = array();

$CONF_PRJ_DEFAULT['debug'] = false;

$CONF_PRJ_DEFAULT['notifications_enabled'] = true;  // set this to false to disable sending out of email notifications for the entire nexProject application

$CONF_PRJ_DEFAULT['fonts_directory'] = $_CONF['path'].'plugins/nexproject/fonts/';  // this is set to the default fonts directory where the vera TTF is located
                                        //the default directory is the system/plugins/nexproject/fonts directory
                                        //change this to suit your installation.

/* Define which blocks to show on the all-projects page - projects.php */
$CONF_PRJ_DEFAULT['leftblocks'] = array ('projectFilter');
$CONF_PRJ_DEFAULT['leftblocks'] = ppGetUserBlocks($CONF_PRJ_DEFAULT['leftblocks']);

/* Project Options */
$CONF_PRJ_DEFAULT['project_name_length']        = 30;
$CONF_PRJ_DEFAULT['lockduration']               = 10;   // Used to lock the Task Table when moving tasks - Set Max Lock Time (default is 10 seconds)
$CONF_PRJ_DEFAULT['min_graph_width']            = 700;  // Minimum width in pixels
$CONF_PRJ_DEFAULT['project_block_rows']         = 50;   // Number of records to show in the 'All Projects' and 'My Projects' Block
$CONF_PRJ_DEFAULT['task_block_rows']            = 10;   // Number of records to show in the 'Tasks' Block
$CONF_PRJ_DEFAULT['project_task_block_rows']    = 20;   // Number of records to show in the project detail view -'Tasks' Block

// HTML definitions for the image that will be used to show subtask names and their order indented
$CONF_PRJ_DEFAULT['subTaskImg']       = '<img src="' . $_CONF['layout_url'] . '/nexproject/images/subtask.gif" BORDER="0">';
$CONF_PRJ_DEFAULT['subTaskOrderImg']  = '<img src="' . $_CONF['layout_url'] . '/nexproject/images/subtask.gif" BORDER="0">';

// Theme settings
$CONF_PRJ_DEFAULT['THEME']=$_CONF['theme']."/nexproject";
$CONF_PRJ_DEFAULT['ROWLIMIT']=1;
//this is the define for using our fonts directory above
$CONF_PRJ_DEFAULT['TTF_DIR']=$CONF_PRJ_DEFAULT['fonts_directory'];


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
function plugin_initconfig_nexproject()
{
    global $_PRJCONF, $CONF_PRJ_DEFAULT;

    if (is_array($_PRJCONF) && (count($_PRJCONF) > 1)) {
          $CONF_PRJ_DEFAULT = array_merge($CONF_PRJ_DEFAULT, $_PRJCONF);
    }

      $c = config::get_instance();
      if (!$c->group_exists('nexproject')) {
        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'nexproject');
        $c->add('prj_main', NULL, 'fieldset', 0, 0, NULL, 0, true, 'nexproject');

        $c->add('debug', $CONF_PRJ_DEFAULT['debug'],'select',
                0, 0, 0, 10, true, 'nexproject');
        $c->add('notifications_enabled', $CONF_PRJ_DEFAULT['notifications_enabled'],'select',
                0, 0, 0, 20, true, 'nexproject');
        $c->add('fonts_directory', $CONF_PRJ_DEFAULT['fonts_directory'],
              'text', 0, 0, 0, 30, true, 'nexproject');

        $c->add('leftblocks', $CONF_PRJ_DEFAULT['leftblocks'],
              '%text', 0, 0, NULL, 40, true, 'nexproject');
        $c->add('project_name_length', $CONF_PRJ_DEFAULT['project_name_length'],
              'text', 0, 0, 0, 50, true, 'nexproject');
        $c->add('lockduration', $CONF_PRJ_DEFAULT['lockduration'],
              'text', 0, 0, 0, 60, true, 'nexproject');
        $c->add('min_graph_width', $CONF_PRJ_DEFAULT['min_graph_width'],
              'text', 0, 0, 0, 70, true, 'nexproject');
        $c->add('project_block_rows', $CONF_PRJ_DEFAULT['project_block_rows'],
              'text', 0, 0, 0, 80, true, 'nexproject');
        $c->add('task_block_rows', $CONF_PRJ_DEFAULT['task_block_rows'],
              'text', 0, 0, 0, 90, true, 'nexproject');
        $c->add('project_task_block_rows', $CONF_PRJ_DEFAULT['project_task_block_rows'],
              'text', 0, 0, 0, 100, true, 'nexproject');


        $c->add('subTaskImg', $CONF_PRJ_DEFAULT['subTaskImg'],
              'text', 0, 0, 0, 110, true, 'nexproject');
        $c->add('subTaskOrderImg', $CONF_PRJ_DEFAULT['subTaskOrderImg'],
              'text', 0, 0, 0, 120, true, 'nexproject');
        $c->add('THEME', $CONF_PRJ_DEFAULT['THEME'],
              'text', 0, 0, 0, 130, true, 'nexproject');
        $c->add('ROWLIMIT', $CONF_PRJ_DEFAULT['ROWLIMIT'],
              'text', 0, 0, 0, 140, true, 'nexproject');
        $c->add('TTF_DIR', $CONF_PRJ_DEFAULT['TTF_DIR'],
              'text', 0, 0, 0, 140, true, 'nexproject');
        return true;
      }
}