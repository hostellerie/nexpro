<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Forum Plugin for Geeklog based framework CMS applications                 |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// | $Id::                                                                     |
// | This file implements the necessary Geeklog Plugin API functions and       |
// | includes some common Forum functions                                      |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2002 - 2009 the following authors:                          |
// | Blaine Lang                 -    blaine@portalparts.com                   |
// +---------------------------------------------------------------------------+
// | FORUM Plugin Authors                                                      |
// | Developed by Blaine and Matthew                                           |
// | Blaine Lang,    contact: blaine@portalparts.com   www.portalparts.com     |
// | Matthew DeWyer, contact: matt@mycws.com           www.cweb.ws             |
// | Prototype & Concept :  Mr.GxBlock of www.gxblock.com                      |
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

$langfile = $_CONF['path'] . 'plugins/forum/language/' . $_CONF['language'] . '.php';
if (file_exists ($langfile)) {
    include_once ($langfile);
} else {
    include_once ($_CONF['path'] . 'plugins/forum/language/english.php');
}

include_once ($_CONF['path'] . 'plugins/forum/autouninstall.php');
require_once $_CONF['path'].'plugins/forum/forum.php';
require_once ($_CONF['path_system'] . 'lib-portalparts.php');
$pi_version = floatval (DB_getItem($_TABLES['plugins'], 'pi_version', "pi_name='forum'"));

// How many messages to show on the Most Popular page
$CONF_FORUM['popular_limit'] = 20;
// How many lines to show on one page in the search results
$CONF_FORUM['show_search_perpage'] = 15;
// How many users to show on one page in the memberlist results
$CONF_FORUM['show_members_perpage'] = 100;
// View Anonymous Posts - registed users can set this false
$CONF_FORUM['show_anonymous_posts'] = 1;
// Only send Notification once - even if more posts are created since your last visit
$CONF_FORUM['notify_once'] = 1;

if ( !isset($CONF_FORUM['enable_user_rating_system']) ) {
    $CONF_FORUM['enable_user_rating_system'] = 0;
}

if ( !empty($_USER['uid']) && $_USER['uid'] > 1 ) {
    $result = DB_query("SELECT * FROM {$_TABLES['gf_userprefs']} where uid = '{$_USER['uid']}'",1);
    if ( DB_numRows($result) > 0 ) {
        $userprefs = DB_fetchArray($result);
        $CONF_FORUM['show_topics_perpage']        = $userprefs['topicsperpage'];
        $CONF_FORUM['show_posts_perpage']         = $userprefs['postsperpage'];
        $CONF_FORUM['popular_limit']              = $userprefs['popularlimit'];
        $CONF_FORUM['show_members_perpage']       = $userprefs['membersperpage'];
        $CONF_FORUM['show_search_perpage']        = $userprefs['searchlines'];
        $CONF_FORUM['show_topicreview']           = $userprefs['showiframe'];
        $CONF_FORUM['show_anonymous_posts']       = $userprefs['viewanonposts'];
        $CONF_FORUM['notify_once']                = $userprefs['notify_once'];
    }
}
$CONF_FORUM['use_glfilter'] = 1; // always use the filter...

$CONF_FORUM['leftblocks'] = array ('forum_menu');
$CONF_FORUM['leftblocks'] = ppGetUserBlocks($CONF_FORUM['leftblocks']);

if ( !isset($CONF_FORUM['showtopic_review_order']) ) {
    $CONF_FORUM['showtopic_review_order'] = 'DESC';
}

if( empty( $LANG_CHARSET )) {
    $CONF_FORUM['charset'] = $_CONF['default_charset'];
    if( empty( $charset )) {
        $CONF_FORUM['charset'] = 'iso-8859-1';
    }
} else {
    $CONF_FORUM['charset'] = $LANG_CHARSET;
}

/**
* Returns the items for this plugin that should appear on the main menu
*/
function plugin_getmenuitems_forum()
{
    global $CONF_FORUM,$_CONF,$LANG_GF00;
    if ($CONF_FORUM['registration_required'] == 0 || SEC_hasRights('forum.user')) {
        $menuitems["$LANG_GF00[pluginlabel]"] = "$_CONF[site_url]/forum/index.php";
        return $menuitems;
    }
}


function plugin_autotags_forum($op,$content='',$autotag='') {
    global $_CONF;

    if ($op == 'tagname' ) {
        return 'forum';
    } elseif ($op == 'parse') {
        if ($autotag['parm2'] == '')  $autotag['parm2'] = 'here';
        $filelink = '<a href="'.$_CONF['site_url'].'/forum/viewtopic.php?showtopic='.$autotag['parm1'].'">'.$autotag['parm2'].'</a>';
        $content = str_replace($autotag['tagstr'],$filelink,$content);
        return $content;
    }
}


/**
* This will put an option for forum admin in the command and control block on moderation.php
*
*/
function plugin_cclabel_forum()
{
    global $_CONF;
    if (SEC_hasRights('forum.edit')) {
        return array('Forum',$_CONF['site_admin_url'] . "/plugins/forum/index.php",$_CONF['site_url'] . '/forum/images/forum.png');
    }
}

/**
* returns the administrative option for this plugin
*
*/
function plugin_getadminoption_forum()
{
    global $_TABLES, $_CONF;

    if (SEC_hasRights('forum.edit')) {
        $numtopics = DB_getITEM($_TABLES['gf_topic'],"count(*)");
        return array('Forum', $_CONF['site_admin_url'] . '/plugins/forum/index.php', $numtopics);
    }

}

/**
* Returns the user menuitem option for this plugin
* Only one menu item can be returned.
*/
function plugin_getuseroption_forum()
{
    global $CONF_FORUM, $_CONF, $LANG_GF00;

    if ($CONF_FORUM['registration_required'] == 0 || SEC_hasRights('forum.user')) {
        return array($LANG_GF00['useradminmenu'], $_CONF['site_url'] . '/forum/userprefs.php', 0);
    }
}


function plugin_user_create_forum ($uid)
{
    global $_TABLES;

    DB_query ("INSERT INTO {$_TABLES['gf_userinfo']} (uid) VALUES ('{$uid}')");
}

/**
* Called if admin deletes a user - Removes any moderator and Watch Records for user
*/
function plugin_user_delete_forum ($uid)
{
    global $_TABLES;

    $username = DB_getItem($_TABLES['users'], "username", "uid=$uid");
    DB_query("DELETE FROM {$_TABLES['gf_moderators']} WHERE mod_username='$username'");
    DB_query("DELETE FROM {$_TABLES['gf_watch']} WHERE uid ='$uid'");
    DB_query("DELETE FROM {$_TABLES['gf_userinfo']} WHERE uid ='$uid'");
    DB_query("DELETE FROM {$_TABLES['gf_userprefs']} WHERE uid ='$uid'");
    DB_query("DELETE FROM {$_TABLES['gf_log']} WHERE uid ='$uid'");
    DB_query("UPDATE {$_TABLES['gf_topic']} SET uid = 1 WHERE uid = '$uid'");
}

function plugin_profileextrassave_forum()
{
    global $_CONF,$_USER, $_POST, $_TABLES;

    require_once ($_CONF['path_html'] . 'forum/include/gf_format.php');
    $uid = $_USER['uid'];
    if ($uid > 1) {
        $location   = gf_preparefordb($_POST['forum_location'],'html');
        $aim        = gf_preparefordb($_POST['forum_aim'],'html');
        $icq        = gf_preparefordb($_POST['forum_icq'],'html');
        $yim        = gf_preparefordb($_POST['forum_yim'],'html');
        $msnm       = gf_preparefordb($_POST['forum_msnm'],'html');
        $interests  = gf_preparefordb($_POST['forum_interests'],'html');
        $occupation = gf_preparefordb($_POST['forum_occupation'],'html');
        DB_save ($_TABLES['gf_userinfo'], "uid,location,aim,yim,icq,msnm,interests,occupation",
                 "'$uid','$location','$aim','$yim','$icq','$msnm','$interests','$occupation'");
   }
}


function plugin_profilevariablesedit_forum($uid, &$template)
{
    global $_TABLES, $LANG_GF04;

    $result = DB_query ("SELECT uid,location,aim,yim,icq,yim,msnm,interests,occupation FROM {$_TABLES['gf_userinfo']} WHERE uid = $uid");
    $A = DB_fetchArray ($result);
    $template->set_var('forum_location', $A['location']);
    $template->set_var('forum_aim', $A['aim']);
    $template->set_var('forum_icq', $A['icq']);
    $template->set_var('forum_yim', $A['yim']);
    $template->set_var('forum_msnm', $A['msnm']);
    $template->set_var('forum_interests', $A['interests']);
    $template->set_var('forum_occupation', $A['occupation']);

    $template->set_var ('lang_forum', $LANG_GF04['label_forum']);
    $template->set_var ('lang_location', $LANG_GF04['label_location']);
    $template->set_var ('lang_aim', $LANG_GF04['label_aim']);
    $template->set_var ('lang_icq', $LANG_GF04['label_icq']);
    $template->set_var ('lang_yim', $LANG_GF04['label_yim']);
    $template->set_var ('lang_msnm', $LANG_GF04['label_msnm']);
    $template->set_var ('lang_interests', $LANG_GF04['label_interests']);
    $template->set_var ('lang_occupation', $LANG_GF04['label_occupation']);
}

