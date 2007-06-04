<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWACTDictionaryExtractor.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/i18n/src/translation/lmbSourceDictionaryExtractor.class.php');

class lmbWACTDictionaryExtractor extends lmbSourceDictionaryExtractor
{
  function extract($code, &$dictionaries = array(), $response = null)
  {
    if(preg_match_all('~\{\$[\'"]([^\'"]+)[\'"]\|i18n(:[\'"]([^\'"]+)[\'"])?~', $code, $matches))
    {
      foreach($matches[1] as $index => $text)
      {
        $domain = $matches[3][$index] ? $matches[3][$index] : 'default';

        if($response)
          $response->write("WACT template: '$text'@$domain\n");

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

?>
