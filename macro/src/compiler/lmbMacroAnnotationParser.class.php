<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/core/src/lmbPHPTokenizer.class.php');

/**
 * class lmbMacroAnnotationParser.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroAnnotationParser
{
  static function extractFromFile($file, $listener)
  {
    $infos = array();
    $tokenizer = new lmbPHPTokenizer(file_get_contents($file));
    while($token = $tokenizer->next())
    {
      if(!is_array($token))
        continue;

      //found class token
      if($token[0] == T_CLASS)
      {
        //fetching class name
        $token = $tokenizer->next();
        $class = $token[1];

        //now checking prev token for /**/
        if(!is_array($prev_token) || $prev_token[0] != T_DOC_COMMENT)
          throw new lmbMacroException('Invalid token, doc comment is expected', array('prev_token' => $prev_token));

        //now parsing annotations
        $annotations = self :: _extractAnnotations($prev_token[1]);
        if(!$annotations)
          throw new lmbMacroException("No annotations found in doc comment '{$prev_token[1]}' in file $file");

        $infos[] = call_user_func_array (array($listener, 'createByAnnotations'), array($file, $class, $annotations));
        //$infos[] = $listener->createByAnnotations($file, $class, $annotations);
      }
      $prev_token = $token;
    }
    return $infos;
  }

  static protected function _extractAnnotations($content)
  {
    if(!preg_match_all('~@(\S+)([^\n]+)?\n~', $content, $matches))
      return false;
    $annotations = array();
    for($i=0;$i<count($matches[0]);$i++)
      $annotations[trim($matches[1][$i])] = trim($matches[2][$i]);
    return $annotations;
  }
}

