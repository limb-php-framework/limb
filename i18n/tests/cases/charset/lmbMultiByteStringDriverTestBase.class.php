<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

abstract class lmbMultiByteStringDriverTestBase extends UnitTestCase
{
    function & _createDriver() {
        return null;
    }

    function test_substr() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_substr("это просто тест", 1), "то просто тест");
        $this->assertEqual($driver->_substr("ääääσαφ", 0, 400), "ääääσαφ");
        $this->assertEqual($driver->_substr("ääääσαφ", 2, 400), "ääσαφ");
        $this->assertEqual($driver->_substr("ääääσαφ", 1, 4), "äääσ");
        $this->assertEqual($driver->_substr("ääääσαφ", -1), "φ");
        $this->assertEqual($driver->_substr("ääääσαφ", 0, -1), "ääääσα");
        $this->assertEqual($driver->_substr("ääääσαφ", 1, -1), "äääσα");
    }

    function test_rtrim() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_rtrim("τελευτατελ\0\n\n\t"), "τελευτατελ");
        $this->assertEqual($driver->_rtrim("τελευτατε?++.*?", ".*?+"), "τελευτατε");
        //intervals stuff not working yet, and it's not clear how it should work
        //$this->assertEqual($driver->_rtrim("τελευτατε\n\t", "\0x00..\0x1F"), "τελευτατε");
    }

    function test_ltrim() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_ltrim("\0\n\n\tτελευτατελ"), "τελευτατελ");
        $this->assertEqual($driver->_ltrim("λτελευτατε", "λ"), "τελευτατε");
        $this->assertEqual($driver->_ltrim("?+.*+?τελευτατε", "?.*+"), "τελευτατε");
    }

    function test_trim() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_trim(" \n\t\0 τελευτατελ\0\n\n\t"), "τελευτατελ");
        $this->assertEqual($driver->_trim("pτελεpυτατελp", "p"), "τελεpυτατελ");
        $this->assertEqual($driver->_trim("pτελεpυτατελp", "pλ"), "τελεpυτατε");
        $this->assertEqual($driver->_trim("?*++?τελευτατε?+.+?", "?.+*"), "τελευτατε");
    }

    function test_str_replace() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_str_replace("ελx", "", "τελxευτατελx"),
                           "τευτατ");
        $this->assertEqual($driver->_str_replace("τ", "υ", "τελευτατελ"),
                           "υελευυαυελ");
        $search = array("τ", "υ");
        $this->assertEqual($driver->_str_replace($search, "λ", "τελευτατελ"),
                           "λελελλαλελ");
        $replace = array("α", "ε");
        $this->assertEqual($driver->_str_replace($search, $replace, "τελευτατελ"),
                           "αελεεαααελ");
    }

    function test_strlen() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_strlen("τελευτατελ"), 10);
        $this->assertEqual($driver->_strlen("τ\nελευτα τελ "), 13);
    }

    function test_strpos() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_strpos("τελευτατελ", "τατ"), 5);
        $this->assertEqual($driver->_strpos("τελευτατελ", "ε"), 1);
        $this->assertEqual($driver->_strpos("τελευτατελ", "ε", 2), 3);
    }

    function test_strrpos() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_strrpos("τελευτατελ", "τατ"), 5);
        $this->assertEqual($driver->_strrpos("τελευτατελ", "ε"), 8);
        $this->assertEqual($driver->_strrpos("τελευτατελ", "ε", 3), 8);
    }

    function test_strtolower() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_strtolower("ТЕСТ"), "тест");
        $this->assertEqual($driver->_strtolower("тЕсТ"), "тест");
    }

    function test_strtoupper() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_strtoupper("тест"), "ТЕСТ");
        $this->assertEqual($driver->_strtoupper("тЕсТ"), "ТЕСТ");
    }

    function test_ucfirst() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_ucfirst("тест"), "Тест");
    }

    function test_ucfirst_Space() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $str = ' Iñtërnâtiônàlizætiøn';
        $ucfirst = ' Iñtërnâtiônàlizætiøn';
        $this->assertEqual($driver->_ucfirst($str),$ucfirst);
    }

    function test_ucfirst_Upper() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $str = 'Ñtërnâtiônàlizætiøn';
        $ucfirst = 'Ñtërnâtiônàlizætiøn';
        $this->assertEqual($driver->_ucfirst($str), $ucfirst);
    }

    function test_strcasecmp() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_strcasecmp("тест", "тест"), 0);
        $this->assertEqual($driver->_strcasecmp("тест", "ТесТ"), 0);
        $this->assertTrue($driver->_strcasecmp("тест", "ТЕСТЫ") < 0);
        $this->assertTrue($driver->_strcasecmp("тесты", "ТЕСТ") > 0);
    }

    function test_substr_count() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $str = "это...просто тест, не стоит воспринимать это...всерьез";

        $this->assertEqual($driver->_substr_count($str, "это..."), 2);
    }

    function test_str_split() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $str = 'Iñtërnâtiônàlizætiøn';
        $array = array(
            'I','ñ','t','ë','r','n','â','t','i','ô','n','à','l','i',
            'z','æ','t','i','ø','n',
        );
        $this->assertEqual($driver->_str_split($str), $array);
    }

    function test_str_split_Newline() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $str = "Iñtërn\nâtiônàl\nizætiøn\n";
        $array = array(
            'I','ñ','t','ë','r','n',"\n",'â','t','i','ô','n','à','l',"\n",'i',
            'z','æ','t','i','ø','n',"\n",
        );
        $this->assertEqual($driver->_str_split($str), $array);
    }

    function test_preg_match() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertTrue($driver->_preg_match("/^(.)/", "тест", $matches));
        $this->assertEqual($matches[1], "т");
    }

    function test_preg_match_all() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertTrue($driver->_preg_match_all("/(.)/", "тест", $matches));

        $this->assertEqual($matches[1][0], "т");
        $this->assertEqual($matches[1][1], "е");
        $this->assertEqual($matches[1][2], "с");
        $this->assertEqual($matches[1][3], "т");
    }

    function test_preg_replace() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_preg_replace("/кошк./", "собаки", "кошки"), "собаки");
    }

    function test_preg_replace_callback() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $this->assertEqual($driver->_preg_replace_callback("/(кошк)(.)/",
                                                           create_function('$m','return $m[1]."i";'),
                                                           "кошки"), "кошкi");
    }

    function test_preg_split() {
        if(!is_object($driver = $this->_createDriver()))
            return;

        $pieces = $driver->_preg_split("/д./", "кошки да собаки");
        $this->assertEqual($pieces[0], "кошки ");
        $this->assertEqual($pieces[1], " собаки");
    }
}


