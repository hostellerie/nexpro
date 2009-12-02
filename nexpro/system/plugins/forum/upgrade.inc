<?php


function forum_upgrade_232() {
    global $_TABLES;

    $_SQL = array();

    /* Drop these extra fields in case they still exist */
    $dropfields = array('img_width','img_height','topicnumchars','newtopic','newreply','mouseover','mouseout');
    $fields = DB_query("SHOW COLUMNS FROM {$_TABLES['gf_settings']}");
    while ($A = DB_fetchArray($fields)) {
        if (in_array($A['Field'],$dropfields)) {
            $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} DROP {$A['Field']}";
        }
    }

    // Version 2.3 to 2.3.2 added one field - Add if this field does not exist
    $fields = DB_query("SHOW COLUMNS FROM {$_TABLES['gf_userprefs']}");
    while ($A = DB_fetchArray($fields)) {
        if (in_array($A['Field'],array('enablenotify'))) {
            $fieldfound = true;
        }
    }
    if (!$fieldfound) {
        $_SQL[] = "ALTER TABLE {$_TABLES['gf_userprefs']} ADD enablenotify tinyint(1) DEFAULT '1' NOT NULL AFTER viewanonposts";
    }

    /* Rename this field */
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} CHANGE  `msgauto` `autorefresh` TINYINT(1) DEFAULT '1' NOT NULL ";

    /* Add these new fields after `allow_notify` */
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD post_htmlmode tinyint(1) DEFAULT '1' NOT NULL AFTER allow_notify";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD allow_userdatefmt tinyint(1) DEFAULT '0' NOT NULL AFTER post_htmlmode";

    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD refresh_delay tinyint(1) DEFAULT '0' NOT NULL AFTER autorefresh";

    /* Add these new fields after `postsperpage` */
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD messagesperpage int(4) DEFAULT '20' NOT NULL AFTER postsperpage";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD searchesperpage int(4) DEFAULT '20' NOT NULL AFTER messagesperpage";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD popular int(4) DEFAULT '0' NOT NULL AFTER searchesperpage";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD speedlimit int(1) DEFAULT '60' NOT NULL AFTER popular";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD use_spamxfilter tinyint(1) DEFAULT '1' NOT NULL AFTER speedlimit";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD use_smiliesplugin tinyint(1) DEFAULT '0' NOT NULL AFTER use_spamxfilter";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD use_pmplugin tinyint(1) DEFAULT '0' NOT NULL AFTER use_smiliesplugin";


    /* Add these new fields after `cb_where` */
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD cb_subjectsize tinyint(1) DEFAULT '40' NOT NULL AFTER cb_where";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD cb_numposts tinyint(1) DEFAULT '5' NOT NULL AFTER cb_subjectsize";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD sb_subjectsize tinyint(1) DEFAULT '20' NOT NULL AFTER cb_numposts";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD sb_numposts tinyint(1) DEFAULT '5' NOT NULL AFTER sb_subjectsize";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD sb_latestposts tinyint(1) DEFAULT '5' NOT NULL AFTER sb_numposts";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD min_comment_len tinyint(1) DEFAULT '5' NOT NULL AFTER sb_latestposts";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD min_name_len tinyint(1) DEFAULT '2' NOT NULL AFTER min_comment_len";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD min_subject_len tinyint(1) DEFAULT '2' NOT NULL AFTER min_name_len";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD html_newline tinyint(1) DEFAULT '0' NOT NULL AFTER min_subject_len";

    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD use_geshi_formatting tinyint(1) DEFAULT '0' NOT NULL AFTER glfilter";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} ADD edit_timewindow int(11) DEFAULT '300' NOT NULL AFTER speedlimit";

    /* Add new forum table fields */
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_forums']} ADD is_hidden tinyint(1) DEFAULT '0' NOT NULL AFTER grp_id";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_forums']} ADD is_readonly tinyint(1) DEFAULT '0' NOT NULL AFTER is_hidden";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_forums']} ADD no_newposts tinyint(1) DEFAULT '0' NOT NULL AFTER is_readonly";

    $_SQL[] = "ALTER TABLE {$_TABLES['gf_moderators']} ADD mod_uid mediumint(8) DEFAULT '0' NOT NULL AFTER mod_id";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_moderators']} ADD mod_groupid mediumint(8) DEFAULT '0' NOT NULL AFTER mod_uid";

    /* Add new userprefs field */
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_userprefs']} ADD notify_once tinyint(1) DEFAULT '0' NOT NULL AFTER showiframe";

    /* Execute SQL now to perform the upgrade */
    for ($i = 1; $i <= count($_SQL); $i++) {
        COM_errorLOG("Forum Plugin 2.5 update: Executing SQL => " . current($_SQL));
        DB_query(current($_SQL),'1');
        if (DB_error()) {
            COM_errorLog("SQL Error during Forum plugin update",1);
            return 1;
            break;
        }
        next($_SQL);
    }

    if (!DB_count($_TABLES['blocks'],'phpblockfn','phpblock_forum_menu')) {
        // Add new block definition for Forum Menu
        $fields = 'is_enabled,name,type,title,tid,blockorder,onleft,phpblockfn,group_id,owner_id,perm_owner,perm_group,perm_members,perm_anon';
        $sql = "INSERT INTO {$_TABLES['blocks']} ($fields) " ;
        $sql .= "VALUES (0, 'forum_menu', 'phpblock', 'Forum Menu', 'all', 0, 1, 'phpblock_forum_menu', 2,2,3,2,2,2)";
        DB_query($sql);
    }

    // Update the moderator records - now that we have a uid field
    $query = DB_query("SELECT mod_id,mod_username FROM {$_TABLES['gf_moderators']}");
    while ($A = DB_fetchArray($query)) {
        $mod_uid = DB_getItem($_TABLES['users'],'uid',"username='{$A['mod_username']}'");
        if ($mod_uid > 0) {
            DB_query("UPDATE {$_TABLES['gf_moderators']} SET mod_uid = $mod_uid WHERE mod_id={$A['mod_id']}");
        }
    }
    COM_errorLog("Success - Completed Forum plugin version 2.5 update",1);
    return 0;
}


