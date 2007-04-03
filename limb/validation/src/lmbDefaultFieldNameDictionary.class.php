<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDefaultFieldNameDictionary.class.php 5106 2007-02-18 09:23:45Z serega $
 * @package    validation
 */

/**
* Repeats interface of WactFormFieldNameDictionary
* FieldNameDictionary translates field names to human readale format
* This is no such interface since we just don't what to introduce any dependency of VALIDATION package on WACT and vice versa
*/
class lmbDefaultFieldNameDictionary
{
  /**
  * Simply returns passed field name as result
  * @param string Field name
  * @return string
  */
  function getFieldName($name)
  {
    return $name;
  }
}

?>