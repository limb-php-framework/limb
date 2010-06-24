<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbSearchTextNormalizer.
 *
 * @package search
 * @version $Id: lmbSearchTextNormalizer.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbSearchTextNormalizer
{
  function process($content)
  {
    $content = lmb_strtolower($content);

    $content = str_replace("\n", ' ', $content );
    $content = str_replace("\t", ' ', $content );
    $content = str_replace("\r", ' ', $content );

    $search = array (
                "'<script[^>]*?>.*?</script>'siu",  	// Strip out javascript
                "'<[\/\!]*?[^<>]*?>'siu",           	// Strip out html tags
                "'([\r\n])[\s]+'u"                 	  // Strip out white space
              );

    $replace = array ('',
                     ' ',
                     ' ');

    $content = preg_replace ($search, $replace, $content);

    $content = preg_replace("#(\.){2,}#", ' ', $content );
    $content = preg_replace("#^\.#", ' ', $content);
    $content = preg_replace("#\s\.#", ' ', $content );
    $content = preg_replace("#\.\s#", ' ', $content);
    $content = preg_replace("#\.$#", ' ', $content);

    $content = preg_replace( "#(\s|^)(\"|'|`)(\w)#", '\\1\\3', $content);
    $content = preg_replace( "#(\w)(\"|'|`)(\s|$)#", '\\1\\3', $content);

    $content = str_replace("&nbsp;", ' ', $content );
    $content = str_replace(":", ' ', $content );
    $content = str_replace(",", ' ', $content );
    $content = str_replace(";", ' ', $content );
    $content = str_replace("(", ' ', $content );
    $content = str_replace(")", ' ', $content );
    $content = str_replace("-", ' ', $content );
    $content = str_replace("+", ' ', $content );
    $content = str_replace("/", ' ', $content );
    $content = str_replace("!", ' ', $content );
    $content = str_replace("?", ' ', $content );
    $content = str_replace("[", ' ', $content );
    $content = str_replace("]", ' ', $content );
    $content = str_replace("$", ' ', $content );
    $content = str_replace("\\", ' ', $content );
    $content = str_replace("<", ' ', $content );
    $content = str_replace(">", ' ', $content );
    $content = str_replace("*", ' ', $content );

    $content = trim(preg_replace("(\s+)", ' ', $content));

    return $content;
  }
}