function forum_upgrade_25() {
    global $_TABLES;

    $_SQL = array();

    /* Add new fields */
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_forums']} ADD topic_count mediumint(8) DEFAULT '0' NOT NULL AFTER no_newposts";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_forums']} ADD post_count mediumint(8) DEFAULT '0' NOT NULL AFTER topic_count";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_forums']} ADD last_post_rec mediumint(8) DEFAULT '0' NOT NULL AFTER post_count";
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_topic']} ADD last_reply_rec mediumint(8) DEFAULT '0' NOT NULL AFTER lastupdated";

    // Check if the field 'allow_htmlsig' exists - from a ver2.5 install
    $query = DB_query("SELECT * FROM {$_TABLES['gf_settings']} ");
    $numfields =  DB_numFields($query);
    $A = DB_fetchArray($query,false);
    $fieldname = DB_fieldName($query,1);
    if (array_key_exists('allow_htmlsig',$A)) {
        /* Rename this field */
        $_SQL[] = "ALTER TABLE {$_TABLES['gf_settings']} CHANGE  `allow_htmlsig` `post_htmlmode` TINYINT(1) DEFAULT '1' NOT NULL ";
    }

    // Disable the convert new lines to BR tags - may not even be needed anymore but will add extra newlines if enabled.
    $_SQL[] = "UPDATE {$_TABLES['gf_settings']} set html_newline=0";
    /* Execute SQL now to perform the upgrade */
    for ($i = 1; $i <= count($_SQL); $i++) {
        COM_errorLOG("Forum Plugin 2.6 update: Executing SQL => " . current($_SQL));
        DB_query(current($_SQL),'1');
        if (DB_error()) {
            COM_errorLog("SQL Error during Forum plugin update",1);
            return 1;
            break;
        }
        next($_SQL);
    }

    COM_errorLog("Success - Completed Forum plugin version 2.6 update",1);
    return 0;

}

function forum_upgrade_30() {
    global $_TABLES;

    $_SQL = array();

    $_SQL[] = "CREATE TABLE IF NOT EXISTS {$_TABLES['gf_bookmarks']} (
      `uid` mediumint(8) NOT NULL,
      `topic_id` int(11) NOT NULL,
      `pid` int(11) NOT NULL default '0',
      KEY `topic_id` (`topic_id`),
      KEY `pid` (`pid`),
      KEY `uid` (`uid`)
    ) TYPE=MyISAM ;";


    $_SQL[] = "CREATE TABLE IF NOT EXISTS {$_TABLES['gf_attachments']} (
      `id` int(11) NOT NULL auto_increment,
      `topic_id` int(11) NOT NULL,
      `repository_id` int(11) default NULL,
      `filename` varchar(255) NOT NULL,
      `tempfile` tinyint(1) NOT NULL default '0',
      `show_inline` tinyint(1) NOT NULL default '0',
      PRIMARY KEY  (`id`),
      KEY `topic_id` (`topic_id`)
    ) Type=MyISAM  AUTO_INCREMENT=1 ;";

    // Set default access to use attachments to be the Root group
    $_SQL[] = "ALTER TABLE {$_TABLES['gf_forums']} ADD use_attachment_grpid mediumint(8) DEFAULT '1' NOT NULL AFTER grp_id";

    /* Execute SQL now to perform the upgrade */
    for ($i = 1; $i <= count($_SQL); $i++) {
        COM_errorLOG("Forum Plugin 3.05 update: Executing SQL => " . current($_SQL));
        DB_query(current($_SQL),'1');
        if (DB_error()) {
            COM_errorLog("SQL Error during Forum plugin update",1);
            return 1;
            break;
        }
        next($_SQL);
    }

    COM_errorLog("Success - Completed Forum plugin version 3.0 update",1);
    return 0;

}


