<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v2.5.1 for the nexPro Portal Server                        |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | css.php                                                                   |
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

class css_menu extends nexmenu {

    function _constructor($options)
    {
        if (isset($options['id'])) {
            parent::_construct($options['id']);
        } else {
            parent::_construct();
        }
    }


    public function getHeaderCode() {
        global $_CONF;
        if ($this->_type == 'header') {
            return LB . '<link rel="stylesheet" type="text/css" href="'.$_CONF['layout_url'].'/nexmenu/cssmenu/headermenu.css">' . LB;
        } elseif ($this->_type == 'block') {
            return LB . '<link rel="stylesheet" type="text/css" href="'.$_CONF['layout_url'].'/nexmenu/cssmenu/blockmenu.css">' . LB;
        }
    }

    public function renderMenu() {
        global $_TABLES,$_CONF;

        $menuItems = false;
        $query = DB_query("SELECT grp_access FROM {$_TABLES['nexmenu']} WHERE pid=0 AND is_enabled=1 AND location='{$this->_type}'");
        while (list ($grp_id) = DB_fetchArray($query)) {
            $grp_name = DB_getItem($_TABLES['groups'],"grp_name","grp_id='{$grp_id}'");
            if (SEC_inGroup($grp_name)) {   // There is atleast 1 item - set true and break out of loop
               $menuItems = true;
               break;
            }
        }
        if ($menuItems) {
            if ($this->_type == 'header') {
                return $this->_renderHeaderMenu();
            } elseif($this->_type == 'block') {
                return $this->_renderBlockMenu();
            }
        } else {
            return '';
        }

    }

    private function _renderHeaderMenu() {
        global $_TABLES,$_CONF;

        $headerbg = DB_getItem($_TABLES['nexmenu_config'], 'headerbg', "theme='{$this->_theme}'");
        $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
        $t->set_file('menu','headermenu.thtml');
        $t->set_file('menuitem','headermenu_item.thtml');
        $t->set_var('background-color', $headerbg);
        $t->set_var('menuitems',$this->_renderMenuItems());
        $t->parse ('output', 'menu');
        return $t->finish ($t->get_var('output'));
    }


    private function _renderBlockMenu() {
        global $_USER,$_TABLES,$_CONF,$CONF_NEXMENU;

        $retval = COM_startBlock( '', '', 'nexmenu/cssmenu/blockheader.thtml');
        $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
        $t->set_file('menu','blockmenu.thtml');
        $t->set_var('menuitems',$this->_renderMenuItems());
        $t->parse ('output', 'menu');
        $retval .= $t->finish ($t->get_var('output'));
        $retval .= COM_endBlock('nexmenu/cssmenu/blockfooter.thtml');

        if ($CONF_NEXMENU['loginform'] AND $_USER['uid'] < 2 AND DB_COUNT($_TABLES['nexmenu'],'url','usermenu') > 0 ) {
            // Now check the default useroptions block or gluserlogin block is not enabled
            $sql = "SELECT * FROM {$_TABLES['blocks']} WHERE is_enabled=1 AND (name='user_block' OR phpblockfn='phpblock_gluserlogin')";
            if (DB_numRows(DB_query($sql)) == 0) {
                $retval .= phpblock_glusermenu();
            }
        }

        return $retval;
    }