function plugin_profilevariablesdisplay_forum ($uid, &$template)
{

    global $_TABLES, $_CONF, $LANG_GF02,$LANG_GF04;
    $postlimit="10";        // How many posts you want displayed

    $query = DB_query ("SELECT id,date,forum,subject FROM {$_TABLES['gf_topic']} WHERE uid = {$uid} ORDER BY date DESC LIMIT 100");
    $numposts = 0;

    while (list($postid,$date,$forum_id,$subject) = DB_fetchArray($query))  {
        $grp_id = DB_getItem($_TABLES['gf_forums'],'grp_id',"forum_id='$forum_id'");
        $groupname = DB_getItem($_TABLES['groups'],'grp_name',"grp_id='$grp_id'");
        if (SEC_inGroup($groupname) OR $grp_id == 2) {
            $numposts++;
            $postdate = COM_getUserDateTimeFormat ($date);
            $template->set_var ('row_number', $numposts . '.');
            $template->set_var ('story_begin_href', '<a href="' . $_CONF['site_url'] . '/forum/viewtopic.php?showtopic=' . $postid . '">');
            $template->set_var ('story_title', $subject);
            $template->set_var ('story_end_href', '</a>');
            $template->set_var ('story_date', $postdate[0]);
            $template->parse ('forum_post_row', 'strow', true);
            if ($numposts >= $postlimit) {
                break;
            }
        }
    }

    if ($numposts == 0) {
        $template->set_var ('forum_post_row', '<tr><td>' .$LANG_GF02['msg155']. '</td></tr>');
    }

    $count = DB_count ($_TABLES['gf_topic'], 'uid', $uid);
    $template->set_var ('forum_lang_number_posts', $LANG_GF02['msg156']);
    $template->set_var ('forum_number_posts', $count);

    $template->set_var ('forum_start_block_last10posts', COM_startBlock
        ( $LANG_GF02['msg158'] . DB_getItem ($_TABLES['users'],
           'username', "uid = $uid")));
    $template->set_var ('forum_headline_last10posts', $LANG_GF02['msg157']);

    /* Display Instant Messaging Handles */
    $result = DB_query ("SELECT uid,location,aim,yim,icq,yim,msnm,interests,occupation FROM {$_TABLES['gf_userinfo']} WHERE uid = $uid");
    $A = DB_fetchArray ($result);
    $template->set_var('forum_location', $A['location']);
    $template->set_var('forum_aim', $A['aim']);
    $template->set_var('forum_icq', $A['icq']);
    $template->set_var('forum_yim', $A['yim']);
    $template->set_var('forum_msnm', $A['msnm']);
    $template->set_var('forum_interests', $A['interests']);
    $template->set_var('forum_occupation', $A['occupation']);

    $template->set_var ('lang_forum', $LANG_GF04['label_forum']);
    $template->set_var ('lang_location', $LANG_GF04['label_location']);
    $template->set_var ('lang_aim', $LANG_GF04['label_aim']);
    $template->set_var ('lang_icq', $LANG_GF04['label_icq']);
    $template->set_var ('lang_yim', $LANG_GF04['label_yim']);
    $template->set_var ('lang_msnm', $LANG_GF04['label_msnm']);
    $template->set_var ('lang_interests', $LANG_GF04['label_interests']);
    $template->set_var ('lang_occupation', $LANG_GF04['label_occupation']);
}

function plugin_statssummary_forum() {
    global $_CONF, $_TABLES, $LANG_GF00;

    // This shows in the summary box
    $total_items = DB_count($_TABLES['gf_topic']);      // Total number of Forum Posts
    $summary_label = $LANG_GF00['statslabel'];
    $retval[] = $summary_label;
    $retval[] = $total_items;
    return $retval;
}

/**
* shows the statistics for the plugin when stats.php is called.
* If $showsitestats is 1 then we are to only print the overall stats in the 'site statistics box'
* otherwise we show the detailed stats for the photo album
*
* @showsitestate        int         Flag to let us know which stats to get
*/
function plugin_showstats_forum($showsitestats)
{
    global $_CONF, $_TABLES, $LANG_GF00,$LANG_GF01;

    $stat_templates = new Template($_CONF['path_layout'] . 'stats');
    $stat_templates->set_file(array('itemstats'=>'itemstatistics.thtml',
                            'statrow'=>'singlestat.thtml'));
    $retval='';
    if ($showsitestats == 1) {
        // This shows in the summary box
        $total_pages = DB_count($_TABLES['gf_topic']);      // Total number of Forum Posts
        $summary_label = $LANG_GF00['statslabel'];        // Label to display

        $retval = "<table border='0' width='100%' cellspacing='0' cellpadding='0'>";
        $retval .= "<tr><td>$summary_label</td>";
        $retval .= "<td align='right'>" . $total_pages . "&nbsp;&nbsp;</td></tr></table>";
    } else {
        $header_arr = array(
            array('text' => $LANG_GF01['TOPICSUBJECT'], 'field' => 'topicsubject','header_class' => 'stats-header-title'),
            array('text' => $LANG_GF01['VIEWS'], 'field' => 'views','header_class' => 'stats-header-count', 'field_class' => 'stats-list-count'),
        );
        $data_arr = array();
        $text_arr = array('has_menu'     => false,
                          'title'        => $LANG_GF00['statsheading1'],
        );
        $result = DB_query("SELECT forum,subject,views,id FROM {$_TABLES['gf_topic']} where pid='0' ORDER BY views desc");
        $nrows  = DB_numRows($result);
        if ($nrows > 0) {
            $displaycount = 0;
            while (list ($forum,$subject, $views,$id) = DB_fetchARRAY($result)) {
                $forum_id = DB_getItem($_TABLES['gf_topic'],'forum',"id='$id'");
                $grp_id = DB_getItem($_TABLES['gf_forums'],'grp_id',"forum_id='$forum_id'");
                $groupname = DB_getItem($_TABLES['groups'],'grp_name',"grp_id='$grp_id'");
                if (SEC_inGroup($groupname) OR $grp_id == 2) {
                    $url = $_CONF['site_url']. "/forum/viewtopic.php?showtopic=$id";
                    $S['topicsubject'] = '<a href="' . $url . '">' . $subject . '</a>';
                    $S['views']  = $views;
                    $data_arr[$displaycount] = $S;
                    $displaycount++;
                    if ($displaycount > 10) {
                        break;
                    }
                }
            }
            $retval .= ADMIN_simpleList("", $header_arr, $text_arr, $data_arr);
        } else {
            $retval .= $LANG_GF00['statsheading3'];
        }
        $header_arr = array(
            array('text' => $LANG_GF01['TOPICSUBJECT'], 'field' => 'topicsubject','header_class' => 'stats-header-title'),
            array('text' => $LANG_GF01['REPLIES'], 'field' => 'replies','header_class' => 'stats-header-count', 'field_class' => 'stats-list-count'),
        );
        $data_arr = array();
        $text_arr = array('has_menu'     => false,
                          'title'        => $LANG_GF00['statsheading2'],
        );
        $result = DB_query("SELECT forum,subject,replies,id FROM {$_TABLES['gf_topic']} where pid='0' ORDER BY replies desc");
        $nrows  = DB_numRows($result);
        if ($nrows > 0) {
            $stat_templates->set_var('item_label',$LANG_GF01['TOPICSUBJECT']);
            $stat_templates->set_var('stat_name',$LANG_GF01['REPLIES']);
            $displaycount=0;
            while (list ($forum,$subject, $replies,$id) = DB_fetchARRAY($result)) {
                $forum_id = DB_getItem($_TABLES['gf_topic'],'forum',"id='$id'");
                $grp_id = DB_getItem($_TABLES['gf_forums'],'grp_id',"forum_id='$forum_id'");
                $groupname = DB_getItem($_TABLES['groups'],'grp_name',"grp_id='$grp_id'");
                if (SEC_inGroup($groupname) OR $grp_id == 2) {
                    $url = $_CONF['site_url']. "/forum/viewtopic.php?showtopic=$id";
                    $url = $_CONF['site_url']. "/forum/viewtopic.php?showtopic=$id";
                    $S['topicsubject'] = '<a href="' . $url . '">' . $subject . '</a>';
                    $S['replies']  = $replies;
                    $data_arr[$displaycount] = $S;
                    $displaycount++;
                    if ($displaycount > 10) {
                        break;
                    }
                }
            }
            $retval .= ADMIN_simpleList("", $header_arr, $text_arr, $data_arr);
        } else {
            $retval .= $LANG_GF00['statsheading3'];
        }
    }
    return $retval;
}


/**
* Geeklog is asking us to provide any new items that show up in the type drop-down
* on search.php.  Let's let users search the Filelistings in the Filemgmt Plugin
*
*/

function plugin_searchtypes_forum()
{
    global $LANG_GF00;

    $tmp['forum'] = $LANG_GF00['searchlabel'];
    return $tmp;
}


