<?php

$_SQL[] = "
CREATE TABLE {$_TABLES['access']} (
  acc_ft_id mediumint(8) NOT NULL default '0',
  acc_grp_id mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (acc_ft_id,acc_grp_id)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['article_images']} (
  ai_sid varchar(40) NOT NULL,
  ai_img_num tinyint(2) unsigned NOT NULL,
  ai_filename varchar(128) NOT NULL,
  PRIMARY KEY (ai_sid,ai_img_num)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['blocks']} (
  bid smallint(5) unsigned NOT NULL auto_increment,
  is_enabled tinyint(1) unsigned NOT NULL DEFAULT '1',
  name varchar(48) NOT NULL default '',
  type varchar(20) NOT NULL default 'normal',
  title varchar(48) default NULL,
  tid varchar(20) NOT NULL default 'All',
  blockorder smallint(5) unsigned NOT NULL default '1',
  content text,
  allow_autotags tinyint(1) unsigned NOT NULL DEFAULT '0',
  rdfurl varchar(255) default NULL,
  rdfupdated datetime NOT NULL default '0000-00-00 00:00:00',
  rdf_last_modified varchar(40) default NULL,
  rdf_etag varchar(40) default NULL,
  rdflimit smallint(5) unsigned NOT NULL default '0',
  onleft tinyint(1) unsigned NOT NULL default '1',
  phpblockfn varchar(128) default '',
  help varchar(255) default '',
  owner_id mediumint(8) unsigned NOT NULL default '1',
  group_id mediumint(8) unsigned NOT NULL default '1',
  perm_owner tinyint(1) unsigned NOT NULL default '3',
  perm_group tinyint(1) unsigned NOT NULL default '3',
  perm_members tinyint(1) unsigned NOT NULL default '2',
  perm_anon tinyint(1) unsigned NOT NULL default '2',
  INDEX blocks_bid(bid),
  INDEX blocks_is_enabled(is_enabled),
  INDEX blocks_tid(tid),
  INDEX blocks_type(type),
  INDEX blocks_name(name),
  INDEX blocks_onleft(onleft),
  PRIMARY KEY  (bid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['commentcodes']} (
  code tinyint(4) NOT NULL default '0',
  name varchar(32) default NULL,
  PRIMARY KEY  (code)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['commentedits']} (
  cid int(10) NOT NULL,
  uid mediumint(8) NOT NULL,
  time datetime NOT NULL,
  PRIMARY KEY (cid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['commentmodes']} (
  mode varchar(10) NOT NULL default '',
  name varchar(32) default NULL,
  PRIMARY KEY  (mode)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['commentnotifications']} (
  cid int(10) default NULL,
  uid mediumint(8) NOT NULL,
  deletehash varchar(32) NOT NULL,
  mid int(10) default NULL,
  PRIMARY KEY  (deletehash)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['comments']} (
  cid int(10) unsigned NOT NULL auto_increment,
  type varchar(30) NOT NULL DEFAULT 'article',
  sid varchar(40) NOT NULL default '',
  date datetime default NULL,
  title varchar(128) default NULL,
  comment text,
  score tinyint(4) NOT NULL default '0',
  reason tinyint(4) NOT NULL default '0',
  pid int(10) unsigned NOT NULL default '0',
  lft mediumint(10) unsigned NOT NULL default '0',
  rht mediumint(10) unsigned NOT NULL default '0',
  indent mediumint(10) unsigned NOT NULL default '0',
  name varchar(32) default NULL,
  uid mediumint(8) NOT NULL default '1',
  ipaddress varchar(15) NOT NULL default '',
  INDEX comments_sid(sid),
  INDEX comments_uid(uid),
  INDEX comments_lft(lft),
  INDEX comments_rht(rht),
  INDEX comments_date(date),
  PRIMARY KEY  (cid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['commentsubmissions']} (
  cid int(10) unsigned NOT NULL auto_increment,
  type varchar(30) NOT NULL default 'article',
  sid varchar(40) NOT NULL,
  date datetime default NULL,
  title varchar(128) default NULL,
  comment text,
  uid mediumint(8) NOT NULL default '1',
  name varchar(32) default NULL,
  pid int(10) NOT NULL default '0',
  ipaddress varchar(15) NOT NULL,
  PRIMARY KEY  (cid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['conf_values']} (
  name varchar(50) default NULL,
  value text,
  type varchar(50) default NULL,
  group_name varchar(50) default NULL,
  default_value text,
  subgroup int(11) default NULL,
  selectionArray int(11) default NULL,
  sort_order int(11) default NULL,
  fieldset int(11) default NULL
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['cookiecodes']} (
  cc_value int(8) unsigned NOT NULL default '0',
  cc_descr varchar(20) NOT NULL default '',
  PRIMARY KEY  (cc_value)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['dateformats']} (
  dfid tinyint(4) NOT NULL default '0',
  format varchar(32) default NULL,
  description varchar(64) default NULL,
  PRIMARY KEY  (dfid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['featurecodes']} (
  code tinyint(4) NOT NULL default '0',
  name varchar(32) default NULL,
  PRIMARY KEY  (code)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['features']} (
  ft_id mediumint(8) NOT NULL auto_increment,
  ft_name varchar(20) NOT NULL default '',
  ft_descr varchar(255) NOT NULL default '',
  ft_gl_core tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (ft_id),
  KEY ft_name (ft_name)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['frontpagecodes']} (
  code tinyint(4) NOT NULL default '0',
  name varchar(32) default NULL,
  PRIMARY KEY  (code)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['group_assignments']} (
  ug_main_grp_id mediumint(8) NOT NULL default '0',
  ug_uid mediumint(8) unsigned default NULL,
  ug_grp_id mediumint(8) unsigned default NULL,
  INDEX group_assignments_ug_main_grp_id(ug_main_grp_id),
  INDEX group_assignments_ug_uid(ug_uid),
  KEY ug_main_grp_id (ug_main_grp_id)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['groups']} (
  grp_id mediumint(8) NOT NULL auto_increment,
  grp_name varchar(50) NOT NULL default '',
  grp_descr varchar(255) NOT NULL default '',
  grp_gl_core tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (grp_id),
  UNIQUE grp_name (grp_name)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['maillist']} (
  code int(1) NOT NULL default '0',
  name char(32) default NULL,
  PRIMARY KEY  (code)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['pingservice']} (
  pid smallint(5) unsigned NOT NULL auto_increment,
  name varchar(128) default NULL,
  ping_url varchar(255) default NULL,
  site_url varchar(255) default NULL,
  method varchar(80) default NULL,
  is_enabled tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (pid),
  INDEX pingservice_is_enabled(is_enabled)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['plugins']} (
  pi_name varchar(30) NOT NULL default '',
  pi_version varchar(20) NOT NULL default '',
  pi_gl_version varchar(20) NOT NULL default '',
  pi_enabled tinyint(1) unsigned NOT NULL default '1',
  pi_homepage varchar(128) NOT NULL default '',
  INDEX plugins_enabled(pi_enabled),
  PRIMARY KEY  (pi_name)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['postmodes']} (
  code char(10) NOT NULL default '',
  name char(32) default NULL,
  PRIMARY KEY  (code)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['sessions']} (
  sess_id int(10) unsigned NOT NULL default '0',
  start_time int(10) unsigned NOT NULL default '0',
  remote_ip varchar(15) NOT NULL default '',
  uid mediumint(8) NOT NULL default '1',
  md5_sess_id varchar(128) default NULL,
  PRIMARY KEY  (sess_id),
  KEY sess_id (sess_id),
  KEY start_time (start_time),
  KEY remote_ip (remote_ip)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['sortcodes']} (
  code char(4) NOT NULL default '0',
  name char(32) default NULL,
  PRIMARY KEY  (code)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['speedlimit']} (
  id int(10) unsigned NOT NULL auto_increment,
  ipaddress varchar(15) NOT NULL default '',
  date int(10) unsigned default NULL,
  type varchar(30) NOT NULL default 'submit',
  PRIMARY KEY (id),
  KEY type_ipaddress (type,ipaddress),
  KEY date (date)
) TYPE = MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['statuscodes']} (
  code int(1) NOT NULL default '0',
  name char(32) default NULL,
  PRIMARY KEY  (code)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['stories']} (
  sid varchar(40) NOT NULL default '',
  uid mediumint(8) NOT NULL default '1',
  draft_flag tinyint(1) unsigned default '0',
  tid varchar(20) NOT NULL default 'General',
  date datetime default NULL,
  title varchar(128) default NULL,
  introtext text,
  bodytext text,
  hits mediumint(8) unsigned NOT NULL default '0',
  numemails mediumint(8) unsigned NOT NULL default '0',
  comments mediumint(8) unsigned NOT NULL default '0',
  comment_expire datetime NOT NULL default '0000-00-00 00:00:00',
  trackbacks mediumint(8) unsigned NOT NULL default '0',
  related text,
  featured tinyint(1) unsigned NOT NULL default '0',
  show_topic_icon tinyint(1) unsigned NOT NULL default '1',
  commentcode tinyint(4) NOT NULL default '0',
  trackbackcode tinyint(4) NOT NULL default '0',
  statuscode tinyint(4) NOT NULL default '0',
  expire DATETIME NOT NULL default '0000-00-00 00:00:00',
  postmode varchar(10) NOT NULL default 'html',
  advanced_editor_mode tinyint(1) unsigned default '0',
  frontpage tinyint(1) unsigned default '1',
  meta_description TEXT NULL,
  meta_keywords TEXT NULL,
  owner_id mediumint(8) NOT NULL default '1',
  group_id mediumint(8) NOT NULL default '2',
  perm_owner tinyint(1) unsigned NOT NULL default '3',
  perm_group tinyint(1) unsigned NOT NULL default '3',
  perm_members tinyint(1) unsigned NOT NULL default '2',
  perm_anon tinyint(1) unsigned NOT NULL default '2',
  INDEX stories_sid(sid),
  INDEX stories_tid(tid),
  INDEX stories_uid(uid),
  INDEX stories_featured(featured),
  INDEX stories_hits(hits),
  INDEX stories_statuscode(statuscode),
  INDEX stories_expire(expire),
  INDEX stories_date(date),
  INDEX stories_frontpage(frontpage),
  PRIMARY KEY  (sid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['storysubmission']} (
  sid varchar(20) NOT NULL default '',
  uid mediumint(8) NOT NULL default '1',
  tid varchar(20) NOT NULL default 'General',
  title varchar(128) default NULL,
  introtext text,
  bodytext text,
  date datetime default NULL,
  postmode varchar(10) NOT NULL default 'html',
  PRIMARY KEY  (sid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['syndication']} (
  fid int(10) unsigned NOT NULL auto_increment,
  type varchar(30) NOT NULL default 'article',
  topic varchar(48) NOT NULL default '::all',
  header_tid varchar(48) NOT NULL default 'none',
  format varchar(20) NOT NULL default 'RSS-2.0',
  limits varchar(5) NOT NULL default '10',
  content_length smallint(5) unsigned NOT NULL default '0',
  title varchar(40) NOT NULL default '',
  description text,
  feedlogo varchar(255),
  filename varchar(40) NOT NULL default 'geeklog.rss',
  charset varchar(20) NOT NULL default 'UTF-8',
  language varchar(20) NOT NULL default 'en-gb',
  is_enabled tinyint(1) unsigned NOT NULL default '1',
  updated datetime NOT NULL default '0000-00-00 00:00:00',
  update_info text,
  PRIMARY KEY (fid),
  INDEX syndication_type(type),
  INDEX syndication_topic(topic),
  INDEX syndication_is_enabled(is_enabled),
  INDEX syndication_updated(updated)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['tokens']} (
  token varchar(32) NOT NULL,
  created datetime NOT NULL,
  owner_id mediumint(8) unsigned NOT NULL,
  urlfor varchar(255) NOT NULL,
  ttl mediumint(8) unsigned NOT NULL default '1',
  PRIMARY KEY (token)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['topics']} (
  tid varchar(20) NOT NULL default '',
  topic varchar(48) default NULL,
  imageurl varchar(255) default NULL,
  meta_description TEXT NULL,
  meta_keywords TEXT NULL,
  sortnum smallint(3) default NULL,
  limitnews tinyint(3) default NULL,
  is_default tinyint(1) unsigned NOT NULL DEFAULT '0',
  archive_flag tinyint(1) unsigned NOT NULL DEFAULT '0',
  owner_id mediumint(8) unsigned NOT NULL default '1',
  group_id mediumint(8) unsigned NOT NULL default '1',
  perm_owner tinyint(1) unsigned NOT NULL default '3',
  perm_group tinyint(1) unsigned NOT NULL default '3',
  perm_members tinyint(1) unsigned NOT NULL default '2',
  perm_anon tinyint(1) unsigned NOT NULL default '2',
  PRIMARY KEY  (tid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['trackback']} (
  cid int(10) unsigned NOT NULL auto_increment,
  sid varchar(40) NOT NULL,
  url varchar(255) default NULL,
  title varchar(128) default NULL,
  blog varchar(80) default NULL,
  excerpt text,
  date datetime default NULL,
  type varchar(30) NOT NULL default 'article',
  ipaddress varchar(15) NOT NULL default '',
  PRIMARY KEY (cid),
  INDEX trackback_sid(sid),
  INDEX trackback_url(url),
  INDEX trackback_type(type),
  INDEX trackback_date(date)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['trackbackcodes']} (
  code tinyint(4) NOT NULL default '0',
  name varchar(32) default NULL,
  PRIMARY KEY  (code)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['usercomment']} (
  uid mediumint(8) NOT NULL default '1',
  commentmode varchar(10) NOT NULL default 'threaded',
  commentorder varchar(4) NOT NULL default 'ASC',
  commentlimit mediumint(8) unsigned NOT NULL default '100',
  PRIMARY KEY  (uid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['userindex']} (
  uid mediumint(8) NOT NULL default '1',
  tids varchar(255) NOT NULL default '',
  etids text,
  aids varchar(255) NOT NULL default '',
  boxes varchar(255) NOT NULL default '',
  noboxes tinyint(4) NOT NULL default '0',
  maxstories tinyint(4) default NULL,
  INDEX userindex_uid(uid),
  INDEX userindex_noboxes(noboxes),
  INDEX userindex_maxstories(maxstories),
  PRIMARY KEY  (uid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['userinfo']} (
  uid mediumint(8) NOT NULL default '1',
  about text,
  location varchar(96) NOT NULL default '',
  pgpkey text,
  userspace varchar(255) NOT NULL default '',
  tokens tinyint(3) unsigned NOT NULL default '0',
  totalcomments mediumint(9) NOT NULL default '0',
  lastgranted int(10) unsigned NOT NULL default '0',
  lastlogin VARCHAR(10) NOT NULL default '0',
  PRIMARY KEY  (uid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['userprefs']} (
  uid mediumint(8) NOT NULL default '1',
  noicons tinyint(1) unsigned NOT NULL default '0',
  willing tinyint(3) unsigned NOT NULL default '1',
  dfid tinyint(3) unsigned NOT NULL default '0',
  tzid varchar(125) NOT NULL default '',
  emailstories tinyint(4) NOT NULL default '1',
  emailfromadmin tinyint(1) NOT NULL default '1',
  emailfromuser tinyint(1) NOT NULL default '1',
  showonline tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (uid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['users']} (
  uid mediumint(8) NOT NULL auto_increment,
  username varchar(16) NOT NULL default '',
  remoteusername varchar(60) NULL,
  remoteservice varchar(60) NULL,
  fullname varchar(80) default NULL,
  passwd varchar(32) NOT NULL default '',
  email varchar(96) default NULL,
  homepage varchar(96) default NULL,
  sig varchar(160) NOT NULL default '',
  regdate datetime NOT NULL default '0000-00-00 00:00:00',
  photo varchar(128) DEFAULT NULL,
  cookietimeout int(8) unsigned default '28800',
  theme varchar(64) default NULL,
  language varchar(64) default NULL,
  pwrequestid varchar(16) default NULL,
  status smallint(5) unsigned NOT NULL default '1',
  num_reminders tinyint(1) NOT NULL default 0,
  PRIMARY KEY  (uid),
  KEY LOGIN (uid,passwd,username),
  INDEX users_username(username),
  INDEX users_fullname(fullname),
  INDEX users_email(email),
  INDEX users_passwd(passwd),
  INDEX users_pwrequestid(pwrequestid)
) TYPE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['vars']} (
  name varchar(20) NOT NULL default '',
  value varchar(128) default NULL,
  PRIMARY KEY  (name)
) TYPE=MyISAM
";


$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (1,3) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (2,3) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (4,3) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (5,9) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (5,11) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (6,9) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (6,11) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (7,12) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (8,5) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (9,8) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (10,4) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (11,6) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (13,10) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (14,11) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (15,11) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (16,4) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (17,10) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (18,10) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (19,11) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (20,14) ";
$_DATA[] = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES (21,15) ";

$_DATA[] = "INSERT INTO {$_TABLES['blocks']} (bid, is_enabled, name, type, title, tid, blockorder, content, rdfurl, rdfupdated, onleft, phpblockfn, group_id, owner_id, perm_owner, perm_group, perm_members, perm_anon) VALUES (1,1,'user_block','gldefault','User Functions','all',30,'','','0000-00-00 00:00:00',1,'',4,2,3,3,2,2) ";
$_DATA[] = "INSERT INTO {$_TABLES['blocks']} (bid, is_enabled, name, type, title, tid, blockorder, content, rdfurl, rdfupdated, onleft, phpblockfn, group_id, owner_id, perm_owner, perm_group, perm_members, perm_anon) VALUES (2,1,'admin_block','gldefault','Admins Only','all',20,'','','0000-00-00 00:00:00',1,'',4,2,3,3,2,2) ";
$_DATA[] = "INSERT INTO {$_TABLES['blocks']} (bid, is_enabled, name, type, title, tid, blockorder, content, rdfurl, rdfupdated, onleft, phpblockfn, group_id, owner_id, perm_owner, perm_group, perm_members, perm_anon) VALUES (3,1,'section_block','gldefault','Topics','all',10,'','','0000-00-00 00:00:00',1,'',4,2,3,3,2,2) ";
$_DATA[] = "INSERT INTO {$_TABLES['blocks']} (bid, is_enabled, name, type, title, tid, blockorder, content, rdfurl, rdfupdated, onleft, phpblockfn, group_id, owner_id, perm_owner, perm_group, perm_members, perm_anon) VALUES (4,1,'whats_new_block','gldefault','What\'s New','all',30,'','','0000-00-00 00:00:00',0,'',4,2,3,3,2,2) ";
$_DATA[] = "INSERT INTO {$_TABLES['blocks']} (bid, is_enabled, name, type, title, tid, blockorder, content, rdfurl, rdfupdated, onleft, phpblockfn, group_id, owner_id, perm_owner, perm_group, perm_members, perm_anon) VALUES (5,1,'first_block','normal','About NexPro','homeonly',20,'<p><b>Welcome to NexPro!</b></p><p>The nexPro Collaboration Server is a suite of Open Source products designed for work automation and web-based applications. Our extensions and plug-ins provide a secure web-based environment for team based collaboration.</p>','','0000-00-00 00:00:00',0,'',4,2,3,3,2,2) ";
$_DATA[] = "INSERT INTO {$_TABLES['blocks']} (bid, is_enabled, name, type, title, tid, blockorder, content, rdfurl, rdfupdated, onleft, phpblockfn, group_id, owner_id, perm_owner, perm_group, perm_members, perm_anon) VALUES (6,1,'whosonline_block','phpblock','Who\'s Online','all',10,'','','0000-00-00 00:00:00',0,'phpblock_whosonline',4,2,3,3,2,2) ";
$_DATA[] = "INSERT INTO {$_TABLES['blocks']} (bid, is_enabled, name, type, title, tid, blockorder, content, rdfurl, rdfupdated, onleft, phpblockfn, group_id, owner_id, perm_owner, perm_group, perm_members, perm_anon) VALUES (7,1,'older_stories','gldefault','Older Stories','all',40,'','','0000-00-00 00:00:00',1,'',4,2,3,3,2,2) ";

$_DATA[] = "INSERT INTO {$_TABLES['commentcodes']} (code, name) VALUES (0,'Comments Enabled') ";
$_DATA[] = "INSERT INTO {$_TABLES['commentcodes']} (code, name) VALUES (-1,'Comments Disabled') ";
$_DATA[] = "INSERT INTO {$_TABLES['commentcodes']} (code, name) VALUES (1,'Comments Closed') ";

$_DATA[] = "INSERT INTO {$_TABLES['commentmodes']} (mode, name) VALUES ('flat','Flat') ";
$_DATA[] = "INSERT INTO {$_TABLES['commentmodes']} (mode, name) VALUES ('nested','Nested') ";
$_DATA[] = "INSERT INTO {$_TABLES['commentmodes']} (mode, name) VALUES ('threaded','Threaded') ";
$_DATA[] = "INSERT INTO {$_TABLES['commentmodes']} (mode, name) VALUES ('nocomment','No Comments') ";

$_DATA[] = "INSERT INTO {$_TABLES['cookiecodes']} (cc_value, cc_descr) VALUES (0,'(don\'t)') ";
$_DATA[] = "INSERT INTO {$_TABLES['cookiecodes']} (cc_value, cc_descr) VALUES (3600,'1 Hour') ";
$_DATA[] = "INSERT INTO {$_TABLES['cookiecodes']} (cc_value, cc_descr) VALUES (7200,'2 Hours') ";
$_DATA[] = "INSERT INTO {$_TABLES['cookiecodes']} (cc_value, cc_descr) VALUES (10800,'3 Hours') ";
$_DATA[] = "INSERT INTO {$_TABLES['cookiecodes']} (cc_value, cc_descr) VALUES (28800,'8 Hours') ";
$_DATA[] = "INSERT INTO {$_TABLES['cookiecodes']} (cc_value, cc_descr) VALUES (86400,'1 Day') ";
$_DATA[] = "INSERT INTO {$_TABLES['cookiecodes']} (cc_value, cc_descr) VALUES (604800,'1 Week') ";
$_DATA[] = "INSERT INTO {$_TABLES['cookiecodes']} (cc_value, cc_descr) VALUES (2678400,'1 Month') ";

$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (0,'','System Default') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (1,'%A %B %d, %Y @%I:%M%p','Sunday March 21, 1999 @10:00PM') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (2,'%A %b %d, %Y @%H:%M','Sunday March 21, 1999 @22:00') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (4,'%A %b %d @%H:%M','Sunday March 21 @22:00') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (5,'%H:%M %d %B %Y','22:00 21 March 1999') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (6,'%H:%M %A %d %B %Y','22:00 Sunday 21 March 1999') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (7,'%I:%M%p - %A %B %d %Y','10:00PM -- Sunday March 21 1999') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (8,'%a %B %d, %I:%M%p','Sun March 21, 10:00PM') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (9,'%a %B %d, %H:%M','Sun March 21, 22:00') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (10,'%m-%d-%y %H:%M','3-21-99 22:00') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (11,'%d-%m-%y %H:%M','21-3-99 22:00') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (12,'%m-%d-%y %I:%M%p','3-21-99 10:00PM') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (13,'%I:%M%p  %B %e, %Y','10:00PM  March 21, 1999') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (14,'%a %b %d, \'%y %I:%M%p','Sun Mar 21, \'99 10:00PM') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (15,'Day %j, %I ish','Day 80, 10 ish') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (16,'%y-%m-%d %I:%M','99-03-21 10:00') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (17,'%d/%m/%y %H:%M','21/03/99 22:00') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (18,'%a %d %b %I:%M%p','Sun 21 Mar 10:00PM') ";
$_DATA[] = "INSERT INTO {$_TABLES['dateformats']} (dfid, format, description) VALUES (19,'%Y-%m-%d %H:%M','1999-03-21 22:00') ";

$_DATA[] = "INSERT INTO {$_TABLES['featurecodes']} (code, name) VALUES (0,'Not Featured') ";
$_DATA[] = "INSERT INTO {$_TABLES['featurecodes']} (code, name) VALUES (1,'Featured') ";

$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (1,'story.edit','Access to story editor',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (2,'story.moderate','Ability to moderate pending stories',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (3,'story.submit','May skip the story submission queue',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (4,'story.ping', 'Ability to send pings, pingbacks, or trackbacks for stories', 1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (5,'user.edit','Access to user editor',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (6,'user.delete','Ability to delete a user',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (7,'user.mail','Ability to send email to members',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (8,'syndication.edit', 'Access to Content Syndication', 1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (9,'webservices.atompub', 'May use Atompub Webservices (if restricted)', 1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (10,'block.edit','Access to block editor',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (11,'topic.edit','Access to topic editor',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (13,'plugin.edit','Can change plugin status',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (14,'group.edit','Ability to edit groups',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (15,'group.delete','Ability to delete groups',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (16,'block.delete','Ability to delete a block',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (17,'plugin.install','Can install/uninstall plugins',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (18,'plugin.upload','Can upload new plugins',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (19,'group.assign','Ability to assign users to groups',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (20, 'comment.moderate', 'Ability to moderate comments', 1)";
$_DATA[] = "INSERT INTO {$_TABLES['features']} (ft_id, ft_name, ft_descr, ft_gl_core) VALUES (21, 'comment.submit', 'Comments are automatically published', 1)";

$_DATA[] = "INSERT INTO {$_TABLES['frontpagecodes']} (code, name) VALUES (0,'Show Only in Topic') ";
$_DATA[] = "INSERT INTO {$_TABLES['frontpagecodes']} (code, name) VALUES (1,'Show on Front Page') ";

$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (2,1,NULL) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (2,NULL,1) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (3,NULL,1) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (4,NULL,1) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (6,NULL,1) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (9,NULL,1) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (10,NULL,1) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (11,NULL,1) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (13,2,NULL) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (12,2,NULL) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (11,2,NULL) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (2,NULL,12) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (2,NULL,10) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (2,NULL,9) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (2,NULL,6) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (2,NULL,4) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (2,NULL,3) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (12,NULL,1) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (9,NULL,11) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (2,NULL,11) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (10,2,NULL) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (9,2,NULL) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (6,2,NULL) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (4,2,NULL) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (3,2,NULL) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (2,2,NULL) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (1,2,NULL) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (5,NULL,1) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (8,NULL,1) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (14,NULL,1) ";
$_DATA[] = "INSERT INTO {$_TABLES['group_assignments']} (ug_main_grp_id, ug_uid, ug_grp_id) VALUES (15,NULL,1) ";

// Traditionally, grp_id 1 = Root, 2 = All Users, 13 = Logged-In Users
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (1,'Root','Has full access to the site',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (2,'All Users','Group that a typical user is added to',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (3,'Story Admin','Has full access to story features',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (4,'Block Admin','Has full access to block features',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (5,'Syndication Admin', 'Can create and modify web feeds for the site',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (6,'Topic Admin','Has full access to topic features',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (7,'Remote Users', 'Users in this group can have authenticated against a remote server.',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (8,'Webservices Users', 'Can use the Webservices API (if restricted)',0) ";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (9,'User Admin','Has full access to user features',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (10,'Plugin Admin','Has full access to plugin features',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (11,'Group Admin','Is a User Admin with access to groups, too',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (12,'Mail Admin','Can use Mail Utility',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (13,'Logged-in Users','All registered members',1) ";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (14, 'Comment Admin', 'Can moderate comments', 1)";
$_DATA[] = "INSERT INTO {$_TABLES['groups']} (grp_id, grp_name, grp_descr, grp_gl_core) VALUES (15, 'Comment Submitters', 'Can submit comments', 0);";

$_DATA[] = "INSERT INTO {$_TABLES['maillist']} (code, name) VALUES (0,'Don\'t Email') ";
$_DATA[] = "INSERT INTO {$_TABLES['maillist']} (code, name) VALUES (1,'Email Headlines Each Night') ";

$_DATA[] = "INSERT INTO {$_TABLES['pingservice']} (pid, name, site_url, ping_url, method, is_enabled) VALUES (1, 'Ping-O-Matic', 'http://pingomatic.com/', 'http://rpc.pingomatic.com/', 'weblogUpdates.ping', 1)";

$_DATA[] = "INSERT INTO {$_TABLES['postmodes']} (code, name) VALUES ('plaintext','Plain Old Text') ";
$_DATA[] = "INSERT INTO {$_TABLES['postmodes']} (code, name) VALUES ('html','HTML Formatted') ";

$_DATA[] = "INSERT INTO {$_TABLES['sortcodes']} (code, name) VALUES ('ASC','Oldest First') ";
$_DATA[] = "INSERT INTO {$_TABLES['sortcodes']} (code, name) VALUES ('DESC','Newest First') ";

$_DATA[] = "INSERT INTO {$_TABLES['statuscodes']} (code, name) VALUES (1,'Refreshing') ";
$_DATA[] = "INSERT INTO {$_TABLES['statuscodes']} (code, name) VALUES (0,'Normal') ";
$_DATA[] = "INSERT INTO {$_TABLES['statuscodes']} (code, name) VALUES (10,'Archive') ";

$_DATA[] = "INSERT INTO {$_TABLES['stories']} (sid, uid, draft_flag, tid, date, title, introtext, bodytext, hits, numemails, comments, related, featured, commentcode, statuscode, postmode, frontpage, owner_id, group_id, perm_owner, perm_group, perm_members, perm_anon) VALUES ('welcome', 2, 0, 'NexPro', '2007-08-26 00:08:34', 'Welcome to Nexpro', '<table width=\"100%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">\r    <tbody>\r        <tr>\r            <td valign=\"top\"><img width=\"184\" height=\"210\" src=\"http://www.nextide.ca/images/library/Image/nexpro%20box.jpg\" alt=\"\" /></td>\r            <td valign=\"top\">\r            The <strong>nexPro</strong> Collaboration Server is&nbsp;a suite of Open Source products designed for work automation and&nbsp;web-based applications. Our extensions and plug-ins provide a secure web-based environment <font size=\"2\">for</font> team based collaboration. Through a full range of administrative options, Collaboration Server can be easily configured to provide a rich and controlled set of capabilities that permissions that can be assigned to site content and features. It creates a destination for all team members to interact while providing real-time visibility to&nbsp;related project and group activities.\r            </td>\r        </tr>\r        <tr>\r            <td colspan=\"2\">&nbsp;&nbsp;&nbsp;\r            <ul>\r                <li><strong>nexPro</strong> Collaboration Server&nbsp;comes pre-integrated with document management, work-flow automation, electronic forms, list and menu administration.</li>\r                <li>There is no charge for a nexPro license but we do require you to select one of our&nbsp;annual support contract that includes software maintenance and updates.</li>\r                <li><strong>nexPro</strong> is a solid platform for creating and operating any web based application.</li>\r                <li>Work automation applications like form approval routing can be created entirely with ''mouse clicks''.&nbsp;</li>\r            </ul>\r            </td>\r        </tr>\r    </tbody>\r</table>\r', '', 100, 1, 0, '', 1, -1, 0, 'html', 1, 2, 3, 3, 2, 2, 2)";

$_DATA[] = "INSERT INTO {$_TABLES['syndication']} (type, topic, header_tid, format, limits, content_length, title, description, filename, charset, language, is_enabled, updated, update_info) VALUES ('geeklog', '::all', 'all', 'RSS-2.0', 10, 1, 'Geeklog Site', 'Another Nifty Geeklog Site', 'geeklog.rss', 'iso-8859-1', 'en-gb', 1, '0000-00-00 00:00:00', NULL)";

$_DATA[] = "INSERT INTO {$_TABLES['storysubmission']} (sid, uid, tid, title, introtext, date, postmode) VALUES ('security-reminder',2,'Geeklog','Are you secure?','<p>This is a reminder to secure your site once you have Geeklog up and running. What you should do:</p>\r\r<ol>\r<li>Change the default password for the Admin account.</li>\r<li>Remove the install directory (you won\'t need it any more).</li>\r</ol>',NOW(),'html') ";

$_DATA[] = "INSERT INTO {$_TABLES['syndication']} (type, topic, header_tid, format, limits, content_length, title, description, filename, charset, language, is_enabled, updated, update_info) VALUES ('article', '::all', 'all', 'RSS-2.0', 10, 1, 'Geeklog Site', 'Another Nifty Geeklog Site', 'geeklog.rss', 'iso-8859-1', 'en-gb', 1, '0000-00-00 00:00:00', NULL)";

$_DATA[] = "INSERT INTO {$_TABLES['topics']} (tid, topic, imageurl, meta_description, meta_keywords, sortnum, limitnews, group_id, owner_id, perm_owner, perm_group, perm_members, perm_anon) VALUES ('General','General News','/images/topics/topic_news.gif','A topic that contains general news related posts.','News, Post, Information',1,10,6,2,3,2,2,2)";
$_DATA[] = "INSERT INTO {$_TABLES['topics']} (tid, topic, imageurl, meta_description, meta_keywords, sortnum, limitnews, group_id, owner_id, perm_owner, perm_group, perm_members, perm_anon) VALUES ('NexPro','NexPro','/images/topics/topic_gl.gif','A topic that contains posts about NexPro.','NexPro, Posts, Information',2,10,6,2,3,2,2,2)";

$_DATA[] = "INSERT INTO {$_TABLES['usercomment']} (uid, commentmode, commentorder, commentlimit) VALUES (1,'nested','ASC',100) ";
$_DATA[] = "INSERT INTO {$_TABLES['usercomment']} (uid, commentmode, commentorder, commentlimit) VALUES (2,'threaded','ASC',100) ";

$_DATA[] = "INSERT INTO {$_TABLES['userindex']} (uid, tids, etids, aids, boxes, noboxes, maxstories) VALUES (1,'','-','','',0,NULL) ";
$_DATA[] = "INSERT INTO {$_TABLES['userindex']} (uid, tids, etids, aids, boxes, noboxes, maxstories) VALUES (2,'','','','',0,NULL) ";

$_DATA[] = "INSERT INTO {$_TABLES['userinfo']} (uid, about, pgpkey, userspace, tokens, totalcomments, lastgranted) VALUES (1,NULL,NULL,'',0,0,0) ";
$_DATA[] = "INSERT INTO {$_TABLES['userinfo']} (uid, about, pgpkey, userspace, tokens, totalcomments, lastgranted) VALUES (2,NULL,NULL,'',0,0,0) ";

$_DATA[] = "INSERT INTO {$_TABLES['userprefs']} (uid, noicons, willing, dfid, tzid, emailstories) VALUES (1,0,0,0,'',0) ";
$_DATA[] = "INSERT INTO {$_TABLES['userprefs']} (uid, noicons, willing, dfid, tzid, emailstories) VALUES (2,0,1,0,'',1) ";

#
# Dumping data for table 'users'
#

$_DATA[] = "INSERT INTO {$_TABLES['users']} (uid, username, fullname, passwd, email, homepage, sig, regdate, cookietimeout, theme, status) VALUES (1,'Anonymous','Anonymous','',NULL,NULL,'',NOW(),0,NULL,3) ";
$_DATA[] = "INSERT INTO {$_TABLES['users']} (uid, username, fullname, passwd, email, homepage, sig, regdate, cookietimeout, theme, status) VALUES (2,'Admin','Nexpro SuperUser','5f4dcc3b5aa765d61d8327deb882cf99','root@localhost','http://www.nextide.ca/','',NOW(),28800,NULL,3) ";

$_DATA[] = "INSERT INTO {$_TABLES['vars']} (name, value) VALUES ('totalhits','0') ";
$_DATA[] = "INSERT INTO {$_TABLES['vars']} (name, value) VALUES ('lastemailedstories','') ";
$_DATA[] = "INSERT INTO {$_TABLES['vars']} (name, value) VALUES ('last_scheduled_run','') ";
$_DATA[] = "INSERT INTO {$_TABLES['vars']} (name, value) VALUES ('database_version','0.0.0') ";

$_DATA[] = "INSERT INTO {$_TABLES['trackbackcodes']} (code, name) VALUES (0,'Trackback Enabled') ";
$_DATA[] = "INSERT INTO {$_TABLES['trackbackcodes']} (code, name) VALUES (-1,'Trackback Disabled') ";

?>
