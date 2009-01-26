<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbPHPTokenizer.class.php');
lmb_require('limb/i18n/src/translation/lmbSourceDictionaryExtractor.class.php');
lmb_require('limb/i18n/src/translation/lmbI18NDictionary.class.php');

/**
 * class lmbPHPDictionaryExtractor.
 *
 * @package i18n
 * @version $Id: lmbPHPDictionaryExtractor.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
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


