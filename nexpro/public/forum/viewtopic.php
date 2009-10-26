<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin 2.6 for Geeklog - The Ultimate Weblog               |
// | Release date: Oct 30,2006                                                 |
// +---------------------------------------------------------------------------+
// | Viewtopic.php                                                             |
// | Main program to view topics and posts in the forum                        |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000,2001,2002,2003 by the following authors:               |
// | Geeklog Author: Tony Bibbs       - tony@tonybibbs.com                     |
// +---------------------------------------------------------------------------+
// | Plugin Authors                                                            |
// | Blaine Lang,                  blaine@portalparts.com, www.portalparts.com |
// | Version 1.0 co-developer:     Matthew DeWyer, matt@mycws.com              |
// | Prototype & Concept :         Mr.GxBlock, www.gxblock.com                 |
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

require_once("../lib-common.php"); // Path to your lib-common.php
require_once( $_CONF['path_system'] . 'classes/timer.class.php' );
$mytimer = new timerobject();
$mytimer->setPercision(2);
$mytimer->startTimer();

require_once ($_CONF['path_html'] . 'forum/include/gf_showtopic.php');
require_once ($_CONF['path_html'] . 'forum/include/gf_format.php');
require_once($_CONF['path'] . 'plugins/forum/debug.php');  // Common Debug Code

$mytimer = new timerobject();
$mytimer->startTimer();

// Pass thru filter any get or post variables to only allow numeric values and remove any hostile data
$showtopic = COM_applyFilter($_REQUEST['showtopic'],true);
$show = COM_applyFilter($_REQUEST['show'],true);
$page = COM_applyFilter($_REQUEST['page'],true);
$mode = COM_applyFilter($_REQUEST['mode']);
$result = DB_query("SELECT forum, pid, subject FROM {$_TABLES['gf_topic']} WHERE id = '$showtopic'"); // <- new
list($forum, $topic_pid, $subject) = DB_fetchArray($result); // <- new
$highlight = $_REQUEST['highlight'];

if ($topic_pid == '') {
    echo COM_siteHeader();
    echo COM_startBlock();
    alertMessage($LANG_GF02['msg172'],$LANG_GF02['msg171']);
    echo COM_endBlock();
    echo COM_siteFooter();
    exit;
}
if ($topic_pid != 0) {
    $showtopic = $topic_pid;
}

if($_REQUEST['onlytopic'] == 1) {
    // Send out the HTML headers and load the stylesheet for the iframe preview
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . LB;
    echo '<html>' . LB;
    echo '<head>' . LB;
    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$_CONF['site_url']}/layout/{$_CONF['theme']}/style.css\"></head>\n";
    echo '<body class="sitebody">';
} else {
    // Display Common headers
    gf_siteHeader($subject);
    //Check is anonymous users can access
    forum_chkUsercanAccess();
    // Now display the forum header
    ForumHeader($forum,$showtopic);
}

// Check if the number of records was specified to show
if (empty($show) AND $CONF_FORUM['show_posts_perpage'] > 0) {
    $show = $CONF_FORUM['show_posts_perpage'];
} elseif (empty($show)) {
    $show = 20;
}

$sql  = "SELECT a.forum,a.pid,a.locked,a.subject,a.replies,b.forum_cat,b.forum_name,b.is_readonly,c.cat_name ";
$sql .= "FROM {$_TABLES['gf_topic']} a ";
$sql .= "LEFT JOIN {$_TABLES['gf_forums']} b ON b.forum_id=a.forum ";
$sql .= "LEFT JOIN {$_TABLES['gf_categories']} c on c.id=b.forum_cat ";
$sql .= "WHERE a.id=$showtopic";
$viewtopic = DB_fetchArray(DB_query($sql),false);
$numpages = ceil(($viewtopic['replies']+1) / $show);

if ($_REQUEST['lastpost']) {
    if($page == 0) {
        $page = $numpages;
    }
    $order = 'ASC';
} else {
    if ($page == 0) {
        $page = 1;
    }
    if ($_REQUEST['onlytopic'] == 1) {
        $order = 'DESC';
    } else {
        $order = 'ASC';
    }
}
if ($page > 1) {
    $offset = ($page - 1) * $show;
} else {
    $offset = 0;
}

