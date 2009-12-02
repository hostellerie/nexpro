<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin 2.6 for Geeklog - The Ultimate Weblog               |
// | Release date: Oct 30,2006                                                 |
// +---------------------------------------------------------------------------+
// | Createtopic.php                                                           |
// | Main program to create topics and posts in the forum                      |
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

require_once('../lib-common.php'); // Path to your lib-common.php
require_once ($_CONF['path_html'] . 'forum/include/include_html.php');
require_once ($_CONF['path_html'] . 'forum/include/gf_showtopic.php');
require_once ($_CONF['path_html'] . 'forum/include/gf_format.php');
require_once ($_CONF['path_html'] . 'forum/include/lib-uploadfiles.php');
require_once($_CONF['path'] . 'plugins/forum/debug.php');  // Common Debug Code

// Display Common headers
gf_siteHeader();

// Pass thru filter any get or post variables to only allow numeric values and remove any hostile data
$id = COM_applyFilter($_REQUEST['id'],true);
$showtopic = COM_applyFilter($_REQUEST['showtopic'],true);
$editpid = COM_applyFilter($_POST['editpid'],true);
$forum = COM_applyFilter($_REQUEST['forum'],true);
$method = COM_applyFilter($_REQUEST['method']);
$page = COM_applyFilter($_REQUEST['page'],true);
$notify = COM_applyFilter($_POST['notify']);
$preview = COM_applyFilter($_REQUEST['preview']);
if (isset($_REQUEST['postmode'])) {
    $postmode = COM_applyFilter($_REQUEST['postmode']);
} else {
    if ($CONF_FORUM['allow_html'] == 0 OR $CONF_FORUM['post_htmlmode'] == 0) {
        $postmode = 'text';
    } else {
        $postmode = 'html';
    }
}
$postmode_switch = COM_applyFilter($_REQUEST['postmode_switch'],true);

ForumHeader($forum,$showtopic);

//Check is anonymous users can post
forum_chkUsercanPost();

if(empty($_USER['uid']) OR $_USER['uid'] == 1 ) {
    $uid = 1;
} else {
    $uid = $_USER['uid'];
}

$forumfiles = array();

// ADD EDITED TOPIC
if (($_POST['submit'] == $LANG_GF01['SUBMIT']) && ($_POST['editpost'] == 'yes')) {
    $editid = COM_applyFilter($_POST['editid'],true);
    $forum = COM_applyFilter($_POST['forum'],true);
    $date = time();

    $editAllowed = false;
    if (forum_modPermission($forum,$_USER['uid'],'mod_edit')) {
        $editAllowed = true;
    } else {
        if ($CONF_FORUM['allowed_editwindow'] > 0) {
            $t1 = DB_getItem($_TABLES['gf_topic'],'date',"id='$id'");
            $t2 = $CONF_FORUM['allowed_editwindow'];
            $time = time();
            if ((time() - $t2) < $t1) {
                $editAllowed = true;
            }
        } else {
            $editAllowed = true;
        }
    }

    if (($editpid < 1) && (trim($_POST['subject']) == '')) {
        BlockMessage('',$LANG_GF02['msg18'],false);
    } elseif (!$editAllowed) {
        $link = "{$_CONF['site_url']}/forum/viewtopic.php?showtopic={$id}";
        alertMessage('',$LANG_GF02['msg189'], sprintf($LANG_GF02['msg187'],$link));
    } else {
        if(strlen(trim($_POST['name'])) > $CONF_FORUM['min_username_length'] && strlen(trim($_POST['comment'])) > $CONF_FORUM['min_comment_length']) {
            if ($CONF_FORUM['use_spamx_filter'] == 1) {
                // Check for SPAM
                $spamcheck = '<h1>' . $_POST['subject'] . '</h1><p>' . $_POST['comment'] . '</p>';
                $result = PLG_checkforSpam($spamcheck, $_CONF['spamx']);
                // Now check the result and redirect to index.php if spam action was taken
                if ($result > 0) {
                    // then tell them to get lost ...
                    echo COM_showMessage( $result, 'spamx' );
                    gf_siteFooter();
                    exit;
                }
            }

            $postmode = gf_chkpostmode($postmode,$postmode_switch);
            $subject  = gf_preparefordb(strip_tags($_POST['subject']),'text');
            $comment  = gf_preparefordb($_POST['comment'],$postmode);
            $mood = COM_applyFilter($_POST['mood']);

            // If user has moderator edit rights only
            $locked = 0;
            $sticky = 0;
            if ($_POST['modedit'] == 1) {
                if ($_POST['locked_switch'] == 1)  $locked = 1;
                if ($_POST['sticky_switch'] == 1)  $sticky = 1;
            }
            $sql = "UPDATE {$_TABLES['gf_topic']} SET subject='$subject',comment='$comment',postmode='$postmode', ";
            $sql .= "mood='$mood', sticky='$sticky', locked='$locked' WHERE (id='$editid')";
            DB_query($sql);

            /* Check for any uploaded files  - during save of edit */
            gf_check4files($editid);

            // Check and see if there are no [file] bbcode tags in content and reset the show_inline value
            // This is needed in case user had used the file bbcode tag and then removed it
            $imagerecs = '';
            $imagerecs = implode(',',$forumfiles);
            $sql = "UPDATE {$_TABLES['gf_attachments']} SET show_inline = 0 WHERE topic_id=$editid ";
            if ($imagerecs != '') $sql .= "AND id NOT IN ($imagerecs)";
            DB_query($sql);

            $topicparent = DB_getITEM($_TABLES['gf_topic'],"pid","id='$editid'");
            if ($topicparent == 0) {
                $topicparent = $editid;
            }

            //NOTIFY - Checkbox variable in form set to "on" when checked and they have not already subscribed to forum
            $notifyRecID = DB_getItem($_TABLES['gf_watch'],'id', "forum_id='$forum' AND topic_id='$topicparent' AND uid='$uid'");
            if ($notify == 'on' AND $notifyRecID < 1) {
                DB_query("INSERT INTO {$_TABLES['gf_watch']} (forum_id,topic_id,uid,date_added) VALUES ('$forum','$topicparent','$_USER[uid]',now() )");
            } elseif ($notify == '' AND $notifyRecID > 1) {
                DB_query("DELETE FROM {$_TABLES['gf_watch']} WHERE id=$notifyRecID");
            }

            // if user has un-checked the Silent option then they want to have user alerted of the edit and update the topic timestamp
            if ($_POST['silentedit'] != 1) {
                DB_query("UPDATE {$_TABLES['gf_topic']} SET lastupdated = $date WHERE id=$topicparent");
                //Remove any lastviewed records in the log so that the new updated topic indicator will appear
                DB_query("DELETE FROM {$_TABLES['gf_log']} WHERE topic='$topicparent' and time > 0");
                // Check for any users subscribed notifications
                gf_chknotifications($forum,$editid,$uid);
            }
            $link = "{$_CONF['site_url']}/forum/viewtopic.php?showtopic=$topicparent&page=$page#$editid";
            forum_statusMessage($LANG_GF02['msg19'],$link,$LANG_GF02['msg19']);
        } else {
            alertMessage($LANG_GF02['msg18']);
        }
    }

    gf_siteFooter();
    exit;
}

