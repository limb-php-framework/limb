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
 * class lmbUTF8IconvDriver.
 *
 * @package i18n
 * @version $Id: lmbUTF8IconvDriver.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbUTF8IconvDriver extends lmbUTF8BaseDriver {
    function _strlen($string) {
        return iconv_strlen($string, 'utf-8');
    }

    function _strpos($str, $search, $offset=null){
        if (is_null($offset)) {
            $old_enc = $this->_setUTF8IconvEncoding();
            $result = iconv_strpos($str, $search);
            $this->_setIconvEncoding($old_enc);
            return $result;
        } else {
            return iconv_strpos($str, $search, (int)$offset, 'utf-8');
        }
    }

    function _strrpos($str, $search, $offset=null){
        if (is_null($offset)) {
            $old_enc = $this->_setUTF8IconvEncoding();
            $result = iconv_strrpos($str, $search);
            $this->_setIconvEncoding($old_enc);
            return $result;
        } else {
            //mb_strrpos doesn't support offset! :(
            return parent ::_strrpos($str, $search, (int)$offset);
        }
    }

    function _substr($str, $offset, $length=null){
        if (is_null($length)) {
            $old_enc = $this->_setUTF8IconvEncoding();
            $result = iconv_substr($str, (int)$offset);
            $this->_setIconvEncoding($old_enc);
            return $result;
        } else {
            return iconv_substr($str, (int)$offset, (int)$length, 'utf-8');
        }
    }

    function _setIconvEncoding($arr) {
        foreach($arr as $type => $enc) {
            iconv_set_encoding($type, $enc);
        }
    }

    function _setUTF8IconvEncoding() {
        $old_enc = iconv_get_encoding();
        $this->_setIconvEncoding(array('input_encoding' => 'utf-8',
                                       'output_encoding' => 'utf-8',
                                       'internal_encoding' => 'utf-8'));
        return $old_enc;
    }
}