$base_url = "{$_CONF['site_url']}/forum/viewtopic.php?showtopic=$showtopic&mode=$mode&show=$show";
$forum_outline_header = new Template($_CONF['path_layout'] . 'forum/layout');
$forum_outline_header->set_file (array ('forum_outline_header'=>'forum_outline_header.thtml'));
$forum_outline_header->set_var ('imgset', $CONF_FORUM['imgset']);
$forum_outline_header->parse ('output', 'forum_outline_header');
echo $forum_outline_header->finish($forum_outline_header->get_var('output'));

// Stop timer and print elapsed time
//$intervalTime = $mytimer->stopTimer();
//COM_errorLog("Start Topic Display Time: $intervalTime");

if ($mode != 'preview') {

    $topicnavbar = new Template($_CONF['path_layout'] . 'forum/layout');
    $topicnavbar->set_file (array ('topicnavbar'=>'topic_navbar.thtml',
            'subscribe' => 'links/subscribe.thtml',
            'print' => 'links/print.thtml',
            'prev' => 'links/prevtopic.thtml',
            'next' => 'links/nexttopic.thtml',
            'new' => 'links/newtopic.thtml',
            'reply' => 'links/replytopic.thtml'));

    $topicnavbar->set_var('layout_url', $_CONF['layout_url']);
    $topicnavbar->set_var('site_url', $_CONF['site_url']);


    $printlink = "{$_CONF['site_url']}/forum/print.php?id=$showtopic";
    $printlinkimg = '<img src="'.gf_getImage('print').'" border="0" align="absmiddle" alt="'.$LANG_GF01['PRINTABLE'].'" TITLE="'.$LANG_GF01['PRINTABLE'].'">';

    if ($topic_pid > 0) {
        $replytopic_id = $topic_pid;
    } else {
        $replytopic_id = $showtopic;
    }

    if ($viewtopic['is_readonly'] == 0 OR forum_modPermission($viewtopic['forum'],$_USER['uid'],'mod_edit')) {
        $newtopiclink = "{$_CONF['site_url']}/forum/createtopic.php?method=newtopic&forum=$forum";
        $newtopiclinkimg = '<img src="'.gf_getImage('post_newtopic').'" border="0" align="absmiddle" alt="'.$LANG_GF01['NEWTOPIC'].'" TITLE="'.$LANG_GF01['NEWTOPIC'].'">';
        if($viewtopic['locked'] != 1) {
            $replytopiclink = "{$_CONF['site_url']}/forum/createtopic.php?method=postreply&forum=$forum&id=$replytopic_id";
            $replytopiclinkimg = '<img src="'.gf_getImage('post_reply').'" border="0" align="absmiddle" alt="'.$LANG_GF01['POSTREPLY'].'" TITLE="'.$LANG_GF01['POSTREPLY'].'">';
            $topicnavbar->set_var ('replytopiclink', $replytopiclink);
            $topicnavbar->set_var ('replytopiclinkimg', $replytopiclinkimg);
            $topicnavbar->set_var ('LANG_reply', $LANG_GF01['POSTREPLY']);
            $topicnavbar->parse ('replytopic_link', 'reply');
        }
    } else {
        $newtopiclink = '';
        $newtopiclinkimg = '';
    }


    $prev_sql = DB_query("SELECT id FROM {$_TABLES['gf_topic']} WHERE (forum='$forum') AND (pid=0) AND (id < '$showtopic') ORDER BY id DESC LIMIT 1");
    $P = DB_fetchArray($prev_sql);
    if ($P['id'] != "") {
        $prevlink = "{$_CONF['site_url']}/forum/viewtopic.php?showtopic={$P['id']}";
        $prevlinkimg = '<img src="'.gf_getImage('prev').'" border="0" align="absmiddle" alt="'.$LANG_GF01['PREVTOPIC'].'" TITLE="'.$LANG_GF01['PREVTOPIC'].'">';
        $topicnavbar->set_var ('prevlinkimg', $prevlinkimg);
        $topicnavbar->set_var ('prevlink', $prevlink);
        $topicnavbar->set_var ('LANG_prevlink',$LANG_GF01['PREVTOPIC']);
        $topicnavbar->parse ('prevtopic_link', 'prev');
    }

    $next_sql = DB_query("SELECT id FROM {$_TABLES['gf_topic']} WHERE (forum='$forum') AND (pid=0) AND (id > '$showtopic') ORDER BY id ASC LIMIT 1");
    $N = DB_fetchArray($next_sql);
    if ($N['id'] > 0) {
        $nextlink = "{$_CONF['site_url']}/forum/viewtopic.php?showtopic={$N['id']}";
        $nextlinkimg = '<img src="'.gf_getImage('next').'" border="0" align="absmiddle" alt="'.$LANG_GF01['NEXTTOPIC'].'" TITLE="'.$LANG_GF01['NEXTTOPIC'].'">';
        $topicnavbar->set_var ('nextlinkimg', $nextlinkimg);
        $topicnavbar->set_var ('nextlink', $nextlink);
        $topicnavbar->set_var ('LANG_nextlink',$LANG_GF01['NEXTTOPIC']);
        $topicnavbar->parse ('nexttopic_link', 'next');
    }

    // Enable TOPIC NOTIFY IF THE USER IS A MEMBER
    if (isset($_USER['uid']) AND $_USER['uid'] > 1) {
        $forumid = $viewtopic['forum'];

        /* Check for a un-subscribe record */
        $ntopicid = -$showtopic;  // Negative value
        if (DB_count($_TABLES['gf_watch'], array('forum_id', 'topic_id', 'uid'), array($forumid, $ntopicid,$_USER['uid'])) > 0) {
            $notifylinkimg = '<img src="'.gf_getImage('notify_on').'" border="0" align="absmiddle" alt="'.$LANG_GF02['msg62'].'" TITLE="'.$LANG_GF02['msg62'].'">';
            $notifylink = "{$_CONF['site_url']}/forum/notify.php?forum=$forumid&submit=save&id=$showtopic";
            $topicnavbar->set_var ('LANG_notify', $LANG_GF01['SubscribeLink']);

        /* Check if user has subscribed to complete forum */
        } elseif (DB_count($_TABLES['gf_watch'], array('forum_id', 'topic_id', 'uid'), array($forumid, '0',$_USER['uid'])) > 0) {
            $notifyID = DB_getItem($_TABLES['gf_watch'],'id', "forum_id='$forumid' AND topic_id='0' AND uid='{$_USER['uid']}'");
            $notifylinkimg = '<img src="'.gf_getImage('notify_off').'" border="0" align="absmiddle" alt="'.$LANG_GF02['msg137'].'" TITLE="'.$LANG_GF02['msg137'].'">';
            $notifylink = "{$_CONF['site_url']}/forum/notify.php?submit=delete2&id=$notifyID&forum=$forumid&topic=$showtopic";
            $topicnavbar->set_var ('LANG_notify', $LANG_GF01['unSubscribeLink']);

        /* Check if user is subscribed to this specific topic */
        } elseif (DB_count($_TABLES['gf_watch'], array('forum_id', 'topic_id', 'uid'), array($forumid, $showtopic,$_USER['uid'])) > 0) {
            $notifyID = DB_getItem($_TABLES['gf_watch'],'id', "forum_id='$forumid' AND topic_id='$showtopic' AND uid='{$_USER['uid']}'");
            $notifylinkimg = '<img src="'.gf_getImage('notify_off').'" border="0" align="absmiddle" alt="'.$LANG_GF02['msg137'].'" TITLE="'.$LANG_GF02['msg137'].'">';
            $notifylink = "{$_CONF['site_url']}/forum/notify.php?submit=delete2&id=$notifyID&forum=$forumid&topic=$showtopic";
            $topicnavbar->set_var ('LANG_notify', $LANG_GF01['unSubscribeLink']);

        } else {
            $notifylinkimg = '<img src="'.gf_getImage('notify_on').'" border="0" align="absmiddle" alt="'.$LANG_GF02['msg62'].'" TITLE="'.$LANG_GF02['msg62'].'">';
            $notifylink = "{$_CONF['site_url']}/forum/notify.php?forum=$forumid&submit=save&id=$showtopic";
            $topicnavbar->set_var ('LANG_notify', $LANG_GF01['SubscribeLink']);
        }

        $topicnavbar->set_var ('notifylinkimg', $notifylinkimg);
        $topicnavbar->set_var ('notifylink', $notifylink);
        $topicnavbar->parse ('subscribe_link', 'subscribe');

    }

    if(function_exists(prj_getSessionProject)) {
        $projectid = prj_getSessionProject();
        if($projectid > 0) {
            $link = "<a href=\"{$_CONF['site_url']}/projects/viewproject.php?pid=$projectid\">{$strings['RETURN2PROJECT']}</a>";
            $topicnavbar->set_var ('return2project',$link);
        }
    }

    $topicnavbar->set_var ('printlink', $printlink);
    $topicnavbar->set_var ('printlinkimg', $printlinkimg);
    $topicnavbar->set_var ('LANG_print', $LANG_GF01['PRINTABLE']);
    $topicnavbar->parse ('print_link', 'print');

    $topicnavbar->set_var ('imgset', $CONF_FORUM['imgset']);
    $topicnavbar->set_var ('navbreadcrumbsimg','<img src="'.gf_getImage('nav_breadcrumbs').'">');
    $topicnavbar->set_var ('navtopicimg','<img src="'.gf_getImage('nav_topic').'">');
    $topicnavbar->set_var('forum_home',$LANG_GF01['INDEXPAGE']);
    $topicnavbar->set_var ('cat_name', DB_getItem($_TABLES['gf_categories'],"cat_name","id={$viewtopic['forum_cat']}"));
    $topicnavbar->set_var ('forum_id', $forum);
    $topicnavbar->set_var ('forum_name', $viewtopic['forum_name']);

    $topicnavbar->set_var ('topic_id', $replytopic_id);

    $topicnavbar->set_var ('newtopiclink', $newtopiclink);
    $topicnavbar->set_var ('newtopiclinkimg', $newtopiclinkimg);
    $topicnavbar->set_var ('LANG_newtopic', $LANG_GF01['NEWTOPIC']);
    $topicnavbar->parse ('newtopic_link', 'new');

    $topicnavbar->set_var ('LANG_next', $LANG_GF01['NEXT']);
    $topicnavbar->set_var ('LANG_TOP', $LANG_GF01['TOP']);
    $topicnavbar->set_var ('subject', $viewtopic['subject']);
    $topicnavbar->set_var ('LANG_HOME', $LANG_GF01['HOMEPAGE']);
    $topicnavbar->set_var ('pagenavigation', COM_printPageNavigation($base_url,$page,$numpages));
    $topicnavbar->parse ('output', 'topicnavbar');
    echo $topicnavbar->finish($topicnavbar->get_var('output'));
} else {
    $preview_header = new Template($_CONF['path_layout'] . 'forum/layout');
    $preview_header->set_file ('header', 'topicpreview_header.thtml');
    $preview_header->set_var ('imgset', $CONF_FORUM['imgset']);
    $preview_header->parse ('output', 'header');
    echo $preview_header->finish($preview_header->get_var('output'));
}

