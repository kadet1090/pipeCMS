<?php
/**
 * HTMLTidy - HTML Parser and Optimiser
 *
 * HTML Parser class
 *
 * Copyright 2009 Nikolay Matsiesvky
 *
 * This file is part of HTMLTidy.
 *
 *   HTMLTidy is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU Lesser General Public License as published by
 *   the Free Software Foundation; either version 2.1 of the License, or
 *   (at your option) any later version.
 *
 *   HTMLTidy is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Lesser General Public License for more details.
 * 
 *   You should have received a copy of the GNU Lesser General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package htmltidy
 * @author Nikolay Matsievsky (speed at webo dot name) 2009
 */
 
 /**
 * HTML Parser class
 *
 
 * This class represents a HTML parser which reads HTML code and saves it in an array.
 * In opposite to most other HTML parsers, it does not use regular expressions and
 * thus has higher reliability.
 * @package htmltidy
 * @author Nikolay Matsievsky (speed at webo dot name) 2009
 * @version 0.1
 */
 
 class htmltidy {
 
 /**
 * Saves the parsed HTML
 * @var array
 * @access public
 */
﻿  var $html = array();
 
/**
 * Contains the version of htmltidy
 * @var string
 * @access private
 */
﻿  var $version = '0.1';

/**
 * Stores the settings
 * @var array
 * @access private
 */
﻿  var $settings = array();

/**
 * Saves the parser-status.
 *
 * Possible values:
 * - in = in content
 * - it = in tag
 * - tt = parsing tag name
 * - ia = in attribute
 * - aa = parsing attribute name
 * - iv = in value / parsing value
 * - ic = in comment (ignore everything)
 *
 * @var string
 * @access private
 */
﻿  var $status = 'in';

/**
 * Saves the content between tags
 * @var string
 * @access private
 */
﻿  var $content = false;

/**
 * Saves the current tag
 * @var string
 * @access private
 */
﻿  var $tag = false;

/**
 * Saves raw HTML code of tag
 * @var string
 * @access private
 */
﻿  var $raw_tag = '';

/**
 * Saves the last tag
 * @var string
 * @access private
 */
﻿  var $last_tag = false;

/**
 * Saves the current attribute
 * @var string
 * @access private
 */
﻿  var $attribute = '';

/**
 * Saves the current quotes in attribute value
 * @var string
 * @access private
 */
﻿  var $quotes = '';

/**
 * Saves the current value
 * @var string
 * @access private
 */
﻿  var $value = '';

/**
 * Sets default settings
 * @access private
 * @version 0.1
 */
﻿  function htmltidy() {
﻿  ﻿  $this->settings['tag_lower_case'] = 1;
﻿  ﻿  $this->settings['attribute_lower_case'] = 1;
﻿  ﻿  $this->settings['allowed_tags'] = array('script', 'link', 'style');
﻿  }

/**
 * Parses HTML in $string. The code is saved as array in $this->html
 * @param string $string the HTML code
 * @access public
 * @return bool
 * @version 0.1
 */
﻿  function parse ($string) {
﻿  ﻿  $string .= " ";
﻿  ﻿  for ($i = 0, $size = strlen($string); $i < $size; $i++ ) {
﻿  ﻿  ﻿  $s = $string{$i};
﻿  ﻿  ﻿  switch ($this->status) {
﻿  ﻿  ﻿  ﻿  /* case in comment */
﻿  ﻿  ﻿  ﻿  case 'ic':
﻿  ﻿  ﻿  ﻿  ﻿  /* end of comment */
﻿  ﻿  ﻿  ﻿  ﻿  if ($s === '-' && @$string{$i+1} === '-' && @$string{$i+2} === '>') {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $i+=2;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->statis = 'in';
﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  /* case in content */
﻿  ﻿  ﻿  ﻿  case 'in':
﻿  ﻿  ﻿  ﻿  ﻿  /* save content within tags */
﻿  ﻿  ﻿  ﻿  ﻿  if ($this->tag) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  if ($this->settings['tag_lower_case']) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag['tag'] = strtolower($this->tag['tag']);
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  if (in_array($this->tag['tag'], $this->settings['allowed_tags'])) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  /* tag is self closed, just add */
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  if ($this->tag['selfclosed']) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag['raw'] = $this->tag_raw;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->html[] = $this->tag;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag_raw = '';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  /* tag is with the closed tag, add content */
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  } elseif ($this->tag['closed'] && $this->tag['tag'] === $this->last_tag['tag']) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  if (trim($this->content)) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->last_tag['content'] = $this->content;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->last_tag['raw'] .= $this->content . $this->tag_raw;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->html[] = $this->last_tag;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->content = '';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->last_tag = false;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag_raw = '';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  /* or don't allow embedded tags  */
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  } else {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->last_tag = $this->tag;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->last_tag['raw'] = $this->tag_raw;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag_raw = '';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag = false;
﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  /* beginning of tag? */
﻿  ﻿  ﻿  ﻿  ﻿  if ($s === '<') {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  if (@$string{$i+1} === '!') {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  /* beginning of comment */
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  if (@$string{$i+2} === '-' && @$string{$i+3} === '-') {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->status = 'ic';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  } else {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->content .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  } else {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag_raw .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->status = 'tt';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag = array();
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  /* or just content? */
﻿  ﻿  ﻿  ﻿  ﻿  } else {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->content .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  /* parsing tag */
﻿  ﻿  ﻿  ﻿  case 'tt':
﻿  ﻿  ﻿  ﻿  ﻿  switch ($s) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "/":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case '>':
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $i--;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->status = 'it';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "\t":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "\n":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "\r":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case " ":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag_raw .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->status = 'it';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  default:
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag_raw .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag['tag'] .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  /* case in tag */
﻿  ﻿  ﻿  ﻿  case 'it':
﻿  ﻿  ﻿  ﻿  ﻿  $this->tag_raw .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  switch ($s) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case " ":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "\n":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "\t":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "\r":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case '/':
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  if (@$string{$i+1} === '>') {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag['selfclosed'] = true;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  } elseif (@$string{$i-1} === '<') {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag['closed'] = true;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->status = 'tt';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case '>':
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->status = 'in';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  /* null content for open tags */
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  if (!$this->tag['closed']) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->content = '';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  default:
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->status = 'aa';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->attribute .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  /* parsing attrinute */
﻿  ﻿  ﻿  ﻿  case 'aa':
﻿  ﻿  ﻿  ﻿  ﻿  switch ($s) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case ">":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "/":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "=":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $i--;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->status = 'ia';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case " ":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "\t":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "\n":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "\r":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag_raw .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->status = 'ia';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  default:
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag_raw .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->attribute .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  /* case in attribute */
﻿  ﻿  ﻿  ﻿  case 'ia':
﻿  ﻿  ﻿  ﻿  ﻿  switch ($s) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case " ":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "\n":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "\t":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "\r":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag_raw .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "=":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag_raw .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->status = 'iv';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  /* attribute w/o value */
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  default:
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  if ($this->settings['attribute_lower_case']) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->attribute = strtolower($this->attribute);
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag[$this->attribute] = $this->attribute;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  /* return to the previous step */
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->status = 'it';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $i--;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  /* case in value */
﻿  ﻿  ﻿  ﻿  case 'iv':
﻿  ﻿  ﻿  ﻿  ﻿  $this->tag_raw .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  switch ($s) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  /* value begins and ends */
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case " ":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case '"':
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  case "'":
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  if (empty($this->quotes)) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->quotes = $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  } else {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  if ($s === $this->quotes) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  if ($this->settings['attribute_lower_case']) {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->attribute = strtolower($this->attribute);
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->tag[$this->attribute] = $this->value;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->value = '';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->quotes = '';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->attribute = '';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->status = 'it';
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  } else {
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->value .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  /* value continues */
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  default:
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  $this->value .= $s;
﻿  ﻿  ﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  ﻿  ﻿  }
﻿  ﻿  ﻿  ﻿  ﻿  break;
﻿  ﻿  ﻿  }
﻿  ﻿  }
﻿  ﻿  return true;
﻿  }

}
?>