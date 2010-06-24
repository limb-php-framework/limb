<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
* Widgets are runtime components which have no compile time template tag.
* They can be created and added by the PHP script controlling the template.
*/


/**
 * Allows plain text to be added
 * Widgets are runtime components which have no compile time template tag.
 * They can be created and added by the PHP script controlling the template.
 * @package wact
 * @version $Id: widgets.inc.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactTextWidget extends WactRuntimeComponent
{
  /**
  * Text to add
  * @var string
  */
  var $text;

  /**
  * Constructs TextComponent
  * @param string text to add
  */
  function __construct($text)
  {
    $this->text = $text;
  }

  /**
  * Override parent method to prevent use of children
  * @return void
  */
  function addChild()
  {
      // Should we kick an error message here?
  }

  /**
  * Outputs the text Widget.
  * @return void
  * @access public
  */
  function render()
  {
    echo ( htmlspecialchars($this->text, ENT_NOQUOTES) );
  }
}

/**
 * Allows a tag to be created, which cannot contain children e.g. img
 * @package wact
 * @version $Id: widgets.inc.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactTagWidget extends WactRuntimeTagComponent
{
  /**
  * Name of the tag
  * @var string
  */
  var $tag;

  /**
  * Whether the tag is closing or not
  * @var boolean
  */
  var $closing = true;

  /**
  * Constructs TagWidget
  * @param string name of tag
  * @param boolean whether tag is closing
  */
  function __construct($tag,$closing=true)
  {
    $this->tag = htmlspecialchars($tag,ENT_QUOTES);
    $this->closing = $closing;
  }

  /**
  * Override parent method to prevent use of children
  * @return void
  * @access public
  */
  function addChild()
  {
    // Should we kick an error message here?
  }

  /**
  * Outputs the tag
  * @return void
  * @access public
  */
  function render()
  {
    echo ( '<'.$this->tag );
    echo ( $this->renderAttributes());
    if ( $this->closing )
      echo ( '/>' );
    else
      echo ( '>' );
  }
}

/**
 * Allows a tag to be created, which can contain children
 * @package wact
 * @version $Id: widgets.inc.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactTagContainerWidget extends WactRuntimeTagComponent
{
  /**
  * Name of the tag
  * @var string
  */
  var $tag;

  /**
  * Constructs TagContainerWidget
  * @param string name of tag
  * @param boolean whether tag is closing
  */
  function __construct($tag)
  {
    $this->tag = htmlspecialchars($tag, ENT_QUOTES);
  }

  /**
  * Outputs the tag, rendering any child components as well
  * @return void
  */
  function render()
  {
    echo ( '<'.$this->tag );
    echo ( $this->renderAttributes().'>');
    parent :: render();
    echo ( '</'.$this->tag.'>' );
  }
}