/**
* This searches for forum posts matching the user query and returns an object
* back to search.php where it will be formated and printed.
*
* @param    string  $query      Keywords user is looking for
* @param    date    $datestart  Start date to get results for
* @param    date    $dateend    End date to get results for
* @param    string  $topic      The topic they were searching in
* @param    string  $type       Type of items they are searching, or 'all' (deprecated)
* @param    int     $author     Get all results by this author
* @param    string  $keyType    search key type: 'all', 'phrase', 'any'
* @param    int     $page       page number of current search (deprecated)
* @param    int     $perpage    number of results per page (deprecated)
* @return   object              search result object
*
*/
function plugin_dopluginsearch_forum($query, $datestart, $dateend, $topic, $type, $author, $keyType, $page, $perpage)
{
    global $_TABLES, $LANG_GF00;

    // Make sure the query is SQL safe
    $query = trim(addslashes($query));

    $usergroups = implode(',',array_values(SEC_getUserGroups()));   

    $sql = "SELECT id, uid, date, subject AS title, comment AS description, views AS hits, ";
    $sql .= "CONCAT('/forum/viewtopic.php?showtopic=', id) AS url ";
    $sql .= "FROM {$_TABLES['gf_topic']} a ";
    $sql .= "LEFT JOIN {$_TABLES['gf_forums']} b on b.forum_id=a.forum ";
    $sql .= "WHERE b.forum_id in ($usergroups) ";

    if (!empty($datestart) && !empty($dateend))
    {
        $delim = substr($datestart, 4, 1);
        if (!empty($delim))
        {
            $DS = explode($delim, $datestart);
            $DE = explode($delim, $dateend);
            $startdate = mktime(0,0,0,$DS[1],$DS[2],$DS[0]);
            $enddate = mktime(23,59,59,$DE[1],$DE[2],$DE[0]);
            $sql .= "AND (date BETWEEN '$startdate' AND '$enddate') ";
        }
    }
    if (!empty ($author)) {
        $sql .= "AND (uid = '$author') ";
    }

    $search = new SearchCriteria('forum', $LANG_GF00['searchlabel']);
    $columns = array('title' => 'subject', 'comment');
    list($sql,$ftsql) = $search->buildSearchSQL($keyType, $query, $columns, $sql);
    $search->setSQL($sql);
    $search->setFTSQL($ftsql);
    $search->setRank(3);

    return $search;
}


/* RSS FEED Related API functions */

function plugin_getfeednames_forum ()
{
    global $_TABLES;

    $feeds = array ();
    $result = DB_query ("SELECT forum_id,forum_name FROM {$_TABLES['gf_forums']} ORDER BY forum_order");
    $num = DB_numRows ($result);

    if ($num > 0) {
        $feeds[] = array ('id' => '0', 'name' => 'all forums');
    }

    for ($i = 0; $i < $num; $i++) {
        $A = DB_fetchArray ($result);
        $feeds[] = array ('id' => $A['forum_id'], 'name' => $A['forum_name']);
    }

    return $feeds;
}

function forum_buildFeedsSql ($forumID, $limits)
{
    global $pi_version;

    $where = '';
    if ($pi_version < 2.6) {
        return '';
    }

    $groups = array ();
    $usergroups = SEC_getUserGroups(1);
    foreach ($usergroups as $group) {
        $groups[] = $group;
    }
    $grouplist = implode(',',$groups);

    if ($forumID > 0) {
        $where = " WHERE forum=$forumID AND no_newposts=0 ";
    } else {
        $where = " WHERE no_newposts=0 ";
    }

    if (!empty($grouplist)) {
		$where .= "AND forum.grp_id IN ($grouplist) ";
    }
    $limitsql = '';
    if (!empty ($limits)) {
        if (substr ($limits, -1) == 'h') { // last xx hours
            $limitsql = '';
            $hours = substr ($limits, 0, -1);
            if (!empty ($where)) {
                $where .= ' AND ';
            }
            $where .= "date >= DATE_SUB(NOW(),INTERVAL $hours HOUR) ORDER BY date DESC";
        } else {
            $limitsql = ' ORDER BY date DESC LIMIT ' . $limits;
        }
    }
    else
    {
        $limitsql = ' ORDER BY date DESC LIMIT 10';
    }

    $sql = $where . $limitsql;

    return $sql;
}

function plugin_getfeedcontent_forum ($feed, &$link, &$update)
{
    global $_CONF, $_TABLES, $CONF_FORUM, $LANG_GF01, $pi_version;

    $content = array();
    $lids = array();
    $ids = array();

    if ($pi_version < 2.6) {
        return $content;
    }

    $allow_smilies = $CONF_FORUM['allow_smilies'];
    $CONF_FORUM['allow_smilies']=0;

    require_once ($_CONF['path_html'] . 'forum/include/gf_format.php');
    if( !function_exists( 'str_ireplace' ))
    {
        require_once( 'PHP/Compat.php' );
        PHP_Compat::loadFunction( 'str_ireplace' );
    }
    require_once ($_CONF['path_html'] . 'forum/include/bbcode/stringparser_bbcode.class.php');

    $result = DB_query ("SELECT topic,limits,content_length FROM {$_TABLES['syndication']} WHERE fid = $feed");
    $F = DB_fetchArray ($result);

    $sql = "SELECT topic.id,topic.forum,topic.subject,topic.uid,topic.name,topic.comment,topic.date,topic.postmode,forum.forum_name,forum.grp_id ";
    $sql .= "FROM {$_TABLES['gf_topic']} topic LEFT JOIN {$_TABLES['gf_forums']} forum ON topic.forum=forum.forum_id ";
    $sql .= forum_buildFeedsSql ($F['topic'], $F['limits']);
    $result = DB_query ($sql);

    while ( $A=DB_fetchArray($result) ) {
        $preface = '';
        if ($F['content_length'] == 0) {
            $comment = '';
        } elseif ($F['content_length'] > 1) {
            $comment = substr($A['comment'],0,$F['content_length']);
        } else {
            $comment = $A['comment'];
            $preface = $LANG_GF01['BY'] . ' ';
            if ( $A['uid'] > 1 ) {
                $preface .= '<a href="' . $_CONF['site_url'] . '/users.php?mode=profile&amp;uid=' . $A['uid'] . '">' . $A['name'] . '</a>';
            } else {
                $preface .= $A['name'];
            }
            $preface .= '<br /><br />';
        }
        // Handle Pre ver 2.5 quoting and New Line Formatting - consider adding this to a migrate function
        if ($CONF_FORUM['pre2.5_mode']) {
            if ( stristr($comment,'[code') == false ) {
                $comment = str_replace('<pre>','[code]',$comment);
                $comment = str_replace('</pre>','[/code]',$comment);
            }
            $comment = str_replace(array("<br />\r\n","<br />\n\r","<br />\r","<br />\n"), '<br />', $comment );
            $comment = preg_replace("/\[QUOTE\sBY=\s(.+?)\]/i","[QUOTE] Quote by $1:",$comment);
            /* Reformat code blocks - version 2.3.3 and prior */
            $comment = str_replace( '<pre class="forumCode">', '[code]', $comment );
            $comment = preg_replace("/\[QUOTE\sBY=(.+?)\]/i","[QUOTE] Quote by $1:",$comment);
        }

        $comment = gf_formatTextBlock($comment,$A['postmode'],'preview');

        $comment = str_replace('{','&#123;',$comment);
        $comment = str_replace('}','&#125;',$comment);

        // we don't have a stylesheet in the news feed, so replace our div with the style...
        $comment = str_replace('<div class="quotemain">','<div style="border: 1px dotted #000;border-left: 4px solid #8394B2;color:#465584;  padding: 4px;  margin: 5px auto 8px auto;">',$comment);

        $desc = $preface . $comment;

        $link = "{$_CONF['site_url']}/forum/viewtopic.php?showtopic={$A['id']}";

        $content[] = array ('title'     => $A['forum_name'] . ' :: ' . $A['subject'],
                            'summary'   => $desc,
                            'text'      => $desc,
                            'link'      => $link,
                            'uid'       => $A['uid'],
                            'date'      => $A['date'],
                            'format'    => $A['postmode'],
                           );
        $ids[] = $A['id'];
    }

    if ($F['topic'] == 0) {
        $link = "{$_CONF['site_url']}/forum/index.php";
    } else {
        $link = "{$_CONF['site_url']}/forum/index.php.php?forum={$F['topic']}";
    }

    if (count($ids) > 0 ) {
        $update = implode (',', $ids);
    }

    $CONF_FORUM['allow_smilies'] = $allow_smilies;

    return $content;
}

function plugin_feedupdatecheck_forum ($feed, $topic, $update_data, $limit)
{
    global $_TABLES;

    $sql = "SELECT id,forum.grp_id FROM {$_TABLES['gf_topic']} topic LEFT JOIN {$_TABLES['gf_forums']} forum ON topic.forum=forum.forum_id " . forum_buildFeedsSql ($topic, $limit);
    $result = DB_query ($sql);
    $num = DB_numRows ($result);

    $ids = array ();
    for ($i = 0; $i < $num; $i++) {
        $A = DB_fetchArray ($result);
        $ids[] = $A['id'];
    }
    $current = implode (',', $ids);

    //COM_errorLog ("Update check for downloads: comparing new list ($current) with old list ($update_data)", 1);

    return ( $current != $update_data ) ? false : true;
}

/* End of RSS Feed Related Functions */



/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_forum() {
    global $CONF_FORUM;
    return $CONF_FORUM['version'] ;

}


/**
* Called by the plugin Editor to run the SQL Update for a plugin update
*/
function plugin_upgrade_forum() {
    global $_TABLES,$_CONF,$CONF_FORUM;

    include ('upgrade.inc');        // Include the upgrade functions

    $curversion = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'forum'");

    switch ($curversion) {

        case '2.3' :
            if (forum_upgrade_232() == 0 )  {
                DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.5RC1' WHERE `pi_name` = 'forum' LIMIT 1");
                if (forum_upgrade_25() == 0 )  {
                    DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.7', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'forum' LIMIT 1");
                }
            }
            break;

        case '2.3.2' :
            if (forum_upgrade_232() == 0 )  {
                DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.5RC1' WHERE `pi_name` = 'forum' LIMIT 1");
                if (forum_upgrade_25() == 0 )  {
                    DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.7', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'forum' LIMIT 1");
                }
            }
            break;

        case '2.5RC1' :
            if (forum_upgrade_25() == 0 )  {
                DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.7', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'forum' LIMIT 1");
            }
            break;

       case '2.6' :
             DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '2.7', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'forum' LIMIT 1");
             break;

       case '2.7' :
             if (forum_upgrade_30() == 0 ) {
                DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '3.0', `pi_gl_version` = '1.4.1' WHERE `pi_name` = 'forum' LIMIT 1");
             }
             break;

       case '3.0' :
            if (forum_upgrade_320() == 0 )  {
                DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '3.2.0', `pi_gl_version` = '1.6.1' WHERE `pi_name` = 'forum' LIMIT 1");
            }
            break;

       case '3.1.0' :
            if (forum_upgrade_320() == 0 )  {
                DB_query("UPDATE {$_TABLES['plugins']} SET `pi_version` = '3.2.0', `pi_gl_version` = '1.6.1' WHERE `pi_name` = 'forum' LIMIT 1");
            }

    }

    include $_CONF['path'].'plugins/forum/forum.php';   // Reload so we have value of $CONF_FORUM['version']
    /* Check if update completed and return a message number to be shown */
    if (DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'forum'") == $CONF_FORUM['version']) {
        return true;
    } else {
        return false;
    }

}


