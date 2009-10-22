<?php
// +--------------------------------------------------------------------------+
// | Forum Plugin for Geeklog based framework CMS applications                |
// +--------------------------------------------------------------------------+
// | install_defaults.php                                                     |
// | $Id::                                                                    |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2002 - 2009 the following authors:                         |
// | Blaine Lang                 -    blaine@portalparts.com                  |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | This program is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU General Public License              |
// | as published by the Free Software Foundation; either version 2           |
// | of the License, or (at your option) any later version.                   |
// |                                                                          |
// | This program is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with this program; if not, write to the Free Software Foundation,  |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.          |
// |                                                                          |
// +--------------------------------------------------------------------------+

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

/*
 * Forum default settings
 *
 * Initial Installation Defaults used when loading the online configuration
 * records. These settings are only used during the initial installation
 * and not referenced any more once the plugin is installed
 *
 */

global $CONF_FORUM_DEFAULT;

$CONF_FORUM_DEFAULT = array();
$CONF_FORUM_DEFAULT['registration_required']  = false;
$CONF_FORUM_DEFAULT['registered_to_post']     = true;
$CONF_FORUM_DEFAULT['allow_html']             = false;
$CONF_FORUM_DEFAULT['post_htmlmode']          = false;
$CONF_FORUM_DEFAULT['use_glfilter']           = true;
$CONF_FORUM_DEFAULT['use_geshi']              = true;
$CONF_FORUM_DEFAULT['use_censor']             = true;
$CONF_FORUM_DEFAULT['show_moods']             = true;
$CONF_FORUM_DEFAULT['allow_smilies']          = true;
$CONF_FORUM_DEFAULT['allow_notification']     = true;
$CONF_FORUM_DEFAULT['allow_user_dateformat']  = true;
$CONF_FORUM_DEFAULT['show_topicreview']       = true;
$CONF_FORUM_DEFAULT['showtopic_review_order'] = 'DESC';
$CONF_FORUM_DEFAULT['use_autorefresh']        = true;
$CONF_FORUM_DEFAULT['autorefresh_delay']      = 5;
$CONF_FORUM_DEFAULT['show_subject_length']    = 40;
$CONF_FORUM_DEFAULT['show_topics_perpage']    = 10;
$CONF_FORUM_DEFAULT['show_posts_perpage']     = 10;
$CONF_FORUM_DEFAULT['show_messages_perpage']  = 20;
$CONF_FORUM_DEFAULT['show_searches_perpage']  = 20;
$CONF_FORUM_DEFAULT['views_tobe_popular']     = 20;
$CONF_FORUM_DEFAULT['convert_break']          = false;
$CONF_FORUM_DEFAULT['min_comment_length']     = 5;
$CONF_FORUM_DEFAULT['min_username_length']    = 2;
$CONF_FORUM_DEFAULT['min_subject_length']     = 2;
$CONF_FORUM_DEFAULT['post_speedlimit']        = 60;
$CONF_FORUM_DEFAULT['use_smilies_plugin']     = false;
$CONF_FORUM_DEFAULT['use_pm_plugin']          = false;
$CONF_FORUM_DEFAULT['use_spamx_filter']       = true;
$CONF_FORUM_DEFAULT['show_centerblock']       = true;
$CONF_FORUM_DEFAULT['centerblock_homepage']   = true;
$CONF_FORUM_DEFAULT['centerblock_where']      = 2;
$CONF_FORUM_DEFAULT['cb_subject_size']        = 40;
$CONF_FORUM_DEFAULT['centerblock_numposts']   = 10;
$CONF_FORUM_DEFAULT['sb_subject_size']        = 20;
$CONF_FORUM_DEFAULT['sb_latestpostonly']      = false;
$CONF_FORUM_DEFAULT['sideblock_numposts']     = 5;
$CONF_FORUM_DEFAULT['allowed_editwindow']     = 0;
$CONF_FORUM_DEFAULT['level1']                 = 1;
$CONF_FORUM_DEFAULT['level2']                 = 15;
$CONF_FORUM_DEFAULT['level3']                 = 35;
$CONF_FORUM_DEFAULT['level4']                 = 70;
$CONF_FORUM_DEFAULT['level5']                 = 120;
$CONF_FORUM_DEFAULT['level1name']             = 'Newbie';
$CONF_FORUM_DEFAULT['level2name']             = 'Junior';
$CONF_FORUM_DEFAULT['level3name']             = 'Chatty';
$CONF_FORUM_DEFAULT['level4name']             = 'Regular Member';
$CONF_FORUM_DEFAULT['level5name']             = 'Active Member';
$CONF_FORUM_DEFAULT['showblocks']              = 'leftblocks';     // noblocks, leftblocks, rightblocks
$CONF_FORUM_DEFAULT['usermenu']                = 'navbar';         // blockmenu, navbar, none
$CONF_FORUM_DEFAULT['mysql4+']                 = false;
$CONF_FORUM_DEFAULT['pre2.5_mode']             = false;
$CONF_FORUM_DEFAULT['silent_edit_default']     = true;
$CONF_FORUM_DEFAULT['avatar_width']            = 115;
$CONF_FORUM_DEFAULT['allow_img_bbcode']        = true;
$CONF_FORUM_DEFAULT['show_moderators']         = false;
$CONF_FORUM_DEFAULT['imgset']                  = $_CONF['layout_url'] .'/forum/image_set';
$CONF_FORUM_DEFAULT['imgset_path']             = $_CONF['path_layout'] .'/forum/image_set';
$CONF_FORUM_DEFAULT['autoimagetype']           = true;
$CONF_FORUM_DEFAULT['image_type_override']     = 'gif';
$CONF_FORUM_DEFAULT['default_Datetime_format'] = '%m/%d/%y %H:%M %p';
$CONF_FORUM_DEFAULT['default_Topic_Datetime_format'] = '%B %d %Y %H:%M %p';
$CONF_FORUM_DEFAULT['contentinfo_numchars']    = 256;
$CONF_FORUM_DEFAULT['linkinfo_width']          = 40;
$CONF_FORUM_DEFAULT['quoteformat'] = "[QUOTE][u]Quote by: %s[/u][p]%s[/p][/QUOTE]";
$CONF_FORUM_DEFAULT['show_last_post_count']    = '20';
$CONF_FORUM_DEFAULT['menustyle']               = 'default';
$CONF_FORUM_DEFAULT['filestorage_plugin']      = 'nexfile';
$CONF_FORUM_DEFAULT['grouptags'] = array(
    'Root'              => 'siteadmin_badge.png',
    'Logged-in Users'   => 'forum_user.png',
    'Group A'           => 'badge1.png',
    'Group B'           => 'badge2.png'
);
$CONF_FORUM_DEFAULT['maxattachments']          = 2;      // Maximum number of attachments allowed in a single post
$CONF_FORUM_DEFAULT['uploadpath']              = $_CONF['path_html'] . 'forum/media';
$CONF_FORUM_DEFAULT['downloadURL']             = $_CONF['site_url'] . '/forum/media';
$CONF_FORUM_DEFAULT['fileperms']               = '0755';  // Needs to be a string for the upload class use.
$CONF_FORUM_DEFAULT['max_uploadimage_width']   = '2100';
$CONF_FORUM_DEFAULT['max_uploadimage_height']  = '1600';
$CONF_FORUM_DEFAULT['max_uploadfile_size']     = '6553600';     // 6.400 MB
$CONF_FORUM_DEFAULT['inlineimage_width']       = '300';
$CONF_FORUM_DEFAULT['inlineimage_height']      = '300';
$CONF_FORUM_DEFAULT['bbcode_signature']        = true;

