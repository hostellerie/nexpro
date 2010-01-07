<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.1.0 for the nexPro Portal Server                         |
// | January 2010                                                              |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | nexpro.php                                                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
// | Blaine Lang            - Blaine DOT Lang AT nextide DOT ca                |
// | Randy Kolenko          - Randy DOT Kolenko AT nextide DOT ca              |
// | Eric de la Chevrotiere - Eric DOT delaChevrotiere AT nextide DOT ca       |
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
$CONF_NEXPRO=array();
$CONF_NEXPRO['version'] = '2.1.0';
$CONF_NEXPRO['pi_name'] = 'nexpro';
$CONF_NEXPRO['pi_display_name'] = 'nexPro';
$CONF_NEXPRO['pi_gl_version'] = '1.6.1';


$_TABLES['tagwords']        = $_DB_table_prefix .'tagwords';
$_TABLES['tagworditems']    = $_DB_table_prefix .'tagword_items';
$_TABLES['tagwordmetrics']  = $_DB_table_prefix .'tagword_metrics';


// +---------------------------------------------------------------------------+
// | User Editable Plugin Parms not available in the Online Config Manager     |
// +---------------------------------------------------------------------------+

//LDAP server settings
$CONF_NEXPRO['ldap_type'] = 'Active Directory';
$CONF_NEXPRO['ldap_server']='example.com';
$CONF_NEXPRO['ldap_ous']=array (
    0   => 'Example OU 1',
    1   => 'Example OU 2'
);
$CONF_NEXPRO['ldap_organization_string']="ou=%s,dc=example,dc=com"; //%s = ou (populated from $CONF_NEXPRO['ldap_ous'])
$CONF_NEXPRO['ldap_bind_string'] = "%s@nextide.ca";  //%s = username, %s = ou (populated from $CONF_NEXPRO['ldap_ous'])
$CONF_NEXPRO['ldap_search_string']="samaccountname=%s"; //%s = username
$CONF_NEXPRO['fullname_parm'] = 'displayname';  //array index of the fullname in the ldap search return
$CONF_NEXPRO['email_parm'] = 'mail';  //array index of the email address in the ldap search return

//Need a valid LDAP user's credentials to populate the fullname of the user
$CONF_NEXPRO['ldap_username'] = 'ldap_user';
$CONF_NEXPRO['ldap_password'] = 'ldap_pass';

//THIS IS REQUIRED IF YOU WANT TO FORCE A USER TO HAVE A REMOTE SERVICE NAME
//set this to the authentication mechanism's value
$CONF_NEXPRO['enable_remote_service_set']=false;   //true to turn on.set this to false if you don't want to force a remote service into that new e user's profile
$CONF_NEXPRO['new_user_remote_service']='LDAP'; //set to whichever remote authentication mechanism you desire.  This is to support our LDAP ability

require_once $_CONF['path_system'] . 'classes/config.class.php';
$nexpro_config = config::get_instance();
$CONF_NEXPRO_2= $nexpro_config->get_config('nexpro');
if(is_array($CONF_NEXPRO_2)) $CONF_NEXPRO=@array_merge($CONF_NEXPRO_2,$CONF_NEXPRO);
?>