// ADD TOPIC
if ($_POST['submit'] == $LANG_GF01['SUBMIT']) {
    $msg = '';
    $date = time();
    $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];

    if($method == 'newtopic') {
        if($_POST['aname'] != '') {
            $name = gf_preparefordb($_POST['aname'],'text');
        } else {
            $name = gf_preparefordb($_POST['name'],'text');
        }

        if ( function_exists('plugin_itemPreSave_captcha') ) {
            $msg = plugin_itemPreSave_captcha('forum',$_POST['captcha']);
            if ( $msg != '' ) {
                $preview = 'Preview';
                $subject = COM_stripslashes($_POST['subject']);
                $retval .= COM_startBlock ($LANG03[17], '',
                             COM_getBlockTemplate ('_msg_block', 'header'))
                        . $msg
                        . COM_endBlock(COM_getBlockTemplate ('_msg_block', 'footer'));
                echo $retval;
            }
        }
        if ( $msg == '' ) {
            if(strlen(trim($name)) > $CONF_FORUM['min_username_length'] AND
                strlen(trim($_POST['subject'])) > $CONF_FORUM['min_subject_length'] AND
                strlen(trim($_POST['comment'])) > $CONF_FORUM['min_comment_length']) {

                COM_clearSpeedlimit ($CONF_FORUM['post_speedlimit'], 'forum');
                $last = COM_checkSpeedlimit ('forum');
                if ($last > 0) {
                    $message = sprintf($LANG_GF01['SPEEDLIMIT'],$last,$CONF_FORUM['post_speedlimit']);
                    alertMessage($message,$LANG_GF02['msg180']);

                } else {
                    if ( $CONF_FORUM['use_spamx_filter'] == 1 ) {
                        // Check for SPAM
                        $spamcheck = '<h1>' . $_POST['subject'] . '</h1><p>' . $_POST['comment'] . '</p>';
                        $result = PLG_checkforSpam($spamcheck, $_CONF['spamx']);
                        // Now check the result and redirect to index.php if spam action was taken
                        if ($result > 0) {
                            // then tell them to get lost ...
                            echo COM_showMessage( $result, 'spamx' );
                            gf_siteFooter();
                            exit;
                        }
                    }
                    $postmode = gf_chkpostmode($postmode,$postmode_switch);
                    $subject = gf_preparefordb(strip_tags($_POST['subject']),'text');

                    if ( strlen ( $subject ) > 100 ) {
                        $subject = substr($subject,0,99);
                    }
                    $comment = gf_preparefordb($_POST['comment'],$postmode);
                    $mood = COM_applyFilter($_POST['mood']);
                    $locked = 0;
                    $sticky = 0;
                    if ($_POST['modedit'] == 1) {
                        if ($_POST['locked_switch'] == 1)  $locked = 1;
                        if ($_POST['sticky_switch'] == 1)  $sticky = 1;
                    }

                    $fields = "forum,name,date,lastupdated,subject,comment,postmode,ip,mood,uid,pid,sticky,locked";
                    $sql  = "INSERT INTO {$_TABLES['gf_topic']} ($fields) ";
                    $sql .= "VALUES ('$forum','$name','$date',$date,'$subject','$comment', ";
                    $sql .= "'$postmode','$REMOTE_ADDR','$mood','$uid','0','$sticky','$locked')";
                    DB_query($sql);

                    // Find the id of the last inserted topic
                    list ($lastid) = DB_fetchArray(DB_query("SELECT max(id) FROM {$_TABLES['gf_topic']} "));
                    /* Check for any uploaded files - during add of new topic */
                    gf_check4files($lastid);

                    // Check and see if there are no [file] bbcode tags in content and reset the show_inline value
                    // This is needed in case user had used the file bbcode tag and then removed it
                    $imagerecs = '';
                    $imagerecs = implode(',',$forumfiles);
                    $sql = "UPDATE {$_TABLES['gf_attachments']} SET show_inline = 0 WHERE topic_id=$lastid ";
                    if ($imagerecs != '') $sql .= "AND id NOT IN ($imagerecs)";
                    DB_query($sql);

                    // Update forums record
                    DB_query("UPDATE {$_TABLES['gf_forums']} SET post_count=post_count+1, topic_count=topic_count+1, last_post_rec=$lastid WHERE forum_id=$forum");

                    // Check for any users subscribed notifications - would only be for users subscribed to the forum
                    $notifyall = COM_applyFilter($_POST['chk_notifyall'],true);  // Force notifyication on for new topics option
                    gf_chknotifications($forum,$lastid,$uid,"forum",$notifyall);
                    //NOTIFY - Checkbox variable in form set to "on" when checked and they have not already subscribed to forum
                    $currentNotifyRecID = DB_getItem($_TABLES['gf_watch'],'id', "forum_id='$forum' AND topic_id=0 AND uid='$uid'");
                    if ($notify == 'on' AND $currentNotifyRecID < 1) {
                        DB_query("INSERT INTO {$_TABLES['gf_watch']} (forum_id,topic_id,uid,date_added) VALUES ('$forum','$lastid','$_USER[uid]',now() )");
                    } elseif ($notify == '' AND $currentNotifyRecID > 1) { // Subscribed to forum - but does not want to be notified about this topic
                        $nlastid = -$lastid;  // Negative Value
                        DB_query("DELETE FROM {$_TABLES['gf_watch']} WHERE uid='$uid' AND forum_id='$forum' and topic_id = '$lastid'");
                        DB_query("DELETE FROM {$_TABLES['gf_watch']} WHERE uid='$uid' AND forum_id='$forum' and topic_id = '$nlastid'");
                        DB_query("INSERT INTO {$_TABLES['gf_watch']} (forum_id,topic_id,uid,date_added) VALUES ('$forum','$nlastid','$uid',now() )");
                    }

                    COM_updateSpeedlimit ('forum');

                    // Insert a new log record for all logged in users that posted so it does not appear as new
                    if ($uid != '1') {
                        DB_query("INSERT INTO {$_TABLES['gf_log']} (uid,forum,topic,time) VALUES ('$_USER[uid]','$forum','$lastid','$date')");
                    }
                    forum_statusMessage($LANG_GF02['msg19'], $_CONF['site_url'] . "/forum/viewtopic.php?showtopic=$lastid",$LANG_GF02['msg19']);
                }

            } else {
                alertMessage($LANG_GF02['msg18']);
            }
        }
// END OF A NEW TOPIC...
     } elseif($method == 'postreply') {
        if ( function_exists('plugin_itemPreSave_captcha') ) {
            $msg = plugin_itemPreSave_captcha('forum',$_POST['captcha']);
            if ( $msg != '' ) {
                $preview = 'Preview';
                $subject = COM_stripslashes($_POST['subject']);
                $retval .= COM_startBlock ($LANG03[17], '',
                             COM_getBlockTemplate ('_msg_block', 'header'))
                        . $msg
                        . COM_endBlock(COM_getBlockTemplate ('_msg_block', 'footer'));
                echo $retval;
            }
        }
        if ( $msg == '' ) {
            //Add Reply
            if($_POST['aname'] != '') {
                $name = gf_preparefordb($_POST['aname'],'text');
            } else {
                $name = gf_preparefordb($_POST['name'],'text');
            }
            if($name != '' && strlen(trim($_POST['comment'])) > $CONF_FORUM['min_comment_length']) {

                COM_clearSpeedlimit ($CONF_FORUM['post_speedlimit'], 'forum');
                $last = COM_checkSpeedlimit ('forum');
                if ($last > 0) {
                    $message = sprintf($LANG_GF01['SPEEDLIMIT'],$last,$CONF_FORUM['post_speedlimit']);
                    alertMessage($message,$LANG_GF02['msg180']);

                } else {
                    if ( $CONF_FORUM['use_spamx_filter'] == 1 ) {
                        // Check for SPAM
                        $spamcheck = '<h1>' . $_POST['subject'] . '</h1><p>' . $_POST['comment'] . '</p>';
                        $result = PLG_checkforSpam($spamcheck, $_CONF['spamx']);
                        // Now check the result and redirect to index.php if spam action was taken
                        if ($result > 0) {
                            // then tell them to get lost ...
                            echo COM_showMessage( $result, 'spamx' );
                            gf_siteFooter();
                            exit;
                        }
                    }

                    DB_query("DELETE FROM {$_TABLES['gf_log']} WHERE topic='$id' and time > 0");

                    // Check for any users subscribed notifications
                    gf_chknotifications($forum,$id,$uid);
                    $postmode = gf_chkpostmode($postmode,$postmode_switch);
                    $subject = gf_preparefordb($_POST['subject'],'text');
                    $comment = gf_preparefordb($_POST['comment'],$postmode);
                    $mood = COM_applyFilter($_POST['mood']);

                    $fields = "name,date,subject,comment,postmode,ip,mood,uid,pid,forum";
                    $sql  = "INSERT INTO {$_TABLES['gf_topic']} ($fields) ";
                    $sql .= "VALUES  ('$name','$date','$subject','$comment',";
                    $sql .= "'$postmode','$REMOTE_ADDR','$mood','$uid','$id','$forum')";
                    DB_query($sql);

                    // Find the id of the last inserted topic
                    list ($lastid) = DB_fetchArray(DB_query("SELECT max(id) FROM {$_TABLES['gf_topic']} "));

                    /* Check for any uploaded files  - during adding reply post */
                    gf_check4files($lastid);

                    // Check and see if there are no [file] bbcode tags in content and reset the show_inline value
                    // This is needed in case user had used the file bbcode tag and then removed it
                    $imagerecs = '';
                    $imagerecs = implode(',',$forumfiles);
                    $sql = "UPDATE {$_TABLES['gf_attachments']} SET show_inline = 0 WHERE topic_id=$lastid ";
                    if ($imagerecs != '') $sql .= "AND id NOT IN ($imagerecs)";
                    DB_query($sql);

                    DB_query("UPDATE {$_TABLES['gf_topic']} SET replies=replies + 1, lastupdated = $date,last_reply_rec=$lastid WHERE id=$id");
                    DB_query("UPDATE {$_TABLES['gf_forums']} SET post_count=post_count+1, last_post_rec=$lastid WHERE forum_id=$forum");

                    //NOTIFY - Checkbox variable in form set to "on" when checked and they don't already have subscribed to forum or topic
                    $nid = -$id;  // Negative Topic ID Value
                    $currentForumNotifyRecID = DB_getItem($_TABLES['gf_watch'],'id', "forum_id='$forum' AND topic_id=0 AND uid='$uid'");
                    $currentTopicNotifyRecID = DB_getItem($_TABLES['gf_watch'],'id', "forum_id='$forum' AND topic_id=$id AND uid='$uid'");
                    $currentTopicUnNotifyRecID = DB_getItem($_TABLES['gf_watch'],'id', "forum_id='$forum' AND topic_id=$nid AND uid='$uid'");
                    if ($notify == 'on' AND $currentForumNotifyRecID < 1) {
                        $sql = "INSERT INTO {$_TABLES['gf_watch']} (forum_id,topic_id,uid,date_added) ";
                        $sql .= "VALUES ('$forum','$id','$_USER[uid]',now() )";
                        DB_query($sql);
                    } elseif ($notify == 'on' AND $currentTopicUnNotifyRecID > 1) { // Had un-subcribed to topic and now wants to subscribe
                        DB_query("DELETE FROM {$_TABLES['gf_watch']} WHERE id=$currentTopicUnNotifyRecID");
                    } elseif ($notify == '' AND $currentTopicNotifyRecID > 1) { // Subscribed to topic - but does not want to be notified anymore
                        DB_query("DELETE FROM {$_TABLES['gf_watch']} WHERE uid='$uid' AND forum_id='$forum' and topic_id = '$id'");
                    } elseif ($notify == '' AND $currentForumNotifyRecID > 1) { // Subscribed to forum - but does not want to be notified about this topic
                        DB_query("DELETE FROM {$_TABLES['gf_watch']} WHERE uid='$uid' AND forum_id='$forum' and topic_id = '$id'");
                        DB_query("DELETE FROM {$_TABLES['gf_watch']} WHERE uid='$uid' AND forum_id='$forum' and topic_id = '$nid'");
                        DB_query("INSERT INTO {$_TABLES['gf_watch']} (forum_id,topic_id,uid,date_added) VALUES ('$forum','$nid','$uid',now() )");
                    }
                    COM_updateSpeedlimit ('forum');
                    $link = "{$_CONF['site_url']}/forum/viewtopic.php?showtopic=$id&lastpost=true#$lastid";
                    forum_statusMessage($LANG_GF02['msg19'],$link,$LANG_GF02['msg19'],true,$forum);
                }

            } else {
                alertMessage($LANG_GF02['msg18']);
            }
        }
    }

    if ( $msg == '' ) {
        gf_siteFooter();
        exit;
    }
}


