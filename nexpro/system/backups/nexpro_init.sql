-- phpMyAdmin SQL Dump
-- version 2.6.0-rc1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Dec 29, 2006 at 02:17 PM
-- Server version: 4.0.20
-- PHP Version: 5.0.0
-- 
-- Database: `nexpro`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_access`
-- 

CREATE TABLE `gl_access` (
  `acc_ft_id` mediumint(8) NOT NULL default '0',
  `acc_grp_id` mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (`acc_ft_id`,`acc_grp_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_access`
-- 

INSERT INTO `gl_access` VALUES (1, 3);
INSERT INTO `gl_access` VALUES (2, 3);
INSERT INTO `gl_access` VALUES (3, 5);
INSERT INTO `gl_access` VALUES (4, 5);
INSERT INTO `gl_access` VALUES (5, 9);
INSERT INTO `gl_access` VALUES (5, 11);
INSERT INTO `gl_access` VALUES (6, 9);
INSERT INTO `gl_access` VALUES (6, 11);
INSERT INTO `gl_access` VALUES (7, 12);
INSERT INTO `gl_access` VALUES (8, 7);
INSERT INTO `gl_access` VALUES (9, 7);
INSERT INTO `gl_access` VALUES (10, 4);
INSERT INTO `gl_access` VALUES (11, 6);
INSERT INTO `gl_access` VALUES (12, 8);
INSERT INTO `gl_access` VALUES (13, 10);
INSERT INTO `gl_access` VALUES (14, 11);
INSERT INTO `gl_access` VALUES (15, 11);
INSERT INTO `gl_access` VALUES (16, 4);
INSERT INTO `gl_access` VALUES (17, 14);
INSERT INTO `gl_access` VALUES (18, 14);
INSERT INTO `gl_access` VALUES (23, 15);
INSERT INTO `gl_access` VALUES (24, 3);
INSERT INTO `gl_access` VALUES (29, 19);
INSERT INTO `gl_access` VALUES (30, 19);
INSERT INTO `gl_access` VALUES (31, 20);
INSERT INTO `gl_access` VALUES (33, 22);
INSERT INTO `gl_access` VALUES (34, 23);
INSERT INTO `gl_access` VALUES (35, 23);
INSERT INTO `gl_access` VALUES (36, 23);
INSERT INTO `gl_access` VALUES (38, 25);
INSERT INTO `gl_access` VALUES (39, 25);
INSERT INTO `gl_access` VALUES (40, 25);
INSERT INTO `gl_access` VALUES (48, 29);
INSERT INTO `gl_access` VALUES (49, 30);
INSERT INTO `gl_access` VALUES (53, 32);
INSERT INTO `gl_access` VALUES (54, 32);
INSERT INTO `gl_access` VALUES (55, 32);
INSERT INTO `gl_access` VALUES (56, 33);
INSERT INTO `gl_access` VALUES (57, 33);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_article_images`
-- 

