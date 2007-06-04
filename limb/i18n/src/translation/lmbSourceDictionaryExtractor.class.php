<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSourceDictionaryExtractor.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