// EDIT MESSAGE
$comment = COM_stripslashes( $_POST['comment'] );

if ($id > 0) {
    $sql  = "SELECT a.forum,a.pid,a.comment,a.date,a.locked,a.subject,a.mood,a.sticky,a.uid,a.name,a.postmode,";
    $sql .= "b.forum_cat,b.forum_name,b.is_readonly,b.use_attachment_grpid,c.cat_name ";
    $sql .= "FROM {$_TABLES['gf_topic']} a ";
    $sql .= "LEFT JOIN {$_TABLES['gf_forums']} b ON b.forum_id=a.forum ";
    $sql .= "LEFT JOIN {$_TABLES['gf_categories']} c on c.id=b.forum_cat ";
    $sql .= "WHERE a.id=$id";
    $edittopic = DB_fetchArray(DB_query($sql),false);
} else {
    $sql  = "SELECT a.forum_name,a.is_readonly,a.use_attachment_grpid,b.cat_name ";
    $sql .= "FROM {$_TABLES['gf_forums']} a ";
    $sql .= "LEFT JOIN {$_TABLES['gf_categories']} b on b.id=a.forum_cat ";
    $sql .= "WHERE a.forum_id=$forum";
    $newtopic = DB_fetchArray(DB_query($sql),false);
}

if ($method == 'edit') {
    $editAllowed = false;
    if (forum_modPermission($edittopic['forum'],$_USER['uid'],'mod_edit')) {
        $editAllowed = true;
        echo '<INPUT TYPE="hidden" NAME="modedit" VALUE="1">';
    } else {
        // User is trying to edit their topic post - this is allowed
        if ($edittopic['date'] > 0 ) {
            if ($CONF_FORUM['allowed_editwindow'] > 0) {   // Check if edit timeframe is still valid
                $t2 = $CONF_FORUM['allowed_editwindow'];
                $time = time();
                if ((time() - $t2) < $edittopic['date']) {
                    $editAllowed = true;
                }
            } else {
                $editAllowed = true;
            }
        }
    }
    // Moderator or logged-in User is editing their topic post
    if ($_USER['uid'] > 1 AND $editAllowed) {
        // Check to see if user has this topic or complete forum is selected for notifications
        $fields1 = array( 'topic_id','uid' );
        $values1 = array( $id,$edittopic['uid'] );
        $fields2 = array( 'topic_id','forum_id','uid' );
        $values2 = array( 0,$edittopic['forum'],$edittopic['uid']);
        // Check if there are any notification records for the topic or the forum - topic_id = 0
        if ((DB_count($_TABLES['gf_watch'],$fields1,$values1) > 0) OR (DB_count($_TABLES['gf_watch'],$fields2,$values2) > 0)) {
            $notify_val= 'checked="checked"';
        }
    } else {
        alertMessage($LANG_GF02['msg72'],$LANG_GF02['msg191']);
        exit;
    }
}