$CONF_FORUM_DEFAULT['allowablefiletypes']    = array(
        'application/x-gzip-compressed'     => '.tgz',
        'application/x-zip-compressed'      => '.zip',
        'application/zip'                   => '.zip',
        'application/x-tar'                 => '.tar',
        'application/x-gtar'                => '.gtar',
        'application/x-gzip'                => '.gz',
        'text/plain'                        => '.php,.txt,.inc',
        'text/html'                         => '.html,.htm',
        'image/bmp'                         => '.bmp,.ico',
        'image/gif'                         => '.gif',
        'image/pjpeg'                       => '.jpg,.jpeg',
        'image/jpeg'                        => '.jpg,.jpeg',
        'image/png'                         => '.png',
        'image/x-png'                       => '.png',
        'audio/mpeg'                        => '.mp3',
        'audio/wav'                         => '.wav',
        'application/pdf'                   => '.pdf',
        'application/x-shockwave-flash'     => '.swf',
        'application/msword'                => '.doc',
        'application/vnd.ms-excel'          => '.xls',
        'application/vnd.ms-powerpoint'     => '.ppt',
        'application/vnd.ms-project'        => '.mpp',
        'application/vnd.visio'             => '.vsd',
        'text/plain'                        => '.txt',
        'application/x-pangaeacadsolutions' => '.dwg',
        'application/x-zip-compresseed'     => '.zip',
        'application/vnd.ms-word.document.macroEnabled.12'                               =>         '.docm',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'        =>         '.docx',
        'application/vnd.ms-word.template.macroEnabled.12'                               =>         '.dotm',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.template'        =>         '.dotx',
        'application/vnd.ms-powerpoint.template.macroEnabled.12'                         =>         '.potm',
        'application/vnd.openxmlformats-officedocument.presentationml.template'          =>         '.potx',
        'application/vnd.ms-powerpoint.addin.macroEnabled.12'                            =>         '.ppam',
        'application/vnd.ms-powerpoint.slideshow.macroEnabled.12'                        =>         '.ppsm',
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow'         =>         '.ppsx',
        'application/vnd.ms-powerpoint.presentation.macroEnabled.12'                     =>         '.pptm',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'      =>         '.pptx',
        'application/vnd.ms-excel.addin.macroEnabled.12'                                 =>         '.xlam',
        'application/vnd.ms-excel.sheet.binary.macroEnabled.12'                          =>         '.xlsb',
        'application/vnd.ms-excel.sheet.macroEnabled.12'                                 =>         '.xlsm',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'              =>         '.xlsx',
        'application/vnd.ms-excel.template.macroEnabled.12'                              =>         '.xltm',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.template'           =>         '.xltx',
        'application/octet-stream'          => '.zip,.vsd,.fla,.psd,.xls,.doc,.ppt,.pdf,.swf,.mpp,.txt,.dwg,.docx,.ppsx,.pptx,.xlsx,.xltx'
        );

