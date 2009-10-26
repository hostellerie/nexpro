<?php
 /* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Geeklog Forums Plugin 2.6 for Geeklog - The Ultimate Weblog               |
// | Release date: Oct 30,2006                                                 |
// +---------------------------------------------------------------------------+
// | Moderation.php                                                            |
// | Forum Moderation Program                                                  |
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

require_once("../lib-common.php");
require_once ($_CONF['path_html'] . 'forum/include/gf_format.php');
require_once ($_CONF['path_html'] . 'forum/include/gf_showtopic.php');
require_once($_CONF['path'] . 'plugins/forum/debug.php');  // Common Debug Code

// Display Common headers
gf_siteHeader();

// Check for access privilege and pass true to check that user is signed in.
forum_chkUsercanAccess(true);
$forum = COM_applyFilter($_REQUEST['forum'],true);
$showtopic = COM_applyFilter($_REQUEST['showtopic'],true);
ForumHeader($forum,$showtopic);

// Pass thru filter any get or post variables to only allow numeric values and remove any hostile data
$fortopicid = COM_applyFilter($_REQUEST['fortopicid'],true);
$moveid = COM_applyFilter($_REQUEST['moveid'],true);
$top = COM_applyFilter($_REQUEST['top']);
$movetoforum = COM_applyFilter($_REQUEST['movetoforum'],true);
$msgid = COM_applyFilter($_REQUEST['msgid'],true);
$msgpid = COM_applyFilter($_REQUEST['msgpid'],true);
$fortopicid = COM_applyFilter($_REQUEST['fortopicid'],true);
$modfunction = COM_applyFilter($_REQUEST['modfunction']);
$submit = $_POST['submit'];

if ($forum == 0) {
    alertMessage($LANG_GF02['msg71']);
    exit();
}

if (forum_modPermission($forum,$_USER['uid'])) {

    //Moderator check OK, everything dealing with moderator permissions go here.
    if($_POST['modconfirmdelete'] == 1 && $msgid != '') {
        if ($submit == $LANG_GF01['CANCEL']) {
               echo COM_refresh("viewtopic.php?showtopic=$msgpid");
            exit();
        } else {

            $topicparent = DB_getITEM($_TABLES['gf_topic'],"pid","id='$msgid'");
            if ($top == 'yes') {
                // Need to check for any attachments and delete if required
                $q1 = DB_query("SELECT id FROM {$_TABLES['gf_topic']} WHERE pid=$msgid OR id=$msgid");
                while($A = DB_fetchArray($q1)) {
                    $q2 = DB_query("SELECT id FROM {$_TABLES['gf_attachments']} WHERE topic_id={$A['id']}");
                    while ($B = DB_fetchArray($q2)) {
                        forum_delAttachment($B['id']);
                    }
                }
                DB_query("DELETE FROM {$_TABLES['gf_topic']} WHERE (id='$msgid')");
                DB_query("DELETE FROM {$_TABLES['gf_topic']} WHERE (pid='$msgid')");
                DB_query("DELETE FROM {$_TABLES['gf_watch']} WHERE (id='$msgid')");
                $postCount = DB_Count($_TABLES['gf_topic'],'forum',$forum);
                DB_query("UPDATE {$_TABLES['gf_forums']} SET topic_count=topic_count-1,post_count=$postCount WHERE forum_id=$forum");
                $query = DB_query("SELECT MAX(id)as maxid FROM {$_TABLES['gf_topic']} WHERE forum=$forum");
                list($last_topic) = DB_fetchArray($query);
                if ($last_topic > 0) {
                    DB_query("UPDATE {$_TABLES['gf_forums']} SET last_post_rec=$last_topic WHERE forum_id=$forum");
                } else {
                    DB_query("UPDATE {$_TABLES['gf_forums']} SET last_post_rec=0 WHERE forum_id=$forum");
                }
            } else {
                // Need to check for any attachments and delete if required
                $q1 = DB_query("SELECT id FROM {$_TABLES['gf_topic']} WHERE id=$msgid");
                while($A = DB_fetchArray($q1)) {
                    $q2 = DB_query("SELECT id FROM {$_TABLES['gf_attachments']} WHERE topic_id={$A['id']}");
                    while ($B = DB_fetchArray($q2)) {
                        forum_delAttachment($B['id']);
                    }
                }
                DB_query("UPDATE {$_TABLES['gf_topic']} SET replies=replies-1 WHERE (id='$topicparent')");
                DB_query("DELETE FROM {$_TABLES['gf_topic']} WHERE (id='$msgid')");
                DB_query("UPDATE {$_TABLES['gf_forums']} SET post_count=post_count-1 WHERE forum_id=$forum");
                // Get the post id for the last post in this topic
                $query = DB_query("SELECT MAX(id)as maxid FROM {$_TABLES['gf_topic']} WHERE forum=$forum");
                list($last_topic) = DB_fetchArray($query);
                if ($last_topic > 0) {
                    DB_query("UPDATE {$_TABLES['gf_forums']} SET last_post_rec=$last_topic WHERE forum_id=$forum");
                }
            }

            if ($topicparent == 0) {
                $topicparent = $msgid;
            } else {
                $lsql = DB_query("SELECT MAX(id) FROM {$_TABLES['gf_topic']} WHERE pid='$topicparent' ");
                list($lastrecid) = DB_fetchArray($lsql);
                if ($lastrecid == NULL) {
                    $topicdatecreated = DB_getITEM($_TABLES['gf_topic'],date,"id=$topicparent");
                    DB_query("UPDATE {$_TABLES['gf_topic']} SET last_reply_rec=$topicparent, lastupdated='$topicdatecreated' WHERE id='$topicparent'");
                } else {
                    $topicdatecreated = DB_getITEM($_TABLES['gf_topic'],date,"id=$lastrecid");
                    DB_query("UPDATE {$_TABLES['gf_topic']} SET last_reply_rec=$lastrecid, lastupdated=$topicdatecreated WHERE id={$topicparent}");
                }
            }

            // Remove any lastviewed records in the log so that the new updated topic indicator will appear
            DB_query("DELETE FROM {$_TABLES['gf_log']} WHERE topic='$topicparent'");

            if ($top == 'yes') {
                $link = "{$_CONF['site_url']}/forum/index.php?forum=$forum";
                forum_statusMessage($LANG_GF02['msg55'],$link,$LANG_GF02['msg55'],true,$forum);
            } else {
                $link = "{$_CONF['site_url']}/forum/viewtopic.php?showtopic=$msgpid";
                forum_statusMessage($LANG_GF02['msg55'],$link,$LANG_GF02['msg55'],true,$forum);
            }
            exit();
        }
    }

    if($_POST['confirmbanip'] == '1') {
        if ($submit == $LANG_GF01['CANCEL']) {
            echo COM_refresh("viewtopic.php?showtopic=$fortopicid");
            exit();
        } else {
            $hostip = COM_applyFilter($_POST['hostip']);
            DB_query("INSERT INTO {$_TABLES['gf_banned_ip']} (host_ip) VALUES ('$hostip')");
            $link = "{$_CONF['site_url']}/forum/viewtopic.php?showtopic=$fortopicid";
            forum_statusMessage($LANG_GF02['msg56'],$link,$LANG_GF02['msg56']);
            gf_siteFooter();
            exit();
        }
    }

    if($_POST['confirm_move'] == '1' AND forum_modPermission($forum,$_USER['uid'],'mod_move') AND $moveid != 0) {
        if ($submit == $LANG_GF01['CANCEL']) {
            echo COM_refresh("viewtopic.php?showtopic=$moveid");
            exit();
        } else {
            $date = time();
            $movetoforum = gf_preparefordb($_POST['movetoforum'],text);
            $movetitle = gf_preparefordb($_POST['movetitle'],text);
            $newforumid = DB_getItem($_TABLES['gf_forums'],"forum_id","forum_name='$movetoforum'");
            /* Check and see if we are splitting this forum thread */

            if (isset($_POST['splittype'])) {  // - Yes
                $curpostpid = DB_getItem($_TABLES['gf_topic'],"pid","id='$moveid'");
                if ($_POST['splittype'] == 'single') {  // Move only the single post - create a new topic
                    $topicdate = DB_getItem($_TABLES['gf_topic'],"date","id='$moveid'");
                    $sql  = "UPDATE {$_TABLES['gf_topic']} SET forum='$newforumid', pid='0',lastupdated='$topicdate', ";
                    $sql .= "subject='$movetitle', replies = '0' WHERE id='$moveid' ";
                    DB_query($sql);
                    DB_query("UPDATE {$_TABLES['gf_topic']} SET replies=replies-1 WHERE id='$curpostpid' ");

                    // Update Topic and Post Count for the effected forums
                    DB_query("UPDATE {$_TABLES['gf_forums']} SET topic_count=topic_count+1, post_count=post_count+1 WHERE forum_id=$newforumid");
				    $topicsQuery = DB_query("SELECT id FROM {$_TABLES['gf_topic']} WHERE forum=$forum and pid=0");
				    $topic_count = DB_numRows($topicsQuery);
                    DB_query("UPDATE {$_TABLES['gf_forums']} SET topic_count=$topic_count, post_count=post_count-1 WHERE forum_id=$forum");

                    // Update the Forum and topic indexes
                    gf_updateLastPost($forum,$curpostpid);
                    gf_updateLastPost($newforumid,$moveid);

                } else {
                    $movesql = DB_query("Select id,date from {$_TABLES['gf_topic']} WHERE pid='$curpostpid' AND id >= '$moveid'");
                    $numreplies = DB_numRows($movesql);
                    $topicparent = 0;
                    while($movetopic = DB_fetchArray($movesql)) {
                        if ($topicparent == 0) {
                            $sql  = "UPDATE {$_TABLES['gf_topic']} SET forum='$newforumid', pid='0',lastupdated='{$movetopic['date']}', ";
                            $sql .= "replies=$numreplies - 1, subject='$movetitle' WHERE id='{$movetopic['id']}'";
                            DB_query($sql);
                            $topicparent = $movetopic['id'];
                        } else {
                            $sql  = "UPDATE {$_TABLES['gf_topic']} SET forum='$newforumid', pid='$topicparent', ";
                            $sql .= "subject='$movetitle' WHERE id='{$movetopic['id']}'";
                            DB_query($sql);
                            $topicdate = DB_getItem($_TABLES['gf_topic'],"date","id='{$movetopic['id']}'");
                            DB_query("UPDATE {$_TABLES['gf_topic']} SET lastupdated='$topicdate' WHERE id='$topicparent'");
                        }
                    }
                    // Update the Forum and topic indexes
                    gf_updateLastPost($forum,$curpostpid);
                    gf_updateLastPost($newforumid,$topicparent);

                    // Update Topic and Post Count for the effected forums
                    DB_query("UPDATE {$_TABLES['gf_forums']} SET topic_count=topic_count+1, post_count=post_count+$numreplies WHERE forum_id=$newforumid");
                    DB_query("UPDATE {$_TABLES['gf_forums']} SET topic_count=topic_count-1, post_count=post_count-$numreplies WHERE forum_id=$forum");
                }
                $link = "{$_CONF['site_url']}/forum/viewtopic.php?showtopic=$moveid";
                forum_statusMessage(sprintf($LANG_GF02['msg183'],$movetoforum),$link,$LANG_GF02['msg183']);

            } else {  // Move complete topic
                $moveResult = DB_query("Select id from {$_TABLES['gf_topic']} WHERE pid=$moveid");
                $postCount = DB_numRows($moveResult) +1;  // Need to account for the parent post
                while($movetopic = DB_fetchArray($moveResult)) {
                    DB_query("UPDATE {$_TABLES['gf_topic']} SET forum='$newforumid' WHERE id='{$movetopic['id']}'");
                }
                // Update any topic subscription records - need to change the forum ID record
                DB_query("UPDATE {$_TABLES['gf_watch']} SET forum_id = '$newforumid' WHERE topic_id='{$moveid}'");
                DB_query("UPDATE {$_TABLES['gf_topic']} SET forum = '$newforumid', moved = '1' WHERE id=$moveid");

                // Update the Last Post Information
                gf_updateLastPost($newforumid,$moveid);
                gf_updateLastPost($forum);

                // Update Topic and Post Count for the effected forums
                DB_query("UPDATE {$_TABLES['gf_forums']} SET topic_count=topic_count+1, post_count=post_count+$postCount WHERE forum_id=$newforumid");
                DB_query("UPDATE {$_TABLES['gf_forums']} SET topic_count=topic_count-1, post_count=post_count-$postCount WHERE forum_id=$forum");

                // Remove any lastviewed records in the log so that the new updated topic indicator will appear
                DB_query("DELETE FROM {$_TABLES['gf_log']} WHERE topic='$moveid'");
                $link = "{$_CONF['site_url']}/forum/viewtopic.php?showtopic=$moveid";
                forum_statusMessage($LANG_GF02['msg163'],$link,$LANG_GF02['msg163']);
            }
            gf_siteFooter();
            exit();

        }
    }

    if($modfunction == 'deletepost' AND forum_modPermission($forum,$_USER['uid'],'mod_delete') AND $fortopicid != 0) {

        if ($top == 'yes') {
            $alertmessage = $LANG_GF02['msg65'] . "<p>";
        } else {
            $alertmessage = '';
        }
        $subject = DB_getITEM($_TABLES['gf_topic'],"subject","id='$msgpid'");
        $alertmessage .= sprintf($LANG_GF02['msg64'],$fortopicid,$subject);

        $promptform  = '<p><FORM ACTION="' .$_CONF['site_url'] . '/forum/moderation.php" METHOD="POST">';
        $promptform .= '<INPUT TYPE="hidden" NAME="modconfirmdelete" VALUE="1">';
        $promptform .= '<INPUT TYPE="hidden" NAME="msgid"  VALUE="' .$fortopicid. '">';
        $promptform .= '<INPUT TYPE="hidden" NAME="forum"  VALUE="' .$forum. '">';
        $promptform .= '<INPUT TYPE="hidden" NAME="msgpid" VALUE="' .$msgpid. '">';
        $promptform .= '<INPUT TYPE="hidden" NAME="top" VALUE="' .$top. '">';
        $promptform .= '<CENTER><INPUT TYPE="submit" NAME="submit" VALUE="' .$LANG_GF01['CONFIRM']. '">&nbsp;&nbsp;';
        $promptform .= '<INPUT TYPE="submit" NAME="submit" VALUE="' .$LANG_GF01['CANCEL']. '"></CENTER>';
        $promptform .= '</CENTER></FORM></p>';
        alertMessage($alertmessage,$LANG_GF02['msg182'],$promptform);

    } elseif($modfunction == 'editpost' AND forum_modPermission($forum,$_USER['uid'],'mod_edit') AND $fortopicid != 0) {
        $page = COM_applyFilter($_REQUEST['page'],true);
        echo COM_refresh("createtopic.php?method=edit&id=$fortopicid&page=$page");
        echo $LANG_GF02['msg110'];

    } elseif($modfunction == 'lockedpost' AND forum_modPermission($forum,$_USER['uid'],'mod_edit') AND $fortopicid != 0) {
        echo COM_refresh("createtopic.php?method=postreply&id=$fortopicid");
        echo $LANG_GF02['msg173'];

    } elseif($modfunction == 'movetopic' AND forum_modPermission($forum,$_USER['uid'],'mod_move') AND $fortopicid != 0) {

        $SECgroups = SEC_getUserGroups();  // Returns an Associative Array - need to parse out the group id's
        $modgroups = '';
        foreach ($SECgroups as $key) {
          if ($modgroups == '') {
             $modgroups = $key;
          } else {
              $modgroups .= ",$key";
          }
        }
        /* Check and see if user had moderation rights to another forum to complete the topic move */
        $sql = "SELECT DISTINCT forum_name FROM {$_TABLES['gf_moderators']} a , {$_TABLES['gf_forums']} b ";
        $sql .= "where a.mod_forum = b.forum_id AND ( a.mod_uid='{$_USER['uid']}' OR a.mod_groupid in ($modgroups))";
        $query = DB_query($sql);

        if (DB_numRows($query) == 0) {
            alertMessage($LANG_GF02['msg181'],$LANG_GF01['WARNING']);
        } else {
            $topictitle = DB_getItem($_TABLES['gf_topic'],"subject","id='$fortopicid'");
            $promptform  =  '<div style="padding:10 0 5 0px;">';
            $promptform .= '<FORM ACTION="' .$_CONF['site_url'] . '/forum/moderation.php" METHOD="POST">';
            $promptform  .= '<INPUT TYPE="hidden" NAME="moveid" VALUE="' .$fortopicid. '">';
            $promptform  .= '<INPUT TYPE="hidden" NAME="confirm_move" VALUE="1">';
            $promptform  .= '<INPUT TYPE="hidden" NAME="forum" VALUE="' .$forum. '">';
            $promptform .= '<div>'.$LANG_GF03['selectforum'];
            $promptform .= '&nbsp;<SELECT NAME="movetoforum" style="width:120px;">';
            while($showforums = DB_fetchArray($query)){
                $promptform  .= "<OPTION>$showforums[forum_name]";
            }
            $promptform  .= '</SELECT>';
            $promptform .= '</div><div style="padding:10 0 5 0px;">'.$LANG_GF02['msg186'].':&nbsp;';
            $promptform .= '<input type="text" size="60" NAME="movetitle" VALUE="' .$topictitle. '">';


            /* Check and see request to move complete topic or split the topic */
            if (DB_getItem($_TABLES['gf_topic'],"pid","id='$fortopicid'") == 0) {
                $promptform .= '</div><div style="padding:20 0 5 20px;">';
                $promptform .= '<input type="submit" NAME="submit" VALUE="' .$LANG_GF03['movetopic']. '">';
                $promptform .= '&nbsp;&nbsp;<INPUT TYPE="submit" NAME="submit" VALUE="' .$LANG_GF01['CANCEL']. '"></div>';
                $promptform .= '</FORM></div>';
                $alertmessage = sprintf($LANG_GF03['movetopicmsg'],$topictitle);
                alertMessage($alertmessage,$LANG_GF02['msg182'],$promptform);
            } else {
                $poster   = DB_getItem($_TABLES['gf_topic'],"name","id='$fortopicid'");
                $postdate = COM_getUserDateTimeFormat(DB_getItem($_TABLES['gf_topic'],"date","id='$fortopicid'"));
                $promptform .= '<div style="padding-top:10px;">'.$LANG_GF03['splitheading'] .'<br>';
                $promptform .= '<input type="radio" name="splittype" value="remaining" CHECKED>'.$LANG_GF03['splitopt1'] .'<br>';
                $promptform .= '<input type="radio" name="splittype" value="single">'.$LANG_GF03['splitopt2'] .'</div>';
                $promptform .= '</div><div style="padding:20 0 5 20px;">';
                $promptform .= '<input type="submit" NAME="submit" VALUE="' .$LANG_GF03['movetopic']. '">';
                $promptform .= '&nbsp;&nbsp;<INPUT TYPE="submit" NAME="submit" VALUE="' .$LANG_GF01['CANCEL']. '"></div>';
                $promptform .= '</FORM></div>';
                $alertmessage = sprintf($LANG_GF03['splittopicmsg'],$topictitle,$poster,$postdate[0]);
                alertMessage($alertmessage,$LANG_GF02['msg182'],$promptform);
            }
        }


    } elseif($modfunction == 'banip' AND forum_modPermission($forum,$_USER['uid'],'mod_ban') AND $fortopicid != 0) {

        $iptobansql = DB_query("SELECT ip FROM {$_TABLES['gf_topic']} WHERE id='$fortopicid'");
        $forumpostipnum = DB_fetchArray($iptobansql);
        if ($forumpostipnum['ip'] == '') {
            alertMessage($LANG_GF02['msg174']);
            exit;
        }
        $alertmessage =  '<p>' .$LANG_GF02['msg68'] . '</p><p>';
        $alertmessage .= sprintf($LANG_GF02['msg69'],$forumpostipnum['ip']) . '</p>';

        $promptform  = '<p><FORM ACTION="' .$_CONF['site_url'] . '/forum/moderation.php" METHOD="POST">';
        $promptform .= '<INPUT TYPE="hidden" NAME="hostip" VALUE="' .$forumpostipnum['ip']. '">';
        $promptform .= '<INPUT TYPE="hidden" NAME="confirmbanip" VALUE="1">';
        $promptform .= '<INPUT TYPE="hidden" NAME="forum" VALUE="' .$forum. '">';
        $promptform .= '<INPUT TYPE="hidden" NAME="fortopicid" VALUE="' .$fortopicid. '">';
        $promptform .= '<CENTER><INPUT TYPE="submit" NAME="submit" VALUE="' .$LANG_GF01['CONFIRM']. '">';
        $promptform .= '&nbsp;&nbsp;<INPUT TYPE="submit" NAME="submit" VALUE="' .$LANG_GF01['CANCEL']. '">';
        $promptform .= '</CENTER></FORM></p>';
        alertMessage($alertmessage,$LANG_GF02['msg182'],$promptform);

    } else {
        alertMessage($LANG_GF02['msg71'],$LANG_GF01['WARNING']);
    }

} else {
    alertMessage($LANG_GF02['msg72'],$LANG_GF01['ACCESSERROR']);
}

gf_siteFooter();
exit;
?>