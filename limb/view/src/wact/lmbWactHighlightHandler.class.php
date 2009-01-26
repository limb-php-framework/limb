<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
@define('XML_HTMLSAX3', 'limb/view/lib/XML/');

require_once(XML_HTMLSAX3 . '/HTMLSax3.php');

/**
 * class lmbWactHighlightHandler.
 *
 * @package view
 * @version $Id$
 */
class lmbWactHighlightHandler
{
  protected $html = '';
  protected $current_tag = '';
  protected $self_closing_tag = false;
  protected $template_path_history = array();
  protected $tag_dictionary = null;
  protected $highlight_page_url;

  function __construct($tag_dictionary, $highlight_page_url)
  {
    $this->tag_dictionary = $tag_dictionary;
    $this->highlight_page_url = $highlight_page_url;
  }

  function setTemplatePathHistory($history)
  {
    $this->template_path_history = $history;
  }

  function writeAttributes($attributes)
  {
    if(!is_array($attributes))
      return;

    foreach ($attributes as $name => $value)
    {
      $name_html = $name;
      $value_html = $value;

      if($this->tag_dictionary->getWactTagInfo($this->current_tag))
      {
        $name_html = "<span class='wact_attr'>{$name}</span>";
        $value_html = "<span class='wact_attr_name'>{$value}</span>";
      }

      if($this->current_tag == 'core:wrap' ||  $this->current_tag == 'core:include')
      {
        if($name == 'file')
        {
          $history = array();
          $history = $this->template_path_history;
          $history[] = $value;

          $history_string = 't[]=' . implode('&t[]=', $history);

          $href = $this->highlight_page_url . "?{$history_string}";

          $value_html = "<a class='template_path' href={$href}>{$value}</a>";
        }
      }

      $this->html .= ' ' . $name_html . '="' . $value_html . '"';
    }
  }

  function openHandler($parser, $name, $attrs)
  {
    if($this->self_closing_tag)
      $this->html .= '&gt;';

    $this->self_closing_tag = true;

    $this->current_tag = strtolower($name);

    if($this->tag_dictionary->getWactTagInfo($name))
      $this->html .= '&lt;<span class="wact_tag">' . $name . '</span>';
    else
      $this->html .= '&lt;<span class="html_tag">' . $name . '</span>';

    $this->writeAttributes($attrs);
  }

  function closeHandler($parser, $name)
  {
    if($this->self_closing_tag)
    {
      $this->html .= '/&gt;';
      $this->self_closing_tag = false;
      return;
    }

    if($this->tag_dictionary->getWactTagInfo($name))
      $this->html .= '&lt;/<span class="wact_tag">' . $name . '</span>&gt;';
    else
      $this->html .= '&lt;/<span class="html_tag">' . $name . '</span>&gt;';
  }

  function dataHandler($parser, $data)
  {
    if($this->self_closing_tag)
    {
      $this->self_closing_tag = false;
      $this->html .= '&gt;';
    }

    $data = str_replace("\t", '  ', $data);
    $this->html .= $data;
  }

  function escapeHandler($parser, $data)
  {
    $this->html .= '<span class="comment">&lt;!' . $data . '&gt;</span>';
  }

  function getHtml()
  {
    $this->html = preg_replace('~(\{(\$|\^|#)[^\}]+\})~', "<span class='expression'>\\1</span>", $this->html);

    $lines = preg_split( "#\r\n|\r|\n#", $this->html);

    $content = '';
    $max = sizeof($lines);
    $digits = strlen("{$max}");

    for($i=0; $i < $max; $i++)
    {
      $j = $i + 1;
      $content .= "<span class='line_number'>{$j}" . str_repeat('&nbsp;', $digits - strlen("{$j}")) . "</span> " .  $lines[$i] . "\n";
    }

    return $content;
  }
}