//  ------- GENERAL FORUM FUNCTIONS --------

function forum_chkUsercanPost() {
    global $_CONF,$CONF_FORUM,$LANG_GF00, $LANG_GF01, $LANG_GF02, $_TABLES, $_USER;

    //Check is anonymous users can post
    if($CONF_FORUM['registered_to_post'] && $_USER['uid'] < 2){
        $url = $_CONF['site_url']. '/users.php?mode=new';
        $display .= COM_startBlock($LANG_GF00['access_denied']);
        $display .= '<br>' .$LANG_GF01['loginreqpost']. '<p>';
        $display .= '<meta http-equiv="refresh" content="3; URL=' .$url. '">';
        $display .= COM_endBlock();
        $display .= COM_siteFooter();
        echo $display;
        exit;
    }
    // Check if IP of user has been banned
    $ip = getenv("REMOTE_ADDR");
    $sqlresult = DB_query ("SELECT * FROM {$_TABLES['gf_banned_ip']} WHERE host_ip like '$ip'");
    $numRows = DB_numRows($sqlresult);
    if ( $numRows > 0 ) {
        echo COM_startBlock($LANG_GF00['access_denied']);
        echo ($LANG_GF02['msg14']);
        printf ($LANG_GF02['msg15'],$_CONF['site_mail']);
        echo COM_endBlock();
        echo COM_siteFooter(true);
        exit();
    }
}

function forum_chkUsercanAccess($secure=false) {
    global $_CONF, $LANG_GF01, $LANG_GF02, $CONF_FORUM, $_USER;

    if ($CONF_FORUM['registration_required'] && $_USER['uid'] < 2) {
            echo  '<TABLE WIDTH="100%" HEIGHT="100"><TR><TD><CENTER>';
        printf($LANG_GF01['loginreqview'], '<A HREF="' .$_CONF['site_url']. '/users.php?mode=new">', '<A HREF="' .$_CONF['site_url']. '/users.php">');
        echo '</CENTER></TD></TR></TABLE>';
        echo COM_siteFooter();
        exit;
    } elseif($secure AND empty($_USER['uid'])) {
        echo '<br>';
        BlockMessage($LANG_GF01['ACCESSERROR'],$LANG_GF02['msg83'],false);
        echo COM_siteFooter();
        exit;
    }
}


function forum_xchsmilies($message,$reverse=false) {

    $search = array(":D", ":)", ":(", "8O", ":?", "B)", ":lol:", ":x", ":P" ,":oops:", ":o",":cry:",
        ":evil:", ":twisted:", ":roll:", ";)", ":!:", ":question:", ":idea:", ":arrow:", ":|",
        ":mrgreen:",":mrt:",":love:",":cat:");
    $replace = array(
    "<img align=absmiddle src='images/smilies/biggrin.gif' alt='Big Grin'>",
    "<img align=absmiddle src='images/smilies/smile.gif' alt='Smile'>",
    "<img align=absmiddle src='images/smilies/frown.gif' alt='Frown'>",
    "<img align=absmiddle src='images/smilies/eek.gif' alt='Eek!'>",
    "<img align=absmiddle src='images/smilies/confused.gif' alt='Confused'>",
    "<img align=absmiddle src='images/smilies/cool.gif' alt='Cool'>",
    "<img align=absmiddle src='images/smilies/lol.gif' alt='Laughing Out Loud'>",
    "<img align=absmiddle src='images/smilies/mad.gif' alt='Angry'>",
    "<img align=absmiddle src='images/smilies/razz.gif' alt='Razz'>",
    "<img align=absmiddle src='images/smilies/redface.gif' alt='Oops!'>",
    "<img align=absmiddle src='images/smilies/surprised.gif' alt='Surprised!'>",
    "<img align=absmiddle src='images/smilies/cry.gif' alt='Cry'>",
    "<img align=absmiddle src='images/smilies/evil.gif' alt='Evil'>",
    "<img align=absmiddle src='images/smilies/twisted.gif' alt='Twisted Evil'>",
    "<img align=absmiddle src='images/smilies/rolleyes.gif' alt='Rolling Eyes'>",
    "<img align=absmiddle src='images/smilies/wink.gif' alt='Wink'>",
    "<img align=absmiddle src='images/smilies/exclaim.gif' alt='Exclaimation'>",
    "<img align=absmiddle src='images/smilies/question.gif' alt='Question'>",
    "<img align=absmiddle src='images/smilies/idea.gif' alt='Idea'>",
    "<img align=absmiddle src='images/smilies/arrow.gif' alt='Arrow'>",
    "<img align=absmiddle src='images/smilies/neutral.gif' alt='Neutral'>",
    "<img align=absmiddle src='images/smilies/mrgreen.gif' alt='Mr. Green'>");

    if (!$reverse) {
        $message = str_replace($search, $replace, $message);
    } else {
        $message = str_replace($replace, $search, $message);
    }
    return $message;
}


function forum_statusMessage($message='',$url,$prompt,$post=false,$forum='') {
    global $_CONF,$_TABLES,$_USER,$LANG_GF02,$CONF_FORUM;

    if ($prompt=="") {
        $prompt = $LANG_GF02['msg140'];
    }
    $statusmsg = new Template($_CONF['path_layout'] . 'forum/layout');
    $statusmsg->set_file (array ('statusmsg'=>'statusmessage.thtml'));
    $statusmsg->set_var ('imgset', $CONF_FORUM['imgset']);
    $statusmsg->set_var ('heading', $LANG_GF02['StatusHeading']);
    $statusmsg->set_var ('message', $message);
    if ($CONF_FORUM['use_autorefresh'] == "0") {
        $statusmsg->set_var ('prompt',sprintf($LANG_GF02['msg139'],$prompt,$url));
        $statusmsg->set_var('autoredirect_javascript','');
    } else {
        $autorefresh_delay = $CONF_FORUM['autorefresh_delay'] * 1000;
        $statusmsg->set_var('autoredirect_javascript',
            "\n\n<script language=\"javascript1.1\">\nwindow.setTimeout(\"window.location.href='{$url}'\",{$autorefresh_delay});\n</script>\n\n");
        if ($post) {
            $forumindex = 'index.php?forum=' . $forum;
            $prompt = sprintf($LANG_GF02['msg162'],$forumindex,$url);
        } else {
            $prompt = sprintf($LANG_GF02['msg141'],$url);
        }
        $statusmsg->set_var ('prompt',$prompt);
    }
    $statusmsg->parse ('output', 'statusmsg');
    echo $statusmsg->finish ($statusmsg->get_var('output'));

    return;
}


