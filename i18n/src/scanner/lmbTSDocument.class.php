<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */


/**
 * class lmbTSDocument.
 *
 * Dom representation of ts-files (qt translation)
 *
 * @package i18n
 * @version $Id: lmbTSDocument.class.php 7994 2009-09-21 13:01:14Z idler $
 */
class lmbTSDocument extends DOMDocument{

  function addMessage($message)
  {
    $message_node = $this->createElement('message');
    $source_node = $this->createElement('source',$message);
    $message_node->appendChild($source_node);

    $target = $this->getElementsByTagName('context')->item(0);
    $target->appendChild($message_node);
  }
}
