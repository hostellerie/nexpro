<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | nexPro Plugin v2.0.1 for the nexPro Portal Server                         |
// | December 2009                                                             |
// | Developed by Nextide Inc. as part of the nexPro suite - www.nextide.ca    |
// +---------------------------------------------------------------------------+
// | multiupload.class.php                                                     |
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
// $Id: multiupload.class.php,v 1.1 2009/03/12 16:27:48 blaine.lang Exp $

/**
* This class will allow you to use the multiple upload component so a user
* can select more than one file at a time in the browse dialog.
*
* @author       Eric de la Chevrotiere <Eric.delaChevrotiere@nextide.ca>
*
*/
class multiupload
{
    var $_upload_handler;
    var $_file_types;
    var $_file_types_desc;
    var $_file_types_allowed;
    var $_file_size_limit;
    var $_file_upload_limit;
    var $_file_queue_limit;
	var $_template_variables_for_substitution=array();


    function multiupload($upload_handler, $file_size_limit='100 MB', $file_types='*.*', $file_types_desc='All Files', $file_upload_limit=1000, $file_queue_limit=0) {
        $this->_upload_handler = $upload_handler;
        $this->_file_types_allowed = $file_types_allowed;
        $this->_file_types = $file_types;
        $this->_file_types_desc = $file_types_desc;
        $this->_file_size_limit = $file_size_limit;
        $this->_file_upload_limit = $file_upload_limit;
        $this->_file_queue_limit = $file_queue_limit;
    }

    function getFormHTML($templatePath='',$templateFile='') {
        global $_CONF;

        if($templatePath=='') $templatePath=$_CONF['path_html'] . 'multiupload';
        if($templateFile=='') $templateFile='component.thtml';

        $p = new Template($templatePath);
        $p->set_file('component', $templateFile);
        $p->set_var('site_url', $_CONF['site_url']);
        $p->set_var('upload_handler', $this->_upload_handler);
        $p->set_var('file_types', $this->_file_types);
        $p->set_var('file_types_desc', $this->_file_types_desc);
        $p->set_var('file_size_limit', $this->_file_size_limit);
        $p->set_var('file_upload_limit', $this->_file_upload_limit);
        $p->set_var('file_queue_limit', $this->_file_queue_limit);

        $p->set_var($this->_template_variables_for_substitution);

        $p->parse('output', 'component');
        return $p->finish($p->get_var('output'));
    }

    function setArrayTemplateVars($arr){
	    $this->_template_variables_for_substitution=$arr;
    }
}

?>