//$intervalTime = $mytimer->stopTimer();
//COM_errorLog("Topic Display Time2: $intervalTime");

// Update the topic view counter and user access log
DB_query("UPDATE {$_TABLES['gf_topic']} SET views=views+1 WHERE id='$showtopic'");
if($_USER['uid'] > 1 ) {
    $query = DB_query("SELECT pid,forum FROM {$_TABLES['gf_topic']} WHERE id={$showtopic}");
    list ($showtopicpid,$forumid) = DB_fetchArray($query);
    if ($showtopicpid == 0 ) {
        $showtopicpid = $showtopic;
    }
    $lrows = DB_count($_TABLES['gf_log'],array('uid','topic'),array($_USER['uid'],$showtopicpid));
    $logtime = time();
    if ($lrows < 1) {
        DB_query("INSERT INTO {$_TABLES['gf_log']} (uid,forum,topic,time) VALUES ('$_USER[uid]','$forum','$showtopicpid','$logtime')");
    } else {
        DB_query("UPDATE {$_TABLES['gf_log']} SET time=$logtime WHERE uid=$_USER[uid] AND topic=$showtopicpid");
    }
}

// Retrieve all the records for this topic
//$intervalTime = $mytimer->stopTimer();
//COM_errorLog("Topic Display Time2b: $intervalTime");

