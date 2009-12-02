<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexScan Plugin v1.0.0 for the nexPro Portal Server                        |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | lib-nextide.php                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007-2010 by the following authors:                         |
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

/**
 * Filter an incoming INT variable
 * Will Return a zero if the variable is null or blank OR return the INT result
 *
 * @param       int   $var    Variable to test
 * @return      int   Returns the cleansed INT variable
 *
 * was nextide_filterInt
 */
function NXCOM_filterInt($var){

    if( !is_numeric( $var ) || ( preg_match( '/^-?\d+$/', $var ) == 0 )){
            $retval = 0;
    } else {
        $retval=$var;
    }
    return $retval;
}

/* Return the HTML for a <select> element options for all users in a GL Group
 * This function will handle groups within groups - so recursive groups to get a full list of users
 * @param        int       $groupid         Group ID to list users for
 * @param        int       $selected        User ID (uid) if there is a current selection
 * @param        boolean   $show_fullname   Name in dropdown - default is fullname if exists else username
 * @return      string    $options        string of select options
 *
 * was nxhtml_listUsers
*/

function NXCOM_listUsers($selected='',$show_fullname=true) {
    global $_TABLES;

    $retval = '<option value="0">--</option>';
    if(!is_array($selected)) {
        $selected = explode(',',$selected);
    }
    $sql = "SELECT uid,username,fullname FROM {$_TABLES['users']} WHERE uid > 1 AND status=3 ";
    if ($show_fullname) {
        $sql .= 'ORDER BY fullname ASC';
    } else {
        $sql .= 'ORDER BY username ASC';
    }
    $query = DB_query($sql);
    // Build an array of names and user id's sorted by name
    $users = array();
    while (list($uid,$username,$fullname) = DB_fetchArray($query)) {
        if ($show_fullname AND trim($fullname) !== '') {
            $users[$uid] = strtolower($fullname);
        } else {
            $users[$uid] = $username;;
        }
    }
    asort($users);

    foreach ($users as $uid => $username) {
        if (strpos($username,' ') > 0) {
            $username = ucwords($username);
        }
        if (in_array($uid,$selected)) {
            $retval .= '<option value="'.$uid.'" SELECTED=selected>';
        } else {
            $retval .= '<option value="'.$uid.'">';
        }
        $retval .= $username . '</option>';

    }
    return $retval;
}

/* Return the HTML for a <select> element options for all users in a GL Group
 * This function will handle groups within groups - so recursive groups to get a full list of users
 * @param        int       $groupid         Group ID to list users for
 * @param        int       $selected        User ID (uid) if there is a current selection
 * @param        boolean   $show_fullname   Name in dropdown - default is fullname if exists else username
 * @return       string    $options        string of select options
 *
 * was nxhtml_listGroupUsers
*/
function NXCOM_listGroupUsers ($groupid=2,$selected=0,$show_fullname=true) {
    global $_CONF,$_USER,$_TABLES;

    $retval = '<option value="0">--</option>';

    $to_check = array ();
    array_push ($to_check, $groupid);
    $groups = array ();
    while (sizeof ($to_check) > 0) {
        $thisgroup = array_pop ($to_check);
        if ($thisgroup > 0) {
            $result = DB_query ("SELECT ug_grp_id FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = $thisgroup");
            $numGroups = DB_numRows ($result);
            for ($i = 0; $i < $numGroups; $i++) {
                $A = DB_fetchArray ($result);
                if (!in_array ($A['ug_grp_id'], $groups)) {
                    if (!in_array ($A['ug_grp_id'], $to_check)) {
                        array_push ($to_check, $A['ug_grp_id']);
                    }
                }
            }
            $groups[] = $thisgroup;
        }
    }
    $groupList = implode (',', $groups);

    $sql = "SELECT DISTINCT {$_TABLES['users']}.uid,{$_TABLES['users']}.username,{$_TABLES['users']}.fullname "
          ."FROM {$_TABLES['group_assignments']},{$_TABLES['users']}  "
          ."WHERE {$_TABLES['users']}.uid > 1 "
          ."AND {$_TABLES['users']}.status = 3 "        // Only Active Users will be shown
          ."AND {$_TABLES['users']}.uid = {$_TABLES['group_assignments']}.ug_uid "
          ."AND ({$_TABLES['group_assignments']}.ug_main_grp_id IN ({$groupList})) ";

    if ($show_fullname) {
        $sql .= "ORDER BY {$_TABLES['users']}.fullname ASC";
    } else {
        $sql .= "ORDER BY {$_TABLES['users']}.username ASC";
    }

    $query = DB_query($sql);
    while (list($uid,$username,$fullname) = DB_fetchArray($query)) {
        if ($selected == $uid) {
            $retval .= '<option value="'.$uid.'" SELECTED=selected>';
        } else {
            $retval .= '<option value="'.$uid.'">';
        }
        if ($show_fullname AND trim($fullname) !== '') {
            $retval .= $fullname . '</option>';
        } else {
            $retval .= $username . '</option>';
        }
    }
    return $retval;
}



