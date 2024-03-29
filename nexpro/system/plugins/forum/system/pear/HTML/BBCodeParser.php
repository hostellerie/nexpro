<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Stijn de Reede <sjr@gmx.co.uk>                               |
// +----------------------------------------------------------------------+
//
// $Id: BBCodeParser.php,v 1.1 2006/08/15 00:09:57 blaine.lang Exp $
//

/**
* @package  HTML_BBCodeParser
* @author   Stijn de Reede  <sjr@gmx.co.uk>
*
*
* This is a parser to replace UBB style tags with their html equivalents. It
* does not simply do some regex calls, but is complete stack based
* parse engine. This ensures that all tags are properly nested, if not,
* extra tags are added to maintain the nesting. This parser should only produce
* xhtml 1.0 compliant code. All tags are validated and so are all their attributes.
* It should be easy to extend this parser with your own tags, see the _definedTags
* format description below.
*
*
* Usage:
* $parser = new HTML_BBCodeParser();
* $parser->setText('normal [b]bold[/b] and normal again');
* $parser->parse();
* echo $parser->getParsed();
* or:
* $parser = new HTML_BBCodeParser();
* echo $parser->qparse('normal [b]bold[/b] and normal again');
* or:
* echo HTML_BBCodeParser::staticQparse('normal [b]bold[/b] and normal again');
*
*
* Setting the options from the ini file:
* $config = parse_ini_file('BBCodeParser.ini', true);
* $options = &PEAR::getStaticProperty('HTML_BBCodeParser', '_options');
* $options = $config['HTML_BBCodeParser'];
* unset($options);
*
*
* The _definedTags variables should be in this format:
* array('tag'                                // the actual tag used
*           => array('htmlopen'  => 'open',  // the opening tag in html
*                    'htmlclose' => 'close', // the closing tag in html,
*                                               can be set to an empty string
*                                               if no closing tag is present
*                                               in html (like <img>)
*                    'allowed'   => 'allow', // tags that are allowed inside
*                                               this tag. Values can be all
*                                               or none, or either of these
*                                               two, followed by a ^ and then
*                                               followed by a comma seperated
*                                               list of exceptions on this
*                    'attributes' => array() // an associative array containing
*                                               the tag attributes and their
*                                               printf() html equivalents, to
*                                               which the first argument is
*                                               the value, and the second is
*                                               the quote. Default would be
*                                               something like this:
*                                               'attr' => 'attr=%2$s%1$s%2$s'
*                   ),
*       'etc'
*           => (...)
*       )
*
*
*/


require_once ('PEAR.php');




class HTML_BBCodeParser
{

    /**
    * An array of tags parsed by the engine, should be overwritten by filters
    *
    * @access   private
    * @var      array
    */
    var $_definedTags  = array();

    /**
    * A string containing the input
    *
    * @access   private
    * @var      string
    */
    var $_text          = '';

    /**
    * A string containing the preparsed input
    *
    * @access   private
    * @var      string
    */
    var $_preparsed     = '';

    /**
    * An array tags and texts build from the input text
    *
    * @access   private
    * @var      array
    */
    var $_tagArray      = array();

    /**
    * A string containing the parsed version of the text
    *
    * @access   private
    * @var      string
    */
    var $_parsed        = '';

    /**
    * An array of options, filled by an ini file or through the contructor
    *
    * @access   private
    * @var      array
    */
    var $_options = array(  'quotestyle'    => 'single',
                            'quotewhat'     => 'all',
                            'open'          => '[',
                            'close'         => ']',
                            'xmlclose'      => true,
                            'filters'       => 'Basic'
                         );

    /**
    * An array of filters used for parsing
    *
    * @access   private
    * @var      array
    */
    var $_filters       = array();




    /**
    * Constructor, initialises the options and filters
    *
    * Sets the private variable _options with base options defined with
    * &PEAR::getStaticProperty(), overwriting them with (if present)
    * the argument to this method.
    * Then it sets the extra options to properly escape the tag
    * characters in preg_replace() etc. The set options are
    * then stored back with &PEAR::getStaticProperty(), so that the filter
    * classes can use them.
    * All the filters in the options are initialised and their defined tags
    * are copied into the private variable _definedTags.
    *
    * @param    array           options to use, can be left out
    * @return   none
    * @access   public
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function HTML_BBCodeParser($options = array())
    {

        /* set the already set options */
        $baseoptions = &PEAR::getStaticProperty('HTML_BBCodeParser', '_options');
        if (is_array($baseoptions)) {
            foreach ($baseoptions as  $k => $v)  {
                $this->_options[$k] = $v;
            }
        }

        /* set the options passed as an argument */
        foreach ($options as $k => $v )  {
           $this->_options[$k] = $v;
        }