    private function _renderMenuItems($pid=0) {
        global $_CONF,$_TABLES,$_USER,$_BLOCK_TEMPLATE;

        foreach ($this->_menuitems as $menuitem) {
            if ($this->_multiLangMode) {
                $label = $this->getMenuLabel($menuitem['id']);
            } else {
                $label = $menuitem['label'];
            }
            $target = ($menuitem['type'] == 2) ? 'target=newWindow;' . $this->_targetFeatures : '';
            $menuitemImage = trim($menuitem['image']);
            if ($menuitemImage != '') {
                // Check and see if the full url is entered
                if (strpos($menuitemImage,'http') === false) {
                    $menuitemImage = $_CONF['site_url'] . '/nexmenu/menuimages/' . $menuitemImage;
                }
            }

            if ($i == $this->_menuitemCount) {
                $lastitem = true;
            } else {
                $lastitem = false;
            }

            // Check and see if this item is a submenu
            if ($menuitem['type'] == 3 ) {      // Type Submenu
                $url = str_replace('[siteurl]',$_CONF['site_url'], $menuitem['url']);
                $url = str_replace('[siteadminurl]',$_CONF['site_admin_url'], $url);
                if ($this->_type == 'header') {
                    $menuitemimagecss = 'headermenuitemimage';
                } else {
                    $menuitemimagecss = 'blocksubmenuitemimage';
                }

                $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
                if ($this->_type == 'header') {
                    $t->set_file('menu','headersubmenu.thtml');
                } else {
                    $t->set_file('menu','submenu.thtml');
                }
                $t->set_var('menuitem_url',$url);
                if ($menuitemImage != '') {
                    $image = '<img src="'.$menuitemImage.'" border="0">&nbsp;';
                    $label = "{$image}<span id=\"$menuitemimagecss\">{$label}</span>";
                    $t->set_var('menuitem_label',$label);
                } else {
                    $t->set_var('menuitem_label',$label);
                }
                if ($pid == 0) {
                    $t->set_var('imgclass','drop');
                } else {
                    $t->set_var('imgclass','fly');
                }
                if ($i == $this->_menuitemCount) {
                    $t->set_var('lastitemclass','class="enclose"');
                }
                parent::initMenuItems($menuitem['id']);
                $t->set_var('submenu_items',$this->_renderMenuItems($menuitem['id']));
                $t->parse ('output', 'menu');
                $retval .= $t->finish ($t->get_var('output'));


            } elseif ($menuitem['type'] == 4 ) {      // Core Menu
                switch ($menuitem['url']) {
                  case "adminmenu" :
                    if ($_USER['uid'] > 1) {
                        $_BLOCK_TEMPLATE['admin_block']  = 'nexmenu/cssmenu/blank.thtml,nexmenu/cssmenu/blank.thtml';
                        $_BLOCK_TEMPLATE['adminoption']  = 'nexmenu/cssmenu/menuitem.thtml,nexmenu/cssmenu/menuitem_on.thtml';
                        $plugin_options .= PLG_getAdminOptions();
                        $nrows = count( $plugin_options );
                        if( SEC_isModerator() OR ($nrows > 0) OR
                            SEC_hasrights('story.edit,block.edit,topic.edit,link.edit,event.edit,poll.edit,user.edit,plugin.edit,user.mail','OR')) {
                            $retval .= COM_adminMenu();
                        }
                    }
                    break;

                  case "usermenu" :
                    if ($_USER['uid'] > 1) {
                        $_BLOCK_TEMPLATE['user_block'] = 'nexmenu/cssmenu/blank.thtml,nexmenu/cssmenu/blank.thtml';
                        $_BLOCK_TEMPLATE['useroption'] = 'nexmenu/cssmenu/menuitem.thtml,nexmenu/cssmenu/menuitem_on.thtml';
                        $retval .=  COM_userMenu();
                    }
                    break;

                  case "topicmenu" :
                     $_BLOCK_TEMPLATE['topicoption'] = 'nexmenu/cssmenu/menuitem2.thtml,nexmenu/cssmenu/menuitem2_on.thtml';
                     $retval .= COM_showTopics(''," sortnum < '{$CONF_NEXMENU['restricted_topics']}'" );
                     break;

                  case "linksmenu" :
                     if ($this->_linksPlugin) {
                         $retval .= nexmenu_showlinks($pid,$this->_type,'site',$numcategories,0,$lastitem);
                     }
                     break;

                  case "spmenu" :
                    if ($this->_staticpagesPlugin) {
                        if ($CONF_NEXMENU['sp_labelonly']) {
                          $sql = "SELECT sp_id,sp_title,sp_label FROM {$_TABLES['staticpage']} WHERE sp_onmenu=1 ";
                          $sql .= COM_getPermSql ('AND');
                          $sql .= 'ORDER BY sp_title';
                          $spquery = DB_query($sql);
                        } else {
                          $sql = "SELECT sp_id,sp_title,sp_label FROM {$_TABLES['staticpage']} ";
                          $sql .= COM_getPermSql ('WHERE');
                          $sql .= 'ORDER BY sp_title';
                          $spquery = DB_query($sql);
                        }
                        while (list ($id, $title,$sp_label) = DB_fetchArray($spquery)) {
                            if (trim($sp_label) == '') {
                                $label = $title;
                            } else {
                                $label = $sp_label;
                            }
                            $url = "{$_CONF['site_url']}/staticpages/index.php?page=$id";
                            $retval .= "<li><a href=\"$url\" $target>$label</a></li>" . LB;
                        }
                    }
                    break;

                  case "pluginmenu" :
                    $result = DB_query("SELECT pi_name FROM {$_TABLES['plugins']} WHERE pi_enabled = 1");
                    $nrows = DB_numRows($result);
                    $menu = array();
                    for ($i = 1; $i <= $nrows; $i++) {
                        $A = DB_fetchArray($result);
                        $function = 'plugin_getmenuitems_' . $A['pi_name'];
                        if (function_exists($function)) {
                            $menuitems = $function();
                            if (is_array($menuitems) AND count($menuitems) > 0) {
                                foreach($menuitems as $plugin_label => $plugin_link) {
                                    $retval .= "<li><a href=\"$plugin_link\" $target>$plugin_label</a></li>" . LB;
                                }
                            }
                        }
                    }
                    break;

                  case "headermenu" :
                    $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
                    $t->set_file(array( 'menu' => 'siteheader_menuitems.thtml',
                                        'menuitem' => 'headermenu_item.thtml',
                                        'menuitem_last' => 'headermenu_item.thtml'));
                    $plugin_menu = PLG_getMenuItems();
                    COM_renderMenu($t,$plugin_menu);
                    $t->parse ('output', 'menu');
                    $retval .= $t->finish ($t->get_var('output'));
                    break;

                }  // End of menutype == 4  (Core Menu)

            } elseif ($menuitem['type'] == 5) {
                if (function_exists($menuitem['url'])) {
                    /* Pass the type of menu to custom php function */
                    $retval .= $menuitem['url']($this->_type);
                }

            } else {
                $url = str_replace('[siteurl]',$_CONF['site_url'], $menuitem['url']);
                $url = str_replace('[siteadminurl]',$_CONF['site_admin_url'], $url);
                // what's our current URL?
                $thisUrl = COM_getCurrentURL();
                if ($menuitemImage != '') {
                    if ($this->_type == 'header') {
                        $menuitemimagecss = 'headermenuitemimage';
                    } else {
                        $menuitemimagecss = 'blockmenuitemimage';
                    }
                    $image = '<img src="'.$menuitemImage.'" border="0">&nbsp;';
                    if ($i == 1 AND $pid > 0) {
                       $retval .= "<li><a href=\"$url\" $target class=\"enclose\">{$image}<span id=\"$menuitemimagecss\">{$label}</span></a></li>" . LB;
                    } elseif ($i == $menurows AND $pid == 0) {
                       $retval .= "<li><a href=\"$url\" $target class=\"enclose\">{$image}<span id=\"$menuitemimagecss\">{$label}</span></a></li>" . LB;
                    } elseif ($url == $thisUrl) {
                        $retval .= "<li id=\"menuitem_current\"><a href=\"$url\" $target>{$image}<span id=\"$menuitemimagecss\">{$label}</span></a></li>" . LB;
                    } else {
                        $retval .= "<li><a href=\"$url\" $target>{$image}<span id=\"$menuitemimagecss\">{$label}</span></a></li>" . LB;
                    }
                } else {
                    if ($i == 1 AND $pid > 0) {
                       $retval .= "<li><a href=\"$url\" $target class=\"enclose\">$label</a></li>" . LB;
                    } elseif ($i == $menurows AND $pid == 0) {
                       $retval .= "<li><a href=\"$url\" $target class=\"enclose\">$label</a></li>" . LB;
                    } elseif ($url == $thisUrl) {
                        $retval .= "<li id=\"menuitem_current\"><a href=\"$url\" $target>$label</a></li>" . LB;
                    } else {
                        $retval .= "<li><a href=\"$url\" $target>$label</a></li>" . LB;
                    }
                }

            }
            $i++;
        }

        // Restore Template Setting
        $_BLOCK_TEMPLATE = $this->_currentBlockTemplate;
        return $retval;

    }

}

?>