if ($CONF_FORUM['mysql4+']) {
    $sql  = "(SELECT * FROM {$_TABLES['gf_topic']} WHERE id='$showtopic') ";
    $sql .= "UNION ALL (SELECT * FROM {$_TABLES['gf_topic']} WHERE pid='$showtopic') ";
    $sql .= "ORDER BY id $order LIMIT $offset, $show";
    $result  = DB_query($sql);
} else {
    $sql = "SELECT * FROM {$_TABLES['gf_topic']} WHERE id='$showtopic' OR pid='$showtopic' ORDER BY id $order LIMIT $offset, $show";
    $result  = DB_query($sql);
}
// Display each post in this topic
$onetwo = 1;
while($topicRec = DB_fetchArray($result)) {
    //$intervalTime = $mytimer->stopTimer();
    //COM_errorLog("Topic Display Time: $intervalTime");
    if ($CONF_FORUM['show_anonymous_posts'] == 0 AND $topicRec['uid'] == 1) {
       echo '<div class="pluginAlert" style="padding:10px;margin:10px;">Your preferences have block anonymous posts enabled</div>';
       break;
       //Do nothing - but this way I don't always have to do this check
    } else {
        echo showtopic($topicRec,$mode,$onetwo,$page);
        $onetwo = ($onetwo == 1) ? 2 : 1;
    }
}