        /* add escape open and close chars to the options for preg escaping */
        $preg_escape = '\^$.[]|()?*+{}';
        if (strstr($preg_escape, $this->_options['open'])) {
            $this->_options['open_esc'] = "\\".$this->_options['open'];
        } else {
            $this->_options['open_esc'] = $this->_options['open'];
        }
        if (strstr($preg_escape, $this->_options['close'])) {
            $this->_options['close_esc'] = "\\".$this->_options['close'];
        } else {
            $this->_options['close_esc'] = $this->_options['close'];
        }

        /* set the options back so that child classes can use them */
        $baseoptions = $this->_options;
        unset($baseoptions);

        /* return if this is a subclass */
        if (is_subclass_of($this, 'HTML_BBCodeParser')) return;

        /* extract the definedTags from subclasses */
        $filters = explode(',', $this->_options['filters']);
        foreach ($filters as $filter) {
            $class = 'HTML_BBCodeParser_Filter_'.$filter;
            @include_once ('HTML/BBCodeParser/Filter/'.$filter.'.php');
            if (!class_exists($class)) {
                PEAR::raiseError("Failed to load filter $filter", null, PEAR_ERROR_DIE);
            }
            $this->_filters[$filter] = new $class;
            $this->_definedTags = array_merge($this->_definedTags, $this->_filters[$filter]->_definedTags);
        }
    }




    /**
    * Executes statements before the actual array building starts
    *
    * This method should be overwritten in a filter if you want to do
    * something before the parsing process starts. This can be useful to
    * allow certain short alternative tags which then can be converted into
    * proper tags with preg_replace() calls.
    * The main class walks through all the filters and and calls this
    * method. The filters should modify their private $_preparsed
    * variable, with input from $_text.
    *
    * @return   none
    * @access   private
    * @see      $_text
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function _preparse()
    {
        /* default: assign _text to _preparsed, to be overwritten by filters */
        $this->_preparsed = $this->_text;

        /* return if this is a subclass */
        if (is_subclass_of($this, 'HTML_BBCodeParser')) return;

        /* walk through the filters and execute _preparse */
        foreach ($this->_filters as $filter) {
            $filter->setText($this->_preparsed);
            $filter->_preparse();
            $this->_preparsed = $filter->getPreparsed();
        }
    }




    /**
    * Builds the tag array from the input string $_text
    *
    * An array consisting of tag and text elements is contructed from the
    * $_preparsed variable. The method uses _buildTag() to check if a tag is
    * valid and to build the actual tag to be added to the tag array.
    *
    * TODO: - rewrite whole method, as this one is old and probably slow
    *       - see if a recursive method would be better than an iterative one
    *
    * @return   none
    * @access   private
    * @see      _buildTag()
    * @see      $_text
    * @see      $_tagArray
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function _buildTagArray()
    {
        $this->_tagArray = array();
        $str = $this->_preparsed;
        $strPos = 0;
        $strLength = strlen($str);

        while ( ($strPos < $strLength) ) {
            $tag = array();
            $openPos = strpos($str, $this->_options['open'], $strPos);
            if ($openPos === false) {
                $openPos = $strLength;
                $nextOpenPos = $strLength;
            }
            if ($openPos + 1 > $strLength) {
                $nextOpenPos = $strLength;
            } else {
                $nextOpenPos = strpos($str, $this->_options['open'], $openPos + 1);
                if ($nextOpenPos === false) {
                    $nextOpenPos = $strLength;
                }
            }
            $closePos = strpos($str, $this->_options['close'], $strPos);
            if ($closePos === false) {
                $closePos = $strLength + 1;
            }

            if ( $openPos == $strPos ) {
                if ( ($nextOpenPos < $closePos) ) {
                    /* new open tag before closing tag: treat as text */
                    $newPos = $nextOpenPos;
                    $tag['text'] = substr($str, $strPos, $nextOpenPos - $strPos);
                    $tag['type'] = 0;
                } else {
                    /* possible valid tag */
                    $newPos = $closePos + 1;
                    $newTag = $this->_buildTag(substr($str, $strPos, $closePos - $strPos + 1));
                    if ( ($newTag !== false) ) {
                        $tag = $newTag;
                    } else {
                    /* no valid tag after all */
                        $tag['text'] = substr($str, $strPos, $closePos - $strPos + 1);
                        $tag['type'] = 0;
                    }
                }
            } else {
                /* just text */
                $newPos = $openPos;
                $tag['text'] = substr($str, $strPos, $openPos - $strPos);
                $tag['type'] = 0;
            }

            /* join 2 following text elements */
            if ($tag['type'] === 0 && isset($prev) && $prev['type'] === 0) {
                $tag['text'] = $prev['text'].$tag['text'];
                array_pop($this->_tagArray);
            }

            $this->_tagArray[] = $tag;
            $prev = $tag;
            $strPos = $newPos;
        }
    }




    /**
    * Builds a tag from the input string
    *
    * This method builds a tag array based on the string it got as an
    * argument. If the tag is invalid, <false> is returned. The tag
    * attributes are extracted from the string and stored in the tag
    * array as an associative array.
    *
    * @param    string          string to build tag from
    * @return   array           tag in array format
    * @access   private
    * @see      _buildTagArray()
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function _buildTag($str)
    {
        $tag = array('text' => $str, 'attributes' => array());

        if (substr($str, 1, 1) == '/') {        /* closing tag */

            $tag['tag'] = substr($str, 2, strlen($str) - 3);
            if ( (in_array($tag['tag'], array_keys($this->_definedTags)) == false) ) {
                return false;                   /* nope, it's not valid */
            } else {
                $tag['type'] = 2;
                return $tag;
            }
        } else {                                /* opening tag */

            $tag['type'] = 1;
            if ( (strpos($str, ' ') == true) && (strpos($str, '=') == false) ) {
                return false;                   /* nope, it's not valid */
            }

            /* tnx to Onno for the regex
               split the tag with arguments and all */
            $oe = $this->_options['open_esc'];
            $ce = $this->_options['close_esc'];
            if (preg_match("!$oe([a-z]+)[^$ce]*$ce!i", $str, $tagArray) == 0) {
                return false;
            }
            $tag['tag'] = $tagArray[1];
            if ( (in_array($tag['tag'], array_keys($this->_definedTags)) == false) ) {
                return false;                   /* nope, it's not valid */
            }

            /* tnx to Onno for the regex
               validate the arguments */
            preg_match_all("![\s$oe]([a-z]+)=([^\s$ce]+)(?=[\s$ce])!i", $str, $attributeArray, PREG_SET_ORDER);
            foreach ($attributeArray as $attribute) {
                if ( (in_array($attribute[1], array_keys($this->_definedTags[$tag['tag']]['attributes'])) == true) ) {
                    $tag['attributes'][$attribute[1]] = $attribute[2];
                }
            }
            return $tag;
        }
    }




    /**
    * Validates the tag array, regarding the allowed tags
    *
    * While looping through the tag array, two following text tags are
    * joined, and it is checked that the tag is allowed inside the
    * last opened tag.
    * By remembering what tags have been opened it is checked that
    * there is correct (xml compliant) nesting.
    * In the end all still opened tags are closed.
    *
    * @return   none
    * @access   private
    * @see      _isAllowed()
    * @see      $_tagArray
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function _validateTagArray()
    {
        $newTagArray = array();
        $openTags = array();
        foreach ($this->_tagArray as $tag) {
            $prevTag = end($newTagArray);
            switch ($tag['type']) {
            case 0:
                if ($prevTag['type'] === 0) {
                    $tag['text'] = $prevTag['text'].$tag['text'];
                    array_pop($newTagArray);
                }
                $newTagArray[] = $tag;
                break;

            case 1:
                if ($this->_isAllowed(end($openTags), $tag['tag']) == false) {
                    $tag['type'] = 0;
                    if ($prevTag['type'] === 0) {
                        $tag['text'] = $prevTag['text'].$tag['text'];
                        array_pop($newTagArray);
                    }
                } else {
                    $openTags[] = $tag['tag'];
                }
                $newTagArray[] = $tag;
                break;

            case 2:
                if ( ($this->_isAllowed(end($openTags), $tag['tag']) == true) || ($tag['tag'] == end($openTags)) ) {
                    if (in_array($tag['tag'], $openTags)) {
                        $tmpOpenTags = array();
                        while (end($openTags) != $tag['tag']) {
                            $newTagArray[] = $this->_buildTag('[/'.end($openTags).']');
                            $tmpOpenTags[] = end($openTags);
                            array_pop($openTags);
                        }
                        $newTagArray[] = $tag;
                        array_pop($openTags);
                        while (end($tmpOpenTags)) {
                            $tmpTag = $this->_buildTag('['.end($tmpOpenTags).']');
                            $newTagArray[] = $tmpTag;
                            $openTags[] = $tmpTag['tag'];
                            array_pop($tmpOpenTags);
                        }
                    }
                } else {
                    $tag['type'] = 0;
                    if ($prevTag['type'] === 0) {
                        $tag['text'] = $prevTag['text'].$tag['text'];
                        array_pop($newTagArray);
                    }
                    $newTagArray[] = $tag;
                }
                break;
            }
        }
        while (end($openTags)) {
            $newTagArray[] = $this->_buildTag('[/'.end($openTags).']');
            array_pop($openTags);
        }
        $this->_tagArray = $newTagArray;
    }




    /**
    * Checks to see if a tag is allowed inside another tag
    *
    * The allowed tags are extracted from the private _definedTags array.
    *
    * @param    array           tag that is on the outside
    * @param    array           tag that is on the inside
    * @return   boolean         return true if the tag is allowed, false
    *                           otherwise
    * @access   private
    * @see      _validateTagArray()
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function _isAllowed($out, $in)
    {
        if (!$out)                                          return true;
        if ($this->_definedTags[$out]['allowed'] == 'all')  return true;
        if ($this->_definedTags[$out]['allowed'] == 'none') return false;

        $ar = explode('^', $this->_definedTags[$out]['allowed']);
        $tags = explode(',', $ar[1]);
        if ($ar[0] == 'none' && in_array($in, $tags))       return true;
        if ($ar[0] == 'all'  && in_array($in, $tags))       return false;
        return false;
    }




    /**
    * Builds a parsed string based on the tag array
    *
    * The correct html and atribute values are extracted from the private
    * _definedTags array.
    *
    * @return   none
    * @access   private
    * @see      $_tagArray
    * @see      $_parsed
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function _buildParsedString()
    {
        $this->_parsed = '';
        foreach ($this->_tagArray as $tag) {
            switch ($tag['type']) {

            /* just text */
            case 0:
                $this->_parsed .= $tag['text'];
                break;

            /* opening tag */
            case 1:
                $this->_parsed .= '<'.$this->_definedTags[$tag['tag']]['htmlopen'];
                if ($this->_options['quotestyle'] == 'single') $q = "'";
                if ($this->_options['quotestyle'] == 'double') $q = '"';
                foreach ($tag['attributes'] as $a => $v) {
                    if (    ($this->_options['quotewhat'] == 'nothing') ||
                            ($this->_options['quotewhat'] == 'strings') && (is_numeric($v)) ) {
                        $this->_parsed .= ' '.sprintf($this->_definedTags[$tag['tag']]['attributes'][$a], $v, '');
                    } else {
                        $this->_parsed .= ' '.sprintf($this->_definedTags[$tag['tag']]['attributes'][$a], $v, $q);
                    }
                }
                if ($this->_definedTags[$tag['tag']]['htmlclose'] == '' && $this->_options['xmlclose']) {
                    $this->_parsed .= ' /';
                }
                $this->_parsed .= '>';
                break;

            /* closing tag */
            case 2:
                if ($this->_definedTags[$tag['tag']]['htmlclose'] != '') {
                    $this->_parsed .= '</'.$this->_definedTags[$tag['tag']]['htmlclose'].'>';
                }
                break;
            }
        }

    }




    /**
    * Sets text in the object to be parsed
    *
    * @param    string          the text to set in the object
    * @return   none
    * @access   public
    * @see      getText()
    * @see      $_text
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function setText($str)
    {
        $this->_text = $str;
    }




    /**
    * Gets the unparsed text from the object
    *
    * @return   string          the text set in the object
    * @access   public
    * @see      setText()
    * @see      $_text
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function getText()
    {
        return $this->_text;
    }




    /**
    * Gets the preparsed text from the object
    *
    * @return   string          the text set in the object
    * @access   public
    * @see      _preparse()
    * @see      $_preparsed
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function getPreparsed()
    {
        return $this->_preparsed;
    }




    /**
    * Gets the parsed text from the object
    *
    * @return   string          the parsed text set in the object
    * @access   public
    * @see      parse()
    * @see      $_parsed
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function getParsed()
    {
        return $this->_parsed;
    }




    /**
    * Parses the text set in the object
    *
    * @return   none
    * @access   public
    * @see      _preparse()
    * @see      _buildTagArray()
    * @see      _validateTagArray()
    * @see      _buildParsedString()
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function parse()
    {
        $this->_preparse();
        $this->_buildTagArray();
        $this->_validateTagArray();
        $this->_buildParsedString();
    }




    /**
    * Quick method to do setText(), parse() and getParsed at once
    *
    * @return   none
    * @access   public
    * @see      parse()
    * @see      $_text
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function qparse($str)
    {
        $this->_text = $str;
        $this->parse();
        return $this->_parsed;
    }




    /**
    * Quick static method to do setText(), parse() and getParsed at once
    *
    * @return   none
    * @access   public
    * @see      parse()
    * @see      $_text
    * @author   Stijn de Reede  <sjr@gmx.co.uk>
    */
    function staticQparse($str)
    {
        $p = new HTML_BBCodeParser();
        $str = $p->qparse($str);
        unset($p);
        return $str;
    }


}


?>