$CONF_FORUM_DEFAULT['inlineimageypes']    = array(
        'image/bmp'                         => '.bmp,',
        'image/gif'                         => '.gif',
        'image/pjpeg'                       => '.jpg,.jpeg',
        'image/jpeg'                        => '.jpg,.jpeg',
        'image/png'                         => '.png',
        'image/x-png'                       => '.png'
);
// new in v3.1.1
$CONF_FORUM_DEFAULT['enable_fm_integration'] = false;
$CONF_FORUM_DEFAULT['allow_memberlist']      = false;


/**
* Initialize Forum plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $CONF_FORUM if available (e.g. from
* an old config.php), uses $CONF_FORUM_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_forum()
{
    global $CONF_FORUM, $CONF_FORUM_DEFAULT;

    if (is_array($CONF_FORUM) && (count($CONF_FORUM) > 1)) {
        $CONF_FORUM_DEFAULT = array_merge($CONF_FORUM_DEFAULT, $CONF_FORUM);
    }
    $c = config::get_instance();
    if (!$c->group_exists('forum')) {

        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'forum');
        $c->add('ff_public', NULL, 'fieldset', 0, 0, NULL, 0, true, 'forum');

        $c->add('registration_required', $CONF_FORUM_DEFAULT['registration_required'], 'select',
                0, 0, 0, 10, true, 'forum');
        $c->add('registered_to_post', $CONF_FORUM_DEFAULT['registered_to_post'], 'select',
                0, 0, 0, 20, true, 'forum');
        $c->add('allow_memberlist', $CONF_FORUM_DEFAULT['allow_memberlist'], 'select',
                0, 0, 0, 30, true, 'forum');
        $c->add('allow_notification', $CONF_FORUM_DEFAULT['allow_notification'], 'select',
                0, 0, 0, 40, true, 'forum');
        $c->add('show_topicreview', $CONF_FORUM_DEFAULT['show_topicreview'], 'select',
                0, 0, 0, 50, true, 'forum');
        $c->add('showtopic_review_order', $CONF_FORUM_DEFAULT['showtopic_review_order'], 'select',
                0, 0, 5, 60, true, 'forum');
        $c->add('allow_user_dateformat', $CONF_FORUM_DEFAULT['allow_user_dateformat'], 'select',
                0, 0, 0, 70, true, 'forum');
        $c->add('use_pm_plugin', $CONF_FORUM_DEFAULT['use_pm_plugin'], 'select',
                0, 0, 0, 80, true, 'forum');
        $c->add('use_autorefresh', $CONF_FORUM_DEFAULT['use_autorefresh'], 'select',
                0, 0, 0, 90, true, 'forum');
        $c->add('autorefresh_delay', $CONF_FORUM_DEFAULT['autorefresh_delay'], 'text',
                0, 0, 0, 100, true, 'forum');
        $c->add('show_topics_perpage', $CONF_FORUM_DEFAULT['show_topics_perpage'], 'text',
                0, 0, 0, 110, true, 'forum');
        $c->add('show_posts_perpage', $CONF_FORUM_DEFAULT['show_posts_perpage'], 'text',
                0, 0, 0, 120, true, 'forum');
        $c->add('show_messages_perpage', $CONF_FORUM_DEFAULT['show_messages_perpage'], 'text',
                0, 0, 0, 130, true, 'forum');
        $c->add('show_searches_perpage', $CONF_FORUM_DEFAULT['show_searches_perpage'], 'text',
                0, 0, 0, 140, true, 'forum');
        $c->add('showblocks', $CONF_FORUM_DEFAULT['showblocks'], 'select',
                0, 0, 3, 150, true, 'forum');
        $c->add('usermenu', $CONF_FORUM_DEFAULT['usermenu'], 'select',
                0, 0, 4, 160, true, 'forum');
        $c->add('menustyle', $CONF_FORUM_DEFAULT['menustyle'], 'select',
                0,0,7,170, true, 'forum');
        $c->add('mysql4+', $CONF_FORUM_DEFAULT['mysql4+'], 'select',
                0, 0, 0, 180, true, 'forum');
        $c->add('pre2.5_mode', $CONF_FORUM_DEFAULT['pre2.5_mode'], 'select',
                0, 0, 0, 190, true, 'forum');
        $c->add('silent_edit_default', $CONF_FORUM_DEFAULT['silent_edit_default'], 'select',
                0, 0, 0, 200, true, 'forum');
        $c->add('avatar_width', $CONF_FORUM_DEFAULT['avatar_width'], 'text',
                0, 0, 0, 210, true, 'forum');
        $c->add('allow_img_bbcode', $CONF_FORUM_DEFAULT['allow_img_bbcode'], 'select',
                0, 0, 0, 220, true, 'forum');
        $c->add('show_moderators', $CONF_FORUM_DEFAULT['show_moderators'], 'select',
                0, 0, 0, 230, true, 'forum');
        $c->add('default_Datetime_format', $CONF_FORUM_DEFAULT['default_Datetime_format'], 'text',
                0, 0, 0, 240, true, 'forum');
        $c->add('default_Topic_Datetime_format', $CONF_FORUM_DEFAULT['default_Topic_Datetime_format'], 'text',
                0, 0, 0, 250, true, 'forum');
        $c->add('contentinfo_numchars', $CONF_FORUM_DEFAULT['contentinfo_numchars'], 'text',
                0, 0, 0, 260, true, 'forum');
        $c->add('linkinfo_width', $CONF_FORUM_DEFAULT['linkinfo_width'], 'text',
                0, 0, 0, 270, true, 'forum');
        $c->add('quoteformat', $CONF_FORUM_DEFAULT['quoteformat'], 'text',
                0, 0, 0, 280, true, 'forum');
        $c->add('show_last_post_count', $CONF_FORUM_DEFAULT['show_last_post_count'], 'text',
                0, 0, 0, 290, true, 'forum');
        $c->add('grouptags',$CONF_FORUM_DEFAULT['grouptags'],'*text',
                0,0,NULL,300,true,'forum');

        $c->add('ff_attachments_settings', NULL, 'fieldset', 0, 1, NULL, 0, true,'forum');

        $c->add('maxattachments', $CONF_FORUM_DEFAULT['maxattachments'], 'text',
                0, 1, 0, 10, true, 'forum');
        $c->add('uploadpath', $CONF_FORUM_DEFAULT['uploadpath'], 'text',
                0, 1, 0, 20, true, 'forum');
        $c->add('downloadURL', $CONF_FORUM_DEFAULT['downloadURL'], 'text',
                0, 1, 0, 30, true, 'forum');
        $c->add('fileperms', $CONF_FORUM_DEFAULT['fileperms'], 'text',
                0, 1, 0, 40, true, 'forum');
        $c->add('max_uploadimage_width', $CONF_FORUM_DEFAULT['max_uploadimage_width'], 'text',
                0, 1, 0, 50, true, 'forum');
        $c->add('max_uploadimage_height', $CONF_FORUM_DEFAULT['max_uploadimage_height'], 'text',
                0, 1, 0, 60, true, 'forum');

        $c->add('max_uploadfile_size', $CONF_FORUM_DEFAULT['max_uploadfile_size'], 'text',
                0, 1, 0, 70, true, 'forum');
        $c->add('inlineimage_width', $CONF_FORUM_DEFAULT['inlineimage_width'], 'text',
                0, 1, 0, 80, true, 'forum');
        $c->add('inlineimage_height', $CONF_FORUM_DEFAULT['inlineimage_height'], 'text',
                0, 1, 0, 90, true, 'forum');
        $c->add('allowablefiletypes',$CONF_FORUM_DEFAULT['allowablefiletypes'], '*text',
                0,1,NULL,100,true,'forum');
        $c->add('inlineimageypes',$CONF_FORUM_DEFAULT['inlineimageypes'], '*text',
                0,1,NULL,110,true,'forum');
        $c->add('filestorage_plugin', $CONF_FORUM_DEFAULT['filestorage_plugin'], 'select',
                0,1,6,120, true, 'forum');

        $c->add('ff_topic_post_settings', NULL, 'fieldset', 0, 2, NULL, 0, true,'forum');

        $c->add('show_subject_length', $CONF_FORUM_DEFAULT['show_subject_length'], 'text',
                0, 2, 0, 10, true, 'forum');
        $c->add('min_username_length', $CONF_FORUM_DEFAULT['min_username_length'], 'text',
                0, 2, 0, 20, true, 'forum');
        $c->add('min_subject_length', $CONF_FORUM_DEFAULT['min_subject_length'], 'text',
                0, 2, 0, 30, true, 'forum');
        $c->add('min_comment_length', $CONF_FORUM_DEFAULT['min_comment_length'], 'text',
                0, 2, 0, 40, true, 'forum');
        $c->add('views_tobe_popular', $CONF_FORUM_DEFAULT['views_tobe_popular'], 'text',
                0, 2, 0, 50, true, 'forum');
        $c->add('post_speedlimit', $CONF_FORUM_DEFAULT['post_speedlimit'], 'text',
                0, 2, 0, 60, true, 'forum');
        $c->add('allowed_editwindow', $CONF_FORUM_DEFAULT['allowed_editwindow'], 'text',
                0, 2, 0, 70, true, 'forum');
        $c->add('allow_html', $CONF_FORUM_DEFAULT['allow_html'], 'select',
                0, 2, 0, 80, true, 'forum');
        $c->add('post_htmlmode', $CONF_FORUM_DEFAULT['post_htmlmode'], 'select',
                0, 2, 0, 90, true, 'forum');
        $c->add('use_censor', $CONF_FORUM_DEFAULT['use_censor'], 'select',
                0, 2, 0, 100, true, 'forum');
        $c->add('use_geshi', $CONF_FORUM_DEFAULT['use_geshi'], 'select',
                0, 2, 0, 120, true, 'forum');
        $c->add('use_spamx_filter', $CONF_FORUM_DEFAULT['use_spamx_filter'], 'select',
                0, 2, 0, 130, true, 'forum');
        $c->add('show_moods', $CONF_FORUM_DEFAULT['show_moods'], 'select',
                0, 2, 0, 140, true, 'forum');
        $c->add('allow_smilies', $CONF_FORUM_DEFAULT['allow_smilies'], 'select',
                0, 2, 0, 150, true, 'forum');
        $c->add('use_smilies_plugin', $CONF_FORUM_DEFAULT['use_smilies_plugin'], 'select',
                0, 2, 0, 160, true, 'forum');

        $c->add('ff_centerblock', NULL, 'fieldset', 0, 3, NULL, 0, true,
                'forum');

        $c->add('show_centerblock', $CONF_FORUM_DEFAULT['show_centerblock'], 'select',
                0, 3, 0, 10, true, 'forum');
        $c->add('centerblock_homepage', $CONF_FORUM_DEFAULT['centerblock_homepage'], 'select',
                0, 3, 0, 20, true, 'forum');
        $c->add('centerblock_numposts', $CONF_FORUM_DEFAULT['centerblock_numposts'], 'text',
                0, 3, 0, 30, true, 'forum');
        $c->add('cb_subject_size', $CONF_FORUM_DEFAULT['cb_subject_size'], 'text',
                0, 3, 0, 40, true, 'forum');
        $c->add('centerblock_where', $CONF_FORUM_DEFAULT['centerblock_where'], 'select',
                0, 3, 2, 50, true, 'forum');

        $c->add('ff_latest_post_block', NULL, 'fieldset', 0, 4, NULL, 0, true,
                'forum');

        $c->add('sideblock_numposts', $CONF_FORUM_DEFAULT['sideblock_numposts'], 'text',
                0, 4, 0, 10, true, 'forum');
        $c->add('sb_subject_size', $CONF_FORUM_DEFAULT['sb_subject_size'], 'text',
                0, 4, 0, 20, true, 'forum');
        $c->add('sb_latestpostonly', $CONF_FORUM_DEFAULT['sb_latestpostonly'], 'select',
                0, 4, 0, 20, true, 'forum');

        $c->add('ff_rank_settings', NULL, 'fieldset', 0, 5, NULL, 0, true,
                'forum');
        $c->add('level1', $CONF_FORUM_DEFAULT['level1'], 'text',
                0, 5, 0, 10, true, 'forum');
        $c->add('level1name', $CONF_FORUM_DEFAULT['level1name'], 'text',
                0, 5, 0, 20, true, 'forum');
        $c->add('level2', $CONF_FORUM_DEFAULT['level2'], 'text',
                0, 5, 0, 30, true, 'forum');
        $c->add('level2name', $CONF_FORUM_DEFAULT['level2name'], 'text',
                0, 5, 0, 40, true, 'forum');
        $c->add('level3', $CONF_FORUM_DEFAULT['level3'], 'text',
                0, 5, 0, 50, true, 'forum');
        $c->add('level3name', $CONF_FORUM_DEFAULT['level3name'], 'text',
                0, 5, 0, 60, true, 'forum');
        $c->add('level4', $CONF_FORUM_DEFAULT['level4'], 'text',
                0, 5, 0, 70, true, 'forum');
        $c->add('level4name', $CONF_FORUM_DEFAULT['level4name'], 'text',
                0, 5, 0, 80, true, 'forum');
        $c->add('level5', $CONF_FORUM_DEFAULT['level5'], 'text',
                0, 5, 0, 90, true, 'forum');
        $c->add('level5name', $CONF_FORUM_DEFAULT['level5name'], 'text',
                0, 5, 0, 100, true, 'forum');
    }

    return true;
}
?>