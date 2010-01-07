<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v3.2 for the nexPro Portal Server                          |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexmenu.class.php                                                         |
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


class nexmenu  {

    // Private Properties  - Use the set Methods to change

    var $_version = 0;                          // Version of this plugin
    var $_debug = false;                        // Set to true if debug output desired
    var $_theme = 'default';                    // Theme to use and select menu style and config for
    var $_menustyle = '';                       // Set to type of menu style class has been initialized for
    var $_type = '';                            // Menu Type where 'header' and 'block' are supported
    var $_targetFeatures = '';                  // Menu default options for menu item
    var $_blockMenuStyle = '';                  // Menu style for the main level Block Menu
    var $_blockSubmenuStyle = '';               // Menu style for the Block Sub Menu
    var $_headerMenuStyle = '';                 // Menu style for the main level Header Menu
    var $_headerSubmenuStyle = '';              // Menu style for the Header Sub Menu
    var $_multiLangMode = false;                // Set to true if Menu will be using Multi Language
    var $_linksPlugin = false;                  // Set to true if the links plugin is enabled
    var $_staticpagesPlugin = false;            // Set to true if the staticpages plugin is enabled
    var $_currentBlockTemplate = '';            // Set to the current Block template as set in the theme functions.php
    var $_groupAccessList = '';                 // Comma separated list of groups for user - set in the constructor
    var $_menuitemCount = 0;                    // Number of menu items
    var $_menuitems = array();                  // Array of menu item details - defined by functon initMenuItems



    /**
    * Constructor
    *
    */
    function nexmenu()
    {

    }

    /**
     * Return an instance of a specific menu type
     *
     * @param  string  $theme    Theme to use for menu style attributes from gl_config table
     * @param  string  $type     Tupe of menu where 'Block' and 'Header' menus are currently supported
     * @param  array   $options  Options for the menu class
     * @return mixed instance of the menu object.
     */
    function &factory($type, $theme='default', $options = false)
    {

        global $_TABLES,$USER;

        $query = DB_query("SELECT header_style,block_style FROM {$_TABLES['nexmenu_config']} WHERE theme='$theme'");
        // Just in case there is no menu style config record for this theme - use the default record
        if (DB_numRows($query) == 0) {
            $theme = 'default';
            $query = DB_query("SELECT header_style,block_style FROM {$_TABLES['nexmenu_config']} WHERE theme='default'");
        }
        list ($headerStyle,$blockStyle) = DB_fetchArray($query);
        if ($type == 'header') {
            $style = strtolower(trim($headerStyle));
        } else {
            $style = strtolower(trim($blockStyle));
        }

        if ($style == '') {
            $object = & new nexmenu();
            $object->_menustyle = $style;
            return $object;
        } else {
            $classfile = "style/{$style}.php";
            if (include_once $classfile) {
                $class = "{$style}_menu";
                if (class_exists($class)) {
                    $object = & new $class();
                    $object->_menustyle = $style;
                    if (!empty($theme)) {
                        $object->setTheme($theme);
                    }
                    if (!empty($type)) {
                        $object->setMenuType($type);
                    }
                    $object->_constructor($options);
                    return $object;
                } else {
                    COM_errorLog("nexmenu.class - Unable to instantiate class $class from $classfile");
                }
            } else {
                COM_errorLog("nexmenu.class - Unable to include file: $classfile");
            }
        }
    }


    function _construct()
    {
        global $_TABLES,$_USER,$CONF_NEXMENU,$_BLOCK_TEMPLATE;

        $this->_currentBlockTemplate = $_BLOCK_TEMPLATE;
        $this->_version = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'nexmenu'");
        $query = DB_query("SELECT * FROM {$_TABLES['nexmenu_config']} WHERE theme='{$this->_theme}'");
        $A = DB_fetchArray($query);
        $this->_blockMenuStyle     = $A['blockmenu_style'];
        $this->_blockSubmenuStyle  = $A['blocksubmenu_style'];
        $this->_headerMenuStyle    = $A['headermenu_style'];
        $this->_headerSubmenuStyle = $A['headersubmenu_style'];
        if ($A['multilanguage'] == 1) {
            $this->_multiLangMode = true;
        } else {
            $this->_multiLangMode = false;
        }
        $this->_targetFeatures = DB_getItem($_TABLES['nexmenu_config'],"targetfeatures");
        if (DB_getItem($_TABLES['plugins'],'pi_enabled',"pi_name = 'links'") == 1) {
            $this->_linksPlugin = true;
        } else {
           $this->_linksPlugin = false;
        }
        if (DB_getItem($_TABLES['plugins'],'pi_enabled',"pi_name = 'staticpages'") == 1) {
           $this->_staticpagesPlugin = true;
        } else {
           $this->_staticpagesPlugin = false;
        }

        // Get list of groups member belongs to
        if (!isset($_USER['uid']) OR $_USER['uid'] < 2) {
            $this->_groupAccessList = '0,2';
        } else {
            $groups = array ();
            $usergroups = SEC_getUserGroups();
            foreach ($usergroups as $group) {
                $groups[] = $group;
            }
            $this->_groupAccessList = implode(',',$groups);
        }

    }

    function setMenuType($type) {
        $this->_type = $type;
    }

    function setTheme($type) {
        $this->_theme = $type;
    }

    public function initMenuItems($pid=0) {
        global $_TABLES;

        $sql = "SELECT id,menutype as type,label,url,grp_access,image FROM {$_TABLES['nexmenu']} ";
        $sql .= "WHERE pid=$pid AND is_enabled=1 AND location='{$this->_type}' ";
        $sql .= "AND grp_access IN ({$this->_groupAccessList}) ";
        $sql .= "ORDER BY menuorder";
        $query = DB_query($sql);
        $this->_menuitemCount = DB_numRows($query);
        unset($this->_menuitems);
        $this->_menuitems = array();
        while ($A = DB_fetchArray($query,false)) {
            array_push($this->_menuitems, $A);
        }

    }



    public function getMenuLabel($id) {
        global $_USER,$_TABLES,$CONF_NEXMENU;

        $label = '';
        if ($this->_multiLangMode AND is_array($CONF_NEXMENU['languages']) AND in_array($_USER['language'],$CONF_NEXMENU['languages'])) {
            $langid = array_search($_USER['language'],$CONF_NEXMENU['languages']);
            $label = DB_getItem($_TABLES['nexmenu_language'],'label',"menuitem=$id AND language=$langid");
        }
        if ($label == '') {
            $label = DB_getItem($_TABLES['nexmenu'],'label',"id=$id");
        }
        return $label;
    }

}


?>