/**
* Generates the HTML <select> options from a config variable
*
* @param        array       $confarra       Array of possible options in format array ( 1 => 'label A', 2=> 'label B');
* @param        string      $selected       Current selected value
* @return       string      $options        string of select options
*
* was nx_configOptionList
*/
function NXCOM_configOptionList($confarray,$selected='') {
    $options = '<option value="0">--</option>  ';
    foreach ($confarray as $key => $label) {
        if ($label != '') {
            if ($selected == $key) {
                $options .= '<option value="'.$key.'" SELECTED=selected>'.$label.'</option>';
            } else {
                $options .= '<option value="'.$key.'">'.$label.'</option>';
            }
        }
    }
    return $options;
}

/**
* Set the Radio button Option for a Yes/No input type field
* Assumes field is in the record set and that it has a value of 0 (no) or 1 (yes)
* Assumes the template variables are like {variablename_chkyes} and {variablename_chkno}
*
* @param        object      $template       Template object to set template variable
* @param        array       $variables      Array of fields in the template that are radio options for a yes/no selection
* @param        array       $record         Current record of fields to compare and set template option
* @return       Nothing                     Matching template variable set automatically
*
* was NXCOM_templateSetRadioYesNo
*/
function NXCOM_templateSetRadioYesNo(&$template,$variables,$record) {

    if (is_array($variables) AND is_array($record)) {
        foreach ($variables as $var) {
            if(array_key_exists($var,$record)) {
                if ($record[$var] == 1) {
                    $template->set_var("{$var}_chkyes",'CHECKED=checked');
                    $template->set_var("{$var}_chkno",'');
                } else {
                    $template->set_var("{$var}_chkno",'CHECKED=checked');
                    $template->set_var("{$var}_chkyes",'');
                }
            }
        }
    }
}

/**
* Set the Radio button Options using a config array to support multiple fields or non Yes/No options
* Field values are defined in the passed in configArray but should typically be integer values
* Assumes the template variables are like {variablename_chk1} and {variablename_chkx} - where x is the int value
*
* @param        object      $template       Template object to set template variable
* @param        array       $configArray    Config Array used to set field value and label
* @param        array       $record         Current record of fields to compare and set template option
* @return       Nothing                     Matching template variable set automatically
*
* was nx_templateSetRadioOptions
*/
function NXCOM_templateSetRadioOptions(&$template,$configArray,$record) {

    if (is_array($configArray) AND is_array($record)) {
        foreach ($configArray as $var => $options) {
            $options = explode(',',$options);
            if(array_key_exists($var,$record)) {
                foreach ($options as $value) {
                    $templatevar = "{$var}_chk{$value}";
                    if ($record[$var] == $value) {
                        $template->set_var($templatevar,'CHECKED=checked');
                    } else {
                        $template->set_var($templatevar,'');
                    }
                }
            }
        }
    }
}


// Common function to convert a timestamp to a display date format
// was nxDisplayDate
function NXCOM_displayDate($timestamp=0,$format='') {
    if ($timestamp <= 0) {
        $timestamp = time();
    }
    if ($format == '') $format = '%m/%d/%Y';
    return strftime($format,$timestamp);

}

/* Convert a text based date MM/DD/YYYY to a unix timestamp integer value */
function NXCOM_convertDate($date,$time='') {

        if (trim($date) == '') {
            return 0;
        }
        // Breakup the string using either a space, fwd slash, bkwd slash or colon as a delimiter
        $atok = strtok($date," /-\\:");
        while ($atok !== FALSE) {
            $atoks[] = $atok;
            $atok = strtok(" /-\\:");  // get the next token
        }
        if ($time == '') {
            $timestamp = mktime(0,0,0,$atoks[0],$atoks[1],$atoks[2]);
        } else {
            $btok = strtok($time," /-\\:");
            while ($btok !== FALSE) {
                $btoks[] = $btok;
                $btok = strtok(" /-\\:");
            }
            $timestamp = mktime($btoks[0],$btoks[1],$btoks[2],$atoks[0],$atoks[1],$atoks[2]);
        }
        return $timestamp;
}



