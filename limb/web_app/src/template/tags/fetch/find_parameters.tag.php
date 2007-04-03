<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: perform_parameter.tag.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
/**
* @tag find:params
* @forbid_end_tag
* @parent_tag_class lmbActiveRecordFetchTag
*/
class lmbFindParametersTag extends WactCompilerTag
{
  function generateContents($code)
  {
    foreach(array_keys($this->attributeNodes) as $key)
    {
      $name = $this->attributeNodes[$key]->getName();

      $this->attributeNodes[$key]->generatePreStatement($code);

      $code->writePhp($this->parent->getComponentRefCode() .
                      '->addFindParam(');
      $this->attributeNodes[$key]->generateExpression($code);
      $code->writePhp(');' . "\n");

      $this->attributeNodes[$key]->generatePostStatement($code);
    }
  }
}

?>