// PREVIEW TOPIC
$numAttachments = 0;
if ($_REQUEST['preview'] == $LANG_GF01['PREVIEW']) {
    $previewitem = array();

    if ($method == 'edit') {
        $previewitem['uid']  = $edittopic['uid'];
        $previewitem['name'] = $edittopic['name'];
        /* Check for any uploaded files */
        $editpost = COM_applyfilter($_POST['id'],true);
        $previewitem['id'] = $editpost;
        gf_check4files($editpost);
        $numAttachments = DB_count($_TABLES['gf_attachments'],'topic_id',$editpost);
    } else {
        if ($uid > 1) {
            $previewitem['name'] = stripslashes($_POST['aname']);
            $previewitem['uid'] = $_USER['uid'];
        } else {
            $previewitem['name'] = stripslashes(urldecode($_POST['aname']));
            $previewitem['uid'] = 1;
        }
        /* Check for any uploaded files */
        gf_check4files($_POST['uniqueid'],true);
        $numAttachments = DB_count($_TABLES['gf_attachments'],array('topic_id','tempfile'),array($_POST['uniqueid'],1));
    }
    $previewitem['date'] = time();
    $subject = $_POST['subject'];
    $previewitem['subject'] = gf_checkHTML($subject);
    $previewitem['postmode'] = gf_chkpostmode($postmode,$postmode_switch);
    $previewitem['mood'] = $_POST['mood'];

    $previewitem['comment'] = trim($comment);

    $forum_outline_header = new Template($_CONF['path_layout'] . 'forum/layout');
    $forum_outline_header->set_file (array ('forum_outline_header'=>'forum_outline_header.thtml'));
    $forum_outline_header->set_var ('imgset', $CONF_FORUM['imgset']);
    $forum_outline_header->parse ('output', 'forum_outline_header');
    echo $forum_outline_header->finish($forum_outline_header->get_var('output'));

    $preview_header = new Template($_CONF['path_layout'] . 'forum/layout');
    $preview_header->set_file (array ('preview_header'=>'topicpreview_header.thtml'));
    $preview_header->set_var ('imgset', $CONF_FORUM['imgset']);
    $preview_header->set_var ('startblock', COM_startBlock('<b>' .$LANG_GF01['TopicPreview']. '</b>','','forum/layout/blockheader.thtml') );
    $preview_header->parse ('output', 'preview_header');
    echo $preview_header->finish($preview_header->get_var('output'));
    echo showtopic($previewitem,'preview');

    $preview_footer = new Template($_CONF['path_layout'] . 'forum/layout');
    $preview_footer->set_file (array ('preview_footer'=>'topicpreview_footer.thtml'));
    $preview_footer->set_var ('imgset', $CONF_FORUM['imgset']);
    $preview_footer->parse ('output', 'preview_footer');
    echo $preview_footer->finish($preview_footer->get_var('output'));

    $forum_outline_footer= new Template($_CONF['path_layout'] . 'forum/layout');
    $forum_outline_footer->set_file (array ('forum_outline_footer'=>'forum_outline_footer.thtml'));
    $forum_outline_footer->set_var ('imgset', $CONF_FORUM['imgset']);
    $forum_outline_footer->parse ('output', 'forum_outline_footer');
    echo $forum_outline_footer->finish ($forum_outline_footer->get_var('output'));
    echo '<br>';

    // If Moderator and editing the parent topic - see if form has skicky or locked checkbox on
    if ($editmoderator AND $editpid == 0) {
        if($method == 'edit') {
            if($_POST['locked_switch'] == 1 ) {
                $locked_val = 'checked="checked"';
            }
            if($_POST['sticky_switch'] == 1 ) {
                $sticky_val = 'checked="checked"';
            }
        }
    }
}