function NXCOM_addslashes($var,$forcemode = false) {
    if (is_array($var)) {
        $PREP = array();
        foreach ($var as $key => $value) {
            if ($forcemode OR !get_magic_quotes_gpc()) {
                $PREP[$key] = addslashes($value);
            } else {
                $PREP[$key] = $value;
            }
        }

    } else {
        if ($forcemode OR !get_magic_quotes_gpc()) {
            $PREP = addslashes($var);
        } else {
            $PREP = $var;
        }
    }
    return $PREP;
}

function NXCOM_set_language_vars(&$template, $LANG) {
    foreach ($LANG as $key => $value) {
        $template->set_var('LANG_' . $key, $value);
    }
}

function NXCOM_set_common_vars(&$template) {
    global $_CONF;

    $template->set_var('site_url', $_CONF['site_url']);
    $template->set_var('site_admin_url', $_CONF['site_admin_url']);
    $template->set_var('layout_url', $_CONF['layout_url']);
}

//filters text passed in as $data
function NXCOM_filterText($data) {
    if (get_magic_quotes_gpc()) {
        $retval = strip_tags($data);
    } else {
        $retval = addslashes(strip_tags($data));
    }
    return $retval;
}


/* Simple helper function to wrap a string in quotes.
*    Used for example in an array_walk to build a SQL
*    array_walk($excludeGroups, 'wrap_each');
*    $excludeGroups = implode(',',$excludeGroups);
*    $sql .= "AND grp_name NOT IN ({$excludeGroups}) ";
*/
function wrap_each(&$item) {
    $item = "'" . addslashes($item) . "'";
}




/* Function to handle email'ing attachements
* @param        string       $to            Receiver, or receivers of the mail (email address). The formatting of this string must comply with RFC 2822.
* @param        string       $from          Who this message is from.
* @param        string       $subject       Subject for this email
* @param        string       $message       Body of message
* @param        string       $directory     Full path location to where the attachment you want to email is located
* @param        string       $filename      Name of the file to attach
* @return       boolean                     Returns TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
*
* RFC 2822 examples for $to and $from
    * user@example.com
    * user@example.com, anotheruser@example.com
    * User <user@example.com>
    * User <user@example.com>, Another User <anotheruser@example.com>

*/
function mail_attachment($to,$from,$subject,$message,$directory,$filename) {
    global $_CONF;

    $mimeboundary = 'x' . md5(time()) . 'x';

    $to=str_replace(",",",\n\t",$to);

    $header .= "From: {$from}\n";
    $header .= "Reply-To: {$from}\n";
    //$header  = "Return-Path: {$from}\n";
    $header .= "To: {$to}\n";

    $header .= "MIME-Version: 1.0\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"{$mimeboundary}\"\n\n";

    $body .= "--{$mimeboundary}\n";
    $body .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
    $body .= "Content-Transfer-Encoding:8bit\n\n";
    $body .= $message ."\n\n";

    $fullFilename = "{$directory}{$filename}";
    if (file_exists($fullFilename)) {
        $fileSize = filesize($fullFilename);
        $pos = strrpos($filename,'.') + 1;
        $fileType = strtolower(substr($filename, $pos));
        $fp = fopen($fullFilename,"rb");
        $fileData = fread($fp,$fileSize);
        fclose($fp);
        $data = chunk_split(base64_encode($fileData));
        $body .= "--{$mimeboundary}\n";
        $body .= "Content-Type: application/octet-stream; name=\"{$filename}\"\n";
        $body .= "Content-Transfer-Encoding: base64\n";
        $body .= "Content-Disposition: attachment; filename=\"{$filename}\"\n\n";
        $body .= $data."\n\n";
    }
    $body .= "--{$mimeboundary}--\n";
    $retval = mail("",$subject,$body,$header,"-f{$from}");

    return $retval;
 }

/**
* Takes an incoming 2 element array and makes their version number sizes identical.
*
* @param    array  $version_array an array with 2 elements which are version strings. e.g. array('1.1','1.2.0')
* @return   array  Returns the 2 elements in the array both with the identical number of major/minor/sub version designations.  False if not successful.
*
*/
function NXCOM_normalizeVersionNumbers($version_array){
    if(count($version_array)<2 || count($version_array)>2){
        return false;
    }
    $v1=$version_array[0];
    $v2=$version_array[1];

    $a1=explode(".",$v1);
    $a2=explode(".",$v2);

    if(count($a1) < count($a2)){  //less test nodes in the current version
        for($cntr=0;$cntr<(count($a2)-count($a1));$cntr++){
            $a1[]=0;
        }
    }elseif(count($a2) < count($a1)){
        for($cntr=0;$cntr<(count($a1)-count($a2));$cntr++){
            $a2[]=0;
        }
    }
    $v1=implode('.',$a1);
    $v2=implode('.',$a2);

    return array($v1,$v2);
}
?>