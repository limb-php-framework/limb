<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * HTML/XHTML/XML parser
 * @package wact
 * @version $Id: WactHTMLParser.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactHTMLParser
{
  protected $file_name;
  /**
  * Parser listener
  * @var WactHTMLParserListener
  */
  protected $observer;
  /**
  * XML document being parsed
  * @var string
  */
  protected $rawtext;
  /**
  * Position in XML document relative to start (0)
  * @var int
  */
  protected $position;
  /**
  * Length of the XML document in characters
  * @var int
  */
  protected $length;

  protected $element_pos;

  /**
  * @var Observer event handler
  * @access protected
  */
  function __construct($observer)
  {
    $this->observer = $observer;
  }

  /*
  * Calculates the line number from the byte index
  * @return int the current line number
  * @access private
  */
  function getLineNumber()
  {
    return 1 + substr_count(substr($this->rawtext, 0, $this->position), "\n");
  }

  function getFile()
  {
    return $this->file_name;
  }

  function getCurrentLocation()
  {
    return new WactSourceLocation($this->getFile(), $this->getLineNumber());
  }

  /**
  * Moves the position forward past any whitespace characters
  * @access protected
  * @return void
  */
  function ignoreWhitespace()
  {
    while ($this->position < $this->length &&
        strpos(" \n\r\t", $this->rawtext{$this->position}) !== FALSE)
    {
      $this->position++;
    }
  }

  /**
  * Begins the parsing operation, setting up any decorators, depending on
  * parse options invoking _parse() to execute parsing
  * @param string XML document to parse
  * @access protected
  * @return void
  */
  function parse($data, $file_name = NULL)
  {
    $this->rawtext = $data;
    $this->length = strlen($data);
    $this->position = 0;
    $this->file_name = $file_name;

    do
    {
      $start = $this->position;
      $this->position = strpos($this->rawtext, '<', $start);
      if ($this->position === FALSE)
      {
        if ($start < $this->length)
          $this->observer->characters(substr($this->rawtext, $start), $this->getCurrentLocation());
        return;
      }

      // any text before < considered as characters
      if ($this->position > $start)
      {
        $characters = substr($this->rawtext, $start, $this->position - $start);
        $this->observer->characters($characters, $this->getCurrentLocation());
      }

      $this->position += 1;   // ignore '<' character

      if ($this->_reachedEndOfFile())
        return;

      $this->element_pos = $this->position;
      $this->position += 1;

      switch($this->rawtext{$this->element_pos})
      {
        // </tag> cases
        case '/':
          $start = $this->position;
          while ($this->position < $this->length && $this->rawtext{$this->position} != '>')
              $this->position++;

          if ($this->_reachedEndOfFile())
            return;

          $tag = substr($this->rawtext, $start, $this->position - $start);

          $this->observer->endTag($tag, $this->getCurrentLocation());
          $this->position += 1;   // ignore '>' string
          break;
        // <?php cases
        case '?':
          $start = $this->position;

          // search instruction type
          while ($this->position < $this->length && strpos(" \n\r\t", $this->rawtext{$this->position}) === FALSE)
            $this->position++;

          if ($this->_reachedEndOfFile())
            return;

          $instruction_type = substr($this->rawtext, $start, $this->position - $start);

          $this->ignoreWhitespace();

          // search instruction end and thus the instruction code
          $start = $this->position;
          $this->position = strpos($this->rawtext, '?>', $start);

          if ($this->position === FALSE)
          {
            $this->observer->characters(substr($this->rawtext, $this->element_pos - 1), $this->getCurrentLocation());
            return;
          }

          $code = substr($this->rawtext, $start, $this->position - $start);
          $this->observer->instruction($instruction_type, $code, $this->getCurrentLocation());

          $this->position += 2;   // ignore '? >' string
          break;
        // <!-- and <% cases
        case '!':
          $start = $this->position - 2;

          if (substr($this->rawtext, $start, 4) == "<!--")
          {
            $position = strpos($this->rawtext, '-->', $start);
            if ($position !== FALSE)
            {
              $raw_text = substr($this->rawtext, $start, $position - $start + 3);
              $this->observer->characters($raw_text, $this->getCurrentLocation());
              $this->position = $position + 3;
              break;
            }
          }

          while ($this->position < $this->length && $this->rawtext{$this->position} != '<')
              $this->position++;

          $characters = substr($this->rawtext, $start, $this->position - $start);
          $this->observer->characters($characters, $this->getCurrentLocation());
          break;
        case '%':
          $start = $this->position - 2;
          while ($this->position < $this->length && $this->rawtext{$this->position} != '<')
              $this->position++;

          $characters = substr($this->rawtext, $start, $this->position - $start);
          $this->observer->characters($characters, $this->getCurrentLocation());
          break;
        // <tag or any < case (e.g. compare operator in javascript block)
        case ' ':
        case "\n":
        case "\n":
        case "\r":
        case "\t":
        case "=":
          $start = $this->position - 2;
          while ($this->position < $this->length && $this->rawtext{$this->position} != '<')
              $this->position++;
          $characters = substr($this->rawtext, $start, $this->position - $start);
          $this->observer->characters($characters, $this->getCurrentLocation());
          break;
        default:
          while ($this->position < $this->length && strpos("/> \n\r\t", $this->rawtext{$this->position}) === FALSE) {
            $this->position++;
          }

          if ($this->_reachedEndOfFile())
            return;

          $tag = substr($this->rawtext, $this->element_pos, $this->position - $this->element_pos);
          $Attributes = array();

          $this->ignoreWhitespace();

          // search end of tag
          while ( $this->position < $this->length &&
                  $this->rawtext{$this->position} != '/' &&
                  $this->rawtext{$this->position} != '>')
          {
              $start = $this->position;
              while ($this->position < $this->length && strpos("/>= \n\r\t", $this->rawtext{$this->position}) === FALSE) {
                  $this->position++;
              }

              if ($this->_reachedEndOfFile())
                return;

              $attributename = substr($this->rawtext, $start, $this->position - $start);
              $attributevalue = NULL;

              $this->ignoreWhitespace();

              if ($this->_reachedEndOfFile())
                return;

              if ( $this->rawtext{$this->position} == '=') {
                  $attributevalue = "";

                  $this->position++;
                  $this->ignoreWhitespace();

                  if ($this->_reachedEndOfFile())
                    return;

                  $quote = $this->rawtext{$this->position};
                  if ($quote == '"' || $quote == "'")
                  {
                      $start = $this->position + 1;
                      $this->position = strpos($this->rawtext, $quote, $start);

                      if ($this->position === FALSE)
                      {
                        $this->observer->characters(substr($this->rawtext, $this->element_pos - 1), $this->getCurrentLocation());
                        return;
                      }

                      $attributevalue = substr($this->rawtext, $start, $this->position - $start);

                      $this->position++;

                      if ($this->_reachedEndOfFile())
                        return;

                      if (strpos("/> \n\r\t", $this->rawtext{$this->position}) === FALSE)
                        throw new WactException('Invalid tag attribute syntax', array('file' => $this->getFile(),
                                                                                      'line' => $this->getLineNumber()));

                  }
                  else
                  {
                      $start = $this->position;
                      while ($this->position < $this->length && strpos("/> \n\r\t", $this->rawtext{$this->position}) === FALSE) {
                          $this->position++;
                      }

                      if ($this->_reachedEndOfFile())
                        return;

                      $attributevalue = substr($this->rawtext, $start, $this->position - $start);
                  }
              }

              $Attributes[$attributename] = $attributevalue;

              $this->ignoreWhitespace();
          }

          if ($this->_reachedEndOfFile())
            return;

          if ($this->rawtext{$this->position} == '/')
          {
              $this->position += 1;

              if ($this->_reachedEndOfFile())
                return;

              if ($this->rawtext{$this->position} != '>')
              {
                throw new WactException('Invalid tag syntax', array('file' => $this->getFile(),
                                                                    'line' => $this->getLineNumber()));
              }

              $this->observer->emptyTag($tag, $Attributes, $this->getCurrentLocation());
          }
          else
          {
            $this->observer->startTag($tag, $Attributes, $this->getCurrentLocation());
          }
          $this->position += 1;

        break;
      }
    }
    while ($this->position < $this->length);
  }

  protected function _reachedEndOfFile()
  {
    if ($this->position >= $this->length)
    {
      $this->observer->characters(substr($this->rawtext, $this->element_pos - 1), $this->getCurrentLocation());
      return true;
    }
    else
      return false;
  }

}