CREATE TABLE `gl_article_images` (
  `ai_sid` varchar(40) NOT NULL default '',
  `ai_img_num` tinyint(2) unsigned NOT NULL default '0',
  `ai_filename` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`ai_sid`,`ai_img_num`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_article_images`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_auditlog`
-- 

CREATE TABLE `gl_auditlog` (
  `uid` mediumint(8) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `script` varchar(255) NOT NULL default '',
  `logentry` varchar(255) NOT NULL default '',
  KEY `uid` (`uid`),
  KEY `date` (`date`),
  KEY `script` (`script`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_auditlog`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_blocks`
-- 

CREATE TABLE `gl_blocks` (
  `bid` smallint(5) unsigned NOT NULL auto_increment,
  `is_enabled` tinyint(1) unsigned NOT NULL default '1',
  `name` varchar(48) NOT NULL default '',
  `type` varchar(20) NOT NULL default 'normal',
  `title` varchar(48) default NULL,
  `tid` varchar(20) NOT NULL default 'All',
  `blockorder` smallint(5) unsigned NOT NULL default '1',
  `content` text,
  `allow_autotags` tinyint(1) unsigned NOT NULL default '0',
  `rdfurl` varchar(255) default NULL,
  `rdfupdated` datetime NOT NULL default '0000-00-00 00:00:00',
  `rdflimit` smallint(5) unsigned NOT NULL default '0',
  `onleft` tinyint(3) unsigned NOT NULL default '1',
  `phpblockfn` varchar(64) default '',
  `help` varchar(255) default '',
  `owner_id` mediumint(8) unsigned NOT NULL default '1',
  `group_id` mediumint(8) unsigned NOT NULL default '1',
  `perm_owner` tinyint(1) unsigned NOT NULL default '3',
  `perm_group` tinyint(1) unsigned NOT NULL default '3',
  `perm_members` tinyint(1) unsigned NOT NULL default '2',
  `perm_anon` tinyint(1) unsigned NOT NULL default '2',
  PRIMARY KEY  (`bid`),
  KEY `blocks_bid` (`bid`),
  KEY `blocks_is_enabled` (`is_enabled`),
  KEY `blocks_tid` (`tid`),
  KEY `blocks_type` (`type`),
  KEY `blocks_name` (`name`),
  KEY `blocks_onleft` (`onleft`)
) TYPE=MyISAM AUTO_INCREMENT=17 ;

-- 
-- Dumping data for table `gl_blocks`
-- 

INSERT INTO `gl_blocks` VALUES (1, 0, 'user_block', 'gldefault', 'User Functions', 'all', 60, '', 0, '', '0000-00-00 00:00:00', 0, 1, '', '', 2, 1, 3, 3, 2, 2);
INSERT INTO `gl_blocks` VALUES (2, 0, 'admin_block', 'gldefault', 'Admins Only', 'all', 50, '', 0, '', '0000-00-00 00:00:00', 0, 1, '', '', 2, 1, 3, 3, 2, 2);
INSERT INTO `gl_blocks` VALUES (3, 0, 'section_block', 'gldefault', 'Topics', 'all', 20, '', 0, '', '0000-00-00 00:00:00', 0, 1, '', '', 2, 1, 3, 3, 2, 2);
INSERT INTO `gl_blocks` VALUES (4, 1, 'polls_block', 'phpblock', 'Poll', 'all', 30, '', 0, '', '0000-00-00 00:00:00', 0, 0, 'phpblock_polls', '', 2, 1, 3, 3, 2, 2);
INSERT INTO `gl_blocks` VALUES (5, 1, 'events_block', 'phpblock', 'Events', 'all', 70, '', 0, '', '0000-00-00 00:00:00', 0, 1, 'phpblock_calendar', '', 2, 1, 3, 3, 2, 2);
INSERT INTO `gl_blocks` VALUES (6, 1, 'whats_new_block', 'gldefault', 'What''s New', 'all', 40, '', 0, '', '0000-00-00 00:00:00', 0, 0, '', '', 2, 1, 3, 3, 2, 2);
INSERT INTO `gl_blocks` VALUES (7, 1, 'first_block', 'normal', 'About GeekLog', 'homeonly', 20, '<p><b>Welcome to GeekLog!</b><p>If you''re already familiar with GeekLog - and especially if you''re not: There have been many improvements to GeekLog since earlier versions that you might want to read up on. Please read the <a href="docs/changes.html">release notes</a>. If you need help, please see the <a href="docs/support.html">support options</a>.', 0, '', '0000-00-00 00:00:00', 0, 0, '', '', 2, 4, 3, 3, 2, 2);
INSERT INTO `gl_blocks` VALUES (8, 1, 'whosonline_block', 'phpblock', 'Who''s Online', 'all', 10, '', 0, '', '0000-00-00 00:00:00', 0, 0, 'phpblock_whosonline', '', 2, 4, 3, 3, 2, 2);
INSERT INTO `gl_blocks` VALUES (9, 1, 'older_stories', 'gldefault', 'Older Stories', 'all', 80, '', 0, '', '0000-00-00 00:00:00', 0, 1, '', '', 2, 1, 3, 3, 2, 2);
INSERT INTO `gl_blocks` VALUES (12, 0, 'forum_menu', 'phpblock', 'Forum Menu', 'all', 30, NULL, 0, NULL, '0000-00-00 00:00:00', 0, 1, 'phpblock_forum_menu', '', 2, 2, 3, 2, 2, 2);
INSERT INTO `gl_blocks` VALUES (13, 1, 'nexmenu', 'phpblock', 'Site Menu', 'all', 40, NULL, 0, NULL, '0000-00-00 00:00:00', 0, 1, 'phpblock_nexmenu', '', 2, 2, 3, 3, 2, 2);
INSERT INTO `gl_blocks` VALUES (14, 1, 'loginblock', 'phpblock', 'Site Login', 'all', 10, '', 0, '', '0000-00-00 00:00:00', 0, 1, 'phpblock_gluserlogin', '', 2, 4, 3, 2, 2, 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_commentcodes`
-- 

CREATE TABLE `gl_commentcodes` (
  `code` tinyint(4) NOT NULL default '0',
  `name` varchar(32) default NULL,
  PRIMARY KEY  (`code`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_commentcodes`
-- 

INSERT INTO `gl_commentcodes` VALUES (0, 'Comments Enabled');
INSERT INTO `gl_commentcodes` VALUES (-1, 'Comments Disabled');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_commentmodes`
-- 

CREATE TABLE `gl_commentmodes` (
  `mode` varchar(10) NOT NULL default '',
  `name` varchar(32) default NULL,
  PRIMARY KEY  (`mode`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_commentmodes`
-- 

INSERT INTO `gl_commentmodes` VALUES ('flat', 'Flat');
INSERT INTO `gl_commentmodes` VALUES ('nested', 'Nested');
INSERT INTO `gl_commentmodes` VALUES ('threaded', 'Threaded');
INSERT INTO `gl_commentmodes` VALUES ('nocomment', 'No Comments');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_comments`
-- 

CREATE TABLE `gl_comments` (
  `cid` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(30) NOT NULL default 'article',
  `sid` varchar(40) NOT NULL default '',
  `date` datetime default NULL,
  `title` varchar(128) default NULL,
  `comment` text,
  `score` tinyint(4) NOT NULL default '0',
  `reason` tinyint(4) NOT NULL default '0',
  `pid` int(10) unsigned NOT NULL default '0',
  `lft` mediumint(10) unsigned NOT NULL default '0',
  `rht` mediumint(10) unsigned NOT NULL default '0',
  `indent` mediumint(10) unsigned NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '1',
  `ipaddress` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`cid`),
  KEY `comments_sid` (`sid`),
  KEY `comments_uid` (`uid`),
  KEY `comments_lft` (`lft`),
  KEY `comments_rht` (`rht`),
  KEY `comments_date` (`date`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_comments`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_cookiecodes`
-- 

CREATE TABLE `gl_cookiecodes` (
  `cc_value` int(8) unsigned NOT NULL default '0',
  `cc_descr` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`cc_value`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_cookiecodes`
-- 

INSERT INTO `gl_cookiecodes` VALUES (0, '(don''t)');
INSERT INTO `gl_cookiecodes` VALUES (3600, '1 Hour');
INSERT INTO `gl_cookiecodes` VALUES (7200, '2 Hours');
INSERT INTO `gl_cookiecodes` VALUES (10800, '3 Hours');
INSERT INTO `gl_cookiecodes` VALUES (28800, '8 Hours');
INSERT INTO `gl_cookiecodes` VALUES (86400, '1 Day');
INSERT INTO `gl_cookiecodes` VALUES (604800, '1 Week');
INSERT INTO `gl_cookiecodes` VALUES (2678400, '1 Month');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_dateformats`
-- 

CREATE TABLE `gl_dateformats` (
  `dfid` tinyint(4) NOT NULL default '0',
  `format` varchar(32) default NULL,
  `description` varchar(64) default NULL,
  PRIMARY KEY  (`dfid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_dateformats`
-- 

INSERT INTO `gl_dateformats` VALUES (0, '', 'System Default');
INSERT INTO `gl_dateformats` VALUES (1, '%A %B %d, %Y @%I:%M%p', 'Sunday March 21, 1999 @10:00PM');
INSERT INTO `gl_dateformats` VALUES (2, '%A %b %d, %Y @%H:%M', 'Sunday March 21, 1999 @22:00');
INSERT INTO `gl_dateformats` VALUES (4, '%A %b %d @%H:%M', 'Sunday March 21 @22:00');
INSERT INTO `gl_dateformats` VALUES (5, '%H:%M %d %B %Y', '22:00 21 March 1999');
INSERT INTO `gl_dateformats` VALUES (6, '%H:%M %A %d %B %Y', '22:00 Sunday 21 March 1999');
INSERT INTO `gl_dateformats` VALUES (7, '%I:%M%p - %A %B %d %Y', '10:00PM -- Sunday March 21 1999');
INSERT INTO `gl_dateformats` VALUES (8, '%a %B %d, %I:%M%p', 'Sun March 21, 10:00PM');
INSERT INTO `gl_dateformats` VALUES (9, '%a %B %d, %H:%M', 'Sun March 21, 22:00');
INSERT INTO `gl_dateformats` VALUES (10, '%m-%d-%y %H:%M', '3-21-99 22:00');
INSERT INTO `gl_dateformats` VALUES (11, '%d-%m-%y %H:%M', '21-3-99 22:00');
INSERT INTO `gl_dateformats` VALUES (12, '%m-%d-%y %I:%M%p', '3-21-99 10:00PM');
INSERT INTO `gl_dateformats` VALUES (13, '%I:%M%p  %B %D, %Y', '10:00PM  March 21st, 1999');
INSERT INTO `gl_dateformats` VALUES (14, '%a %b %d, ''%y %I:%M%p', 'Sun Mar 21, ''99 10:00PM');
INSERT INTO `gl_dateformats` VALUES (15, 'Day %j, %I ish', 'Day 80, 10 ish');
INSERT INTO `gl_dateformats` VALUES (16, '%y-%m-%d %I:%M', '99-03-21 10:00');
INSERT INTO `gl_dateformats` VALUES (17, '%d/%m/%y %H:%M', '21/03/99 22:00');
INSERT INTO `gl_dateformats` VALUES (18, '%a %d %b %I:%M%p', 'Sun 21 Mar 10:00PM');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_events`
-- 

CREATE TABLE `gl_events` (
  `eid` varchar(20) NOT NULL default '',
  `title` varchar(128) default NULL,
  `description` text,
  `postmode` varchar(10) NOT NULL default 'plaintext',
  `datestart` date default NULL,
  `dateend` date default NULL,
  `url` varchar(255) default NULL,
  `hits` mediumint(8) unsigned NOT NULL default '0',
  `owner_id` mediumint(8) unsigned NOT NULL default '1',
  `group_id` mediumint(8) unsigned NOT NULL default '1',
  `perm_owner` tinyint(1) unsigned NOT NULL default '3',
  `perm_group` tinyint(1) unsigned NOT NULL default '3',
  `perm_members` tinyint(1) unsigned NOT NULL default '2',
  `perm_anon` tinyint(1) unsigned NOT NULL default '2',
  `address1` varchar(40) default NULL,
  `address2` varchar(40) default NULL,
  `city` varchar(60) default NULL,
  `state` char(2) default NULL,
  `zipcode` varchar(5) default NULL,
  `allday` tinyint(1) NOT NULL default '0',
  `event_type` varchar(40) NOT NULL default '',
  `location` varchar(128) default NULL,
  `timestart` time default NULL,
  `timeend` time default NULL,
  PRIMARY KEY  (`eid`),
  KEY `events_eid` (`eid`),
  KEY `events_event_type` (`event_type`),
  KEY `events_datestart` (`datestart`),
  KEY `events_dateend` (`dateend`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_events`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_eventsubmission`
-- 

CREATE TABLE `gl_eventsubmission` (
  `eid` varchar(20) NOT NULL default '',
  `title` varchar(128) default NULL,
  `description` text,
  `location` varchar(128) default NULL,
  `datestart` date default NULL,
  `dateend` date default NULL,
  `url` varchar(255) default NULL,
  `allday` tinyint(1) NOT NULL default '0',
  `zipcode` varchar(5) default NULL,
  `state` char(2) default NULL,
  `city` varchar(60) default NULL,
  `address2` varchar(40) default NULL,
  `address1` varchar(40) default NULL,
  `event_type` varchar(40) NOT NULL default '',
  `timestart` time default NULL,
  `timeend` time default NULL,
  PRIMARY KEY  (`eid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_eventsubmission`
-- 

INSERT INTO `gl_eventsubmission` VALUES ('2005100114064662', 'Geeklog installed', 'Today, you successfully installed this Geeklog site.', 'Your webserver', '2006-08-14', '2006-08-14', 'http://www.geeklog.net/', 1, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_featurecodes`
-- 

CREATE TABLE `gl_featurecodes` (
  `code` tinyint(4) NOT NULL default '0',
  `name` varchar(32) default NULL,
  PRIMARY KEY  (`code`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_featurecodes`
-- 

INSERT INTO `gl_featurecodes` VALUES (0, 'Not Featured');
INSERT INTO `gl_featurecodes` VALUES (1, 'Featured');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_features`
-- 

CREATE TABLE `gl_features` (
  `ft_id` mediumint(8) NOT NULL auto_increment,
  `ft_name` varchar(20) NOT NULL default '',
  `ft_descr` varchar(255) NOT NULL default '',
  `ft_gl_core` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`ft_id`),
  KEY `ft_name` (`ft_name`)
) TYPE=MyISAM AUTO_INCREMENT=58 ;

-- 
-- Dumping data for table `gl_features`
-- 

INSERT INTO `gl_features` VALUES (1, 'story.edit', 'Access to story editor', 1);
INSERT INTO `gl_features` VALUES (2, 'story.moderate', 'Ability to moderate pending stories', 1);
INSERT INTO `gl_features` VALUES (5, 'user.edit', 'Access to user editor', 1);
INSERT INTO `gl_features` VALUES (6, 'user.delete', 'Ability to delete a user', 1);
INSERT INTO `gl_features` VALUES (7, 'user.mail', 'Ability to send email to members', 1);
INSERT INTO `gl_features` VALUES (8, 'calendar.moderate', 'Ability to moderate pending events', 0);
INSERT INTO `gl_features` VALUES (9, 'calendar.edit', 'Access to event editor', 0);
INSERT INTO `gl_features` VALUES (10, 'block.edit', 'Access to block editor', 1);
INSERT INTO `gl_features` VALUES (11, 'topic.edit', 'Access to topic editor', 1);
INSERT INTO `gl_features` VALUES (13, 'plugin.edit', 'Access to plugin editor', 1);
INSERT INTO `gl_features` VALUES (14, 'group.edit', 'Ability to edit groups', 1);
INSERT INTO `gl_features` VALUES (15, 'group.delete', 'Ability to delete groups', 1);
INSERT INTO `gl_features` VALUES (16, 'block.delete', 'Ability to delete a block', 1);
INSERT INTO `gl_features` VALUES (17, 'staticpages.edit', 'Ability to edit a static page', 0);
INSERT INTO `gl_features` VALUES (18, 'staticpages.delete', 'Ability to delete static pages', 0);
INSERT INTO `gl_features` VALUES (19, 'story.submit', 'May skip the story submission queue', 1);
INSERT INTO `gl_features` VALUES (21, 'calendar.submit', 'May skip the event submission queue', 0);
INSERT INTO `gl_features` VALUES (22, 'staticpages.PHP', 'Ability use PHP in static pages', 0);
INSERT INTO `gl_features` VALUES (23, 'spamx.admin', 'Full access to Spam-X plugin', 0);
INSERT INTO `gl_features` VALUES (24, 'story.ping', 'Ability to send pings, pingbacks, or trackbacks for stories', 1);
INSERT INTO `gl_features` VALUES (3, 'links.moderate', 'Ability to moderate pending links', 0);
INSERT INTO `gl_features` VALUES (4, 'links.edit', 'Access to links editor', 0);
INSERT INTO `gl_features` VALUES (20, 'links.submit', 'May skip the links submission queue', 0);
INSERT INTO `gl_features` VALUES (12, 'polls.edit', 'Access to polls editor', 0);
INSERT INTO `gl_features` VALUES (53, 'nexfile.admin', 'Plugin Full Administration Rights', 0);
INSERT INTO `gl_features` VALUES (54, 'nexfile.user', 'Plugin user permission - Required if user will have edit rights to pages', 0);
INSERT INTO `gl_features` VALUES (49, 'nexlist.edit', 'Access to links editor', 0);
INSERT INTO `gl_features` VALUES (29, 'forum.edit', 'Forum Admin', 0);
INSERT INTO `gl_features` VALUES (30, 'forum.user', 'Forum Viewer', 0);
INSERT INTO `gl_features` VALUES (31, 'nexmenu.edit', 'Plugin Administration Rights', 0);
INSERT INTO `gl_features` VALUES (55, 'nexfile.edit', 'Plugin Selected File and Category Admin', 0);
INSERT INTO `gl_features` VALUES (33, 'syndication.edit', 'Access to Content Syndication', 1);
INSERT INTO `gl_features` VALUES (40, 'nexflow.edit', 'nexFlow template, variable and handler editing', 0);
INSERT INTO `gl_features` VALUES (39, 'nexflow.user', 'nexFlow Access', 0);
INSERT INTO `gl_features` VALUES (38, 'nexflow.admin', 'nexFlow Full Admin', 0);
INSERT INTO `gl_features` VALUES (48, 'nexForm.edit', 'Plugin Full Administration Rights', 0);
INSERT INTO `gl_features` VALUES (56, 'nexcontent.user', 'Plugin user permission - Required if user will have edit rights to pages', 0);
INSERT INTO `gl_features` VALUES (57, 'nexcontent.edit', 'Plugin Full Administration Rights', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_forum_banned_ip`
-- 

CREATE TABLE `gl_forum_banned_ip` (
  `host_ip` varchar(255) default NULL,
  KEY `index1` (`host_ip`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_forum_banned_ip`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_forum_categories`
-- 

CREATE TABLE `gl_forum_categories` (
  `cat_order` smallint(4) NOT NULL default '0',
  `cat_name` varchar(255) NOT NULL default '',
  `cat_dscp` text NOT NULL,
  `id` int(2) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_forum_categories`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_forum_forums`
-- 

CREATE TABLE `gl_forum_forums` (
  `forum_order` int(4) NOT NULL default '0',
  `forum_name` varchar(255) NOT NULL default '0',
  `forum_dscp` text NOT NULL,
  `forum_id` int(4) NOT NULL auto_increment,
  `forum_cat` int(3) NOT NULL default '0',
  `grp_id` mediumint(8) NOT NULL default '2',
  `is_hidden` tinyint(1) NOT NULL default '0',
  `is_readonly` tinyint(1) NOT NULL default '0',
  `no_newposts` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`forum_id`),
  KEY `forum_cat` (`forum_cat`),
  KEY `forum_id` (`forum_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_forum_forums`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_forum_log`
-- 

CREATE TABLE `gl_forum_log` (
  `uid` mediumint(8) NOT NULL default '0',
  `forum` mediumint(3) NOT NULL default '0',
  `topic` mediumint(3) NOT NULL default '0',
  `time` varchar(40) NOT NULL default '0',
  KEY `uid_forum` (`uid`,`forum`),
  KEY `uid_topic` (`uid`,`topic`),
  KEY `forum` (`forum`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_forum_log`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_forum_moderators`
-- 

CREATE TABLE `gl_forum_moderators` (
  `mod_id` int(11) NOT NULL auto_increment,
  `mod_uid` mediumint(8) NOT NULL default '0',
  `mod_groupid` mediumint(8) NOT NULL default '0',
  `mod_username` varchar(30) default NULL,
  `mod_forum` varchar(30) default NULL,
  `mod_delete` tinyint(1) NOT NULL default '0',
  `mod_ban` tinyint(1) NOT NULL default '0',
  `mod_edit` tinyint(1) NOT NULL default '0',
  `mod_move` tinyint(1) NOT NULL default '0',
  `mod_stick` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`mod_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_forum_moderators`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_forum_settings`
-- 

CREATE TABLE `gl_forum_settings` (
  `slogan` varchar(255) NOT NULL default '',
  `registrationrequired` tinyint(1) unsigned NOT NULL default '0',
  `registerpost` tinyint(1) unsigned NOT NULL default '0',
  `allowhtml` tinyint(1) unsigned NOT NULL default '1',
  `glfilter` tinyint(1) unsigned NOT NULL default '0',
  `use_geshi_formatting` tinyint(1) NOT NULL default '1',
  `censor` tinyint(1) unsigned NOT NULL default '1',
  `showmood` tinyint(1) unsigned NOT NULL default '1',
  `allowsmilies` tinyint(1) unsigned NOT NULL default '1',
  `allowavatar` tinyint(1) unsigned NOT NULL default '1',
  `allow_notify` tinyint(1) unsigned NOT NULL default '1',
  `allow_htmlsig` tinyint(1) NOT NULL default '0',
  `allow_userdatefmt` tinyint(1) NOT NULL default '0',
  `showiframe` tinyint(1) unsigned NOT NULL default '1',
  `autorefresh` tinyint(1) NOT NULL default '1',
  `refresh_delay` tinyint(1) NOT NULL default '0',
  `xtrausersettings` tinyint(1) unsigned NOT NULL default '0',
  `viewtopicnumchars` int(4) NOT NULL default '20',
  `topicsperpage` int(4) NOT NULL default '10',
  `postsperpage` int(4) NOT NULL default '10',
  `messagesperpage` int(4) NOT NULL default '0',
  `searchesperpage` int(4) NOT NULL default '0',
  `popular` int(4) NOT NULL default '0',
  `speedlimit` int(1) NOT NULL default '60',
  `edit_timewindow` int(11) NOT NULL default '3600',
  `use_spamxfilter` tinyint(1) NOT NULL default '0',
  `use_smiliesplugin` tinyint(1) NOT NULL default '0',
  `use_pmplugin` tinyint(1) NOT NULL default '0',
  `imgset` varchar(30) NOT NULL default '',
  `cb_enable` tinyint(1) NOT NULL default '0',
  `cb_homepage` tinyint(1) NOT NULL default '0',
  `cb_where` tinyint(1) NOT NULL default '0',
  `cb_subjectsize` tinyint(1) NOT NULL default '0',
  `cb_numposts` tinyint(1) NOT NULL default '0',
  `sb_subjectsize` tinyint(1) NOT NULL default '0',
  `sb_numposts` tinyint(1) NOT NULL default '0',
  `sb_latestposts` tinyint(1) NOT NULL default '0',
  `min_comment_len` tinyint(1) NOT NULL default '0',
  `min_name_len` tinyint(1) NOT NULL default '0',
  `min_subject_len` tinyint(1) NOT NULL default '0',
  `html_newline` tinyint(1) NOT NULL default '0',
  `level1` int(5) NOT NULL default '1',
  `level2` int(5) NOT NULL default '15',
  `level3` int(5) NOT NULL default '35',
  `level4` int(5) NOT NULL default '70',
  `level5` int(5) NOT NULL default '120',
  `level1name` varchar(40) NOT NULL default 'Newbie',
  `level2name` varchar(40) NOT NULL default 'Junior',
  `level3name` varchar(40) NOT NULL default 'Chatty',
  `level4name` varchar(40) NOT NULL default 'Regular Member',
  `level5name` varchar(40) NOT NULL default 'Active Member'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_forum_settings`
-- 

INSERT INTO `gl_forum_settings` VALUES ('', 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 5, 0, 20, 10, 10, 20, 20, 20, 60, 60, 0, 0, 0, '', 1, 0, 1, 10, 2, 30, 5, 0, 5, 2, 2, 0, 1, 15, 35, 70, 120, 'Newbie', 'Junior', 'Chatty', 'Regular Member', 'Active Member');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_forum_topic`
-- 

CREATE TABLE `gl_forum_topic` (
  `id` mediumint(8) NOT NULL auto_increment,
  `forum` int(3) NOT NULL default '0',
  `pid` mediumint(8) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `name` varchar(50) default NULL,
  `date` varchar(12) default NULL,
  `lastupdated` varchar(12) default NULL,
  `email` varchar(50) default NULL,
  `website` varchar(100) NOT NULL default '',
  `subject` varchar(100) NOT NULL default '',
  `comment` longtext,
  `postmode` varchar(10) NOT NULL default '',
  `replies` bigint(10) NOT NULL default '0',
  `views` bigint(10) NOT NULL default '0',
  `ip` varchar(255) default NULL,
  `mood` varchar(100) default 'indifferent',
  `sticky` tinyint(1) NOT NULL default '0',
  `moved` tinyint(1) NOT NULL default '0',
  `locked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `forum_idx` (`forum`),
  KEY `idxtopicuid` (`uid`),
  KEY `idxtopicpid` (`pid`),
  KEY `idxdate` (`date`),
  KEY `idxlastdate` (`lastupdated`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_forum_topic`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_forum_userinfo`
-- 

CREATE TABLE `gl_forum_userinfo` (
  `uid` mediumint(8) NOT NULL default '0',
  `location` varchar(128) NOT NULL default '',
  `aim` varchar(128) NOT NULL default '',
  `icq` varchar(128) NOT NULL default '',
  `yim` varchar(128) NOT NULL default '',
  `msnm` varchar(128) NOT NULL default '',
  `interests` varchar(255) NOT NULL default '',
  `occupation` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`uid`)
) TYPE=MyISAM COMMENT='Forum Extra User Profile Information';

-- 
-- Dumping data for table `gl_forum_userinfo`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_forum_userprefs`
-- 

CREATE TABLE `gl_forum_userprefs` (
  `uid` mediumint(8) NOT NULL default '0',
  `topicsperpage` int(3) NOT NULL default '5',
  `postsperpage` int(3) NOT NULL default '5',
  `popularlimit` int(3) NOT NULL default '10',
  `messagesperpage` int(3) NOT NULL default '20',
  `searchlines` int(3) NOT NULL default '20',
  `viewanonposts` tinyint(1) NOT NULL default '1',
  `enablenotify` tinyint(1) NOT NULL default '1',
  `alwaysnotify` tinyint(1) NOT NULL default '0',
  `membersperpage` int(3) NOT NULL default '20',
  `showiframe` tinyint(1) NOT NULL default '1',
  `notify_once` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`uid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_forum_userprefs`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_forum_watch`
-- 

CREATE TABLE `gl_forum_watch` (
  `id` mediumint(8) NOT NULL auto_increment,
  `forum_id` mediumint(8) NOT NULL default '0',
  `topic_id` mediumint(8) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `date_added` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`),
  KEY `forum_id` (`forum_id`),
  KEY `topic_id` (`topic_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_forum_watch`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_frontpagecodes`
-- 

CREATE TABLE `gl_frontpagecodes` (
  `code` tinyint(4) NOT NULL default '0',
  `name` varchar(32) default NULL,
  PRIMARY KEY  (`code`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_frontpagecodes`
-- 

INSERT INTO `gl_frontpagecodes` VALUES (0, 'Show Only in Topic');
INSERT INTO `gl_frontpagecodes` VALUES (1, 'Show on Front Page');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nexmenu`
-- 

CREATE TABLE `gl_nexmenu` (
  `id` mediumint(8) NOT NULL auto_increment,
  `pid` mediumint(8) NOT NULL default '0',
  `menutype` tinyint(3) NOT NULL default '0',
  `location` varchar(16) NOT NULL default 'block',
  `menuorder` mediumint(8) NOT NULL default '0',
  `label` varchar(64) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `grp_access` mediumint(8) NOT NULL default '2',
  `is_enabled` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  KEY `menuorder` (`menuorder`)
) TYPE=MyISAM COMMENT='nexmenu Main table to setup menus' AUTO_INCREMENT=13 ;

-- 
-- Dumping data for table `gl_nexmenu`
-- 

INSERT INTO `gl_nexmenu` VALUES (12, 0, 1, 'block', 10, 'Home', 'http://cpqltop/nexpro/index.php', 2, 1);
INSERT INTO `gl_nexmenu` VALUES (2, 0, 3, 'block', 20, 'User Menu', '', 2, 1);
INSERT INTO `gl_nexmenu` VALUES (3, 0, 3, 'block', 30, 'Admin Menu', '', 13, 1);
INSERT INTO `gl_nexmenu` VALUES (4, 0, 4, 'block', 40, 'My Static Pages', 'spmenu', 2, 1);
INSERT INTO `gl_nexmenu` VALUES (8, 2, 4, 'block', 10, 'menuitems', 'usermenu', 2, 1);
INSERT INTO `gl_nexmenu` VALUES (9, 3, 4, 'block', 10, 'menuitems', 'adminmenu', 2, 1);
INSERT INTO `gl_nexmenu` VALUES (11, 0, 1, 'block', 50, 'Logout', 'http://cpqltop/nexpro/users.php?mode=logout', 13, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_group_assignments`
-- 

CREATE TABLE `gl_group_assignments` (
  `ug_main_grp_id` mediumint(8) NOT NULL default '0',
  `ug_uid` mediumint(8) unsigned default NULL,
  `ug_grp_id` mediumint(8) unsigned default NULL,
  KEY `group_assignments_ug_main_grp_id` (`ug_main_grp_id`),
  KEY `group_assignments_ug_uid` (`ug_uid`),
  KEY `ug_main_grp_id` (`ug_main_grp_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_group_assignments`
-- 

INSERT INTO `gl_group_assignments` VALUES (2, 1, NULL);
INSERT INTO `gl_group_assignments` VALUES (2, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (3, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (4, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (5, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (6, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (7, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (8, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (9, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (10, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (11, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (13, 2, NULL);
INSERT INTO `gl_group_assignments` VALUES (12, 2, NULL);
INSERT INTO `gl_group_assignments` VALUES (11, 2, NULL);
INSERT INTO `gl_group_assignments` VALUES (2, NULL, 12);
INSERT INTO `gl_group_assignments` VALUES (2, NULL, 10);
INSERT INTO `gl_group_assignments` VALUES (2, NULL, 9);
INSERT INTO `gl_group_assignments` VALUES (2, NULL, 8);
INSERT INTO `gl_group_assignments` VALUES (2, NULL, 7);
INSERT INTO `gl_group_assignments` VALUES (2, NULL, 6);
INSERT INTO `gl_group_assignments` VALUES (2, NULL, 5);
INSERT INTO `gl_group_assignments` VALUES (2, NULL, 4);
INSERT INTO `gl_group_assignments` VALUES (2, NULL, 3);
INSERT INTO `gl_group_assignments` VALUES (12, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (9, NULL, 11);
INSERT INTO `gl_group_assignments` VALUES (2, NULL, 11);
INSERT INTO `gl_group_assignments` VALUES (10, 2, NULL);
INSERT INTO `gl_group_assignments` VALUES (9, 2, NULL);
INSERT INTO `gl_group_assignments` VALUES (8, 2, NULL);
INSERT INTO `gl_group_assignments` VALUES (7, 2, NULL);
INSERT INTO `gl_group_assignments` VALUES (6, 2, NULL);
INSERT INTO `gl_group_assignments` VALUES (5, 2, NULL);
INSERT INTO `gl_group_assignments` VALUES (4, 2, NULL);
INSERT INTO `gl_group_assignments` VALUES (3, 2, NULL);
INSERT INTO `gl_group_assignments` VALUES (2, 2, NULL);
INSERT INTO `gl_group_assignments` VALUES (1, 2, NULL);
INSERT INTO `gl_group_assignments` VALUES (14, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (15, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (30, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (32, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (19, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (20, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (31, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (22, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (25, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (29, NULL, 1);
INSERT INTO `gl_group_assignments` VALUES (33, NULL, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_groups`
-- 

CREATE TABLE `gl_groups` (
  `grp_id` mediumint(8) NOT NULL auto_increment,
  `grp_name` varchar(50) NOT NULL default '',
  `grp_descr` varchar(255) NOT NULL default '',
  `grp_gl_core` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`grp_id`),
  UNIQUE KEY `grp_name` (`grp_name`)
) TYPE=MyISAM AUTO_INCREMENT=34 ;

-- 
-- Dumping data for table `gl_groups`
-- 

INSERT INTO `gl_groups` VALUES (1, 'Root', 'Has full access to the site', 1);
INSERT INTO `gl_groups` VALUES (2, 'All Users', 'Group that a typical user is added to', 1);
INSERT INTO `gl_groups` VALUES (3, 'Story Admin', 'Has full access to story features', 1);
INSERT INTO `gl_groups` VALUES (4, 'Block Admin', 'Has full access to block features', 1);
INSERT INTO `gl_groups` VALUES (5, 'Links Admin', 'Has full access to links features', 0);
INSERT INTO `gl_groups` VALUES (6, 'Topic Admin', 'Has full access to topic features', 1);
INSERT INTO `gl_groups` VALUES (7, 'Calendar Admin', 'Has full access to event features', 0);
INSERT INTO `gl_groups` VALUES (8, 'Polls Admin', 'Has full access to polls features', 0);
INSERT INTO `gl_groups` VALUES (9, 'User Admin', 'Has full access to user features', 1);
INSERT INTO `gl_groups` VALUES (10, 'Plugin Admin', 'Has full access to plugin features', 1);
INSERT INTO `gl_groups` VALUES (11, 'Group Admin', 'Is a User Admin with access to groups, too', 1);
INSERT INTO `gl_groups` VALUES (12, 'Mail Admin', 'Can use Mail Utility', 1);
INSERT INTO `gl_groups` VALUES (13, 'Logged-in Users', 'All registered members', 1);
INSERT INTO `gl_groups` VALUES (14, 'Static Page Admin', 'Can administer static pages', 0);
INSERT INTO `gl_groups` VALUES (15, 'spamx Admin', 'Users in this group can administer the Spam-X plugin', 0);
INSERT INTO `gl_groups` VALUES (16, 'Remote Users', 'Users in this group can have authenticated against a remote server.', 1);
INSERT INTO `gl_groups` VALUES (30, 'nexList Admin', 'Has full access to nexlist features', 0);
INSERT INTO `gl_groups` VALUES (33, 'nexcontent Admin', 'Users in this group can administer the nexcontent plugin', 0);
INSERT INTO `gl_groups` VALUES (19, 'forum Admin', 'Users in this group can administer the forum plugin', 0);
INSERT INTO `gl_groups` VALUES (20, 'nexmenu Admin', 'Users in this group can administer the nexmenu plugin', 0);
INSERT INTO `gl_groups` VALUES (32, 'nexFile Admin', 'Has full access to nexfile features', 0);
INSERT INTO `gl_groups` VALUES (22, 'Syndication Admin', 'Can create and modify web feeds for the site', 1);
INSERT INTO `gl_groups` VALUES (25, 'nexflow Admin', 'Users in this group can administer the nexflow plugin', 0);
INSERT INTO `gl_groups` VALUES (29, 'nexForm Admin', 'Has full access to nexForm features', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_links`
-- 

CREATE TABLE `gl_links` (
  `lid` varchar(40) NOT NULL default '',
  `category` varchar(32) default NULL,
  `url` varchar(255) default NULL,
  `description` text,
  `title` varchar(96) default NULL,
  `hits` int(11) NOT NULL default '0',
  `date` datetime default NULL,
  `owner_id` mediumint(8) unsigned NOT NULL default '1',
  `group_id` mediumint(8) unsigned NOT NULL default '1',
  `perm_owner` tinyint(1) unsigned NOT NULL default '3',
  `perm_group` tinyint(1) unsigned NOT NULL default '2',
  `perm_members` tinyint(1) unsigned NOT NULL default '2',
  `perm_anon` tinyint(1) unsigned NOT NULL default '2',
  PRIMARY KEY  (`lid`),
  KEY `links_lid` (`lid`),
  KEY `links_category` (`category`),
  KEY `links_date` (`date`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_links`
-- 

INSERT INTO `gl_links` VALUES ('geeklog.net', 'Geeklog Sites', 'http://www.geeklog.net/', 'Visit the Geeklog homepage for support, FAQs, updates, add-ons, and a great community.', 'Geeklog Project Homepage', 0, '2006-08-14 19:17:10', 1, 5, 3, 2, 2, 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_linksubmission`
-- 

CREATE TABLE `gl_linksubmission` (
  `lid` varchar(40) NOT NULL default '',
  `category` varchar(32) default NULL,
  `url` varchar(255) default NULL,
  `description` text,
  `title` varchar(96) default NULL,
  `hits` int(11) default NULL,
  `date` datetime default NULL,
  PRIMARY KEY  (`lid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_linksubmission`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_maillist`
-- 

CREATE TABLE `gl_maillist` (
  `code` int(1) NOT NULL default '0',
  `name` char(32) default NULL,
  PRIMARY KEY  (`code`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_maillist`
-- 

INSERT INTO `gl_maillist` VALUES (0, 'Don''t Email');
INSERT INTO `gl_maillist` VALUES (1, 'Email Headlines Each Night');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_appgroups`
-- 

CREATE TABLE `gl_nf_appgroups` (
  `id` int(11) NOT NULL auto_increment,
  `AppGroup` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `gl_nf_appgroups`
-- 

INSERT INTO `gl_nf_appgroups` VALUES (1, 'Sample AppGroup 1');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_handlers`
-- 

CREATE TABLE `gl_nf_handlers` (
  `id` int(11) NOT NULL auto_increment,
  `handler` varchar(255) NOT NULL default '',
  `nf_handlerTypeID` int(11) NOT NULL default '0',
  `description` longtext,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `gl_nf_handlers`
-- 

INSERT INTO `gl_nf_handlers` VALUES (1, 'nexflow/batchhandler1.php', 0, NULL);
INSERT INTO `gl_nf_handlers` VALUES (2, 'testsuite/yes-no.php', 0, '');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_ifoperators`
-- 

CREATE TABLE `gl_nf_ifoperators` (
  `id` int(11) NOT NULL default '0',
  `operator` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_nf_ifoperators`
-- 

INSERT INTO `gl_nf_ifoperators` VALUES (1, '=');
INSERT INTO `gl_nf_ifoperators` VALUES (2, '>');
INSERT INTO `gl_nf_ifoperators` VALUES (3, '<');
INSERT INTO `gl_nf_ifoperators` VALUES (4, '!=');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_ifprocessarguments`
-- 

CREATE TABLE `gl_nf_ifprocessarguments` (
  `id` int(11) NOT NULL default '0',
  `label` varchar(200) NOT NULL default '',
  `logicalEntry` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_nf_ifprocessarguments`
-- 

INSERT INTO `gl_nf_ifprocessarguments` VALUES (1, 'Last Task Status is Success', 'lasttasksuccess');
INSERT INTO `gl_nf_ifprocessarguments` VALUES (2, 'Last Task Status is Cancel', 'lasttaskcancel');
INSERT INTO `gl_nf_ifprocessarguments` VALUES (3, 'Last Task Status is Hold', 'lasttaskhold');
INSERT INTO `gl_nf_ifprocessarguments` VALUES (4, 'Last Task Status is Aborted', 'lasttaskaborted');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_notifications`
-- 

CREATE TABLE `gl_nf_notifications` (
  `queueID` int(11) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `notification_sent` tinyint(1) NOT NULL default '0',
  KEY `queueID` (`queueID`),
  KEY `uid` (`uid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_nf_notifications`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_process`
-- 

CREATE TABLE `gl_nf_process` (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateID` int(11) NOT NULL default '0',
  `complete` int(11) default NULL,
  `initiator_uid` int(11) default NULL,
  `pid` int(11) default NULL,
  `initiatedDate` date default NULL,
  `completedDate` date default NULL,
  PRIMARY KEY  (`id`),
  KEY `nf_templateID` (`nf_templateID`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `gl_nf_process`
-- 

INSERT INTO `gl_nf_process` VALUES (1, 1, 1, NULL, 0, '2006-12-22', '2006-12-22');
INSERT INTO `gl_nf_process` VALUES (2, 1, 1, NULL, 0, '2006-12-22', '2006-12-22');
INSERT INTO `gl_nf_process` VALUES (3, 1, 1, NULL, 0, '2006-12-22', '2006-12-22');
INSERT INTO `gl_nf_process` VALUES (4, 1, 0, NULL, 0, '2006-12-22', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_processvariables`
-- 

CREATE TABLE `gl_nf_processvariables` (
  `id` int(11) NOT NULL auto_increment,
  `nf_processID` int(11) NOT NULL default '0',
  `variableValue` varchar(255) NOT NULL default '',
  `nf_templateVariableID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `nf_processID` (`nf_processID`),
  KEY `nf_templateVariableID` (`nf_templateVariableID`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `gl_nf_processvariables`
-- 

INSERT INTO `gl_nf_processvariables` VALUES (1, 1, '2', 1);
INSERT INTO `gl_nf_processvariables` VALUES (2, 2, '2', 1);
INSERT INTO `gl_nf_processvariables` VALUES (3, 3, '2', 1);
INSERT INTO `gl_nf_processvariables` VALUES (4, 4, '2', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_productionassignments`
-- 

CREATE TABLE `gl_nf_productionassignments` (
  `id` int(11) NOT NULL auto_increment,
  `task_id` int(8) NOT NULL default '0',
  `uid` int(8) NOT NULL default '0',
  `nf_processVariable` mediumint(8) NOT NULL default '0',
  `assignBack_uid` mediumint(8) NOT NULL default '0',
  `last_updated` int(11) NOT NULL default '0',
  `security_hash` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `task_id` (`task_id`),
  KEY `nf_processVariable` (`nf_processVariable`),
  KEY `assignBack_uid` (`assignBack_uid`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `gl_nf_productionassignments`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_queue`
-- 

CREATE TABLE `gl_nf_queue` (
  `id` int(11) NOT NULL auto_increment,
  `nf_processID` int(11) NOT NULL default '0',
  `nf_templateDataID` int(11) default '0',
  `status` int(11) default NULL,
  `uid` int(11) default NULL,
  `archived` int(11) default NULL,
  `prePopulate` int(11) default NULL,
  `createdDate` datetime default NULL,
  `completedDate` datetime default NULL,
  `nextReminderTime` datetime default NULL,
  `startedDate` datetime default NULL,
  `numRemindersSent` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `nf_processID` (`nf_processID`),
  KEY `nf_templateDataID` (`nf_templateDataID`),
  KEY `status` (`status`),
  KEY `archived` (`archived`)
) TYPE=MyISAM AUTO_INCREMENT=17 ;

-- 
-- Dumping data for table `gl_nf_queue`
-- 

INSERT INTO `gl_nf_queue` VALUES (1, 1, 1, 1, 2, 1, NULL, '2006-12-22 12:59:01', '2006-12-22 13:09:25', NULL, '2006-12-22 12:59:14', 0);
INSERT INTO `gl_nf_queue` VALUES (2, 1, 2, 1, NULL, 1, NULL, '2006-12-22 13:09:25', '2006-12-22 13:09:45', NULL, NULL, 0);
INSERT INTO `gl_nf_queue` VALUES (3, 1, 3, 1, NULL, 1, NULL, '2006-12-22 13:09:45', '2006-12-22 13:09:46', NULL, NULL, 0);
INSERT INTO `gl_nf_queue` VALUES (4, 1, 5, 1, NULL, 1, NULL, '2006-12-22 13:09:46', '2006-12-22 13:09:48', NULL, NULL, 0);
INSERT INTO `gl_nf_queue` VALUES (5, 1, 6, 1, NULL, 1, NULL, '2006-12-22 13:09:48', '2006-12-22 13:09:57', NULL, NULL, 0);
INSERT INTO `gl_nf_queue` VALUES (6, 2, 1, 1, 2, 1, NULL, '2006-12-22 13:11:33', '2006-12-22 13:11:45', NULL, '2006-12-22 13:11:40', 0);
INSERT INTO `gl_nf_queue` VALUES (7, 2, 2, 1, NULL, 1, NULL, '2006-12-22 13:11:45', '2006-12-22 13:11:46', NULL, NULL, 0);
INSERT INTO `gl_nf_queue` VALUES (8, 2, 3, 1, NULL, 1, NULL, '2006-12-22 13:11:46', '2006-12-22 13:16:03', NULL, NULL, 0);
INSERT INTO `gl_nf_queue` VALUES (9, 2, 5, 1, NULL, 1, NULL, '2006-12-22 13:16:03', '2006-12-22 13:16:03', NULL, NULL, 0);
INSERT INTO `gl_nf_queue` VALUES (10, 2, 6, 1, NULL, 1, NULL, '2006-12-22 13:16:03', '2006-12-22 13:16:03', NULL, NULL, 0);
INSERT INTO `gl_nf_queue` VALUES (11, 3, 1, 1, 2, 1, NULL, '2006-12-22 14:58:38', '2006-12-22 14:58:51', NULL, '2006-12-22 14:58:44', 0);
INSERT INTO `gl_nf_queue` VALUES (12, 3, 2, 1, NULL, 1, NULL, '2006-12-22 14:58:51', '2006-12-22 14:58:51', NULL, NULL, 0);
INSERT INTO `gl_nf_queue` VALUES (13, 3, 3, 1, NULL, 1, NULL, '2006-12-22 14:58:51', '2006-12-22 14:58:53', NULL, NULL, 0);
INSERT INTO `gl_nf_queue` VALUES (14, 3, 5, 1, NULL, 1, NULL, '2006-12-22 14:58:53', '2006-12-22 14:58:54', NULL, NULL, 0);
INSERT INTO `gl_nf_queue` VALUES (15, 3, 6, 1, NULL, 1, NULL, '2006-12-22 14:58:54', '2006-12-22 14:59:01', NULL, NULL, 0);
INSERT INTO `gl_nf_queue` VALUES (16, 4, 1, 1, 2, 0, NULL, '2006-12-22 16:39:58', NULL, NULL, '2006-12-28 17:27:05', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_queuefrom`
-- 

CREATE TABLE `gl_nf_queuefrom` (
  `id` int(11) NOT NULL auto_increment,
  `queueID` int(11) default NULL,
  `fromQueueID` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=13 ;

-- 
-- Dumping data for table `gl_nf_queuefrom`
-- 

INSERT INTO `gl_nf_queuefrom` VALUES (1, 2, 1);
INSERT INTO `gl_nf_queuefrom` VALUES (2, 3, 2);
INSERT INTO `gl_nf_queuefrom` VALUES (3, 4, 3);
INSERT INTO `gl_nf_queuefrom` VALUES (4, 5, 4);
INSERT INTO `gl_nf_queuefrom` VALUES (5, 7, 6);
INSERT INTO `gl_nf_queuefrom` VALUES (6, 8, 7);
INSERT INTO `gl_nf_queuefrom` VALUES (7, 9, 8);
INSERT INTO `gl_nf_queuefrom` VALUES (8, 10, 9);
INSERT INTO `gl_nf_queuefrom` VALUES (9, 12, 11);
INSERT INTO `gl_nf_queuefrom` VALUES (10, 13, 12);
INSERT INTO `gl_nf_queuefrom` VALUES (11, 14, 13);
INSERT INTO `gl_nf_queuefrom` VALUES (12, 15, 14);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_steptype`
-- 

CREATE TABLE `gl_nf_steptype` (
  `id` int(11) NOT NULL default '0',
  `stepType` varchar(50) NOT NULL default '',
  `flexField` varchar(100) default NULL,
  `is_interactiveStepType` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_nf_steptype`
-- 

INSERT INTO `gl_nf_steptype` VALUES (1, 'Manual Web', NULL, 1);
INSERT INTO `gl_nf_steptype` VALUES (2, 'And', NULL, 0);
INSERT INTO `gl_nf_steptype` VALUES (4, 'Batch', NULL, 0);
INSERT INTO `gl_nf_steptype` VALUES (5, 'If', NULL, 0);
INSERT INTO `gl_nf_steptype` VALUES (6, 'batch function', NULL, 0);
INSERT INTO `gl_nf_steptype` VALUES (7, 'interactive function', NULL, 1);
INSERT INTO `gl_nf_steptype` VALUES (8, 'nexForm', NULL, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_template`
-- 

CREATE TABLE `gl_nf_template` (
  `id` int(11) NOT NULL auto_increment,
  `templateName` varchar(100) NOT NULL default '',
  `useProject` int(11) NOT NULL default '0',
  `AppGroup` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `gl_nf_template`
-- 

INSERT INTO `gl_nf_template` VALUES (1, 'TEST FLOW 1 - Manual Web Test', 0, 0);
INSERT INTO `gl_nf_template` VALUES (2, 'TEST FLOW 2 - AND Test 1: 2 Branch', 0, 0);
INSERT INTO `gl_nf_template` VALUES (3, 'TEST FLOW 3 - AND Test 2: 3 Branch Regen', 0, 0);
INSERT INTO `gl_nf_template` VALUES (4, 'TEST FLOW 4 - OR Test', 0, 0);
INSERT INTO `gl_nf_template` VALUES (5, 'TEST FLOW 5 - IF Test 1: Variable Value', 0, 0);
INSERT INTO `gl_nf_template` VALUES (6, 'TEST FLOW 6 - IF Test 2: Task Status', 0, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_templateassignment`
-- 

CREATE TABLE `gl_nf_templateassignment` (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateDataID` int(11) NOT NULL default '0',
  `uid` int(11) default NULL,
  `gid` int(11) default NULL,
  `nf_processVariable` int(11) default NULL,
  `nf_prenotifyVariable` int(11) default NULL,
  `nf_postnotifyVariable` int(11) default NULL,
  `nf_remindernotifyVariable` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `nf_templateDataID` (`nf_templateDataID`),
  KEY `nf_processVariable` (`nf_processVariable`),
  KEY `nf_prenotifyVariable` (`nf_prenotifyVariable`),
  KEY `nf_postnotifyVariable` (`nf_postnotifyVariable`),
  KEY `nf_remindernotifyVariable` (`nf_remindernotifyVariable`)
) TYPE=MyISAM AUTO_INCREMENT=11 ;

-- 
-- Dumping data for table `gl_nf_templateassignment`
-- 

INSERT INTO `gl_nf_templateassignment` VALUES (1, 1, 2, NULL, 1, NULL, NULL, NULL);
INSERT INTO `gl_nf_templateassignment` VALUES (2, 8, 2, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `gl_nf_templateassignment` VALUES (3, 9, 2, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `gl_nf_templateassignment` VALUES (4, 15, 2, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `gl_nf_templateassignment` VALUES (5, 20, 2, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `gl_nf_templateassignment` VALUES (6, 21, 2, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `gl_nf_templateassignment` VALUES (7, 23, NULL, NULL, 4, NULL, NULL, NULL);
INSERT INTO `gl_nf_templateassignment` VALUES (8, 36, NULL, NULL, 6, NULL, NULL, NULL);
INSERT INTO `gl_nf_templateassignment` VALUES (9, 40, NULL, NULL, 6, NULL, NULL, NULL);
INSERT INTO `gl_nf_templateassignment` VALUES (10, 44, NULL, NULL, 6, NULL, NULL, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_templatedata`
-- 

CREATE TABLE `gl_nf_templatedata` (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateID` int(11) NOT NULL default '0',
  `logicalID` int(11) default NULL,
  `nf_stepType` tinyint(1) NOT NULL default '0',
  `nf_handlerId` int(11) NOT NULL default '0',
  `firstTask` tinyint(1) default NULL,
  `taskname` varchar(150) default NULL,
  `assignedByVariable` tinyint(4) default NULL,
  `argumentVariable` varchar(255) default NULL,
  `argumentProcess` varchar(255) default NULL,
  `operator` varchar(10) default NULL,
  `ifValue` varchar(255) default NULL,
  `regenerate` tinyint(1) default NULL,
  `regenAllLiveTasks` tinyint(1) default '0',
  `isDynamicForm` int(11) NOT NULL default '0',
  `dynamicFormVariableID` int(11) NOT NULL default '0',
  `isDynamicTaskName` int(11) NOT NULL default '0',
  `dynamicTaskNameVariableID` int(11) NOT NULL default '0',
  `function` varchar(255) NOT NULL default '',
  `formid` mediumint(8) NOT NULL default '0',
  `optionalParm` varchar(64) NOT NULL default '',
  `reminderInterval` tinyint(1) NOT NULL default '0',
  `subsequentReminderInterval` tinyint(1) NOT NULL default '0',
  `last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
  `prenotify_message` varchar(255) NOT NULL default '',
  `postnotify_message` varchar(255) NOT NULL default '',
  `reminder_message` varchar(255) NOT NULL default '',
  `numReminders` tinyint(1) NOT NULL default '0',
  `escalateVariableID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `nf_templateID` (`nf_templateID`)
) TYPE=MyISAM AUTO_INCREMENT=48 ;

-- 
-- Dumping data for table `gl_nf_templatedata`
-- 

INSERT INTO `gl_nf_templatedata` VALUES (1, 1, 1, 1, 2, 1, 'Accept or Reject', 1, NULL, NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, '', 0, 'op-parm-test', 0, 0, '2006-09-20 10:40:38', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (2, 1, 2, 5, 0, 0, 'Check Point', 0, '0', '1', '0', '', 0, 0, 0, 0, 0, 0, '', 0, '', 0, 0, '2006-09-20 10:34:13', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (3, 1, 3, 6, 0, 0, 'Pass', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_passfail', 0, 'pass', 0, 0, '2006-09-20 14:21:40', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (4, 1, 4, 6, 0, 0, 'Fail', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_passfail', 0, 'fail', 0, 0, '2006-09-20 14:26:34', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (5, 1, 5, 5, 0, 0, 'Check Point', 0, '0', '1', '0', '', 0, 0, 0, 0, 0, 0, '', 0, '', 0, 0, '2006-09-20 15:13:22', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (6, 1, 6, 6, 0, 0, 'No Operation', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_dummy', 0, '', 0, 0, '2006-09-20 14:44:43', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (7, 2, 1, 6, 0, 1, 'No Op', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #1): Branching out to tasks #2 AND #3)', 0, 0, '2006-09-20 16:39:30', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (8, 2, 2, 7, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_yesno', 0, 'Interactive Function (Task #2): Complete. Next: Task #4', 0, 0, '2006-09-20 16:43:06', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (9, 2, 3, 7, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_yesno', 0, 'Interactive Function (Task #3): Complete.  Next: Task #4', 0, 0, '2006-09-21 10:37:25', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (10, 2, 4, 2, 0, 0, 'AND Gate', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, '', 0, 0, '2006-09-20 16:31:23', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (11, 2, 5, 6, 0, 0, 'No Op', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #5): End of Workflow.  Test Passed!', 0, 0, '2006-09-20 16:43:29', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (12, 3, 1, 6, 0, 1, 'No Op', 0, NULL, NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #1): Branching to Task #2, #3, #4', 0, 0, '2006-09-21 10:47:49', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (13, 3, 2, 6, 0, 0, 'Task 1', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #2): Now Moving to AND (Task #6)', 0, 0, '2006-09-21 10:49:35', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (14, 3, 3, 6, 0, 0, 'Task 2', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #3): Now Moving to AND (Task #6)', 0, 0, '2006-09-21 11:15:41', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (15, 3, 4, 7, 0, 0, 'Task 3', 0, NULL, NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, 'nf_testsuite_yesno', 0, 'Interactive Function (Task #4): If accept, goto 6, else, goto 4', 0, 0, '2006-09-21 16:07:24', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (16, 3, 5, 5, 0, 0, 'Check Point', 0, '0', '1', '0', '', 0, 0, 0, 0, 0, 0, '', 0, '', 0, 0, '2006-09-21 11:00:30', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (17, 3, 6, 2, 0, 0, 'AND Gate', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '', 0, '', 0, 0, '2006-09-21 11:01:18', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (18, 3, 7, 6, 0, 0, 'No Op', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #7): Passed AND gate. EoW. Test Passed!', 0, 0, '2006-09-21 11:07:15', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (19, 4, 1, 6, 0, 1, 'No Op', 0, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #1): Branch out to tasks #2 and #3', 0, 0, '2006-09-22 11:27:08', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (20, 4, 2, 7, 0, 0, 'Task 1', 1, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_yesno', 0, 'Interactive Function (Task #2): Completed.', 0, 0, '2006-09-22 11:30:13', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (21, 4, 3, 7, 0, 0, 'Task 2', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_yesno', 0, 'Interactive Task (Task #3): Completed.', 0, 0, '2006-09-22 11:27:18', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (22, 4, 4, 6, 0, 0, 'No Op', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 3, 'Batch Function (Task #5): EoW.  Successful Test!', 0, 0, '2006-09-22 11:24:34', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (23, 5, 1, 7, 0, 1, 'Set VAR1', 1, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_setvar1', 0, '', 0, 0, '2006-09-21 16:34:30', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (24, 5, 2, 5, 0, 0, 'If VAR1 &gt; 5', 0, '5', '0', '2', '5', 0, 0, 0, 0, 0, 0, '', 0, '', 0, 0, '2006-09-21 16:32:44', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (25, 5, 3, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is greater than 5', 0, 0, '2006-09-21 16:28:43', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (26, 5, 4, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is NOT greater than 5', 0, 0, '2006-09-21 16:28:24', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (27, 5, 5, 5, 0, 0, 'If VAR1', 0, '5', '0', '3', '5', 0, 0, 0, 0, 0, 0, '', 0, '', 0, 0, '2006-09-21 16:51:07', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (28, 5, 6, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is less than 5', 0, 0, '2006-09-21 16:43:33', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (29, 5, 7, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is NOT less than 5', 0, 0, '2006-09-21 16:42:17', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (30, 5, 8, 5, 0, 0, 'If VAR1 = 5', 0, '5', '0', '1', '5', 0, 0, 0, 0, 0, 0, '', 0, '9', 0, 0, '2006-09-21 16:36:21', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (31, 5, 9, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is equal to 5', 0, 0, '2006-09-21 16:41:17', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (32, 5, 10, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is NOT equal to 5', 0, 0, '2006-09-21 16:39:50', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (33, 5, 11, 5, 0, 0, 'If VAR1 != 5', 0, '5', '0', '4', '5', 0, 0, 0, 0, 0, 0, '', 0, '', 0, 0, '2006-09-21 16:37:19', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (34, 5, 12, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is NOT equal to 5', 0, 0, '2006-09-21 16:39:05', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (35, 5, 13, 6, 0, 0, 'New Task', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'VAR1 is equal to 5', 0, 0, '2006-09-21 16:38:38', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (36, 6, 1, 7, 0, 1, 'Success Task', 1, '6', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_success', 0, 'Batch Function (Task #1): Success', 0, 0, '2006-09-22 15:06:27', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (37, 6, 2, 5, 0, 0, 'If Success', 0, '0', '1', '0', '', NULL, 0, 0, 0, 0, 0, '', 0, '', 0, 0, '2006-09-22 11:48:49', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (38, 6, 3, 6, 0, 0, 'Test Pass', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #3): Test Passed', 0, 0, '2006-09-22 13:36:05', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (39, 6, 4, 6, 0, 0, 'Test Fail', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #4): Test Failed', 0, 0, '2006-09-22 13:38:30', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (40, 6, 5, 7, 0, 0, 'Cancel Task', 1, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_cancel', 0, 'Interactive Function (Task #5): Cancel Task', 0, 0, '2006-09-22 15:05:09', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (41, 6, 6, 5, 0, 0, 'If Cancel', 0, '0', '2', '0', '', 0, 0, 0, 0, 0, 0, '', 0, '', 0, 0, '2006-09-22 13:53:32', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (42, 6, 7, 6, 0, 0, 'Test Pass', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #7): Test Passed', 0, 0, '2006-09-22 14:00:03', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (43, 6, 8, 6, 0, 0, 'Test Fail', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #8): Test Failed', 0, 0, '2006-09-22 14:00:33', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (44, 6, 13, 7, 0, 0, 'Abort Task', 1, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_abort', 0, 'Interactive Function (Task #13): Abort Task', 0, 0, '2006-09-22 15:54:46', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (45, 6, 14, 5, 0, 0, 'If Aborted', 0, '0', '4', '0', '', 0, 0, 0, 0, 0, 0, '', 0, '', 0, 0, '2006-09-22 13:54:54', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (46, 6, 15, 6, 0, 0, 'Test Pass', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #15): Test Passed', 0, 0, '2006-09-22 13:59:17', '', '', '', 0, 0);
INSERT INTO `gl_nf_templatedata` VALUES (47, 6, 16, 6, 0, 0, 'Test Fail', 0, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 'nf_testsuite_noop', 0, 'Batch Function (Task #16): Test Failed', 0, 0, '2006-09-22 13:59:02', '', '', '', 0, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_templatedatanextstep`
-- 

CREATE TABLE `gl_nf_templatedatanextstep` (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateDataFrom` int(11) NOT NULL default '0',
  `nf_templateDataTo` int(11) default '0',
  `nf_templateDataToFalse` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=44 ;

-- 
-- Dumping data for table `gl_nf_templatedatanextstep`
-- 

INSERT INTO `gl_nf_templatedatanextstep` VALUES (1, 1, 2, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (2, 2, 3, 4);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (3, 3, 5, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (4, 4, 5, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (5, 5, 6, 1);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (6, 7, 8, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (7, 7, 9, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (8, 8, 10, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (9, 9, 10, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (10, 10, 11, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (11, 12, 13, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (12, 12, 14, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (13, 12, 15, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (14, 15, 16, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (15, 16, 17, 15);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (16, 13, 17, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (17, 14, 17, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (18, 17, 18, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (19, 19, 20, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (20, 19, 21, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (21, 20, 22, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (22, 21, 22, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (23, 23, 24, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (24, 24, 25, 26);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (25, 25, 27, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (26, 26, 27, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (27, 27, 28, 29);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (28, 28, 30, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (29, 29, 30, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (30, 30, 31, 32);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (31, 31, 33, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (32, 32, 33, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (33, 33, 34, 35);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (34, 36, 37, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (35, 37, 38, 39);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (36, 38, 40, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (37, 39, 40, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (38, 40, 41, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (39, 41, 42, 43);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (40, 42, 44, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (41, 43, 44, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (42, 44, 45, NULL);
INSERT INTO `gl_nf_templatedatanextstep` VALUES (43, 45, 46, 47);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_templatevariables`
-- 

CREATE TABLE `gl_nf_templatevariables` (
  `id` int(11) NOT NULL auto_increment,
  `nf_templateID` int(11) NOT NULL default '0',
  `nf_variableTypeID` int(11) NOT NULL default '0',
  `variableName` varchar(100) NOT NULL default '',
  `variableValue` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `nf_templateID` (`nf_templateID`),
  KEY `nf_variableTypeID` (`nf_variableTypeID`)
) TYPE=MyISAM AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `gl_nf_templatevariables`
-- 

INSERT INTO `gl_nf_templatevariables` VALUES (1, 1, 0, 'INITIATOR', '');
INSERT INTO `gl_nf_templatevariables` VALUES (2, 3, 0, 'INITIATOR', '');
INSERT INTO `gl_nf_templatevariables` VALUES (3, 4, 0, 'INITIATOR', '');
INSERT INTO `gl_nf_templatevariables` VALUES (4, 5, 0, 'INITIATOR', '');
INSERT INTO `gl_nf_templatevariables` VALUES (5, 5, 0, 'VAR1', '1');
INSERT INTO `gl_nf_templatevariables` VALUES (6, 6, 0, 'INITIATOR', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nf_userawayprefs`
-- 

CREATE TABLE `gl_nf_userawayprefs` (
  `uid` mediumint(8) NOT NULL default '0',
  `away_start` int(11) NOT NULL default '0',
  `away_return` int(11) NOT NULL default '0',
  `reassign_uid` mediumint(8) NOT NULL default '0',
  `reason` varchar(255) NOT NULL default '',
  `is_active` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`uid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_nf_userawayprefs`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nfproject_approvals`
-- 

CREATE TABLE `gl_nfproject_approvals` (
  `id` int(11) NOT NULL auto_increment,
  `process_id` mediumint(8) NOT NULL default '0',
  `form_id` mediumint(8) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `date_updated` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `form_id` (`form_id`),
  KEY `uid` (`uid`),
  KEY `process_id` (`process_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nfproject_approvals`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nfproject_comments`
-- 

CREATE TABLE `gl_nfproject_comments` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` mediumint(8) NOT NULL default '0',
  `task_id` mediumint(8) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0',
  `comment` longtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nfproject_comments`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nfproject_forms`
-- 

CREATE TABLE `gl_nfproject_forms` (
  `id` mediumint(8) NOT NULL auto_increment,
  `project_id` mediumint(8) NOT NULL default '0',
  `trial_id` mediumint(8) NOT NULL default '0',
  `form_id` mediumint(8) NOT NULL default '0',
  `formtype` varchar(32) NOT NULL default '',
  `results_id` mediumint(8) NOT NULL default '0',
  `created_by_taskid` mediumint(8) NOT NULL default '0',
  `created_by_uid` mediumint(8) NOT NULL default '0',
  `is_locked_by_uid` mediumint(8) NOT NULL default '0',
  `is_trial_project` tinyint(1) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `form_id` (`form_id`),
  KEY `results_id` (`results_id`),
  KEY `created_by_taskid` (`created_by_taskid`),
  KEY `trial_id` (`trial_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nfproject_forms`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nfproject_taskhistory`
-- 

CREATE TABLE `gl_nfproject_taskhistory` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` mediumint(8) NOT NULL default '0',
  `process_id` mediumint(8) NOT NULL default '0',
  `task_id` mediumint(8) NOT NULL default '0',
  `assigned_uid` mediumint(8) NOT NULL default '0',
  `date_assigned` int(11) NOT NULL default '0',
  `date_started` int(11) NOT NULL default '0',
  `date_completed` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `assigned_uid` (`assigned_uid`),
  KEY `process_id` (`process_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nfproject_taskhistory`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nfproject_timestamps`
-- 

CREATE TABLE `gl_nfproject_timestamps` (
  `id` mediumint(8) NOT NULL auto_increment,
  `project_id` mediumint(8) NOT NULL default '0',
  `project_formid` mediumint(8) NOT NULL default '0',
  `process_id` mediumint(8) NOT NULL default '0',
  `statusmsg` varchar(255) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `project_id` (`project_id`),
  KEY `project_formid` (`project_formid`),
  KEY `process_id` (`process_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nfproject_timestamps`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nfprojects`
-- 

CREATE TABLE `gl_nfprojects` (
  `id` mediumint(8) NOT NULL auto_increment,
  `project_num` varchar(12) default NULL,
  `wf_process_id` mediumint(8) NOT NULL default '0',
  `wf_task_id` mediumint(8) NOT NULL default '0',
  `originator_uid` mediumint(8) NOT NULL default '0',
  `description` varchar(255) NOT NULL default '',
  `cust1` varchar(255) NOT NULL default '',
  `cust2` varchar(255) NOT NULL default '',
  `cust3` varchar(255) NOT NULL default '',
  `status` tinyint(1) NOT NULL default '0',
  `prev_status` tinyint(1) NOT NULL default '0',
  `related_processes` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `resultsid` (`project_num`),
  KEY `wf_templateid` (`wf_process_id`),
  KEY `originator_uid` (`originator_uid`),
  KEY `status` (`status`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nfprojects`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxcontent`
-- 

CREATE TABLE `gl_nxcontent` (
  `help` longtext
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_nxcontent`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxcontent_images`
-- 

CREATE TABLE `gl_nxcontent_images` (
  `id` mediumint(8) NOT NULL auto_increment,
  `page_id` mediumint(8) NOT NULL default '0',
  `imagenum` tinyint(5) NOT NULL default '0',
  `imagefile` varchar(64) NOT NULL default '',
  `autoscale` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `page_id` (`page_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nxcontent_images`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxcontent_pages`
-- 

CREATE TABLE `gl_nxcontent_pages` (
  `id` mediumint(8) NOT NULL auto_increment,
  `sid` varchar(40) NOT NULL default '',
  `pid` mediumint(8) NOT NULL default '0',
  `gid` varchar(32) NOT NULL default '',
  `type` varchar(16) NOT NULL default '',
  `pageorder` int(5) NOT NULL default '0',
  `name` varchar(32) NOT NULL default '',
  `blockformat` varchar(32) NOT NULL default '',
  `heading` varchar(255) NOT NULL default '',
  `content` longtext NOT NULL,
  `hits` int(11) NOT NULL default '0',
  `menutype` tinyint(1) NOT NULL default '0',
  `is_menu_newpage` tinyint(1) NOT NULL default '0',
  `submenu_item` tinyint(1) NOT NULL default '0',
  `show_submenu` tinyint(1) NOT NULL default '1',
  `show_blockmenu` tinyint(1) NOT NULL default '1',
  `show_breadcrumbs` tinyint(1) NOT NULL default '1',
  `is_draft` tinyint(1) NOT NULL default '0',
  `grp_access` tinyint(5) NOT NULL default '0',
  `owner_id` mediumint(8) NOT NULL default '0',
  `group_id` mediumint(8) NOT NULL default '0',
  `perm_owner` tinyint(1) NOT NULL default '0',
  `perm_group` tinyint(1) NOT NULL default '0',
  `perm_members` tinyint(1) NOT NULL default '0',
  `perm_anon` tinyint(1) NOT NULL default '0',
  `pagetitle` varchar(255) NOT NULL default '',
  `meta_description` text NOT NULL,
  `meta_keywords` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `parent` (`pid`),
  KEY `pageorder` (`pageorder`),
  KEY `gid` (`gid`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `gl_nxcontent_pages`
-- 

INSERT INTO `gl_nxcontent_pages` VALUES (1, '', 0, '', 'category', 10, 'frontpage', 'none', 'Front Page Folder', 'Create a page under this folder if you want to have a page loaded as the frontpage', 1, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, '', '', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxfile_access`
-- 

CREATE TABLE `gl_nxfile_access` (
  `accid` mediumint(9) NOT NULL auto_increment,
  `catid` mediumint(9) NOT NULL default '0',
  `uid` mediumint(9) NOT NULL default '0',
  `grp_id` mediumint(9) NOT NULL default '0',
  `view` tinyint(1) NOT NULL default '0',
  `upload` tinyint(1) NOT NULL default '0',
  `upload_direct` tinyint(1) NOT NULL default '0',
  `upload_ver` tinyint(1) NOT NULL default '0',
  `approval` tinyint(1) NOT NULL default '0',
  `admin` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`accid`),
  KEY `catid` (`catid`)
) TYPE=MyISAM COMMENT='nexfile Access Rights - for user or group access to category' AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nxfile_access`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxfile_categories`
-- 

CREATE TABLE `gl_nxfile_categories` (
  `cid` mediumint(9) NOT NULL auto_increment,
  `pid` mediumint(8) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `image` varchar(255) default NULL,
  `auto_create_notifications` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  KEY `pid` (`pid`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nxfile_categories`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxfile_filedetail`
-- 

CREATE TABLE `gl_nxfile_filedetail` (
  `fid` mediumint(8) NOT NULL default '0',
  `description` longtext NOT NULL,
  `platform` varchar(32) NOT NULL default '',
  `hits` mediumint(9) NOT NULL default '0',
  `rating` tinyint(4) NOT NULL default '0',
  `votes` tinyint(4) unsigned NOT NULL default '0',
  `comments` tinyint(4) unsigned NOT NULL default '0',
  KEY `fid` (`fid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_nxfile_filedetail`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxfile_files`
-- 

CREATE TABLE `gl_nxfile_files` (
  `fid` mediumint(8) NOT NULL auto_increment,
  `cid` mediumint(8) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `title` varchar(128) NOT NULL default '',
  `version` tinyint(3) unsigned NOT NULL default '1',
  `ftype` varchar(16) NOT NULL default '',
  `size` mediumint(9) NOT NULL default '0',
  `thumbnail` varchar(255) NOT NULL default '',
  `thumbtype` varchar(16) NOT NULL default '',
  `submitter` mediumint(8) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `date` int(8) NOT NULL default '0',
  `version_ctl` tinyint(1) NOT NULL default '0',
  `status_changedby_uid` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`fid`),
  KEY `cid` (`cid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nxfile_files`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxfile_filesubmissions`
-- 

CREATE TABLE `gl_nxfile_filesubmissions` (
  `id` mediumint(8) NOT NULL auto_increment,
  `fid` mediumint(8) NOT NULL default '0',
  `cid` mediumint(8) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `tempname` varchar(255) NOT NULL default '',
  `title` varchar(128) NOT NULL default '',
  `ftype` varchar(16) NOT NULL default '',
  `description` longtext NOT NULL,
  `version` tinyint(3) unsigned NOT NULL default '1',
  `version_note` longtext NOT NULL,
  `size` mediumint(9) NOT NULL default '0',
  `thumbnail` varchar(255) NOT NULL default '',
  `thumbtype` varchar(16) NOT NULL default '',
  `submitter` mediumint(8) NOT NULL default '0',
  `date` int(8) NOT NULL default '0',
  `version_ctl` tinyint(1) NOT NULL default '0',
  `notify` tinyint(1) NOT NULL default '1',
  `status` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cid` (`cid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nxfile_filesubmissions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxfile_fileversions`
-- 

CREATE TABLE `gl_nxfile_fileversions` (
  `id` mediumint(9) NOT NULL auto_increment,
  `fid` mediumint(9) NOT NULL default '0',
  `fname` varchar(255) NOT NULL default '',
  `ftype` varchar(16) NOT NULL default '',
  `version` tinyint(3) unsigned NOT NULL default '0',
  `size` mediumint(9) NOT NULL default '0',
  `notes` longtext NOT NULL,
  `date` int(11) NOT NULL default '0',
  `uid` mediumint(9) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fid` (`fid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nxfile_fileversions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxfile_notifications`
-- 

CREATE TABLE `gl_nxfile_notifications` (
  `id` mediumint(9) NOT NULL auto_increment,
  `fid` mediumint(9) NOT NULL default '0',
  `cid` mediumint(9) NOT NULL default '0',
  `uid` mediumint(9) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fid` (`fid`),
  KEY `cid` (`cid`),
  KEY `uid` (`uid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nxfile_notifications`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxform_definitions`
-- 

CREATE TABLE `gl_nxform_definitions` (
  `id` mediumint(8) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `shortname` varchar(32) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `responses` mediumint(8) NOT NULL default '0',
  `template` varchar(128) NOT NULL default '',
  `post_method` varchar(32) NOT NULL default '',
  `post_option` varchar(255) NOT NULL default '',
  `fieldsets` longtext NOT NULL,
  `before_formid` mediumint(8) NOT NULL default '0',
  `after_formid` mediumint(8) NOT NULL default '0',
  `show_as_tab` tinyint(1) NOT NULL default '0',
  `tab_label` varchar(128) NOT NULL default '',
  `metavalues` mediumint(8) NOT NULL default '0',
  `intro_text` longtext NOT NULL,
  `after_post_text` longtext NOT NULL,
  `on_submit` varchar(255) NOT NULL default '',
  `return_url` varchar(255) NOT NULL default '',
  `perms_view` mediumint(8) NOT NULL default '2',
  `perms_access` mediumint(8) NOT NULL default '2',
  `perms_edit` mediumint(8) NOT NULL default '2',
  `status` tinyint(1) NOT NULL default '0',
  `gid` varchar(32) NOT NULL default '',
  `results_table` varchar(64) NOT NULL default '',
  `admin_url` varchar(255) NOT NULL default '',
  `comments` longtext NOT NULL,
  `show_mandatory_note` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `gl_nxform_definitions`
-- 

INSERT INTO `gl_nxform_definitions` VALUES (1, 'Install Test', 'Install Test', 1121781422, 33, 'default', 'dbsave', '', '', 0, 44, 1, '', 0, 'This is a test of the plugin install - just some random fields.', '', '', '', 26, 1, 1, 1, '', '', '', '', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxform_fields`
-- 

CREATE TABLE `gl_nxform_fields` (
  `id` mediumint(8) NOT NULL auto_increment,
  `formid` mediumint(8) NOT NULL default '0',
  `tfid` mediumint(8) NOT NULL default '0',
  `type` varchar(32) NOT NULL default '',
  `fieldorder` mediumint(8) NOT NULL default '0',
  `field_name` varchar(64) NOT NULL default '',
  `label` text NOT NULL,
  `style` varchar(32) NOT NULL default 'default',
  `layout` varchar(16) NOT NULL default '',
  `col_width` smallint(1) default NULL,
  `col_padding` smallint(1) default NULL,
  `is_vertical` tinyint(1) NOT NULL default '0',
  `is_newline` tinyint(1) NOT NULL default '1',
  `is_mandatory` tinyint(1) NOT NULL default '0',
  `is_searchfield` tinyint(1) NOT NULL default '0',
  `is_resultsfield` tinyint(1) NOT NULL default '0',
  `is_reverseorder` tinyint(1) NOT NULL default '0',
  `is_htmlfiltered` tinyint(1) NOT NULL default '0',
  `is_internaluse` tinyint(1) NOT NULL default '0',
  `hidelabel` tinyint(1) NOT NULL default '0',
  `field_attributes` varchar(255) NOT NULL default '',
  `field_help` varchar(255) NOT NULL default '',
  `field_values` longtext NOT NULL,
  `value_by_function` tinyint(1) NOT NULL default '0',
  `validation` varchar(255) NOT NULL default '',
  `javascript` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `formid` (`formid`),
  KEY `fieldorder` (`fieldorder`)
) TYPE=MyISAM AUTO_INCREMENT=9 ;

-- 
-- Dumping data for table `gl_nxform_fields`
-- 

INSERT INTO `gl_nxform_fields` VALUES (1, 1, 1, 'select', 10, 'sel-frm1_1', 'Salutation', '1', '', NULL, NULL, 0, 1, 0, 0, 1, 0, 0, 0, 0, '', '', 'fe_getSalutations', 1, '', '');
INSERT INTO `gl_nxform_fields` VALUES (2, 1, 2, 'text', 20, 'txt-frm1_2', 'Name', '2', '', NULL, NULL, 0, 1, 0, 0, 1, 0, 0, 0, 0, '', '', '', 0, '', '');
INSERT INTO `gl_nxform_fields` VALUES (3, 1, 3, 'text', 30, 'txt-frm1_3', 'Address1', '1', '', NULL, NULL, 0, 1, 1, 0, 1, 0, 0, 0, 0, 'size="60" maxlength="40"', '', 'your address here', 0, '', 'realname="Full address is required"');
INSERT INTO `gl_nxform_fields` VALUES (5, 1, 4, 'textarea1', 40, 'ta1-frm1_5', 'Comments', '1', '', NULL, NULL, 0, 1, 0, 0, 1, 0, 0, 0, 0, 'cols="80" rows="3"', '', 'This will clear when you click on field. Testing adding JS to the field', 0, '', 'onFocus=&quot;this.value=''''&quot;;');
INSERT INTO `gl_nxform_fields` VALUES (7, 1, 7, 'text', 50, 'txt-frm1_7', 'Age', '1', '', NULL, NULL, 0, 1, 0, 0, 0, 0, 0, 0, 0, 'size=&quot;3&quot;', '', '', 0, '', '');
INSERT INTO `gl_nxform_fields` VALUES (6, 1, 9, 'checkbox', 60, 'chk-frm1_6', 'Sign up for newsletter', '1', '', NULL, NULL, 0, 1, 0, 0, 0, 0, 0, 0, 0, '', '', 'yes', 0, '', '');
INSERT INTO `gl_nxform_fields` VALUES (8, 1, 10, 'submit', 70, 'sub-frm1_8', '', '1', '', NULL, NULL, 0, 1, 0, 0, 0, 0, 0, 0, 0, '', '', 'Submit', 0, '', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxform_metadata`
-- 

CREATE TABLE `gl_nxform_metadata` (
  `id` mediumint(8) NOT NULL auto_increment,
  `pid` mediumint(8) NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `gl_nxform_metadata`
-- 

INSERT INTO `gl_nxform_metadata` VALUES (1, 0, 'Industry One');
INSERT INTO `gl_nxform_metadata` VALUES (2, 0, 'Industry Two');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxform_resdata`
-- 

CREATE TABLE `gl_nxform_resdata` (
  `id` int(11) NOT NULL auto_increment,
  `result_id` mediumint(8) NOT NULL default '0',
  `field_id` mediumint(8) NOT NULL default '0',
  `field_data` varchar(255) NOT NULL default '',
  `is_dynamicfield_result` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `field_id` (`field_id`),
  KEY `result_id` (`result_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nxform_resdata`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxform_resdata_tmp`
-- 

CREATE TABLE `gl_nxform_resdata_tmp` (
  `id` int(11) NOT NULL auto_increment,
  `result_id` mediumint(8) NOT NULL default '0',
  `field_id` mediumint(8) NOT NULL default '0',
  `field_data` varchar(255) NOT NULL default '',
  `is_dynamicfield_result` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `field_id` (`field_id`),
  KEY `result_id` (`result_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nxform_resdata_tmp`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxform_restext`
-- 

CREATE TABLE `gl_nxform_restext` (
  `result_id` mediumint(11) NOT NULL default '0',
  `field_id` mediumint(8) NOT NULL default '0',
  `field_data` longtext NOT NULL,
  `is_dynamicfield_result` tinyint(1) NOT NULL default '0',
  KEY `field_id` (`field_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_nxform_restext`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxform_restext_tmp`
-- 

CREATE TABLE `gl_nxform_restext_tmp` (
  `result_id` mediumint(11) NOT NULL default '0',
  `field_id` mediumint(8) NOT NULL default '0',
  `field_data` longtext NOT NULL,
  `is_dynamicfield_result` tinyint(1) NOT NULL default '0',
  KEY `field_id` (`field_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_nxform_restext_tmp`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxform_results`
-- 

CREATE TABLE `gl_nxform_results` (
  `id` int(11) NOT NULL auto_increment,
  `form_id` mediumint(8) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `related_results` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nxform_results`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxform_results_tmp`
-- 

CREATE TABLE `gl_nxform_results_tmp` (
  `id` int(11) NOT NULL auto_increment,
  `form_id` mediumint(8) NOT NULL default '0',
  `uid` mediumint(8) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `related_results` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_nxform_results_tmp`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxlist`
-- 

CREATE TABLE `gl_nxlist` (
  `id` mediumint(8) NOT NULL auto_increment,
  `plugin` varchar(32) NOT NULL default '',
  `category` varchar(32) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `listfields` varchar(255) NOT NULL default '1',
  `view_perms` mediumint(8) NOT NULL default '2',
  `edit_perms` mediumint(8) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `gl_nxlist`
-- 

INSERT INTO `gl_nxlist` VALUES (1, 'all', 'Testing', 'Example List', 'This is an example list definition that has 2 fields. The one field is using a function to provide the dropdown list options. This function could be obtaining the list of options from a list maintained by this plugin - so it can build on itself.', 'User Name', 2, 2, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxlist_fields`
-- 

CREATE TABLE `gl_nxlist_fields` (
  `id` mediumint(8) NOT NULL auto_increment,
  `lid` mediumint(8) NOT NULL default '0',
  `fieldname` varchar(64) NOT NULL default '',
  `value_by_function` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `lid` (`lid`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `gl_nxlist_fields`
-- 

INSERT INTO `gl_nxlist_fields` VALUES (1, 1, 'Username', 'nexlistGetUsers');
INSERT INTO `gl_nxlist_fields` VALUES (2, 1, 'Location', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_nxlist_items`
-- 

CREATE TABLE `gl_nxlist_items` (
  `id` mediumint(8) NOT NULL auto_increment,
  `lid` mediumint(8) NOT NULL default '0',
  `value` longtext NOT NULL,
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `lid` (`lid`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `gl_nxlist_items`
-- 

INSERT INTO `gl_nxlist_items` VALUES (1, 1, '1', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_personal_events`
-- 

CREATE TABLE `gl_personal_events` (
  `eid` varchar(20) NOT NULL default '',
  `title` varchar(128) default NULL,
  `event_type` varchar(40) NOT NULL default '',
  `datestart` date default NULL,
  `dateend` date default NULL,
  `address1` varchar(40) default NULL,
  `address2` varchar(40) default NULL,
  `city` varchar(60) default NULL,
  `state` char(2) default NULL,
  `zipcode` varchar(5) default NULL,
  `allday` tinyint(1) NOT NULL default '0',
  `url` varchar(255) default NULL,
  `description` text,
  `postmode` varchar(10) NOT NULL default 'plaintext',
  `owner_id` mediumint(8) unsigned NOT NULL default '1',
  `group_id` mediumint(8) unsigned NOT NULL default '1',
  `perm_owner` tinyint(1) unsigned NOT NULL default '3',
  `perm_group` tinyint(1) unsigned NOT NULL default '3',
  `perm_members` tinyint(1) unsigned NOT NULL default '2',
  `perm_anon` tinyint(1) unsigned NOT NULL default '2',
  `uid` mediumint(8) NOT NULL default '0',
  `location` varchar(128) default NULL,
  `timestart` time default NULL,
  `timeend` time default NULL,
  PRIMARY KEY  (`eid`,`uid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_personal_events`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_pingservice`
-- 

CREATE TABLE `gl_pingservice` (
  `pid` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(128) default NULL,
  `ping_url` varchar(255) default NULL,
  `site_url` varchar(255) default NULL,
  `method` varchar(80) default NULL,
  `is_enabled` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`pid`),
  KEY `pingservice_is_enabled` (`is_enabled`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `gl_pingservice`
-- 

INSERT INTO `gl_pingservice` VALUES (1, 'Ping-O-Matic', 'http://rpc.pingomatic.com/', 'http://pingomatic.com/', 'weblogUpdates.ping', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_plugins`
-- 

CREATE TABLE `gl_plugins` (
  `pi_name` varchar(30) NOT NULL default '',
  `pi_version` varchar(20) NOT NULL default '',
  `pi_gl_version` varchar(20) NOT NULL default '',
  `pi_enabled` tinyint(3) unsigned NOT NULL default '1',
  `pi_homepage` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`pi_name`),
  KEY `plugins_enabled` (`pi_enabled`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_plugins`
-- 

INSERT INTO `gl_plugins` VALUES ('staticpages', '1.4.3', '1.4.1', 1, 'http://www.geeklog.net/');
INSERT INTO `gl_plugins` VALUES ('spamx', '1.1.0', '1.4.1', 1, 'http://www.pigstye.net/gplugs/staticpages/index.php/spamx');
INSERT INTO `gl_plugins` VALUES ('links', '1.0.1', '1.4.1', 1, 'http://www.geeklog.net/');
INSERT INTO `gl_plugins` VALUES ('polls', '1.1.0', '1.4.1', 1, 'http://www.geeklog.net/');
INSERT INTO `gl_plugins` VALUES ('nexlist', '1.3', '1.4.1', 1, 'http://www.nextide.ca/');
INSERT INTO `gl_plugins` VALUES ('nexcontent', '1.0.7', '1.4', 1, 'http://www.portalparts.com');
INSERT INTO `gl_plugins` VALUES ('forum', '2.5RC1', '1.3.11', 1, 'http://www.portalparts.com/');
INSERT INTO `gl_plugins` VALUES ('nexmenu', '1.0.6', '1.3.11', 1, 'http://www.portalparts.com');
INSERT INTO `gl_plugins` VALUES ('nexfile', '1.0.8', '1.4.1', 1, 'http://www.nextide.ca/');
INSERT INTO `gl_plugins` VALUES ('calendar', '1.0.0', '1.4.1', 1, 'http://www.geeklog.net/');
INSERT INTO `gl_plugins` VALUES ('nexflow', '2.0.0', '1.4.1', 1, 'http://www.nextide.ca');
INSERT INTO `gl_plugins` VALUES ('nexForm', '1.2.0', '1.4.1', 1, 'http://www.geeklog.net/');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_pollanswers`
-- 

CREATE TABLE `gl_pollanswers` (
  `qid` varchar(20) NOT NULL default '',
  `aid` tinyint(3) unsigned NOT NULL default '0',
  `answer` varchar(255) default NULL,
  `votes` mediumint(8) unsigned default NULL,
  `remark` varchar(255) default NULL,
  PRIMARY KEY  (`qid`,`aid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_pollanswers`
-- 

INSERT INTO `gl_pollanswers` VALUES ('geeklogfeaturepoll', 1, 'Trackbacks', 0, NULL);
INSERT INTO `gl_pollanswers` VALUES ('geeklogfeaturepoll', 2, 'Links and Polls plugins', 0, NULL);
INSERT INTO `gl_pollanswers` VALUES ('geeklogfeaturepoll', 3, 'Revamped admin areas', 0, NULL);
INSERT INTO `gl_pollanswers` VALUES ('geeklogfeaturepoll', 4, 'FCKeditor included', 0, NULL);
INSERT INTO `gl_pollanswers` VALUES ('geeklogfeaturepoll', 5, 'Remote user authentication', 0, NULL);
INSERT INTO `gl_pollanswers` VALUES ('geeklogfeaturepoll', 6, 'Other', 0, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_pollquestions`
-- 

CREATE TABLE `gl_pollquestions` (
  `qid` varchar(20) NOT NULL default '',
  `question` varchar(255) default NULL,
  `voters` mediumint(8) unsigned default NULL,
  `date` datetime default NULL,
  `display` tinyint(4) NOT NULL default '0',
  `commentcode` tinyint(4) NOT NULL default '0',
  `statuscode` tinyint(4) NOT NULL default '0',
  `owner_id` mediumint(8) unsigned NOT NULL default '1',
  `group_id` mediumint(8) unsigned NOT NULL default '1',
  `perm_owner` tinyint(1) unsigned NOT NULL default '3',
  `perm_group` tinyint(1) unsigned NOT NULL default '3',
  `perm_members` tinyint(1) unsigned NOT NULL default '2',
  `perm_anon` tinyint(1) unsigned NOT NULL default '2',
  PRIMARY KEY  (`qid`),
  KEY `pollquestions_qid` (`qid`),
  KEY `pollquestions_display` (`display`),
  KEY `pollquestions_commentcode` (`commentcode`),
  KEY `pollquestions_statuscode` (`statuscode`),
  KEY `pollquestions_date` (`date`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_pollquestions`
-- 

INSERT INTO `gl_pollquestions` VALUES ('geeklogfeaturepoll', 'What is the best new feature of Geeklog?', 0, '2006-08-14 19:17:10', 1, 0, 0, 2, 8, 3, 3, 2, 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_pollvoters`
-- 

CREATE TABLE `gl_pollvoters` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `qid` varchar(20) NOT NULL default '',
  `ipaddress` varchar(15) NOT NULL default '',
  `date` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_pollvoters`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_postmodes`
-- 

CREATE TABLE `gl_postmodes` (
  `code` char(10) NOT NULL default '',
  `name` char(32) default NULL,
  PRIMARY KEY  (`code`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_postmodes`
-- 

INSERT INTO `gl_postmodes` VALUES ('plaintext', 'Plain Old Text');
INSERT INTO `gl_postmodes` VALUES ('html', 'HTML Formatted');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_sessions`
-- 

CREATE TABLE `gl_sessions` (
  `sess_id` int(10) unsigned NOT NULL default '0',
  `start_time` int(10) unsigned NOT NULL default '0',
  `remote_ip` varchar(15) NOT NULL default '',
  `uid` mediumint(8) NOT NULL default '1',
  `md5_sess_id` varchar(128) default NULL,
  PRIMARY KEY  (`sess_id`),
  KEY `sess_id` (`sess_id`),
  KEY `start_time` (`start_time`),
  KEY `remote_ip` (`remote_ip`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_sessions`
-- 

INSERT INTO `gl_sessions` VALUES (1550107180, 1167419741, '172.16.0.10', 2, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_sortcodes`
-- 

CREATE TABLE `gl_sortcodes` (
  `code` char(4) NOT NULL default '0',
  `name` char(32) default NULL,
  PRIMARY KEY  (`code`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_sortcodes`
-- 

INSERT INTO `gl_sortcodes` VALUES ('ASC', 'Oldest First');
INSERT INTO `gl_sortcodes` VALUES ('DESC', 'Newest First');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_spamx`
-- 

CREATE TABLE `gl_spamx` (
  `name` varchar(20) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  KEY `spamx_name` (`name`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_spamx`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_speedlimit`
-- 

CREATE TABLE `gl_speedlimit` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ipaddress` varchar(15) NOT NULL default '',
  `date` int(10) unsigned default NULL,
  `type` varchar(30) NOT NULL default 'submit',
  PRIMARY KEY  (`id`),
  KEY `type_ipaddress` (`type`,`ipaddress`),
  KEY `date` (`date`)
) TYPE=MyISAM AUTO_INCREMENT=9 ;

-- 
-- Dumping data for table `gl_speedlimit`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_staticpage`
-- 

CREATE TABLE `gl_staticpage` (
  `sp_id` varchar(40) NOT NULL default '',
  `sp_uid` mediumint(8) NOT NULL default '1',
  `sp_title` varchar(128) NOT NULL default '',
  `sp_content` text NOT NULL,
  `sp_hits` mediumint(8) unsigned NOT NULL default '0',
  `sp_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `sp_format` varchar(20) NOT NULL default '',
  `sp_onmenu` tinyint(1) unsigned NOT NULL default '0',
  `sp_label` varchar(64) default NULL,
  `owner_id` mediumint(8) unsigned NOT NULL default '1',
  `group_id` mediumint(8) unsigned NOT NULL default '1',
  `perm_owner` tinyint(1) unsigned NOT NULL default '3',
  `perm_group` tinyint(1) unsigned NOT NULL default '2',
  `perm_members` tinyint(1) unsigned NOT NULL default '2',
  `perm_anon` tinyint(1) unsigned NOT NULL default '2',
  `sp_centerblock` tinyint(1) unsigned NOT NULL default '0',
  `sp_tid` varchar(20) NOT NULL default 'none',
  `sp_where` tinyint(1) unsigned NOT NULL default '1',
  `sp_php` tinyint(1) unsigned NOT NULL default '0',
  `sp_nf` tinyint(1) unsigned default '0',
  `sp_inblock` tinyint(1) unsigned default '1',
  `postmode` varchar(16) NOT NULL default 'html',
  `sp_help` varchar(255) NOT NULL default '''''',
  PRIMARY KEY  (`sp_id`),
  KEY `staticpage_sp_uid` (`sp_uid`),
  KEY `staticpage_sp_date` (`sp_date`),
  KEY `staticpage_sp_onmenu` (`sp_onmenu`),
  KEY `staticpage_sp_centerblock` (`sp_centerblock`),
  KEY `staticpage_sp_tid` (`sp_tid`),
  KEY `staticpage_sp_where` (`sp_where`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_staticpage`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_statuscodes`
-- 

CREATE TABLE `gl_statuscodes` (
  `code` int(1) NOT NULL default '0',
  `name` char(32) default NULL,
  PRIMARY KEY  (`code`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_statuscodes`
-- 

INSERT INTO `gl_statuscodes` VALUES (1, 'Refreshing');
INSERT INTO `gl_statuscodes` VALUES (0, 'Normal');
INSERT INTO `gl_statuscodes` VALUES (10, 'Archive');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_stories`
-- 

CREATE TABLE `gl_stories` (
  `sid` varchar(40) NOT NULL default '',
  `uid` mediumint(8) NOT NULL default '1',
  `draft_flag` tinyint(3) unsigned default '0',
  `tid` varchar(20) NOT NULL default 'General',
  `date` datetime default NULL,
  `title` varchar(128) default NULL,
  `introtext` text,
  `bodytext` text,
  `hits` mediumint(8) unsigned NOT NULL default '0',
  `numemails` mediumint(8) unsigned NOT NULL default '0',
  `comments` mediumint(8) unsigned NOT NULL default '0',
  `trackbacks` mediumint(8) unsigned NOT NULL default '0',
  `related` text,
  `featured` tinyint(3) unsigned NOT NULL default '0',
  `show_topic_icon` tinyint(1) unsigned NOT NULL default '1',
  `commentcode` tinyint(4) NOT NULL default '0',
  `trackbackcode` tinyint(4) NOT NULL default '0',
  `statuscode` tinyint(4) NOT NULL default '0',
  `expire` datetime NOT NULL default '0000-00-00 00:00:00',
  `postmode` varchar(10) NOT NULL default 'html',
  `advanced_editor_mode` tinyint(1) unsigned default '0',
  `frontpage` tinyint(3) unsigned default '1',
  `in_transit` tinyint(1) unsigned default '0',
  `owner_id` mediumint(8) NOT NULL default '1',
  `group_id` mediumint(8) NOT NULL default '2',
  `perm_owner` tinyint(1) unsigned NOT NULL default '3',
  `perm_group` tinyint(1) unsigned NOT NULL default '3',
  `perm_members` tinyint(1) unsigned NOT NULL default '2',
  `perm_anon` tinyint(1) unsigned NOT NULL default '2',
  PRIMARY KEY  (`sid`),
  KEY `stories_sid` (`sid`),
  KEY `stories_tid` (`tid`),
  KEY `stories_uid` (`uid`),
  KEY `stories_featured` (`featured`),
  KEY `stories_hits` (`hits`),
  KEY `stories_statuscode` (`statuscode`),
  KEY `stories_expire` (`expire`),
  KEY `stories_date` (`date`),
  KEY `stories_frontpage` (`frontpage`),
  KEY `stories_in_transit` (`in_transit`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_stories`
-- 

INSERT INTO `gl_stories` VALUES ('welcome', 2, 0, 'GeekLog', '2006-08-14 19:17:10', 'Welcome to Geeklog!', '<p>Welcome and let me be the first to congratulate you on installing GeekLog. Please take the time to read everything in the <a href="docs/index.html">docs directory</a>. Geeklog now has enhanced, user-based security.  You should thoroughly understand how these work before you run a production Geeklog Site.\r\r<p>To log into your new GeekLog site, please use this account:\r<p>Username: <b>Admin</b><br>\rPassword: <b>password</b>', '<p><b>And don''t forget to change your password after logging in!</b>', 100, 1, 0, 0, '', 1, 1, 0, 0, 0, '0000-00-00 00:00:00', 'html', 0, 1, 0, 2, 3, 3, 2, 2, 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_storysubmission`
-- 

CREATE TABLE `gl_storysubmission` (
  `sid` varchar(20) NOT NULL default '',
  `uid` mediumint(8) NOT NULL default '1',
  `tid` varchar(20) NOT NULL default 'General',
  `title` varchar(128) default NULL,
  `introtext` text,
  `date` datetime default NULL,
  `postmode` varchar(10) NOT NULL default 'html',
  PRIMARY KEY  (`sid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_storysubmission`
-- 

INSERT INTO `gl_storysubmission` VALUES ('security-reminder', 2, 'GeekLog', 'Are you secure?', '<p>This is a reminder to secure your site once you have Geeklog up and running. What you should do:</p>\r\r<ol>\r<li>Change the default password for the Admin account.</li>\r<li>Remove the install directory (you won''t need it any more).</li>\r</ol>', '2006-08-14 19:17:10', 'html');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_syndication`
-- 

CREATE TABLE `gl_syndication` (
  `fid` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(30) NOT NULL default 'geeklog',
  `topic` varchar(48) NOT NULL default '::all',
  `header_tid` varchar(48) NOT NULL default 'none',
  `format` varchar(20) NOT NULL default 'RSS-2.0',
  `limits` varchar(5) NOT NULL default '10',
  `content_length` smallint(5) unsigned NOT NULL default '0',
  `title` varchar(40) NOT NULL default '',
  `description` text,
  `feedlogo` varchar(255) default NULL,
  `filename` varchar(40) NOT NULL default 'geeklog.rss',
  `charset` varchar(20) NOT NULL default 'UTF-8',
  `language` varchar(20) NOT NULL default 'en-gb',
  `is_enabled` tinyint(1) unsigned NOT NULL default '1',
  `updated` datetime NOT NULL default '0000-00-00 00:00:00',
  `update_info` text,
  PRIMARY KEY  (`fid`),
  KEY `syndication_type` (`type`),
  KEY `syndication_topic` (`topic`),
  KEY `syndication_is_enabled` (`is_enabled`),
  KEY `syndication_updated` (`updated`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `gl_syndication`
-- 

INSERT INTO `gl_syndication` VALUES (1, 'geeklog', '::all', 'all', 'RSS-2.0', '10', 1, 'Geeklog Site', 'Another Nifty Geeklog Site', NULL, 'geeklog.rss', 'UTF-8', 'en-gb', 1, '2006-08-14 19:17:15', 'welcome');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_topics`
-- 

CREATE TABLE `gl_topics` (
  `tid` varchar(20) NOT NULL default '',
  `topic` varchar(48) default NULL,
  `imageurl` varchar(255) default NULL,
  `sortnum` tinyint(3) default NULL,
  `limitnews` tinyint(3) default NULL,
  `is_default` tinyint(1) unsigned NOT NULL default '0',
  `archive_flag` tinyint(1) unsigned NOT NULL default '0',
  `owner_id` mediumint(8) unsigned NOT NULL default '1',
  `group_id` mediumint(8) unsigned NOT NULL default '1',
  `perm_owner` tinyint(1) unsigned NOT NULL default '3',
  `perm_group` tinyint(1) unsigned NOT NULL default '3',
  `perm_members` tinyint(1) unsigned NOT NULL default '2',
  `perm_anon` tinyint(1) unsigned NOT NULL default '2',
  PRIMARY KEY  (`tid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_topics`
-- 

INSERT INTO `gl_topics` VALUES ('General', 'General News', '/images/topics/topic_news.gif', 1, 10, 0, 0, 2, 6, 3, 2, 2, 2);
INSERT INTO `gl_topics` VALUES ('GeekLog', 'GeekLog', '/images/topics/topic_gl.gif', 2, 10, 0, 0, 2, 6, 3, 2, 2, 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_trackback`
-- 

CREATE TABLE `gl_trackback` (
  `cid` int(10) unsigned NOT NULL auto_increment,
  `sid` varchar(40) NOT NULL default '',
  `url` varchar(255) default NULL,
  `title` varchar(128) default NULL,
  `blog` varchar(80) default NULL,
  `excerpt` text,
  `date` datetime default NULL,
  `type` varchar(30) NOT NULL default 'article',
  `ipaddress` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`cid`),
  KEY `trackback_sid` (`sid`),
  KEY `trackback_url` (`url`),
  KEY `trackback_type` (`type`),
  KEY `trackback_date` (`date`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `gl_trackback`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gl_trackbackcodes`
-- 

CREATE TABLE `gl_trackbackcodes` (
  `code` tinyint(4) NOT NULL default '0',
  `name` varchar(32) default NULL,
  PRIMARY KEY  (`code`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_trackbackcodes`
-- 

INSERT INTO `gl_trackbackcodes` VALUES (0, 'Trackback Enabled');
INSERT INTO `gl_trackbackcodes` VALUES (-1, 'Trackback Disabled');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_tzcodes`
-- 

CREATE TABLE `gl_tzcodes` (
  `tz` char(3) NOT NULL default '',
  `offset` int(1) default NULL,
  `description` varchar(64) default NULL,
  PRIMARY KEY  (`tz`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_tzcodes`
-- 

INSERT INTO `gl_tzcodes` VALUES ('ndt', -9000, 'Newfoundland Daylight');
INSERT INTO `gl_tzcodes` VALUES ('adt', -10800, 'Atlantic Daylight');
INSERT INTO `gl_tzcodes` VALUES ('edt', -14400, 'Eastern Daylight');
INSERT INTO `gl_tzcodes` VALUES ('cdt', -18000, 'Central Daylight');
INSERT INTO `gl_tzcodes` VALUES ('mdt', -21600, 'Mountain Daylight');
INSERT INTO `gl_tzcodes` VALUES ('pdt', -25200, 'Pacific Daylight');
INSERT INTO `gl_tzcodes` VALUES ('ydt', -28800, 'Yukon Daylight');
INSERT INTO `gl_tzcodes` VALUES ('hdt', -32400, 'Hawaii Daylight');
INSERT INTO `gl_tzcodes` VALUES ('bst', 3600, 'British Summer');
INSERT INTO `gl_tzcodes` VALUES ('mes', 7200, 'Middle European Summer');
INSERT INTO `gl_tzcodes` VALUES ('sst', 7200, 'Swedish Summer');
INSERT INTO `gl_tzcodes` VALUES ('fst', 7200, 'French Summer');
INSERT INTO `gl_tzcodes` VALUES ('wad', 28800, 'West Australian Daylight');
INSERT INTO `gl_tzcodes` VALUES ('cad', 37800, 'Central Australian Daylight');
INSERT INTO `gl_tzcodes` VALUES ('ead', 39600, 'Eastern Australian Daylight');
INSERT INTO `gl_tzcodes` VALUES ('nzd', 46800, 'New Zealand Daylight');
INSERT INTO `gl_tzcodes` VALUES ('gmt', 0, 'Greenwich Mean');
INSERT INTO `gl_tzcodes` VALUES ('utc', 0, 'Universal (Coordinated)');
INSERT INTO `gl_tzcodes` VALUES ('wet', 0, 'Western European');
INSERT INTO `gl_tzcodes` VALUES ('wat', -3600, 'West Africa');
INSERT INTO `gl_tzcodes` VALUES ('at', -7200, 'Azores');
INSERT INTO `gl_tzcodes` VALUES ('gst', -10800, 'Greenland Standard');
INSERT INTO `gl_tzcodes` VALUES ('nft', -12600, 'Newfoundland');
INSERT INTO `gl_tzcodes` VALUES ('nst', -12600, 'Newfoundland Standard');
INSERT INTO `gl_tzcodes` VALUES ('ast', -14400, 'Atlantic Standard');
INSERT INTO `gl_tzcodes` VALUES ('est', -18000, 'Eastern Standard');
INSERT INTO `gl_tzcodes` VALUES ('cst', -21600, 'Central Standard');
INSERT INTO `gl_tzcodes` VALUES ('mst', -25200, 'Mountain Standard');
INSERT INTO `gl_tzcodes` VALUES ('pst', -28800, 'Pacific Standard');
INSERT INTO `gl_tzcodes` VALUES ('yst', -32400, 'Yukon Standard');
INSERT INTO `gl_tzcodes` VALUES ('hst', -36000, 'Hawaii Standard');
INSERT INTO `gl_tzcodes` VALUES ('cat', -36000, 'Central Alaska');
INSERT INTO `gl_tzcodes` VALUES ('ahs', -36000, 'Alaska-Hawaii Standard');
INSERT INTO `gl_tzcodes` VALUES ('nt', -39600, 'Nome');
INSERT INTO `gl_tzcodes` VALUES ('idl', -43200, 'International Date Line West');
INSERT INTO `gl_tzcodes` VALUES ('cet', 3600, 'Central European');
INSERT INTO `gl_tzcodes` VALUES ('met', 3600, 'Middle European');
INSERT INTO `gl_tzcodes` VALUES ('mew', 3600, 'Middle European Winter');
INSERT INTO `gl_tzcodes` VALUES ('swt', 3600, 'Swedish Winter');
INSERT INTO `gl_tzcodes` VALUES ('fwt', 3600, 'French Winter');
INSERT INTO `gl_tzcodes` VALUES ('eet', 7200, 'Eastern Europe, USSR Zone 1');
INSERT INTO `gl_tzcodes` VALUES ('bt', 10800, 'Baghdad, USSR Zone 2');
INSERT INTO `gl_tzcodes` VALUES ('it', 12600, 'Iran');
INSERT INTO `gl_tzcodes` VALUES ('zp4', 14400, 'USSR Zone 3');
INSERT INTO `gl_tzcodes` VALUES ('zp5', 18000, 'USSR Zone 4');
INSERT INTO `gl_tzcodes` VALUES ('ist', 19800, 'Indian Standard');
INSERT INTO `gl_tzcodes` VALUES ('zp6', 21600, 'USSR Zone 5');
INSERT INTO `gl_tzcodes` VALUES ('was', 25200, 'West Australian Standard');
INSERT INTO `gl_tzcodes` VALUES ('jt', 27000, 'Java (3pm in Cronusland!)');
INSERT INTO `gl_tzcodes` VALUES ('cct', 28800, 'China Coast, USSR Zone 7');
INSERT INTO `gl_tzcodes` VALUES ('jst', 32400, 'Japan Standard, USSR Zone 8');
INSERT INTO `gl_tzcodes` VALUES ('cas', 34200, 'Central Australian Standard');
INSERT INTO `gl_tzcodes` VALUES ('eas', 36000, 'Eastern Australian Standard');
INSERT INTO `gl_tzcodes` VALUES ('nzt', 43200, 'New Zealand');
INSERT INTO `gl_tzcodes` VALUES ('nzs', 43200, 'New Zealand Standard');
INSERT INTO `gl_tzcodes` VALUES ('id2', 43200, 'International Date Line East');
INSERT INTO `gl_tzcodes` VALUES ('idt', 10800, 'Israel Daylight');
INSERT INTO `gl_tzcodes` VALUES ('iss', 7200, 'Israel Standard');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_usercomment`
-- 

CREATE TABLE `gl_usercomment` (
  `uid` mediumint(8) NOT NULL default '1',
  `commentmode` varchar(10) NOT NULL default 'threaded',
  `commentorder` varchar(4) NOT NULL default 'ASC',
  `commentlimit` mediumint(8) unsigned NOT NULL default '100',
  PRIMARY KEY  (`uid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_usercomment`
-- 

INSERT INTO `gl_usercomment` VALUES (1, 'nested', 'ASC', 100);
INSERT INTO `gl_usercomment` VALUES (2, 'threaded', 'ASC', 100);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_userindex`
-- 

CREATE TABLE `gl_userindex` (
  `uid` mediumint(8) NOT NULL default '1',
  `tids` varchar(255) NOT NULL default '',
  `etids` text,
  `aids` varchar(255) NOT NULL default '',
  `boxes` varchar(255) NOT NULL default '',
  `noboxes` tinyint(4) NOT NULL default '0',
  `maxstories` tinyint(4) default NULL,
  PRIMARY KEY  (`uid`),
  KEY `userindex_uid` (`uid`),
  KEY `userindex_noboxes` (`noboxes`),
  KEY `userindex_maxstories` (`maxstories`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_userindex`
-- 

INSERT INTO `gl_userindex` VALUES (1, '', '-', '', '', 0, NULL);
INSERT INTO `gl_userindex` VALUES (2, '', '', '', '', 0, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_userinfo`
-- 

CREATE TABLE `gl_userinfo` (
  `uid` mediumint(8) NOT NULL default '1',
  `about` text,
  `location` varchar(96) NOT NULL default '',
  `pgpkey` text,
  `userspace` varchar(255) NOT NULL default '',
  `tokens` tinyint(3) unsigned NOT NULL default '0',
  `totalcomments` mediumint(9) NOT NULL default '0',
  `lastgranted` int(10) unsigned NOT NULL default '0',
  `lastlogin` varchar(10) NOT NULL default '0',
  PRIMARY KEY  (`uid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_userinfo`
-- 

INSERT INTO `gl_userinfo` VALUES (1, NULL, '', NULL, '', 0, 0, 0, '0');
INSERT INTO `gl_userinfo` VALUES (2, NULL, '', NULL, '', 0, 0, 0, '1167416488');

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_userprefs`
-- 

CREATE TABLE `gl_userprefs` (
  `uid` mediumint(8) NOT NULL default '1',
  `noicons` tinyint(3) unsigned NOT NULL default '0',
  `willing` tinyint(3) unsigned NOT NULL default '1',
  `dfid` tinyint(3) unsigned NOT NULL default '0',
  `tzid` char(3) NOT NULL default 'edt',
  `emailstories` tinyint(4) NOT NULL default '1',
  `emailfromadmin` tinyint(1) NOT NULL default '1',
  `emailfromuser` tinyint(1) NOT NULL default '1',
  `showonline` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`uid`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_userprefs`
-- 

INSERT INTO `gl_userprefs` VALUES (1, 0, 0, 0, '', 0, 1, 1, 1);
INSERT INTO `gl_userprefs` VALUES (2, 0, 1, 0, 'edt', 1, 1, 1, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_users`
-- 

CREATE TABLE `gl_users` (
  `uid` mediumint(8) NOT NULL auto_increment,
  `username` varchar(16) NOT NULL default '',
  `remoteusername` varchar(60) default NULL,
  `remoteservice` varchar(60) default NULL,
  `fullname` varchar(80) default NULL,
  `passwd` varchar(32) NOT NULL default '',
  `email` varchar(96) default NULL,
  `homepage` varchar(96) default NULL,
  `sig` varchar(160) NOT NULL default '',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `photo` varchar(128) default NULL,
  `cookietimeout` int(8) unsigned default '28800',
  `theme` varchar(64) default NULL,
  `language` varchar(64) default NULL,
  `pwrequestid` varchar(16) default NULL,
  `status` smallint(5) unsigned NOT NULL default '1',
  PRIMARY KEY  (`uid`),
  KEY `LOGIN` (`uid`,`passwd`,`username`),
  KEY `users_username` (`username`),
  KEY `users_fullname` (`fullname`),
  KEY `users_email` (`email`),
  KEY `users_passwd` (`passwd`),
  KEY `users_pwrequestid` (`pwrequestid`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `gl_users`
-- 

INSERT INTO `gl_users` VALUES (1, 'Anonymous', NULL, NULL, 'Anonymous', '', NULL, NULL, '', '2006-08-14 19:17:10', NULL, 0, NULL, NULL, NULL, 3);
INSERT INTO `gl_users` VALUES (2, 'Admin', NULL, NULL, 'Geeklog SuperUser', '5f4dcc3b5aa765d61d8327deb882cf99', 'root@localhost', 'http://localhost/nextide3', '', '2006-08-14 19:17:10', NULL, 28800, NULL, NULL, 'NULL', 3);

-- --------------------------------------------------------

-- 
-- Table structure for table `gl_vars`
-- 

CREATE TABLE `gl_vars` (
  `name` varchar(20) NOT NULL default '',
  `value` varchar(128) default NULL,
  PRIMARY KEY  (`name`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gl_vars`
-- 

INSERT INTO `gl_vars` VALUES ('totalhits', '512');
INSERT INTO `gl_vars` VALUES ('lastemailedstories', '');
INSERT INTO `gl_vars` VALUES ('last_scheduled_run', '1167338857');
INSERT INTO `gl_vars` VALUES ('spamx.counter', '0');
INSERT INTO `gl_vars` VALUES ('forum_admin', '19');
INSERT INTO `gl_vars` VALUES ('nexmenu_admin', '20');
INSERT INTO `gl_vars` VALUES ('nexflow_admin', '25');
INSERT INTO `gl_vars` VALUES ('nexcontent_admin', '33');
        