/* Function to create a new forum
*
* @param        string     $name        Forum name
* @param        string     $category    Category id to add the forum
* @param        string     $dscp        Optional Category Description
* @param        string     $order       Optional Display order
* @param        string     $order       Optional Group ID if a private group - Default Group 'All Users'
* @param        string     $order       Optional Readonly flag
* @param        string     $order       Optional Hidden flag
* @param        string     $order       Optional Don't show in newposts and centerblock
* @return       string                  Returns the Forum ID for the new forum if successful
*/
function forum_addForum($name,$category,$dscp="",$order="",$grp_id=2,$is_readonly=0,$is_hidden=0,$no_newposts=0,$attachmentgroup=1) {
    global $_TABLES, $_USER;

    $fields = 'forum_order,forum_name,forum_dscp,forum_cat,grp_id,is_readonly,is_hidden,no_newposts,use_attachment_grpid';
    DB_query("INSERT INTO {$_TABLES['gf_forums']} ($fields)
        VALUES ('$order','$name','$dscp','$category','$grp_id','$is_readonly','$is_hidden','$no_newposts',$attachmentgroup)");

    $query = DB_query("SELECT max(forum_id) FROM {$_TABLES['gf_forums']} ");
    list ($forumid) = DB_fetchArray($query);
    $modquery = DB_query("SELECT * FROM {$_TABLES['gf_moderators']} WHERE mod_uid='{$_USER['uid']}' AND mod_forum='$forumid'");
    if (DB_numrows($modquery) < 1){
        $fields = 'mod_uid,mod_username,mod_forum,mod_delete,mod_ban,mod_edit,mod_move,mod_stick';
        DB_query("INSERT INTO {$_TABLES['gf_moderators']} ($fields) VALUES ('{$_USER['uid']}','{$_USER[username]}', '$forumid','1','1','1','1','1')");
    }
    return $forumid;
}

/* Function to delete a forum
*
* @param        string     $id        Forum id to delete
* @return       boolean               Returns true
*/
function forum_deleteForum($id) {
    global $_TABLES;
    DB_query("DELETE FROM {$_TABLES['gf_forums']} WHERE forum_id='$id'");
    DB_query("DELETE FROM {$_TABLES['gf_topic']} WHERE forum='$id'");
    DB_query("DELETE FROM {$_TABLES['gf_moderators']} WHERE mod_forum='$id'");
    DB_query("DELETE FROM {$_TABLES['gf_watch']} WHERE forum_id ='$id'");
    return true;
}


function phpblock_forum_newposts() {
    global $_CONF, $_USER, $_TABLES, $LANG_GF01, $LANG_GF02, $CONF_FORUM,$pi_version;

    if ($pi_version < 2.6) {
        return;
    }
    $mode = COM_applyFilter($_GET['mode']);
    $order = COM_applyFilter($_GET['order']);
    if (DB_getItem($_TABLES['plugins'],'pi_enabled', 'pi_name="forum"')) {
        // Check if user has clicked on a sort option in the block
        if ($mode == 'forumblock') {
            if (($order == 'new') OR ($order == '')) {
                $view_modemsg = sprintf($LANG_GF02['msg74'],$post_limit);
                if ($CONF_FORUM['sb_latestpostonly']) {
                    $orderby = 'lastupdated';
                } else {
                    $orderby = 'date';
                }
            } elseif ($order == 'views') {
                $view_modemsg = sprintf($LANG_GF02['msg75'],$post_limit);
                $orderby = 'views';
            } elseif ($order == 'posts') {
                $view_modemsg = sprintf($LANG_GF02['msg76'],$post_limit);
                $orderby = 'replies';
            }
        } else {
            $view_modemsg = sprintf($LANG_GF02['msg74'],$post_limit);
                if ($CONF_FORUM['sb_latestpostonly']) {
                    $orderby = 'lastupdated';
                } else {
                    $orderby = 'date';
                }
        }

        $block = new Template($_CONF['path_layout'] . 'forum/layout/blocks');
        $block->set_file (array ('block'=>'latestpostsblock.thtml','topicrecord' => 'block_displayline.thtml'));
        $block->set_var ('phpself', $_CONF['site_url'] .'/index.php');
        $block->set_var ('layout_url', $_CONF['layout_url']);
        $block->set_var ('LANG_ORDERBY', $LANG_GF01['ORDERBY']);
        $block->set_var ('LANG_NEW', $LANG_GF01['NEW']);
        $block->set_var ('LANG_VIEWS', $LANG_GF01['VIEWS']);
        $block->set_var ('LANG_POSTS', $LANG_GF01['POSTS']);
        $block->set_var ('view_modemsg', $view_modemsg);

        // Select SQL to either show the latest posts even if they are for the same topic
        // Or show only the lastest post for each of the last updated topics
        if ($CONF_FORUM['sb_latestpostonly']) {
            $sql = "SELECT a.id,a.forum,a.name,a.date,a.subject,a.replies,a.views,a.uid,a.pid FROM {$_TABLES['gf_topic']} a ";
            $sql .= "LEFT JOIN {$_TABLES['gf_forums']} b ON a.forum=b.forum_id ";
            $sql .= "WHERE a.pid=0 AND b.no_newposts = 0 ORDER BY $orderby DESC";
            $result = DB_query($sql);
        } else {
            $sql = "SELECT a.id,a.forum,a.name,a.date,a.subject,a.replies,a.views,a.uid,a.pid FROM {$_TABLES['gf_topic']} a ";
            $sql .= "LEFT JOIN {$_TABLES['gf_forums']} b ON a.forum=b.forum_id ";
            $sql .= "WHERE b.no_newposts = 0 ORDER BY $orderby DESC";
            $result = DB_query($sql);
        }

        $nrows  = DB_numrows($result);
        if ($nrows == 0) {
            return;
        }
        $onetwo = 1;
        $displaycount = 0;

        for ($i =1; $i <= $nrows; $i++) {

            $A = DB_fetchArray($result,false);
            if (trim($A['subject']) == '') {
                $A['subject'] = DB_getItem($_TABLES['gf_topic'],'subject',"id='{$A['pid']}'");
            }

            $fresult = DB_query ("SELECT grp_id, no_newposts FROM {$_TABLES['gf_forums']} WHERE forum_id='{$A['forum']}'");
            list ($grp_id, $no_newposts) = DB_fetchArray ($fresult);
            $groupname = DB_getItem($_TABLES['groups'],'grp_name',"grp_id='$grp_id'");

            if ($no_newposts==0 AND (SEC_inGroup($groupname) OR $grp_id == 2)) {
                $displaycount++;
                if ($CONF_FORUM['use_censor']) {
                    $A['subject'] = COM_checkWords($A['subject']);
                }
                $fullsubject = $A['subject'];
                if (strlen ($A['subject']) > $CONF_FORUM['sb_subject_size']) {
                    $A['subject'] = substr($A['subject'], 0, $CONF_FORUM['sb_subject_size']);
                    $A['subject'] .= '..';
                    $profilelink = '';
                }

                if ($A['replies'] > 0 AND $CONF_FORUM['sb_latestpostonly']) {
                    $lastreplySQL = DB_query("Select uid,name,date from {$_TABLES['gf_topic']} WHERE pid={$A['id']} ORDER BY date DESC LIMIT 1");
                    list ($uid,$postername,$postdate) = DB_fetchArray($lastreplySQL);
                } else {
                    $postername = $A['name'];
                    $postdate = $A['date'];
                    $uid = $A['uid'];
                }
                if ($uid > 1) {
                    $postername = COM_getDisplayName($uid);
                    $profilelink = "<a href='{$_CONF['site_url']}/users.php?mode=profile&amp;uid={$uid}'>";
                } else {
                    $profilelink = '';
                }

               $block->set_var ('site_url', $_CONF['site_url']);
               $block->set_var ('css_id', $onetwo);
               $block->set_var ('img_dir', $CONF_FORUM['imgset']);
               $block->set_var ('forum_id', $A['forum']);
               $block->set_var ('forum_name', DB_getItem($_TABLES['gf_forums'],'forum_name',"forum_id='{$A['forum']}'"));
               $block->set_var ('topic_id', $A['id']);
               $block->set_var ('topic_subject', $A['subject']);
               $block->set_var ('fullsubject', $fullsubject);
               $block->set_var ('topic_id', $A['id']);
               $block->set_var ('profilelink', $profilelink);
               $block->set_var ('user_name', $postername);
               $block->set_var ('LANG_BY', $LANG_GF01['BY']);
               $block->set_var ('LANG_ON', $LANG_GF01['ON']);
               $block->set_var ('LANG_VIEWS', $LANG_GF01['VIEWS']);
               $block->set_var ('LANG_REPLIES', $LANG_GF01['REPLIES']);
               $block->set_var ('views', $A['views']);
               $block->set_var ('replies', $A['replies']);
               if ($CONF_FORUM['allow_user_dateformat']) {
                   $date = COM_getUserDateTimeFormat ($postdate);
                   $block->set_var ('date', $date[0]);
               } else {
                   $block->set_var ('date', strftime($CONF_FORUM['default_Datetime_format'], $postdate));
               }

               $block->parse ('block_records', 'topicrecord',true);

                if ($onetwo == 1) {
                    $onetwo = 2;
                } else {
                    $onetwo = 1;
                }
                if ($displaycount >= $CONF_FORUM['sideblock_numposts']) {
                    break;
                }
            }
        }

        if ($displaycount == 0) {
            return;
        }

        $block->parse ('output', 'block');
        $retval = $block->finish($block->get_var('output'));
        return $retval;
    } else {
        return;
    }
}


/**
* Display latest forum posts in the center block.
*
* @param   where   int      where the block will be displayed (0..2)
* @param   page    int      page number
* @param   topic   string   topic ID
* @return          string   HTML for the center blcok (can be empty)
*/
function plugin_centerblock_forum ($where = 1, $page = 1, $topic = '')
{
    global $_CONF, $_USER, $_TABLES, $LANG_GF01,$CONF_FORUM;
    global $LANG_GF02, $mode, $order,$pi_version;

    //$TIMER = new timerobject();
    //$TIMER->startTimer();
    //$exectime = $TIMER->stopTimer();

    if ($pi_version < 2.6) {
        return;
    }

    if ( (!isset($_USER['uid']) || $_USER['uid'] < 2) && $CONF_FORUM['registration_required'] == 1 )  {
        return;
    }

    $retval = '';

    // Retrieve Centerblock Settings
    $cb_enable = $CONF_FORUM['show_centerblock'];
    $cb_where = $CONF_FORUM['centerblock_where'];
    $cb_homepage = $CONF_FORUM['centerblock_homepage'];

    // If enabled only for homepage and this is not page 1 or a topic page,
    // then set disable flag
    if ($cb_homepage == 1 AND ($page > 1 OR !empty ($topic))) {
        $cb_enable = 0;
    } elseif ($cb_homepage == 0 and $page > 1) {
        $cb_where = 1;      // Top of Page
    }

    // Check if there are no featured articles in this topic
    // and if so then place it at the top of the page
    if (!empty ($topic)) {
        $wheresql = "WHERE tid='$topic' AND featured > 0";
    } else {
        $wheresql = 'WHERE featured = 1';
    }
    $query = DB_query ("SELECT count(*) as count FROM {$_TABLES['stories']} $wheresql");
    $result = DB_fetchArray ($query);
    if ($result['count'] == 0 and $cb_where == 2) {
        $cb_where = 1;
    }

    if ($cb_enable AND ($cb_where == $where)) {

        $block = new Template ($_CONF['path_layout'] . 'forum/layout/blocks');
        $block->set_file (array ('block'=>'centerblock.thtml','record' => 'centerblock_displayline.thtml'));
        $block->set_var ('phpself', $_CONF['site_url'] .'/index.php');
        $block->set_var ('startblock', COM_startBlock($LANG_GF02['msg170']));
        $block->set_var ('endblock', COM_endBlock());
        $block->set_var ('site_url', $_CONF['site_url']);
        $block->set_var ('layout_url', $_CONF['layout_url']);
        $block->set_var ('LANG_title', $LANG_GF02['msg170']);
        $block->set_var ('LANG_FORUM', $LANG_GF01['FORUM']);
        $block->set_var ('LANG_TOPIC', $LANG_GF01['TOPIC']);
        $block->set_var ('LANG_LASTPOST', $LANG_GF01['LASTPOST']);
        $block->set_var ('LANG_viewlastpost', $LANG_GF02['msg160']);
        $block->set_var ('LANG_forumjump', $LANG_GF02['msg195']);

        $groups = array ();
        $usergroups = SEC_getUserGroups();
        foreach ($usergroups as $group) {
            $groups[] = $group;
        }
        $grouplist = implode(',',$groups);

        $sql  = "SELECT a.id, a.forum, a.name, a.date, a.lastupdated, a.last_reply_rec, a.subject, ";
        $sql .= "a.comment, a.uid, a.name, a.pid, a.replies, a.views, b.forum_name  ";
        $sql .= "FROM {$_TABLES['gf_topic']} a ";
        $sql .= "LEFT JOIN {$_TABLES['gf_forums']} b ON a.forum=b.forum_id ";
        $sql .= "WHERE pid=0 AND b.grp_id IN ($grouplist) AND b.no_newposts = 0 ";
        $sql .= "ORDER BY lastupdated DESC LIMIT {$CONF_FORUM['centerblock_numposts']}";
        $result = DB_query ($sql);

        if (DB_numRows($result) == 0) {
            return;
        }

        while ($A = DB_fetchArray ($result)) {
            $fullsubject = "{$A['subject']}\n{$LANG_GF01['POSTEDBY']}:{$A['name']}{$LANG_GF01['VIEWS']}:{$A['views']}, {$LANG_GF01['REPLIES']}:{$A['replies']}";
            if(strlen ($A['subject']) > $CONF_FORUM['cb_subject_size']) {
                $A['subject'] = substr ($A['subject'], 0, $CONF_FORUM['cb_subject_size']);
                $A['subject'] .= '...';
            }

            if ($CONF_FORUM['allow_user_dateformat']) {
                $firstdate = COM_getUserDateTimeFormat ($A['date']);
                $firstdate = $firstdate[0];
                $lastdate = COM_getUserDateTimeFormat ($A['lastupdated']);
                $lastdate = $lastdate[0];
            } else {
                $firstdate = strftime($CONF_FORUM['default_Datetime_format'], $A['date']);
                $lastdate = strftime($CONF_FORUM['default_Datetime_format'], $A['lastupdated']);
            }
            if ($A['uid'] > 1) {
                $topicinfo = "{$A['subject']}<br>{$LANG_GF01['STARTEDBY']} " . COM_getDisplayName($A['uid']) . ', ';
                //$topicinfo .= sprintf($LANG_GF01['LASTREPLYBY'],COM_getDisplayName($A['uid']));
            } else {
                $topicinfo = "{$A['subject']}<br>{$LANG_GF01['STARTEDBY']},{$A['name']},";
            }

            $topicinfo .= "{$firstdate}<br>{$LANG_GF01['VIEWS']}:{$A['views']}, {$LANG_GF01['REPLIES']}:{$A['replies']}<br>";

			if (empty ($A['last_reply_rec']) OR $A['last_reply_rec'] < 1) {
				$lastid = $A['id'];
				$lastpostinfo = wordwrap(strip_tags(substr($A['comment'],0,$CONF_FORUM['contentinfo_numchars'])),$CONF_FORUM['linkinfo_width'],"<br />\n");
			} else {
				$qlreply = DB_query("SELECT id,uid,name,comment FROM {$_TABLES['gf_topic']} WHERE id={$A['last_reply_rec']}");
				$B = DB_fetchArray($qlreply);
				$lastid = $B['id'];
				$lastcomment = $B['comment'];
				if ($B['uid'] > 1) {
					$topicinfo .= sprintf($LANG_GF01['LASTREPLYBY'],COM_getDisplayName($B['uid']));
				} else {
					$topicinfo .= sprintf($LANG_GF01['LASTREPLYBY'],$B['name']);
				}
				$lastpostinfo = wordwrap(strip_tags(substr($lastcomment,0,$CONF_FORUM['contentinfo_numchars'])),$CONF_FORUM['linkinfo_width'],"<br />\n");
            }

            $cssid = ($cssid ==1) ? 2: 1;
            $block->set_var ('lastpostinfo', $lastpostinfo);
            $block->set_var ('lastpostid', $lastid);
            $block->set_var ('topicinfo', $topicinfo);
            $block->set_var ('cssid', $cssid);
            $block->set_var ('img_dir', $CONF_FORUM['imgset']);
            $block->set_var ('forum_id', $A['forum']);
            $block->set_var ('forum_name', $A['forum_name']);
            $block->set_var ('topic_id', $A['id']);
            $block->set_var ('topic_subject', $A['subject']);
            $block->set_var ('fullsubject', $fullsubject);
            $block->set_var ('views', $A['views']);
            $block->set_var ('replies', $A['replies']);
            $block->set_var ('date',$lastdate );
            $block->set_var ('lastpostby',$A['name']);
            $block->parse ('block_records', 'record',true);
        }

        $block->parse ('output', 'block');
        $retval .= $block->finish ($block->get_var ('output'));
    }

    //$exectime = $TIMER->stopTimer();
    //COM_errorLog("Centerblock Execution Time: $exectime seconds");

    return $retval;
}

// +---------------------------------------------------------------------------+
// | Commmon Forum Functions                                                   |
// +---------------------------------------------------------------------------+

function forum_modPermission($forum,$uid='',$permission='') {
    global $_USER,$_TABLES;

    if ($uid == '') {
        $uid = $_USER['uid'];
    }

    if ($uid <=1) {
        return FALSE;  // Invalid ID or anonymous user
    }

    if ((isset($_USER['uid']) && $uid == $_USER['uid']) && (SEC_inGroup('Root') || SEC_hasRights('forum.edit'))) {
        return true;
    }

    /* Retrieve all moderator records for this forum */
    $modrecords = DB_query("SELECT * FROM {$_TABLES['gf_moderators']} WHERE mod_forum='$forum'");
    while ($A = DB_fetchArray($modrecords)) {
        if ($A['mod_groupid'] > 0) {
            if (SEC_inGroup($A['mod_groupid'],$uid)) {
                if ($permission != '') {
                    if ($A[$permission] == '1') {
                        return TRUE;
                    }
                 } else {
                    return TRUE;
                 }
           }
        } else {
            if ($A['mod_uid'] == $uid) {
                if ($permission != '') {
                    if ($A[$permission] == '1') {
                        return TRUE;
                    }
                 } else {
                    return TRUE;
                 }
            }
        }
    }

    /* If I get this far then I exited the loop without finding a permission record */
    return FALSE;

}

function forum_delAttachment($deleteid) {
    global $_TABLES,$_FM_TABLES,$CONF_FORUM,$filemgmt_FileStore;

    // Check and see if the file is in the repository or a saved to the local forum data area
    $lid = DB_getItem($_TABLES['gf_attachments'],'repository_id',"id=$deleteid");
    if ($lid > 0 AND DB_count($_FM_TABLES['filemgmt_filedetail'],"lid",$lid ) == 1) {
        // Verify Access rights
        $groupsql = filemgmt_buildAccessSql();
        $sql = "SELECT COUNT(*) FROM {$_FM_TABLES['filemgmt_filedetail']} a ";
        $sql .= "LEFT JOIN {$_FM_TABLES['filemgmt_cat']} b ON a.cid=b.cid ";
        $sql .= "WHERE a.lid='$lid' $groupsql";
        list($testaccess_cnt) = DB_fetchArray(DB_query($sql));
        if ($testaccess_cnt == 1) {
            $name = DB_getItem($_FM_TABLES['filemgmt_filedetail'],"url","lid=$lid");
            DB_query("DELETE FROM {$_FM_TABLES['filemgmt_filedesc']} WHERE lid='$lid'");
            DB_query("DELETE FROM {$_FM_TABLES['filemgmt_votedata']} WHERE lid='$lid'");
            DB_query("DELETE FROM {$_FM_TABLES['filemgmt_filedetail']} WHERE lid='$lid'");
            DB_query("DELETE FROM {$_FM_TABLES['filemgmt_brokenlinks']} WHERE lid='$lid'");
            $fullname  = $filemgmt_FileStore . $name;
            if ($fullname != '' && file_exists($fullname) && (!is_dir($fullname))) {
                @unlink($fullname);
            }
            DB_query("DELETE FROM {$_TABLES['gf_attachments']} WHERE id=$deleteid");
        }

    } else {
        $filename = DB_getItem($_TABLES['gf_attachments'],'filename',"id=$deleteid");
        $filedata = explode(':', $filename);
        $filename = $filedata[0];
        $realname = $filedata[1];
        $fullname = "{$CONF_FORUM['uploadpath']}/$filename";
        @unlink($fullname);
        $thumbnail = "{$CONF_FORUM['uploadpath']}/tn/$filename";
        @unlink($thumbnail);
        DB_query("DELETE FROM {$_TABLES['gf_attachments']} WHERE id=$deleteid");
    }
}

// +---------------------------------------------------------------------------+
// | Forum's OWN Plugin API Functions                                          |
// +---------------------------------------------------------------------------+


function forumPLG_showsmilies() {
    global $_CONF,$CONF_FORUM;

    // Check and see if glMessenger is installed
    if ($CONF_FORUM['use_smilies_plugin'] AND function_exists(msg_showsmilies)) {
        return msg_showsmilies();
    } else {
        $t = new Template($_CONF['path_layout'] . 'forum/layout');
        $t->set_file ('smilies' , 'smilies.thtml');
        $t->set_var ('imgset', "{$_CONF['site_url']}/forum/images");
        $t->parse ('output', 'smilies');
        return $t->finish($t->get_var('output'));
    }
}

function forumPLG_restoreEmoticons($str) {

    // Check and see if glMessenger is installed
    if (function_exists(msg_showsmilies)) {
        return msg_restoreEmoticons($str);
    } else {
        forum_xchsmilies($str);
    }
}

function forumPLG_getPMlink($member) {
    global $_TABLES,$_CONF;
    if (DB_getItem($_TABLES['plugins'],'pi_enabled',"pi_name='messenger'") == 1) {
        return "{$_CONF['site_url']}/messenger/index.php?action=newpm&toname={$member};";
    } else {
        return '';
    }
}

function forumPLG_getMemberBadge($uid) {
    global $_CONF,$CONF_FORUM;

    if ($uid > 1) {
        if (!SEC_inGroup('Root',$uid)) {
            foreach ($CONF_FORUM['grouptags'] as $groupid => $buttonimg) {
                if (SEC_inGroup($groupid,$uid)) {
                    return "<img src=\"{$CONF_FORUM['imgset']}/badges/{$buttonimg}\"><br>";
                }
            }
        } else {
            return "<img src=\"{$CONF_FORUM['imgset']}/badges/{$CONF_FORUM['grouptags']['Root']}\"><br>";
        }
    }
}

/* Send out new post notification - this version will send out an HTML formatted email based
*  Uses a template which can be unique per user's language preference
*  Note POST['chk_notifypersonal'] if set will send out a single email vs individual emails per user
*  This method can only use a single language template - site default language
*
* @param        int         $forumid        Forum id
* @param        int         $topicic        Topic id
* @param        int         $poster         UID of the user that created the new post
* @param        string      $type           'forum' or 'topic'
* @param        array       $notifusers     Array of users to notify where the record key is the UID . $uid => user email
* @param        string      $allusers       String of email addresses that can be used if a single email is being sent out
* @return       boolean               Returns true
*/
function forumPLG_sendNotification($forumid, $topicid, $poster, $type='topic', $notifyusers='', $allusers='') {
    global $_TABLES,$_CONF,$CONF_FORUM,$LANG_GF01,$LANG_GF02;

    if ($poster > 1) {
        $postername = COM_getDisplayName($poster);
    } else {
        $postername = DB_getItem($_TABLES['gf_topic'],'name',"id=$topicid");
    }
    $topicname = DB_getItem($_TABLES['gf_topic'],'subject',"id=$topicid");
    $forum_name = DB_getItem($_TABLES['gf_forums'],forum_name, "forum_id='$forumid'");
    $subjectline = "{$_CONF['site_name']} {$LANG_GF02['msg22']}";
    $notifyextracontent = '';

    if ($_POST['notificationtype'] != 'basic') {
        $topiclink = "{$_CONF['site_url']}/forum/viewtopic.php?showtopic=$topicid";
        $forumlink = "{$_CONF['site_url']}/forum/index.php?forum=$forumid";
        $tpl = new Template($_CONF['path_layout'] . 'forum/layout');
        $tpl->set_file ('notification', 'forum_htmlnotification.thtml');
        $tpl->set_var('site_url',$_CONF['site_url']);
        $tpl->set_var('site_name',$_CONF['site_name']);
        $tpl->set_var('layour_url',$_CONF['layout_url']);
        $tpl->set_var('topic_link',$topiclink);
        $tpl->set_var('topic_name',$topicname);
        if ($type == 'forum') {
            $tpl->set_var('notification_type',sprintf($LANG_GF02['notify_newtopic'],$topicname,$postername,$forum_name));
        } else {
            $tpl->set_var('notification_type',sprintf($LANG_GF02['notify_newreply'],$topicname,$postername));
        }
        $tpl->set_var ( 'notification_title', sprintf($LANG_GF02['notification_title'],$_CONF['site_name']));
        $tpl->set_var ('quicklinks_title',$LANG_GF02['quicklinks_title']);
        $tpl->set_var ('about_title',$LANG_GF02['quicklinks_about_title']);
        $tpl->set_var ('settings_title',$LANG_GF02['quicklinks_settings_title']);
        $tpl->set_var ('site_title',$LANG_GF02['quicklinks_site_title']);
        $tpl->set_var ('about_message',sprintf($LANG_GF02['quicklinks_about_msg'],$topiclink,$topicname,$forumlink,$forum_name));
        $tpl->set_var ('site_link',sprintf($LANG_GF02['quicklinks_site_link'],$_CONF['site_url'],$_CONF['site_name']));
        $tpl->set_var ('settings_link',sprintf($LANG_GF02['quicklinks_settings_link'],"{$_CONF['site_url']}/forum/userprefs.php"));
        $tpl->set_var ('subscriptions_link',sprintf($LANG_GF02['quicklinks_notifications_link'],"{$_CONF['site_url']}/forum/notify.php"));

        // filter forum post content and perform post formatting
        $topicdata = array(
            'id'        => $topicid,
            'uid'       => $poster,
            'comment'   => $_POST['comment']
            );

        $formattedPost = gf_formatPostforNotification($topicdata);

        $tpl->set_var('forum_name',$forum_name);

        if ($_POST['notificationtype'] == 'full') {
            $tpl->set_var('content',$formattedPost);
        } elseif($_POST['notificationtype'] == 'summary') {
            $tpl->set_var('content',substr($formattedPost,0,200));
        } elseif ($_POST['notificationtype'] == 'custom') {
            $tpl->set_var('content',$_POST['customnotification']);
        }

        $tpl->set_var('member_name',$postername);
        $tpl->set_var('topic_link',"{$_CONF['site_url']}/forum/viewtopic.php?showtopic=$topicid");
        $tpl->set_var('admin_link',"{$_CONF['site_url']}/forum/notify.php");
        $tpl->parse ('output', 'notification');
        $formattedMessage = $tpl->finish($tpl->get_var('output'));

        /* Enable for debugging if you need to see a copy of the formatted message */
        $file = $_CONF['path_data'] . 'notification.html';
        $fp = fopen($file,'w');
        if ($fp) {
            fwrite($fp,$formattedMessage);
            fclose($fp);
        }

        if ($_POST['notificationtype'] == 'full') {
            $notifyextracontent  = $LANG_GF02['notify_premsg'];
            $notifyextracontent .= $_POST['comment'];
            $notifyextracontent .= $LANG_GF02['notify_postmsg'];

        } elseif($_POST['notificationtype'] == 'summary') {
            $notifyextracontent  = $LANG_GF02['notify_premsg'];
            $notifyextracontent .= substr($_POST['comment'],0,200);
            $notifyextracontent .= $LANG_GF02['notify_postmsg'];
        } elseif ($_POST['notificationtype'] == 'custom') {
            $notifyextracontent  = $LANG_GF02['notify_premsg'];
            $notifyextracontent .= $_POST['customnotification'];
            $notifyextracontent .= $LANG_GF02['notify_postmsg'];
        }
    }

    if ($type == 'forum') {
        $textMessage .= sprintf($LANG_GF02['notify_newtopic'],$topicname,$postername,$forum_name, $_CONF['site_name'],$_CONF['site_url'],$pid);
    } else {
        $textMessage .= sprintf($LANG_GF02['notify_newreply'],$topicname,$postername);
    }
    $textMessage .= $notifyextracontent;
    $textMessage .= "\nLink to topic: {$_CONF['site_url']}/forum/viewtopic.php?showtopic=$topicid";
    $textMessage .= "\n\n" . sprintf($LANG_GF02['notify_reason'],"{$_CONF['site_url']}/forum/notify.php");

    if ($_POST['chk_notifypersonal'] == 1 AND $email_allusers != '') {
        if ($CONF_FORUM['log_notifications']) {
            COM_errorLog("Forum Notification for topic:$topicid sent to: $toemail");
        }
        $message = $formattedMessage;
        COM_mail($email_allusers,$subjectline,$message,$from,true);

    } elseif (is_array($notifyusers)) {
        reset($notifyusers);
        foreach ($notifyusers as $touid => $toemail) {
            $toname = COM_getDisplayName($touid);
            if ($_POST['notificationtype'] == 'basic' OR DB_getItem($_TABLES['gf_userprefs'],'notification_format',"uid=$touid") == 'text') {
                $message  = "{$LANG_GF01['HELLO']} $toname,\n" . $textMessage;
                if ($CONF_FORUM['log_notifications']) {
                    COM_errorLog("Forum TEXT Notification for topic:$topicid sent to: $toemail");
                }
            } else {
                $message = $formattedMessage;
                if ($CONF_FORUM['log_notifications']) {
                    COM_errorLog("Forum HTML Notification for topic:$topicid sent to: $toemail");
                }
            }
            COM_mail($toemail,$subjectline,$message,'',true);
        }
    }
    return true;

}


/* End of forum plugin API functions */



// +---------------------------------------------------------------------------+
// | Forum functions that are used to generate menus                           |
// +---------------------------------------------------------------------------+

function phpblock_forum_menu() {
    global $_TABLES,$_CONF,$_USER,$CONF_FORUM,$LANG_GF01,$LANG_GF02,$LANG_GF07,$CONF_NEXMENU,$_BLOCK_TEMPLATE;

    if ($CONF_FORUM['menustyle'] == 'glmenu') {

        if ($CONF_GLMENU['milonicmode']) {
            $retval = COM_startBlock( $LANG_GF00['forumblock'], '', 'glmenu/milonicmenu/blockheader-blockmenu.thtml');
            $retval .= 'aI("text='.$LANG_GF01['INDEXPAGE'].';url='.$_CONF['site_url'].'/forum/index.php;");' . LB;
            if ($_USER['uid'] > 1) {
                $retval .= 'aI("text='.$LANG_GF01['USERPREFS'].';url='.$_CONF['site_url'].'/forum/userprefs.php;");' . LB;
                $retval .= 'aI("text='.$LANG_GF01['SUBSCRIPTIONS'].';url='.$_CONF['site_url'].'/forum/notify.php;");' . LB;
            }
            if ($CONF_GLMENU['allow_memberlist']) {
                $retval .= 'aI("text='.$LANG_GF02['msg88'].';url='.$_CONF['site_url'].'/forum/memberlist.php;");' . LB;
            }
            $retval .= 'aI("text='.$LANG_GF07['3'].';url='.$_CONF['site_url'].'/forum/index.php?op=popular;");' . LB;
            $retval .= COM_endBlock('glmenu/milonicmenu/blockfooter-blockmenu.thtml');
        } else {
            $retval = COM_startBlock( $LANG01[47], '', 'glmenu/blockheader.thtml');
            $t = new Template($_CONF['path_layout'] . 'glmenu/cssmenu');
            $t->set_file('menu','blockmenu.thtml');

            $links = "<li><a href=\"{$_CONF['site_url']}/forum/index.php\" TITLE=\"{$LANG_GF02['msg196']}\">{$LANG_GF01['INDEXPAGE']}</a></li>" . LB;
            if ($_USER['uid'] > 1) {
                $links .= "<li><a href=\"{$_CONF['site_url']}/forum/index.php?op=markallread\" TITLE=\"{$LANG_GF02['msg197']}\">{$LANG_GF01['MARKALLREAD']}</a></li>" . LB;
                $links .= "<li><a href=\"{$_CONF['site_url']}/forum/userprefs.php\" TITLE=\"{$LANG_GF02['msg198']}\">{$LANG_GF01['USERPREFS']}</a></li>" . LB;
                $links .= "<li><a href=\"{$_CONF['site_url']}/forum/notify.php\" TITLE=\"{$LANG_GF02['msg199']}\">{$LANG_GF01['SUBSCRIPTIONS']}</a></li>" . LB;
            }
            $links .= "<li><a href=\"{$_CONF['site_url']}/forum/memberlist.php\" TITLE=\"{$LANG_GF02['msg200']}\">{$LANG_GF02['msg88']}</a></li>" . LB;
            $links .= "<li><a href=\"{$_CONF['site_url']}/forum/index.php?op=popular\" TITLE=\"{$LANG_GF02['msg201']}\">{$LANG_GF07['3']}</a></li>" . LB;

            $t->set_var('menuitems', $links);
            $t->parse ('output', 'menu');
            $retval .= $t->finish ($t->get_var('output'));
            $retval .= COM_endBlock('glmenu/blockfooter.thtml');
        }

    } elseif ($CONF_FORUM['menustyle'] == 'nexmenu') {

        if ($CONF_NEXMENU['milonicmode']) {
            $retval = COM_startBlock( $LANG_GF00['forumblock'], '', 'nexmenu/milonicmenu/blockheader-blockmenu.thtml');
            $retval .= 'aI("text='.$LANG_GF01['INDEXPAGE'].';url='.$_CONF['site_url'].'/forum/index.php;");' . LB;
            if ($_USER['uid'] > 1) {
                $retval .= 'aI("text='.$LANG_GF01['USERPREFS'].';url='.$_CONF['site_url'].'/forum/userprefs.php;");' . LB;
                $retval .= 'aI("text='.$LANG_GF01['SUBSCRIPTIONS'].';url='.$_CONF['site_url'].'/forum/notify.php;");' . LB;
            }
            $retval .= 'aI("text='.$LANG_GF02['msg88'].';url='.$_CONF['site_url'].'/forum/memberlist.php;");' . LB;
            $retval .= 'aI("text='.$LANG_GF07['3'].';url='.$_CONF['site_url'].'/forum/index.php?op=popular;");' . LB;
            $retval .= COM_endBlock('nexmenu/milonicmenu/blockfooter-blockmenu.thtml');
        } else {
            $retval = COM_startBlock( $LANG01[47], '', 'nexmenu/blockheader.thtml');
            $t = new Template($_CONF['path_layout'] . 'nexmenu/cssmenu');
            $t->set_file('menu','blockmenu.thtml');

            $links = "<li><a href=\"{$_CONF['site_url']}/forum/index.php\" TITLE=\"{$LANG_GF02['msg196']}\">{$LANG_GF01['INDEXPAGE']}</a></li>" . LB;
            if ($_USER['uid'] > 1) {
                $links .= "<li><a href=\"{$_CONF['site_url']}/forum/index.php?op=markallread\" TITLE=\"{$LANG_GF02['msg197']}\">{$LANG_GF01['MARKALLREAD']}</a></li>" . LB;
                $links .= "<li><a href=\"{$_CONF['site_url']}/forum/userprefs.php\" TITLE=\"{$LANG_GF02['msg198']}\">{$LANG_GF01['USERPREFS']}</a></li>" . LB;
                $links .= "<li><a href=\"{$_CONF['site_url']}/forum/notify.php\" TITLE=\"{$LANG_GF02['msg199']}\">{$LANG_GF01['SUBSCRIPTIONS']}</a></li>" . LB;
            }
            $links .= "<li><a href=\"{$_CONF['site_url']}/forum/memberlist.php\" TITLE=\"{$LANG_GF02['msg200']}\">{$LANG_GF02['msg88']}</a></li>" . LB;
            $links .= "<li><a href=\"{$_CONF['site_url']}/forum/index.php?op=popular\" TITLE=\"{$LANG_GF02['msg201']}\">{$LANG_GF07['3']}</a></li>" . LB;

            $t->set_var('menuitems', $links);
            $t->parse ('output', 'menu');
            $retval .= $t->finish ($t->get_var('output'));
            $retval .= COM_endBlock('nexmenu/blockfooter.thtml');
        }

    } else {
        $retval = '<table width="100%" border="0" cellpadding="1" cellspacing="1"><tr><td>';
        $retval .= "<a href=\"{$_CONF['site_url']}/forum/index.php\" TITLE=\"{$LANG_GF02['msg196']}\">{$LANG_GF01['INDEXPAGE']}</a>";
        $retval .= '</td></tr><tr><td>';
        if ($_USER['uid'] > 1) {
            $retval .= "<a href=\"{$_CONF['site_url']}/forum/index.php?op=markallread\" TITLE=\"{$LANG_GF02['msg197']}\">{$LANG_GF01['MARKALLREAD']}</a>";
            $retval .= '</td></tr><tr><td>';
            $retval .= "<a href=\"{$_CONF['site_url']}/forum/userprefs.php\" TITLE=\"{$LANG_GF02['msg198']}\">{$LANG_GF01['USERPREFS']}</a>";
            $retval .= '</td></tr><tr><td>';
            $retval .= "<a href=\"{$_CONF['site_url']}/forum/notify.php\" TITLE=\"{$LANG_GF02['msg199']}\">{$LANG_GF01['SUBSCRIPTIONS']}</a>";
            $retval .= '</td></tr><tr><td>';
        }
        $retval .= "<a href=\"{$_CONF['site_url']}/forum/memberlist.php\" TITLE=\"{$LANG_GF02['msg200']}\">{$LANG_GF02['msg88']}</a>";
        $retval .= '</td></tr><tr><td>';
        $retval .= "<a href=\"{$_CONF['site_url']}/forum/index.php?op=popular\" TITLE=\"{$LANG_GF02['msg201']}\">{$LANG_GF07['3']}</a>";
        $retval .= '</td></tr></table>';
    }
    return $retval;

}


function nexmenu_showforums() {
    global $_CONF,$_TABLES,$CONF_NEXMENU;

    $retval = '';
    $qforums = DB_query("SELECT forum_name,forum_id,grp_id FROM {$_TABLES['gf_forums']} ORDER BY forum_cat, forum_order ASC");
    while (list($forum_name,$forum_id, $forum_grp) = DB_fetchArray($qforums)) {
        $groupname = DB_getItem($_TABLES['groups'],'grp_name',"grp_id='$forum_grp'");
        if (SEC_inGroup($groupname)) {
            if ($CONF_NEXMENU['milonicmode']) {
                $retval .= 'aI("text='.$forum_name.';url='.$_CONF['site_url'].'/forum/index.php?forum='.$forum_id.';");';
            } else {
                $retval .= "<li><a href=\"{$_CONF['site_url']}/forum/index.php?forum={$forum_id}\">$forum_name</a></li>";
            }
        }
    }
    return $retval;
}


function glmenu_showforums() {
    global $_CONF,$_TABLES,$CONF_GLMENU;

    $retval = '';
    $glMenu_version = DB_getItem($_TABLES['plugins'],'pi_version',"pi_name = 'glmenu'");
    $qforums = DB_query("SELECT forum_name,forum_id,grp_id FROM {$_TABLES['gf_forums']} ORDER BY forum_cat, forum_order ASC");
    while (list($forum_name,$forum_id, $forum_grp) = DB_fetchArray($qforums)) {
        $groupname = DB_getItem($_TABLES['groups'],'grp_name',"grp_id='$forum_grp'");
        if (SEC_inGroup($groupname)) {
            if ($CONF_GLMENU['milonicmode']) {
                $retval .= 'aI("text='.$forum_name.';url='.$_CONF['site_url'].'/forum/index.php?forum='.$forum_id.';");';
            } else {
                $retval .= "<li><a href=\"{$_CONF['site_url']}/forum/index.php?forum={$forum_id}\">$forum_name</a></li>";
            }
        }
    }
    return $retval;
}

?>