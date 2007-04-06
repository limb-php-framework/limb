<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactHTMLParser.class.php 5553 2007-04-06 09:05:17Z serega $
 * @package    wact
 */

/**
* HTML/XHTML/XML parser
* fast parser robustly handles malformed input.
* All events are triggered by valid markup.
* If markup is invalid, it is treated as data event.
*/
class WactHTMLParser
{
    var $publicId;
    var $Observer;
    /**
    * XML document being parsed
    * @var string
    * @access private
    */
    var $rawtext;
    /**
    * Position in XML document relative to start (0)
    * @var int
    * @access private
    */
    var $position;
    /**
    * Length of the XML document in characters
    * @var int
    * @access private
    */
    var $length;

    /**
    * @var Observer event handler
    * @access protected
    */
    function __construct($Observer) {
        $this->Observer = $Observer;
    }

    /*
    * Calculates the line number from the byte index
    * @return int the current line number
    * @access private
    */
    function getLineNumber() {
        return 1 + substr_count(substr($this->rawtext, 0, $this->position), "\n");
    }

    function getPublicId() {
        return $this->publicId;
    }

    function getCurrentLocation()
    {
      return new WactSourceLocation($this->getPublicId(), $this->getLineNumber());
    }

    /**
    * Moves the position forward past any whitespace characters
    * @access protected
    * @return void
    */
    function ignoreWhitespace() {
        while ($this->position < $this->length &&
            strpos(" \n\r\t", $this->rawtext{$this->position}) !== FALSE) {
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
    function parse($data, $publicId = NULL) {
        $this->rawtext = $data;
        $this->length = strlen($data);
        $this->position = 0;
        $this->publicId = $publicId;

        do {
            $start = $this->position;
            $this->position = strpos($this->rawtext, '<', $start);
            if ($this->position === FALSE) {
                if ($start < $this->length) {
                    $this->Observer->characters(substr($this->rawtext, $start));
                }
                return;
            }

            if ($this->position > $start) {
                $this->Observer->characters(substr($this->rawtext, $start, $this->position - $start));
            }

            $this->position += 1;   // ignore '<' character
            if ($this->position >= $this->length) {
                $this->Observer->unexpectedEOF('<');
                return;
            }

            $element_pos = $this->position;
            $this->position += 1;

            switch($this->rawtext{$element_pos}) {
            case '/':
                $start = $this->position;
                while ($this->position < $this->length && $this->rawtext{$this->position} != '>') {
                    $this->position++;
                }
                if ($this->position >= $this->length) {
                    $this->Observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
                    return;
                }

                $tag = substr($this->rawtext, $start, $this->position - $start);

                $this->Observer->endElement($tag);
                $this->position += 1;   // ignore '>' string
                break;
            case '?':
                $start = $this->position;
                while ($this->position < $this->length && strpos(" \n\r\t", $this->rawtext{$this->position}) === FALSE) {
                    $this->position++;
                }
                if ($this->position >= $this->length) {
                    $this->Observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
                    return;
                }

                $target = substr($this->rawtext, $start, $this->position - $start);

                $this->ignoreWhitespace();

                $start = $this->position;
                $this->position = strpos($this->rawtext, '?>', $start);
                if ($this->position === FALSE) {
                    $this->Observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
                    return;
                }

                $this->Observer->processingInstruction($target,
                    substr($this->rawtext, $start, $this->position - $start));

                $this->position += 2;   // ignore '? >' string
                break;
            case '%':
                if ($this->position >= $this->length) {
                    $this->Observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
                    return;
                }

                $start = $this->position;
                $this->position = strpos($this->rawtext, '%>', $start);
                if ($this->position === FALSE) {
                    $this->Observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
                    return;
                }

                $this->Observer->jasp(substr($this->rawtext, $start, $this->position - $start));

        /* changed to multi line comment per request on list
        http://sourceforge.net/mailarchive/forum.php?thread_id=5925242&forum_id=35579
        ignore '%>' string
        */
                $this->position += 2;
                break;
            case '!':
                if ($this->position >= $this->length) {
                    $this->Observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
                    return;
                }

                $start = $this->position;
                if (substr($this->rawtext, $start, 2) == "--") {
                    $this->position = strpos($this->rawtext, '-->', $start);
                    if ($this->position === FALSE) {
                        $this->Observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
                        return;
                    }
                    $this->Observer->comment(
                        substr($this->rawtext, $start + 2, $this->position - $start - 2));
                    $this->position += 3;
                } else if (strcasecmp(substr($this->rawtext, $start, 7), 'DOCTYPE') == 0) {
                    while ($this->position < $this->length && $this->rawtext{$this->position} != '>') {
                        $this->position++;
                    }
                    if ($this->position >= $this->length) {
                        $this->Observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
                        return;
                    }
                    $this->Observer->doctype(
                        substr($this->rawtext, $start, $this->position - $start));
                    $this->position += 1;
                } else if (substr($this->rawtext, $start, 7) == '[CDATA[') {
                    $this->Observer->characters(substr($this->rawtext, $start - 2, 9));
                    $this->position += 7;
                    break;
                } else {
                    while ($this->position < $this->length && $this->rawtext{$this->position} != '>') {
                        $this->position++;
                    }
                    if ($this->position >= $this->length) {
                        $this->Observer->unexpectedEOF(
                            substr($this->rawtext, $element_pos - 1));
                        return;
                    }
                    $this->position += 1;
                    $this->Observer->escape(
                        substr($this->rawtext, $start, $this->position - $start));
                }
                break;
            default:
                while ($this->position < $this->length && strpos("/> \n\r\t", $this->rawtext{$this->position}) === FALSE) {
                    $this->position++;
                }
                if ($this->position >= $this->length) {
                    $this->Observer->unexpectedEOF(
                        substr($this->rawtext, $element_pos - 1));
                    return;
                }

                $tag = substr($this->rawtext, $element_pos, $this->position - $element_pos);
                $Attributes = array();

                $this->ignoreWhitespace();

                while ( $this->position < $this->length &&
                        $this->rawtext{$this->position} != '/' &&
                        $this->rawtext{$this->position} != '>') {

                    $start = $this->position;
                    while ($this->position < $this->length && strpos("/>= \n\r\t", $this->rawtext{$this->position}) === FALSE) {
                        $this->position++;
                    }
                    if ($this->position >= $this->length) {
                        $this->Observer->unexpectedEOF(
                            substr($this->rawtext, $element_pos - 1));
                        return;
                    }

                    $attributename = substr($this->rawtext, $start, $this->position - $start);
                    $attributevalue = NULL;

                    $this->ignoreWhitespace();
                    if ($this->position >= $this->length) {
                        $this->Observer->unexpectedEOF(
                            substr($this->rawtext, $element_pos - 1));
                        return;
                    }

                    if ( $this->rawtext{$this->position} == '=') {
                        $attributevalue = "";

                        $this->position++;
                        $this->ignoreWhitespace();
                        if ($this->position >= $this->length) {
                            $this->Observer->unexpectedEOF(
                                substr($this->rawtext, $element_pos - 1));
                            return;
                        }

                        $quote = $this->rawtext{$this->position};
                        if ($quote == '"' || $quote == "'") {
                            $start = $this->position + 1;
                            $this->position = strpos($this->rawtext, $quote, $start);
                            if ($this->position === FALSE) {
                                $this->Observer->unexpectedEOF(
                                    substr($this->rawtext, $element_pos - 1));
                                return;
                            }

                            $attributevalue = substr($this->rawtext, $start, $this->position - $start);

                            $this->position++;
                            if ($this->position >= $this->length) {
                                $this->Observer->unexpectedEOF(
                                    substr($this->rawtext, $element_pos - 1));
                                return;
                            }

                            if (strpos("/> \n\r\t", $this->rawtext{$this->position}) === FALSE) {
                                $this->Observer->invalidAttributeSyntax();
                            }

                        } else {
                            $start = $this->position;
                            while ($this->position < $this->length && strpos("/> \n\r\t", $this->rawtext{$this->position}) === FALSE) {
                                $this->position++;
                            }
                            if ($this->position >= $this->length) {
                                $this->Observer->unexpectedEOF(substr($this->rawtext, $element_pos - 1));
                                return;
                            }
                            $attributevalue = substr($this->rawtext, $start, $this->position - $start);
                        }
                    }

                    $Attributes[$attributename] = $attributevalue;

                    $this->ignoreWhitespace();
                }

                if ($this->position >= $this->length) {
                    $this->Observer->unexpectedEOF(
                        substr($this->rawtext, $element_pos - 1));
                    return;
                }

                if ($this->rawtext{$this->position} == '/') {
                    $this->position += 1;
                    if ($this->position >= $this->length) {
                        $this->Observer->unexpectedEOF(
                            substr($this->rawtext, $element_pos - 1));
                        return;
                    }

                    if ($this->rawtext{$this->position} != '>') {
                        $start = $this->position;
                        while ($this->position < $this->length && $this->rawtext{$this->position} != '>') {
                            $this->position++;
                        }
                        if ($this->position >= $this->length) {
                            $this->Observer->invalidEntitySyntax(
                                substr($this->rawtext, $element_pos - 1));
                            break;
                        }

                        $this->Observer->invalidEntitySyntax(
                            substr($this->rawtext, $element_pos - 1, $this->position - $element_pos + 2));
                        $this->position += 1;
                        break;
                    }

                    $this->Observer->emptyElement($tag, $Attributes);
                } else {
                    $this->Observer->startElement($tag, $Attributes);
                }
                $this->position += 1;

                break;
            }
        } while ($this->position < $this->length);
    }

}
?>