function forum_upgrade_320() {
    global $_CONF,$_TABLES,$CONF_FORUM,$_DB_table_prefix;

    $CONF_FORUM = array();

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    $plugin_path = $_CONF['path'] . 'plugins/forum/';

    require_once $plugin_path . 'install_defaults.php';


    /* Need to rename table names so that GL 1.6.0 autoinstaller feature will work
     * Table name needs to match the $_TABLES array key
    */
    $OLD_TABLES['gf_userprefs']    = $_DB_table_prefix . 'forum_userprefs';
    $OLD_TABLES['gf_topic']        = $_DB_table_prefix . 'forum_topic';
    $OLD_TABLES['gf_categories']   = $_DB_table_prefix . 'forum_categories';
    $OLD_TABLES['gf_forums']       = $_DB_table_prefix . 'forum_forums';
    $OLD_TABLES['gf_settings']     = $_DB_table_prefix . 'forum_settings';
    $OLD_TABLES['gf_watch']        = $_DB_table_prefix . 'forum_watch';
    $OLD_TABLES['gf_moderators']   = $_DB_table_prefix . 'forum_moderators';
    $OLD_TABLES['gf_banned_ip']    = $_DB_table_prefix . 'forum_banned_ip';
    $OLD_TABLES['gf_log']          = $_DB_table_prefix . 'forum_log';
    $OLD_TABLES['gf_userinfo']     = $_DB_table_prefix . 'forum_userinfo';
    $OLD_TABLES['gf_attachments']  = $_DB_table_prefix . 'forum_attachments';
    $OLD_TABLES['gf_bookmarks']    = $_DB_table_prefix . 'forum_bookmarks';

    foreach ($OLD_TABLES as $key => $oldtablename) {
        $sql = "RENAME TABLE {$oldtablename} TO {$_TABLES[$key]}";
        DB_query("RENAME TABLE {$oldtablename} TO {$_TABLES[$key]}"); ;
    }

    DB_query("ALTER TABLE {$_TABLES['gf_userprefs']} ADD `notification_format` VARCHAR( 8 ) NOT NULL DEFAULT 'text'");      
    
    if ( isset($_TABLES['gf_settings']) ) {
        $result = DB_query("SELECT * FROM {$_TABLES['gf_settings']}",1);
        $numRows = DB_numRows($result);
        if ( $numRows > 0 ) {
            $A = DB_fetchArray($result);

            if ( isset($A['registrationrequired']) )
                $CONF_FORUM['registration_required']  = $A['registrationrequired'];
            if ( isset($A['registerpost']))
                $CONF_FORUM['registered_to_post']     = $A['registerpost'];
            if ( isset($A['allowhtml']) )
                $CONF_FORUM['allow_html']             = $A['allowhtml'];
            if ( isset($A['post_htmlmode']) )
                $CONF_FORUM['post_htmlmode']          = $A['post_htmlmode'];
            if ( isset($A['glfilter']) )
                $CONF_FORUM['use_glfilter']           = $A['glfilter'];
            if ( isset($A['use_geshi_formatting']) )
                $CONF_FORUM['use_geshi']              = $A['use_geshi_formatting'];
            if ( isset($A['censor']) )
                $CONF_FORUM['use_censor']             = $A['censor'];
            if ( isset($A['showmood']) )
                $CONF_FORUM['show_moods']             = $A['showmood'];
            if ( isset($A['allowsmilies']) )
                $CONF_FORUM['allow_smilies']          = $A['allowsmilies'];
            if ( isset($A['allow_notify']) )
                $CONF_FORUM['allow_notification']     = $A['allow_notify'];

            if ( isset($A['allow_userdatefmt'] ) )
                $CONF_FORUM['allow_user_dateformat']  = $A['allow_userdatefmt'];
            if ( isset($A['showiframe']) )
                $CONF_FORUM['show_topicreview']       = $A['showiframe'];
            if ( isset($A['autorefresh']) )
                $CONF_FORUM['use_autorefresh']        = $A['autorefresh'];
            if ( isset($A['refresh_delay'] ) )
                $CONF_FORUM['autorefresh_delay']      = $A['refresh_delay'];
            if ( isset($A['viewtopicnumchars']) )
                $CONF_FORUM['show_subject_length']    = $A['viewtopicnumchars'];
            if ( isset($A['topicsperpage']) )
                $CONF_FORUM['show_topics_perpage']    = $A['topicsperpage'];
            if ( isset($A['postsperpage']) )
                $CONF_FORUM['show_posts_perpage']     = $A['postsperpage'];
            if ( isset($A['messagesperpage']) )
                $CONF_FORUM['show_messages_perpage']  = $A['messagesperpage'];
            if ( isset($A['searchesperpage']) )
                $CONF_FORUM['show_searches_perpage']  = $A['searchesperpage'];
            if ( isset($A['popular']) )
                $CONF_FORUM['views_tobe_popular']     = $A['popular'];
            if ( isset($A['html_newline']) )
                $CONF_FORUM['convert_break']          = $A['html_newline'];
            if ( isset($A['min_comment_len']) )
                $CONF_FORUM['min_comment_length']     = $A['min_comment_len'];
            if ( isset($A['min_name_len']) )
                $CONF_FORUM['min_username_length']    = $A['min_name_len'];
            if ( isset($A['min_subject_len']) )
                $CONF_FORUM['min_subject_length']     = $A['min_subject_len'];
            if ( isset($A['speedlimit']) )
                $CONF_FORUM['post_speedlimit']        = $A['speedlimit'];
            if ( isset($A['use_smilieplugin']) )
                $CONF_FORUM['use_smilies_plugin']     = $A['use_smiliesplugin'];
            if ( isset($A['use_pmplugin']) )
                $CONF_FORUM['use_pm_plugin']          = $A['use_pmplugin'];
            if ( isset($A['use_spamxfilter']) )
                $CONF_FORUM['use_spamx_filter']       = $A['use_spamxfilter'];
            if ( isset($A['cb_enable']) )
                $CONF_FORUM['show_centerblock']       = $A['cb_enable'];
            if ( isset($A['cb_homepage']) )
                $CONF_FORUM['centerblock_homepage']   = $A['cb_homepage'];
            if ( isset($A['cb_where']) )
                $CONF_FORUM['centerblock_where']      = $A['cb_where'];
            if ( isset($A['cb_subjectsize']) )
                $CONF_FORUM['cb_subject_size']        = $A['cb_subjectsize'];
            if ( isset($A['cb_numposts']) )
                $CONF_FORUM['centerblock_numposts']   = $A['cb_numposts'];
            if ( isset($A['sb_subjectsize']) )
                $CONF_FORUM['sb_subject_size']        = $A['sb_subjectsize'];
            if ( isset($A['sb_latestposts']) )
                $CONF_FORUM['sb_latestpostonly']      = $A['sb_latestposts'];
            if ( isset($A['sb_numposts']) )
                $CONF_FORUM['sideblock_numposts']     = $A['sb_numposts'];
            if ( isset($A['edit_timewindow']) )
                $CONF_FORUM['allowed_editwindow']     = $A['edit_timewindow'];
            if ( isset($A['level1']) )
                $CONF_FORUM['level1']                 = $A['level1'];
            if ( isset($A['level2']) )
                $CONF_FORUM['level2']                 = $A['level2'];
            if ( isset($A['level3']) )
                $CONF_FORUM['level3']                 = $A['level3'];
            if ( isset($A['level4']) )
                $CONF_FORUM['level4']                 = $A['level4'];
            if ( isset($A['level5']) )
                $CONF_FORUM['level5']                 = $A['level5'];
            if ( isset($A['level1name']) )
                $CONF_FORUM['level1name']             = $A['level1name'];
            if ( isset($A['level2name']) )
                $CONF_FORUM['level2name']             = $A['level2name'];
            if ( isset($A['level3name']) )
                $CONF_FORUM['level3name']             = $A['level3name'];
            if ( isset($A['level4name']) )
                $CONF_FORUM['level4name']             = $A['level4name'];
            if ( isset($A['level5name']) )
                $CONF_FORUM['level5name']             = $A['level5name'];
        }
    }  
    
    if (plugin_initconfig_forum()) {
        COM_errorLog('Forum version 3.1 upgrade: Successfully initialized online config settings');
        return 0;
    } else {
        COM_errorLog('Forum version 3.1 upgrade error: Error during online config initialization');
        return 1;
    }

}

?>