<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPHPDictionaryExtractor.class.php 5411 2007-03-29 10:07:12Z pachanga $
 * @package    i18n
 */
lmb_require('limb/util/src/util/lmbPHPTokenizer.class.php');
lmb_require('limb/i18n/src/translation/lmbSourceDictionaryExtractor.class.php');
lmb_require('limb/i18n/src/translation/lmbI18NDictionary.class.php');

class lmbPHPDictionaryExtractor extends lmbSourceDictionaryExtractor
{
  protected $tokenizer;

  function __construct()
  {
    $this->tokenizer = new lmbPHPTokenizer();
  }

  function extract($code, &$dictionaries = array(), $response = null)
  {
    $this->tokenizer->input($code);

    while($token = $this->tokenizer->next())
    {
      if(is_array($token) && $token[0] == T_STRING && $token[1] == 'lmb_i18n')
      {
        $parenthesis = array();
        if($this->tokenizer->next() == "(")
        {
          $text_token = $this->tokenizer->next();
          if(!is_array($text_token) || $text_token[0] != T_CONSTANT_ENCAPSED_STRING)
            continue;

          array_push($parenthesis, 1);
          $text = trim($text_token[1], '"\'');

          //getting tokens until function closes its last )
          $buffer = array();
          while($parenthesis && $token = $this->tokenizer->next())
          {
            if($token == ")")
              array_pop($parenthesis);
            elseif($token == "(")
              array_push($parenthesis, 1);

            $buffer[] = $token;
          }

          $domain = 'default';
          if(sizeof($buffer) > 2)
          {
            $domain_token = $buffer[sizeof($buffer)-2];
            if(is_array($domain_token) && $domain_token[0] == T_CONSTANT_ENCAPSED_STRING)
              $domain = trim($domain_token[1], '"\'');
          }

          if($response)
            $response->write("PHP source: '$text'@$domain\n");

          if(!isset($dictionaries[$domain]))
          {
            $dictionary = new lmbI18NDictionary();
            $dictionaries[$domain] = $dictionary;
          }
          else
            $dictionary = $dictionaries[$domain];

          $dictionary->add($text);
        }
      }
    }
  }
}

?>
