<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexMenu Plugin v2.5.1 for the nexPro Portal Server                        |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | milonic.php                                                               |
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

class milonic_menu extends nexmenu {

    var $_headerMenuStyle = '';
    var $_headerMenuProperties = '';

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

        $code .= '<SCRIPT language=JavaScript src="' . $_CONF['site_url'] . '/nexmenu/milonic/milonic_src.js" type=text/javascript></SCRIPT>' ."\n";
        $code .= '<script language=JavaScript>' ."\n";
        $code .= '    if(ns4)_d.write("<scr"+"ipt language=JavaScript src=' . $_CONF['site_url'] . '/nexmenu/milonic/mmenuns4.js><\/scr"+"ipt>");' ."\n";
        $code .= '    else _d.write("<scr"+"ipt language=JavaScript src=' . $_CONF['site_url'] . '/nexmenu/milonic/mmenudom.js><\/scr"+"ipt>");' ."\n";
        $code .= '</script>' ."\n";
        $code .= '<SCRIPT language=JavaScript src="' . $_CONF['layout_url'] . '/nexmenu/milonicmenu/menustyles.php" type=text/javascript></SCRIPT>' ."\n";
        $code .= '<script type="text/javascript">' .LB;

        // Need to generate all the milonic code for the submenus
        $code .=  $this->_milonicMenuJavascript() . LB;
        $code .= '</script>' . LB;

        return $code;
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
            $query = DB_query("SELECT headermenu_properties,blockmenu_properties FROM {$_TABLES['nexmenu_config']}");
            list ($this->_headerMenuProperties,$this->_blockMenuProperties) = DB_fetchArray($query);
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
        global $_TABLES,$_CONF,$CONF_NEXMENU;

        $retval = '<script> with(milonic=new menuname("Site Menu")) {';
        $retval .= $CONF_NEXMENU['headermenu_default_styles'] . $this->_headerMenuProperties;
        $retval .= 'style='.$this->_headerMenuStyle.';';
        $retval .= $this->_renderMenuItems();
        $retval .=  '} drawMenus();' . LB . '</script>' . LB;

