<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/i18n/src/charset/lmbUTF8BaseDriver.class.php');

// This class is based on Harry Fuecks' phputf8 library code(http://sourceforge.net/projects/phputf8)

/**
 * class lmbUTF8MbstringDriver.
 *
 * @package i18n
 * @version $Id: lmbUTF8MbstringDriver.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbUTF8MbstringDriver extends lmbUTF8BaseDriver {
    function _strlen($string) {
        return mb_strlen($string, 'utf-8');
    }

     function _substr($str, $start, $length=null)
     {
        if (is_null($length)) {
            $old_enc = mb_internal_encoding();
            mb_internal_encoding('UTF-8');
            $result = mb_substr($str, (int)$start);
            if ( $old_enc ) mb_internal_encoding($old_enc);
            return $result;
        } else {
            return mb_substr($str, (int)$start, (int)$length, 'UTF-8');
        }
     }

    // implement with mb_ereg_* ?
    // function _str_replace($s,$r,$str){}

    // should be implemented with mb_split ?
    // function  _ltrim($str, $charlist=''){}

    // should be implemented with mb_split ?
    // function  _rtrim($str, $charlist=''){}

    // should be implemented with mb_split ?
    // function  _trim($str,$charlist=''){}

    function _strtolower($string) {
        return mb_strtolower($string, 'utf-8');
    }

    function _strtoupper($string) {
        return mb_strtoupper($string, 'utf-8');
    }

    function _strpos($haystack, $needle, $offset=null) {
        if (is_null($offset)) {
            $old_enc = mb_internal_encoding();
            mb_internal_encoding('utf-8');
            $result = mb_strpos($haystack, $needle);
            if ( $old_enc ) mb_internal_encoding($old_enc);
            return $result;
        } else {
            return mb_strpos($haystack, $needle, (int)$offset, 'utf-8');
        }
    }

    function _strrpos($haystack, $needle, $offset=null) {
        if (is_null($offset)) {
            $old_enc = mb_internal_encoding();
            mb_internal_encoding('utf-8');
            $result = mb_strrpos($haystack, $needle);
            if ( $old_enc ) mb_internal_encoding($old_enc);
            return $result;
        } else {
            //mb_strrpos doesn't support offset! :(
            return parent ::_strrpos($haystack, $needle, (int)$offset);
        }
    }
}