// NEW TOPIC OR REPLY
if(($method == 'newtopic' || $method == 'postreply' || $method == 'edit') || ($preview == "Preview")) {
    if ( $preview == 'Preview' ) {
        $edittopic['subject'] = COM_stripslashes($_POST['subject']);
    }
    // validate the forum is actually the forum the topic belongs in...
    if ( $method == 'postreply' || $method=='edit') {
        if ( ($forum != 0) && $forum != $edittopic['forum'] ) {
            echo '<br>';
            BlockMessage('ERROR',$LANG_GF02['msg87'],false);
            $forum_outline_footer= new Template($_CONF['path_layout'] . 'forum/layout');
            $forum_outline_footer->set_file (array ('forum_outline_footer'=>'forum_outline_footer.thtml'));
            $forum_outline_footer->set_var ('imgset', $CONF_FORUM['imgset']);
            $forum_outline_footer->parse ('output', 'forum_outline_footer');
            echo $forum_outline_footer->finish ($forum_outline_footer->get_var('output'));
            gf_siteFooter();
            exit;
        }
    }
    if ( $method == 'newtopic' && ($newtopic['is_readonly'] == 1 ) ) {
        /* Check if this user has moderation rights now to allow a post to a locked topic */
        if (!forum_modPermission($forum,$_USER['uid'],'mod_edit')) {
            echo '<br>';
            BlockMessage('ERROR',$LANG_GF02['msg87'],false);
            $forum_outline_footer= new Template($_CONF['path_layout'] . 'forum/layout');
            $forum_outline_footer->set_file (array ('forum_outline_footer'=>'forum_outline_footer.thtml'));
            $forum_outline_footer->set_var ('imgset', $CONF_FORUM['imgset']);
            $forum_outline_footer->parse ('output', 'forum_outline_footer');
            echo $forum_outline_footer->finish ($forum_outline_footer->get_var('output'));
            gf_siteFooter();
            exit;
        }
    }
    if ($method == 'postreply' AND ( $edittopic['locked'] == 1 || $edittopic['is_readonly'] == 1 )) {
        /* Check if this user has moderation rights now to allow a post to a locked topic */
        if (!forum_modPermission($edittopic['forum'],$_USER['uid'],'mod_edit')) {
            echo '<br>';
            BlockMessage('ERROR',$LANG_GF02['msg87'],false);
            $forum_outline_footer= new Template($_CONF['path_layout'] . 'forum/layout');
            $forum_outline_footer->set_file (array ('forum_outline_footer'=>'forum_outline_footer.thtml'));
            $forum_outline_footer->set_var ('imgset', $CONF_FORUM['imgset']);
            $forum_outline_footer->parse ('output', 'forum_outline_footer');
            echo $forum_outline_footer->finish ($forum_outline_footer->get_var('output'));
            gf_siteFooter();
            exit;
        }
    }

    $forum_outline_header = new Template($_CONF['path_layout'] . 'forum/layout');
    $forum_outline_header->set_file (array ('forum_outline_header'=>'forum_outline_header.thtml'));
    $forum_outline_header->set_var ('imgset', $CONF_FORUM['imgset']);
    $forum_outline_header->parse ('output', 'forum_outline_header');
    echo $forum_outline_header->finish($forum_outline_header->get_var('output'));

    if ($method == 'postreply' OR ($method == 'edit' AND $subject == '')) {
        $subject = $edittopic['subject'];
    } else {
        $subject = COM_stripslashes($subject);
    }

    $topicnavbar = new Template($_CONF['path_layout'] . 'forum/layout');
    $topicnavbar->set_file (array ('topicnavbar'=>'post_topic_navbar.thtml'));
    $topicnavbar->set_var ('imgset', $CONF_FORUM['imgset']);
    $topicnavbar->set_var ('navbreadcrumbsimg','<img src="'.gf_getImage('nav_breadcrumbs').'">');
    $topicnavbar->set_var ('navtopicimg','<img src="'.gf_getImage('nav_topic').'">');
    $topicnavbar->set_var ('site_url', $_CONF['site_url']);
    $topicnavbar->set_var ('layout_url', $_CONF['layout_url']);
    $topicnavbar->set_var ('phpself', $_CONF['site_url'] .'/forum/createtopic.php');

    if(empty($subject)) {
        $topicnavbar->set_var('show_subject','none');
    }

    if($method == 'newtopic' || $method == 'postreply') {
        $uniqueid = COM_applyFilter($_POST['uniqueid'],true);
        if ($uniqueid == 0) {
              $topicnavbar->set_var('uniqueid',mt_rand());
        } else {
            $topicnavbar->set_var('uniqueid',$uniqueid);
        }
    }

    if ($method == 'newtopic' AND $forum > 0 ) {  // User creating a newtopic
        $topicnavbar->set_var ('forum_id', $forum);
        $topicnavbar->set_var ('cat_name',$newtopic['cat_name']);
        $topicnavbar->set_var ('forum_name', $newtopic['forum_name']);
    } else {
        $topicnavbar->set_var ('forum_id', $edittopic['forum']);
        $topicnavbar->set_var ('cat_name',$edittopic['cat_name']);
        $topicnavbar->set_var ('forum_name', $edittopic['forum_name']);
    }
    // run the subject through the HTML filter to ensure no XSS
    // issues.
    $subject = gf_checkHTML($subject);
    $topicnavbar->set_var ('topic_id', $id);
    $topicnavbar->set_var ('subject', $subject);
    $topicnavbar->set_var ('LANG_HOME', $LANG_GF01['HOMEPAGE']);
    $topicnavbar->set_var('forum_home',$LANG_GF01['INDEXPAGE']);
    $topicnavbar->set_var ('hidden_id', $id);
    $topicnavbar->set_var ('hidden_editpost','');
    $topicnavbar->set_var ('hidden_editpid', '');
    $topicnavbar->set_var ('hidden_editid', '');
    $topicnavbar->set_var ('hidden_method', '');
    $topicnavbar->set_var ('page', $page);

    $topicnavbar->set_var ('LANG_bhelp', $LANG_GF01['b_help']);
    $topicnavbar->set_var ('LANG_ihelp', $LANG_GF01['i_help']);
    $topicnavbar->set_var ('LANG_uhelp', $LANG_GF01['u_help']);
    $topicnavbar->set_var ('LANG_qhelp', $LANG_GF01['q_help']);
    $topicnavbar->set_var ('LANG_chelp', $LANG_GF01['c_help']);
    $topicnavbar->set_var ('LANG_lhelp', $LANG_GF01['l_help']);
    $topicnavbar->set_var ('LANG_ohelp', $LANG_GF01['o_help']);
    $topicnavbar->set_var ('LANG_phelp', $LANG_GF01['p_help']);
    $topicnavbar->set_var ('LANG_whelp', $LANG_GF01['w_help']);
    $topicnavbar->set_var ('LANG_ahelp', $LANG_GF01['a_help']);
    $topicnavbar->set_var ('LANG_shelp', $LANG_GF01['s_help']);
    $topicnavbar->set_var ('LANG_fhelp', $LANG_GF01['f_help']);
    $topicnavbar->set_var ('LANG_hhelp', $LANG_GF01['h_help']);

    if (forum_modPermission($edittopic['forum'],$_USER['uid'],'mod_edit')) {
        $editmoderator = TRUE;
        $topicnavbar->set_var ('hidden_modedit', '1');
    } else {
        $topicnavbar->set_var ('hidden_modedit', '0');
        $editmoderator = FALSE;
    }

    if (empty($GLOBALS['gf_errmsg'])) {
        $topicnavbar->set_var('show_alert','none');
    } else {
        $topicnavbar->set_var('show_alert','');
        $topicnavbar->set_var('error_msg',$GLOBALS['gf_errmsg']);
    }
    if($method == 'newtopic') {
        $postmessage = $LANG_GF02['PostTopic'];
        $topicnavbar->set_var ('hidden_method', 'newtopic');
        $editpid = 0;
    } elseif($method == 'postreply') {
        $postmessage = $LANG_GF02['PostReply'];
        $topicnavbar->set_var ('hidden_method', 'postreply');
        if ( $preview != 'Preview' ) {
            $subject = $LANG_GF01['RE'] . $subject;
        }
        $quoteid = COM_applyFilter($_REQUEST['quoteid'],true);
        $edittopic['mood'] = '';
        if($quoteid > 0) {
            $quotesql = DB_query("SELECT * FROM {$_TABLES['gf_topic']} WHERE id='$quoteid'");
            $quotearray = DB_fetchArray($quotesql);
            $quotearray['comment'] = stripslashes($quotearray['comment']);
            if ($CONF_FORUM['pre2.5_mode'] == true ) {
                if ( $quotearray['postmode'] == 'html' || $quotearray['postmode'] == 'HTML' ) {
                    if (!class_exists('StringParser') ) {
                        require_once ($_CONF['path_html'] . 'forum/include/bbcode/stringparser_bbcode.class.php');
                    }
                    $comment = gf_formatOldPost($quotearray['comment'],'html');
                    $comment = sprintf($CONF_FORUM['quoteformat'],$quotearray['name'],$comment);
                } else {
                    $quotearray['comment'] = str_replace("&#36;","$", $quotearray['comment']);
                    $comment = sprintf($CONF_FORUM['quoteformat'],$quotearray['name'],$quotearray['comment']);
                }
            } else {
                $comment = sprintf($CONF_FORUM['quoteformat'],$quotearray['name'],$quotearray['comment']);
            }
        }

        $editpid=$id;

    } elseif($method == 'edit') {
        $postmessage = $LANG_GF02['EditTopic'];
        $topicnavbar->set_var ('hidden_method', 'edit');
        $topicnavbar->set_var ('hidden_editpost','yes');
        if ($editmoderator) {
            $username = $edittopic['name'];
        } elseif ($uid > 1) {
            $username = COM_getDisplayName($uid);
        }

        $subject = $edittopic['subject'];
        if($preview != 'Preview') {
            $comment = str_ireplace('</textarea>','&lt;/textarea&gt;',$edittopic['comment']);
            $postmode = $edittopic['postmode'];
        } else {
            $comment = str_ireplace('</textarea>','&lt;/textarea&gt;',$comment);
            $postmode = $_POST['postmode'];
        }
        if (strstr($edittopic['comment'],'<pre class="forumCode">') === false) {
            $comment = htmlspecialchars($comment,ENT_QUOTES, $CONF_FORUM['charset']);
        }
        $editpid = $edittopic['pid'];
        $topicnavbar->set_var ('hidden_editpid', $editpid);
        $topicnavbar->set_var ('hidden_editid', $id);

    }
    $topicnavbar->parse ('output', 'topicnavbar');
    echo $topicnavbar->finish($topicnavbar->get_var('output'));

    if ($uid < 2) {
        $submissionformtop = new Template($_CONF['path_layout'] . 'forum/layout');
        $submissionformtop->set_file (array ('submissionformtop'=>'submissionform_anontop.thtml'));
        $submissionformtop->set_var ('layout_url', $_CONF['layout_url']);
        $submissionformtop->set_var ('post_message', $postmessage);
        $submissionformtop->set_var ('LANG_NAME', $LANG_GF02['msg33']);
        $submissionformtop->set_var ('name', stripcslashes($_POST['aname']));
        $submissionformtop->parse ('output', 'submissionformtop');
        echo $submissionformtop->finish($submissionformtop->get_var('output'));

    } else {
        $submissionformtop = new Template($_CONF['path_layout'] . 'forum/layout');
        $submissionformtop->set_file (array ('submissionformtop'=>'submissionform_membertop.thtml'));
        $submissionformtop->set_var ('layout_url', $_CONF['layout_url']);
        $submissionformtop->set_var ('post_message', $postmessage);
        $submissionformtop->set_var ('LANG_NAME', $LANG_GF02['msg33']);

        if (!isset($username) OR $username == '') {
            if ($method == 'edit') {
                if ($editmoderator) {
                    $username = $username;
                } elseif ($useredit == $LANG_GF01['YES']) {
                    $username = COM_getDisplayName($_USER['uid']);
                }
            } else {
                $username = COM_getDisplayName($_USER['uid']);
            }
        }

        $submissionformtop->set_var ('username', $username);
        $submissionformtop->set_var ('xusername', urlencode($username));
        $submissionformtop->parse ('output', 'submissionformtop');
        echo $submissionformtop->finish($submissionformtop->get_var('output'));
    }

    if ($CONF_FORUM['show_moods']) {
        if ($_POST['mood'] != '') {
            $edittopic['mood'] = $_POST['mood'];
        }
        if ($edittopic['mood'] == '') {
            $moodoptions = '<OPTION value="" SELECTED>' . $LANG_GF01['NOMOOD'];
        }
        if ($dir = @opendir("{$CONF_FORUM['imgset_path']}/moods")) {
            while (($file = readdir($dir)) !== false) {
                if ((strlen($file) > 3) && eregi('gif',$file)) {
                    $file = str_replace(array('.gif','.jpg'), array('',''), $file);
                    if($file == $edittopic['mood']) {
                        $moodoptions .= "<OPTION SELECTED>" . $file. "\n";
                    } else {
                        $moodoptions .= "<OPTION>" .$file. "\n";
                    }
                } else {
                    $moodoptions .= '';
                }
            }
            closedir($dir);
        }

        $submissionform_moods = new Template($_CONF['path_layout'] . 'forum/layout');
        $submissionform_moods->set_file (array ('submissionform_moods'=>'submissionform_moods.thtml'));
        $submissionform_moods->set_var ('LANG_MOOD', $LANG_GF02['msg36']);
        $submissionform_moods->set_var ('moodoptions', $moodoptions);
        $submissionform_moods->parse ('output', 'submissionform_moods');
        echo $submissionform_moods->finish($submissionform_moods->get_var('output'));
    }

    $sub_dot = '...';
    $sub_none = '';
    $subject = str_replace($sub_dot, $sub_none, $subject);
    if($method == 'newtopic') {
        $required = $LANG_GF01['REQUIRED'];
    } elseif($method == 'postreply') {
        $required = $LANG_GF01['OPTIONAL'];
    } elseif($method == 'edit') {
        if ($editpid == 0) {
            $required = $LANG_GF01['REQUIRED'];
        } else {
            $required = $LANG_GF01['OPTIONAL'];
        }
    }

    // Now check if you need to show the HTML attribute editing buttons and BB code display field
    $chkpostmode = gf_chkpostmode($postmode,$postmode_switch);
    if ($chkpostmode != $postmode) {
        $postmode = $chkpostmode;
        $postmode_switch = 0;
    }

    $submissionform_code = new Template($_CONF['path_layout'] . 'forum/layout');
    $submissionform_code->set_file (array ('submissionform_code'=>'submissionform_code.thtml'));
    $submissionform_code->set_var ('site_url', $_CONF['site_url']);
    $submissionform_code->set_var ('LANG_code', $LANG_GF01['CODE']);
    $submissionform_code->set_var ('LANG_fontcolor', $LANG_GF01['FONTCOLOR']);
    $submissionform_code->set_var ('LANG_fontsize', $LANG_GF01['FONTSIZE']);
    $submissionform_code->set_var ('LANG_closetags', $LANG_GF01['CLOSETAGS']);
    $submissionform_code->set_var ('LANG_codetip', $LANG_GF01['CODETIP']);
    $submissionform_code->set_var ('LANG_tiny', $LANG_GF01['TINY']);
    $submissionform_code->set_var ('LANG_small', $LANG_GF01['SMALL']);
    $submissionform_code->set_var ('LANG_normal', $LANG_GF01['NORMAL']);
    $submissionform_code->set_var ('LANG_large', $LANG_GF01['LARGE']);
    $submissionform_code->set_var ('LANG_huge', $LANG_GF01['HUGE']);

    $submissionform_code->set_var ('LANG_default', $LANG_GF01['DEFAULT']);
    $submissionform_code->set_var ('LANG_dkred', $LANG_GF01['DKRED']);
    $submissionform_code->set_var ('LANG_red', $LANG_GF01['RED']);
    $submissionform_code->set_var ('LANG_orange', $LANG_GF01['ORANGE']);
    $submissionform_code->set_var ('LANG_brown', $LANG_GF01['BROWN']);
    $submissionform_code->set_var ('LANG_yellow', $LANG_GF01['YELLOW']);
    $submissionform_code->set_var ('LANG_green', $LANG_GF01['GREEN']);
    $submissionform_code->set_var ('LANG_olive', $LANG_GF01['OLIVE']);
    $submissionform_code->set_var ('LANG_cyan', $LANG_GF01['CYAN']);
    $submissionform_code->set_var ('LANG_blue', $LANG_GF01['BLUE']);
    $submissionform_code->set_var ('LANG_dkblue', $LANG_GF01['DKBLUE']);
    $submissionform_code->set_var ('LANG_indigo', $LANG_GF01['INDIGO']);
    $submissionform_code->set_var ('LANG_violet', $LANG_GF01['VIOLET']);
    $submissionform_code->set_var ('LANG_white', $LANG_GF01['WHITE']);
    $submissionform_code->set_var ('LANG_black', $LANG_GF01['BLACK']);

    if ($CONF_FORUM['allow_img_bbcode']) {
        $submissionform_code->set_var ('hide_imgbutton_begin','');
        $submissionform_code->set_var ('hide_imgbutton_end','');
    } else {
        $submissionform_code->set_var ('hide_imgbutton_begin','<!--');
        $submissionform_code->set_var ('hide_imgbutton_end','-->');
    }

    $submissionform_code->parse ('output', 'submissionform_code');
    echo $submissionform_code->finish($submissionform_code->get_var('output'));

    if(!$CONF_FORUM['allow_smilies']) {
        $smilies = '';
    } else {
        $smilies =  forumPLG_showsmilies();
    }

    // if this is the first time showing the new submission form - then check if notify option should be on
    if (!isset($_POST['preview'])) {
        if ($editpid > 0) {
            $notifyTopicid = $editpid;
        } else {
            $notifyTopicid = $id;
        }
        if ($CONF_FORUM['mysql4+']) {
            $sql  = "(SELECT id FROM {$_TABLES['gf_watch']} WHERE ((topic_id='$notifyTopicid' AND uid='$uid')) ) UNION ALL ";
            $sql .= "(SELECT id FROM {$_TABLES['gf_watch']} WHERE ((forum_id='{$edittopic['forum']}') AND (topic_id='0') and (uid='$uid')) ) ";
            $notifyquery = DB_query($sql);
        } else {
            $sql = "SELECT id FROM {$_TABLES['gf_watch']} WHERE ((topic_id='$notifyTopicid' AND uid='$uid') ";
            $sql .= "OR ((forum_id='{$edittopic['forum']}') AND (topic_id='0') and (uid='$uid')))";
            $notifyquery = DB_query($sql);
        }

        if (DB_getItem($_TABLES['gf_userprefs'],'alwaysnotify', "uid='$uid'") == 1 OR DB_numRows($notifyquery) > 0) {
            $notify = 'on';
            // check and see if user has un-subscribed to this topic
            $nid = -$notifyTopicid;
            if ($notifyTopicid > 0 AND DB_getItem($_TABLES['gf_watch'],'id', "forum_id='{$edittopic['forum']}' AND topic_id=$nid AND uid='$uid'") > 1) {
                $notigy = '';
            }
        } else {
            $notify = '';
        }
    }
    if ($editmoderator) {
        if ($notify == 'on' OR $_POST['notify'] == 'on') {
            $notify_val = 'checked=CHECKED';
        } else {
            $notify_val = '';
        }
        $notify_prompt = $LANG_GF02['msg38']. '<br><input type="checkbox" name="notify" ' .$notify_val. '>';

        // check that this is the parent topic - only able to make it skicky or locked
        if ($editpid == 0) {
            if (!isset($locked_val) and !isset($sticky_val) AND $method == 'edit') {
                if( (!isset($_POST['locked_switch']) AND $edittopic['locked'] == 1) OR $_POST['locked_switch'] == 1 ) {
                    $locked_val = 'checked="checked"';
                }
                if( (!isset($_POST['sticky_switch']) AND $edittopic['sticky'] == 1) OR $_POST['sticky_switch'] == 1 ) {
                    $sticky_val = 'checked="checked"';
                }
            }
            $locked_prompt = $LANG_GF02['msg109']. '<br><input type="checkbox" name="locked_switch" ' .$locked_val. ' VALUE=1>';
            $sticky_prompt = $LANG_GF02['msg61']. '<br><input type="checkbox" name="sticky_switch" ' .$sticky_val. ' VALUE=1>';
        }
    } else {
        if ($uid > 1) {
            if ($notify == 'on') {
                $notify_val = 'checked="checked"';
            } else {
                $notify_val = '';
            }
            $notify_prompt = $LANG_GF02['msg38']. '<br><input type="checkbox" name="notify" ' .$notify_val. '>';
            $locked_prompt = '';
        } else {
            $notify_prompt = '';
            $locked_prompt = '';
        }
    }

    if($postmode == 'html' || $postmode == 'HTML') {
        $postmode_msg = $LANG_GF01['TEXTMODE'];
    } else {
         $postmode_msg = $LANG_GF01['HTMLMODE'];
    }
    if($CONF_FORUM['allow_html'] || SEC_inGroup( 'Root' )) {
        $mode_prompt = $postmode_msg. '<br><input type="checkbox" name="postmode_switch" value="1"><input type="hidden" name="postmode" value="{postmode}">';
    }

    $submissionform_main = new Template($_CONF['path_layout'] . 'forum/layout');
    $submissionform_main->set_file (array ('submissionform_main'=>'submissionform_main.thtml'));

    if($method == 'edit') {
        if ($CONF_FORUM['pre2.5_mode']) {
            /* Reformat code blocks - version 2.3.3 and prior */
            $comment = str_replace( '<pre class="forumCode">', '[code]', $comment );
            $comment = str_replace( '<pre>', '[code]', $comment );
            $comment = str_replace( '</pre>', '[/code]', $comment );
        }
        $edit_prompt = $LANG_GF02['msg190'] . '<br><input type="checkbox" name="silentedit" ';
        if ($_POST['silentedit'] == 1 OR ( !isset($_POST['modedit']) AND $CONF_FORUM['silent_edit_default'])) {
             $edit_prompt .= 'checked="checked" ';
        }
        $edit_prompt .= 'value="1">';
        $submissionform_main->set_var('attachments',gf_showattachments($id,'edit'));

    } else {
        $edit_prompt = '&nbsp;';
        $submissionform_main->set_var('attachments','');
        if ($uniqueid > 0) {
            $submissionform_main->set_var('attachments',gf_showattachments($uniqueid,'edit'));
        }
    }

    $subject = str_replace('"', '&quot;',$subject);
    $submissionform_main->set_unknowns('keep');
    $submissionform_main->set_var ('LANG_SUBJECT', $LANG_GF01['SUBJECT']);
    $submissionform_main->set_var ('LANG_OPTIONS', $LANG_GF01['OPTIONS']);
    $submissionform_main->set_var ('mode_prompt', $mode_prompt);
    $submissionform_main->set_var ('notify_prompt', $notify_prompt);
    $submissionform_main->set_var ('locked_prompt', $locked_prompt);
    $submissionform_main->set_var ('sticky_prompt', $sticky_prompt);
    $submissionform_main->set_var ('edit_prompt', $edit_prompt);
    $submissionform_main->set_var ('LANG_SUBMIT', $LANG_GF01['SUBMIT']);
    $submissionform_main->set_var ('LANG_PREVIEW', $LANG_GF01['PREVIEW']);
    $submissionform_main->set_var ('required', $required);
    $submissionform_main->set_var ('subject', $subject);
    $submissionform_main->set_var ('smilies', $smilies);
    $submissionform_main->set_var ('LANG_attachments',$LANG_GF10['attachments']);
    $submissionform_main->set_var ('LANG_maxattachments',sprintf($LANG_GF10['maxattachments'],$CONF_FORUM['maxattachments']));

    // Check and see if the filemgmt plugin is installed and enabled
    if (function_exists('filemgmt_buildAccessSql')) {
        // Generate the select dropdown HTML for the filemgmt categories
        $submissionform_main->set_var('filemgmt_category_options',gf_makeFilemgmtCatSelect($uid));
        $submissionform_main->set_var('LANG_usefilemgmt',$LANG_GF10['usefilemgmt']);
        $submissionform_main->set_var('LANG_description', $LANG_GF10['description']);
        $submissionform_main->set_var('LANG_category', $LANG_GF10['category']);
    } elseif (function_exists('fm_recursiveCatAddFileList')) {
        // Generate the select dropdown HTML for the nexfile categories user has upload_direct access right
        $htmlOptions = fm_recursiveCatAddFileList('');
        if ($htmlOptions == '') {
            $submissionform_main->set_var('show_filemgmt_option','none');
        } else {
            $html  = '<select name="filemgmtcat"  style="text-indent: 0px;"><option value="0">'.$LANG_FM02['SELECT_CATEGORY'].'</option>';
            $html .= $htmlOptions . '</select>';
            $submissionform_main->set_var('filemgmt_category_options',$html);
            $submissionform_main->set_var('LANG_usefilemgmt',$LANG_GF10['usefilemgmt']);
            $submissionform_main->set_var('LANG_description', $LANG_GF10['description']);
            $submissionform_main->set_var('LANG_category', $LANG_GF10['category']);
        }
    } else {
        $submissionform_main->set_var('show_filemgmt_option','none');
    }

    if ($uid == 1) {
        $submissionform_main->set_var ('hide_notify','none');
    }
    if ( function_exists('plugin_templatesetvars_captcha') ) {
        plugin_templatesetvars_captcha('forum', $submissionform_main);
    } else {
        $submissionform_main->set_var ('captcha','');
    }

    // Check and see if user is allowed to add attachments and has not exceeded max allowed
    if ($method == 'newtopic' AND SEC_inGroup($CONF_FORUM['notifyall_allowedGroup'])) {
		$submissionform_main->set_var('check_notifyall','CHECKED=checked');
        if (!SEC_inGroup($newtopic['use_attachment_grpid'])) {
            $submissionform_main->set_var('use_attachments','none');
        } elseif($numAttachments >= $CONF_FORUM['maxattachments']) {
            $submissionform_main->set_var('show_attachments','none');
        }
        if ($CONF_FORUM['notifyall_default'] == 1) {
            $submissionform_main->set_var('chk_notifyall','CHECKED=checked');
        }
    } else {
		$submissionform_main->set_var('show_notifyall','none');
        if (!SEC_inGroup($edittopic['use_attachment_grpid'])) {
           $submissionform_main->set_var('use_attachments','none');
        } elseif ($numAttachments >= $CONF_FORUM['maxattachments']) {
            $submissionform_main->set_var('show_attachments','none');
        }
    }

    /* Topic notification Options - show/hide as per config settings */
    if (!SEC_inGroup($CONF_FORUM['notifyoptions_allowedGroup'])) {
        $submissionform_main->set_var('show_notifyoptions','none');
    } else {
        $submissionform_main->set_var('lang_notify1',$LANG_GF11['basic']);
        $submissionform_main->set_var('lang_notify2',$LANG_GF11['fullpost']);
        $submissionform_main->set_var('lang_notify3',$LANG_GF11['summary']);
        $submissionform_main->set_var('lang_notify4',$LANG_GF11['custom']);
        $submissionform_main->set_var('lang_notifyoptions',$LANG_GF11['notifyoptions']);
        $submissionform_main->set_var('lang_notifyoverride',$LANG_GF11['notifyoverride']);
        $submissionform_main->set_var('lang_notifycustom',$LANG_GF11['notifycustom']);
        $submissionform_main->set_var('lang_custommsg',$LANG_GF11['custom_msg']);

        $submissionform_main->set_var('show_custombox','none');
        if ($CONF_FORUM['notify_defaultOption'] == 'basic') {
            $submissionform_main->set_var('sel_notify1','SELECTED=selected');
        } elseif ($CONF_FORUM['notify_defaultOption'] == 'full') {
            $submissionform_main->set_var('sel_notify2','SELECTED=selected');
        } elseif ($CONF_FORUM['notify_defaultOption'] == 'summary') {
            $submissionform_main->set_var('sel_notify3','SELECTED=selected');
        } elseif ($CONF_FORUM['notify_defaultOption'] == 'custom') {
            $submissionform_main->set_var('sel_notify4','SELECTED=selected');
            $submissionform_main->set_var('show_custombox','');
        }
    }
    if($method == 'edit') {
        if($CONF_FORUM['allow_smilies']) {
            if (function_exists(msg_restoreEmoticons) AND $CONF_FORUM['use_smilies_plugin']) {
                $comment = msg_restoreEmoticons($comment);
            } else {
                $comment = forum_xchsmilies($comment,true);
            }
        }
        $submissionform_main->set_var ('post_message', $comment);
    } else {
        $submissionform_main->set_var ('post_message', htmlspecialchars($comment,ENT_QUOTES, $CONF_FORUM['charset']));
    }

    $submissionform_main->set_var ('postmode', $postmode);
    $submissionform_main->parse ('output', 'submissionform_main');
    echo $submissionform_main->finish($submissionform_main->get_var('output'));
    echo '</FORM>';

    $forum_outline_footer= new Template($_CONF['path_layout'] . 'forum/layout');
    $forum_outline_footer->set_file (array ('forum_outline_footer'=>'forum_outline_footer.thtml'));
    $forum_outline_footer->set_var ('imgset', $CONF_FORUM['imgset']);
    $forum_outline_footer->parse ('output', 'forum_outline_footer');
    echo $forum_outline_footer->finish ($forum_outline_footer->get_var('output'));

    //Topic Review
    if(($method != 'newtopic' && $_POST['editpost'] != 'yes') && ($method == 'postreply' || $preview == 'Preview')) {
        if ($CONF_FORUM['show_topicreview']) {
            echo "<IFRAME SRC=\"{$_CONF['site_url']}/forum/viewtopic.php?mode=preview&showtopic=$id&onlytopic=1&lastpost=true\" HEIGHT=\"300\" WIDTH=\"100%\"></IFRAME>";
        }
    }
    //End Topic Review

}