        return $retval;
    }


    private function _renderBlockMenu() {
        global $_USER,$_TABLES,$_CONF,$CONF_NEXMENU;

        $retval ='<script>
                    with(milonic=new menuname("Site Menu")) {' . LB;
        $retval .= 'style='.$this->_blockMenuStyle.';' . LB;
        $retval .= $CONF_NEXMENU['blockmenu_default_styles'] . $this->_blockMenuProperties . LB;
        $retval .= $this->_renderMenuItems();
        $retval .= '} drawMenus();' . LB . '</script>' . LB;

        if ($CONF_NEXMENU['loginform'] AND $_USER['uid'] < 2 AND DB_COUNT($_TABLES['nexmenu'],'url','usermenu') > 0 ) {
            // Now check the default useroptions block or gluserlogin block is not enabled
            $sql = "SELECT * FROM {$_TABLES['blocks']} WHERE is_enabled=1 AND (name='user_block' OR phpblockfn='phpblock_gluserlogin')";
            if (DB_numRows(DB_query($sql)) == 0) {
                $retval .= phpblock_glusermenu();
            }
        }

        return $retval;
    }


   private function _renderMenuItems() {
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

                if ($menuitemImage != '') {
                    $retval .= 'aI("image='.$menuitemImage.';text='.$label.';'.'url='.$url.';'.$target.'showmenu=nexmenu'.$menuitem['id'].';");';
                } else {
                    $retval .= 'aI("text='.$label.';'.'url='.$url.';'.$target.'showmenu=nexmenu'.$menuitem['id'].';");';
                }


            } elseif ($menuitem['type'] == 4 ) {      // Core Menu
                switch ($menuitem['url']) {
                  case "adminmenu" :
                    if ($_USER['uid'] > 1) {
                        $_BLOCK_TEMPLATE['admin_block']  = 'nexmenu/milonicmenu/blockheader-blank.thtml,nexmenu/milonicmenu/blockfooter-blank.thtml';
                        $_BLOCK_TEMPLATE['adminoption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
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
                        $_BLOCK_TEMPLATE['user_block']  = 'nexmenu/milonicmenu/blockheader-blank.thtml,nexmenu/milonicmenu/blockfooter-blank.thtml';
                        $_BLOCK_TEMPLATE['useroption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
                        $retval .=  COM_userMenu();
                    }
                    break;

                  case "topicmenu" :
                    $_BLOCK_TEMPLATE['topicoption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
                     $retval .= COM_showTopics(''," sortnum < '{$CONF_NEXMENU['restricted_topics']}'" );
                     break;

                  case "linksmenu" :
                     if ($this->_linksPlugin) {
                         $retval .= $this->_milonicLinksPluginSiteLinks();
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
                            $retval .= 'aI("text='.$label.';url='.$_CONF['site_url'].'/staticpages/index.php?page='.$id.';");';
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
                                    $retval .= 'aI("text='.$plugin_label.';'.$target.'url='.$plugin_link.';");';
                                }
                            }
                        }
                    }
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
                    $retval .= 'aI("image='.$menuitemImage.';text='.$label.';url='.$url.';'.$target.';");';
                } else {
                    $retval .= 'aI("text='.$label.';url='.$url.';'.$target.';");';
                }
            }
            $i++;
        }

        // Restore Template Setting
        $_BLOCK_TEMPLATE = $this->_currentBlockTemplate;
        return $retval;

    }

    /* Generate the JS Menu Functions that are now needed - one for each menu item that has a submenu */
    private function _milonicMenuJavascript() {
        global $_CONF,$_TABLES,$_USER,$_BLOCK_TEMPLATE;

        $current_BLOCK_TEMPLATE = $_BLOCK_TEMPLATE;
        $pquery = DB_query("SELECT DISTINCT id,location FROM {$_TABLES['nexmenu']} WHERE menutype=3 and is_enabled=1");
        while (list($pid,$location) = DB_fetchArray($pquery)) {
            $retval .= LB . 'with(milonic=new menuname("nexmenu'.$pid.'")) {';
                    if ($location == 'block') {
                        $retval .= 'style='.$this->_blockSubmenuStyle .';';
                    } else {
                        $retval .= 'style='.$this->_headerSubmenuStyle .';';
                    }
                    $sql = "SELECT id,location, menutype,label,url,image,grp_access FROM {$_TABLES['nexmenu']} ";
                    $sql .= "WHERE pid='{$pid}' AND is_enabled=1 ORDER BY menuorder";
                    $query = DB_query($sql);
                    while (list ($id,$location,$menutype,$label,$url,$menuitemImage,$grp_id) = DB_fetchArray($query)) {
                        $grp_name = DB_getItem($_TABLES['groups'],"grp_name","grp_id='{$grp_id}'");
                        $target = ($menutype == 2) ? 'target=_new;' : '';
                        if ($menuitemImage != '') {
                            // Check and see if the full url is entered
                            if (strpos($menuitemImage,'http') === false) {
                                $menuitemImage = $_CONF['site_url'] . '/nexmenu/menuimages/' . $menuitemImage;
                            }
                        }
                        if (SEC_inGroup($grp_name)) {
                            $menudata = '';
                            if ($menutype == 3) {
                                if ($menuitemImage != '') {
                                    $menudata = 'aI("image='.$menuitemImage.';text='.$label.';showmenu=nexmenu'.$id.';");';
                                } else {
                                    $menudata = 'aI("text='.$label.';showmenu=nexmenu'.$id.';");';
                                }
                            } elseif ($menutype == 4 ) {
                                switch ($url) {
                                  case "adminmenu" :
                                    if ($_USER['uid'] > 1) {
                                      $_BLOCK_TEMPLATE['admin_block']  = 'nexmenu/milonicmenu/blockheader-blank.thtml,nexmenu/milonicmenu/blockfooter-blank.thtml';
                                      $_BLOCK_TEMPLATE['adminoption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
                                      $menudata = COM_adminMenu();
                                    }
                                    break;

                                  case "usermenu" :
                                    if ($_USER['uid'] > 1) {
                                       $_BLOCK_TEMPLATE['user_block']  = 'nexmenu/milonicmenu/blockheader-blank.thtml,nexmenu/milonicmenu/blockfooter-blank.thtml';
                                       $_BLOCK_TEMPLATE['useroption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
                                       $menudata = COM_userMenu();
                                    }
                                    break;

                                  case "topicmenu" :
                                    $_BLOCK_TEMPLATE['topicoption']  = 'nexmenu/milonicmenu/option.thtml,nexmenu/milonicmenu/option_off.thtml';
                                    $menudata = COM_showTopics(''," sortnum < '50'" );
                                    break;

                                  case "linksmenu" :
                                     if ($this->_linksPlugin) {
                                          $menudata .= $this->_milonicLinksPluginSiteLinks();
                                     }
                                    break;

                                  case "spmenu" :
                                   if ($CONF_NEXMENU['sp_labelonly']) {
                                      $spquery = DB_query("SELECT sp_id,sp_title FROM {$_TABLES['staticpage']} WHERE sp_onmenu=1 ORDER BY sp_title");
                                    } else {
                                      $spquery = DB_query("SELECT sp_id,sp_title FROM {$_TABLES['staticpage']} ORDER BY sp_title");
                                    }
                                    while (list ($id, $title) = DB_fetchArray($spquery)) {
                                        $menudata .= 'aI("text='.$title.';url='.$_CONF['site_url'].'/staticpages/index.php?page='.$id.';");';
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
                                            foreach($menuitems as $plugin_label => $plugin_link) {
                                                $menudata .= 'aI("text='.$plugin_label.';'.$target.'url='.$plugin_link.';");';
                                            }
                                        }
                                    }
                                    break;

                                }  // End of Case

                            } elseif ($menutype == 5) {
                                if (function_exists($url)) {
                                    $menudata = $url();
                                }
                            } else {
                                $url = str_replace('[siteurl]',$_CONF['site_url'], $url);
                                $url = str_replace('[siteadminurl]',$_CONF['site_admin_url'], $url);
                                if ($menuitemImage != '') {
                                    $menudata = 'aI("image='.$menuitemImage.';text='.$label.';url='.$url.';'.$target.';");';
                                } else {
                                    $menudata = 'aI("text='.$label.';url='.$url.';'.$target.';");';
                                }
                            }

                            if ($menudata != '') {
                                $retval .= $menudata;
                            }
                        }
                    }  // End of While
          $retval .= '}';
        }


        /* GL MENU API */
        /* Check if any plugins have included a custom Menu function to generate any needed javascript for submenus */
        $result = DB_query("SELECT pi_name FROM {$_TABLES['plugins']} WHERE pi_enabled = 1");
        $nrows = DB_numRows($result);
        for ($i = 1; $i <= $nrows; $i++) {
            $A = DB_fetchArray($result);
            $function = 'plugin_nexmenuCreateMenus_' . $A['pi_name'];
            if (function_exists($function)) {
                $jscode = $function();
                $retval .= $jscode;
            }
        }

        if ($this->_linksPlugin) {
            $retval .= $this->_milonicSubLinkMenuCode('site');
        }

        $_BLOCK_TEMPLATE = $current_BLOCK_TEMPLATE;
        return $retval;
    }

    /* Links Plugin: Recursive function used to generate any needed submenus */
    private function _milonicSubLinkMenuCode($cid='site')
    {
        global $_CONF,$_TABLES,$CONF_NEXMENU;

        $retval = '';

        $sql = "SELECT cid,category FROM {$_TABLES['linkcategories']} WHERE pid='$cid' " . COM_getPermSQL( 'AND' );
        $qlinkcat = DB_query($sql);
        $numcategories = DB_numRows($qlinkcat);
        if ($numcategories > 0) {
            while (list($catid,$category) = DB_fetchArray($qlinkcat)) {
                $retval .= LB . 'with(milonic=new menuname("links-'.$catid.'")) {';
                    if ($this->_type == 'block') {
                        $retval .=  'style='. $this->_blockSubmenuStyle .';';
                    } else {
                        $retval .=  'style='. $this->_headerSubmenuStyle .';';
                    }
                    $sql = "SELECT title,lid FROM {$_TABLES['links']} WHERE cid='$catid' " . COM_getPermSQL( 'AND' );
                    $query = DB_query($sql);
                    while (list ($title,$lid) = DB_fetchArray ($query)) {
                        $url = $_CONF['site_url'].'/links/portal.php?what=link&item='.$lid;
                        $retval .= 'aI("text='.$title.';url='.$url.';");';
                    }
                    // Add links to any sub-menu's
                    $sql = "SELECT cid,category FROM {$_TABLES['linkcategories']} WHERE pid='$catid' " . COM_getPermSQL( 'AND' );
                    $query = DB_query($sql);
                    while (list ($cid,$category) = DB_fetchArray ($query)) {
                        $retval .= 'aI("text='.$category.';'.'showmenu=links-'.$cid.';");';
                    }
                    $retval .= '}' . LB;
                    $retval .= $this->_milonicSubLinkMenuCode($catid);
            }
        }
        return $retval;
    }



    /* Links plugin: Generate the links for the top level 'site' category */
    private function _milonicLinksPluginSiteLinks()
    {
        global $_CONF,$_TABLES,$CONF_NEXMENU;

            $sql = "SELECT title,lid FROM {$_TABLES['links']} WHERE cid='site' " . COM_getPermSQL( 'AND' );
            $query = DB_query($sql);
            while (list ($title,$lid) = DB_fetchArray ($query)) {
                $url = $_CONF['site_url'].'/links/portal.php?what=link&item='.$lid;
                $retval .= 'aI("text='.$title.';url='.$url.';");';
            }
            $sql = "SELECT cid,category FROM {$_TABLES['linkcategories']} WHERE pid='site' " . COM_getPermSQL( 'AND' );
            $qlinkcat = DB_query($sql);
            $numcategories = DB_numRows($qlinkcat);
            if ($numcategories > 0) {
                while (list($catid,$category) = DB_fetchArray($qlinkcat)) {
                    $retval .= 'aI("text='.$category.';'.'url='.$url.';'.$target.'showmenu=links-'.$catid.';");';
                }
            }
        return $retval;
    }


}

?>