if ($mode != 'preview') {
    $topic_footer = new Template($_CONF['path_layout'] . 'forum/layout');
    $topic_footer->set_file (array ('topicfooter'=>'topicfooter.thtml',
            'new' => 'links/newtopic.thtml',
            'reply' => 'links/replytopic.thtml'
    ));

    if ($viewtopic['is_readonly'] == 0 OR forum_modPermission($viewtopic['forum'],$_USER['uid'],'mod_edit')) {
        $newtopiclink = "{$_CONF['site_url']}/forum/createtopic.php?method=newtopic&forum=$forum";
        $newtopiclinkimg = '<img src="'.gf_getImage('post_newtopic').'" border="0" align="absmiddle" alt="'.$LANG_GF01['NEWTOPIC'].'" TITLE="'.$LANG_GF01['NEWTOPIC'].'">';
        $topic_footer->set_var('layout_url', $_CONF['layout_url']);
        $topicDisplayTime = $mytimer->stopTimer();
        $topic_footer->set_var ('page_generated_time', sprintf($LANG_GF02['msg179'],$topicDisplayTime));
        $topic_footer->set_var ('newtopiclink', $newtopiclink);
        $topic_footer->set_var ('newtopiclinkimg', $newtopiclinkimg);
        $topic_footer->set_var ('LANG_newtopic', $LANG_GF01['NEWTOPIC']);
        $topic_footer->parse ('newtopic_link', 'new');

        if($viewtopic['locked'] != 1) {
            $replytopiclink = "{$_CONF['site_url']}/forum/createtopic.php?method=postreply&forum=$forum&id=$replytopic_id";
            $replytopiclinkimg = '<img src="'.gf_getImage('post_reply').'" border="0" align="absmiddle" alt="'.$LANG_GF01['POSTREPLY'].'" TITLE="'.$LANG_GF01['POSTREPLY'].'">';
            $topic_footer->set_var ('replytopiclink', $replytopiclink);
            $topic_footer->set_var ('replytopiclinkimg', $replytopiclinkimg);
            $topic_footer->set_var ('LANG_reply', $LANG_GF01['POSTREPLY']);
            $topic_footer->parse ('replytopic_link', 'reply');
        }
    }


} else {
    $base_url .= '&onlytopic=1';
    $topic_footer = new Template($_CONF['path_layout'] . 'forum/layout');
    $topic_footer->set_file (array ('topicfooter'=>'topicfooter_preview.thtml'));
}

$topic_footer->set_var ('pagenavigation', COM_printPageNavigation($base_url,$page, $numpages));
$topic_footer->set_var ('forum_id', $forum);
$topic_footer->set_var ('imgset', $CONF_FORUM['imgset']);
$topic_footer->parse ('output', 'topicfooter');
echo $topic_footer->finish($topic_footer->get_var('output'));

$forum_outline_footer= new Template($_CONF['path_layout'] . 'forum/layout');
$forum_outline_footer->set_file (array ('forum_outline_footer'=>'forum_outline_footer.thtml'));
$forum_outline_footer->set_var ('imgset', $CONF_FORUM['imgset']);
$forum_outline_footer->parse ('output', 'forum_outline_footer');
echo $forum_outline_footer->finish ($forum_outline_footer->get_var('output'));

$intervalTime = $mytimer->stopTimer();
//COM_errorLog("End Topic Display Time: $intervalTime");

if(!isset($_REQUEST['onlytopic']) || $_REQUEST['onlytopic'] != 1) {
    echo BaseFooter();
    // Display Common headers
    gf_siteFooter();
} else {
    echo '</body>' . LB;
    echo '</html>' . LB;
}

?>