gf_siteFooter();


/*
* Function is called to check for notifications that may be setup by forum users
* A record in the forum_watch table is created for each users's subsctribed notifications
* Users can subscribe to a complete forum or individual topics.
* If they have both selected - we only want to send one notification - hense the SQL LIMIT 1
*
* This function needs to be called when there is a new topic or a reply
*/
function gf_chknotifications($forumid,$topicid,$userid,$type='topic',$notifyall=0) {
    global $_TABLES,$LANG_GF01,$LANG_GF02,$_CONF,$CONF_FORUM,$_USER;

    if ($CONF_FORUM['allow_notification'] != '1') {
        return;
    }

    $pid = DB_getItem($_TABLES['gf_topic'],pid,"id='$topicid'");
    if ($pid == 0) {
      $pid = $topicid;
    }

	if ($notifyall == 1) {
		// Get the groupid for this forum and retrieve all the users that have view access for notification
		$grp_id = DB_getItem($_TABLES['gf_forums'], 'grp_id',"forum_id=$forumid");
		require_once( $_CONF['path_system'] . 'lib-user.php' );
		$groups = USER_getChildGroups ($grp_id);
		$groupList = implode (',', $groups);

		$sql = "SELECT DISTINCT {$_TABLES['users']}.uid "
		  ."FROM {$_TABLES['group_assignments']},{$_TABLES['users']} $join_userinfo "
		  ."WHERE {$_TABLES['users']}.uid > 1 "
		  ."AND {$_TABLES['users']}.uid = {$_TABLES['group_assignments']}.ug_uid "
		  ."AND ({$_TABLES['group_assignments']}.ug_main_grp_id IN ({$groupList}))";
    	$sqlresult = DB_query($sql);
	} else {
	    $sql = "SELECT * FROM {$_TABLES['gf_watch']} WHERE ((topic_id='$pid') OR ((forum_id='$forumid') AND (topic_id='0') )) GROUP BY uid";
    	$sqlresult = DB_query($sql);
	}
    $postername = COM_getDisplayName($userid);
    $nrows = DB_numRows($sqlresult);

    $topicrecsql = DB_query("SELECT subject,name,forum FROM {$_TABLES['gf_topic']} WHERE id='$pid'");
    $topicrec = DB_fetchArray($topicrecsql);
    $from = '';     // Default to being from the site

    /* Option only used in internal sites where email addresses can be shown
     * Needs option in the submissionform_main.thtml as well
    */
    $_POST['chk_notifypersonal']  = 0;   // BL Set to disable feature - remove if being used

    if ($_POST['chk_notifypersonal'] == 1) {
        $query = DB_query("SELECT username,fullname,email FROM {$_TABLES['users']} WHERE uid={$_USER['uid']}");
        list ($username,$fullname,$email) = DB_fetchArray($query);
        if ($fullname != '') {
            $from = "$fullname <$email>";
        } else {
            $from = "$usernmae <$email>";
        }
    }
   $notifyusers = array();
    $email_allusers = '';

	// Loop through all the user records to notify
	for ($i =1; $i <= $nrows; $i++) {
        $N = DB_fetchArray($sqlresult);
        // Don't need to send a notification to the user that posted this message and users with NOTIFY disabled
        if ($N['uid'] > 1 AND $N['uid'] != $userid ) {

            // if the topic_id is 0 for this record - user has subscribed to complete forum. Check if they have opted out of this forum topic.
            if (DB_count($_TABLES['gf_watch'],array('uid','forum_id','topic_id'),array($N['uid'],$forumid,-$topicid)) == 0) {

                // Check if user does not want to receive multiple notifications for same topic and already has been notified
                $userNotifyOnceOption = DB_getItem($_TABLES['gf_userprefs'],'notify_once',"uid='{$N['uid']}'");
                // Retrieve the log record for this user if it exists then check if user has viewed this topic yet
                // The logtime value may be 0 which indicates the user has not yet viewed the topic
                $lsql = DB_query("SELECT time FROM {$_TABLES['gf_log']} WHERE uid='{$N['uid']}' AND forum='$forumid' AND topic='$topicid'");
                if (DB_numRows($lsql) == 1) {
                    $nologRecord = false;
                    list ($logtime) = DB_fetchArray($lsql);
                } else {
                    $nologRecord = true;
                    $logtime = 0;
                }

                if  ($userNotifyOnceOption == 0 OR ($userNotifyOnceOption == 1 AND ($nologRecord OR $logtime != 0)) ) {
                    $sql = "SELECT username,fullname,email,status FROM {$_TABLES['users']} WHERE uid={$N['uid']}";
                    $userrec = DB_fetchArray(DB_query($sql),false);
                    $notifyusers[$N['uid']] = $userrec;
                    if ($email_allusers != '') $email_allusers .= ',';
                    $email_allusers .= $userrec['email'];
                }
            }
        }
    }

	// Manually now add the user who is posting so they are on the email notification
	$myemail = DB_getItem($_TABLES['users'],'email',"uid={$_USER['uid']}");
    if ($email_allusers != '') $email_allusers .= ',';
    $email_allusers .= $myemail;

    $notifyextracontent = '';
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

    if (count($notifyusers) > 0) {
        $subjectline = "{$_CONF['site_name']} {$LANG_GF02['msg22']}";
        if ($_POST['chk_notifypersonal'] == 1) {
            $toemail = $email_allusers;
            $message  = "{$LANG_GF01['HELLOMEMBER']},\n\n";
            if ($type=='forum') {
                $forum_name = DB_getItem($_TABLES['gf_forums'],forum_name, "forum_id='$forumid'");
                $message .= sprintf($LANG_GF02['notify_newtopic'],$topicrec['subject'],$topicrec['name'],$forum_name, $_CONF['site_name'],$_CONF['site_url'],$pid);
            } else {
                $message .= sprintf($LANG_GF02['notify_newreply'],$topicrec['subject'],$postername,$topicrec['name'],$_CONF['site_name'],$_CONF['site_url'],$pid);
            }
			$message .= $LANG_GF02['replynote'];

            $message .= $notifyextracontent;
            $message .= sprintf($LANG_GF02['notify_reason'],"{$_CONF['site_url']}/forum/notify.php",$_CONF['site_name'],$LANG_GF01['ADMIN']);

            if ($nologRecord and $userNotifyOnceOption == 1 ) {
                DB_query("INSERT INTO {$_TABLES['gf_log']} (uid,forum,topic,time) VALUES ($touid, '$forumid', '$topicid','0') ");
            }
            COM_errorLog("COM_mail($toemail,$subjectline,$message");
            COM_mail($toemail,$subjectline,$message,$from);

        } else {

            reset($notifyusers);
            foreach ($notifyusers as $touid => $B) {
                if ($B['status'] == USER_ACCOUNT_ACTIVE) {
                    if ($fullname != '') {
                        $nameparts = explode(' ',$B['fullname']);
                        $toname = ucfirst($nameparts[0]);
                    } else {
                        $toname = $B['username'];
                    }
                    $message  = "{$LANG_GF01['HELLO']} $toname,\n\n";

                    if ($type=='forum') {
                        $forum_name = DB_getItem($_TABLES['gf_forums'],forum_name, "forum_id='$forumid'");
                        $message .= sprintf($LANG_GF02['notify_newtopic'],$topicrec['subject'],$topicrec['name'],$forum_name, $_CONF['site_name'],$_CONF['site_url'],$pid);
                    } else {
                        $message .= sprintf($LANG_GF02['notify_newreply'],$topicrec['subject'],$postername,$topicrec['name'],$_CONF['site_name'],$_CONF['site_url'],$pid);
                    }
                    $message .= $notifyextracontent;
                    $message .= sprintf($LANG_GF02['notify_reason'],"{$_CONF['site_url']}/forum/notify.php",$_CONF['site_name'],$LANG_GF01['ADMIN']);

                    if ($nologRecord and $userNotifyOnceOption == 1 ) {
                        DB_query("INSERT INTO {$_TABLES['gf_log']} (uid,forum,topic,time) VALUES ($touid, '$forumid', '$topicid','0') ");
                    }
                    COM_errorLog("COM_mail({$B['email']},$subjectline,$message");
                    COM_mail($B['email'],$subjectline,$message);
                }
            }
        }
    }

}

?>