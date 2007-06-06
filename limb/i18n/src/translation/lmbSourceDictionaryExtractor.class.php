<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * abstract class lmbSourceDictionaryExtractor.
 *
 * @package i18n
 * @version $Id: lmbSourceDictionaryExtractor.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
abstract class lmbSourceDictionaryExtractor
{
  abstract function extract($code, &$dictionaries = array(), $response = null);

  function extractFromFile($file, &$dictionaries = array(), $response = null)
  {
    $this->extract(file_get_contents($file), $dictionaries, $response);
  }
}

?>
