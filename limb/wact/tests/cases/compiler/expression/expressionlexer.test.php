<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: expressionlexer.test.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/expression/WactExpressionLexer.class.php';
require_once 'limb/wact/src/compiler/expression/WactExpressionLexerParallelRegex.class.php';
require_once 'limb/wact/src/compiler/expression/WactExpressionLexerStateStack.class.php';

class TestOfExpressionLexerParallelRegex extends UnitTestCase {

  function testNoPatterns() {
    $regex = new WactExpressionLexerParallelRegex(false);
    $this->assertFalse($regex->match("Hello", $match));
    $this->assertEqual($match, "");
  }

  function testNoSubject() {
    $regex = new WactExpressionLexerParallelRegex(false);
    $regex->addPattern(".*");
    $this->assertTrue($regex->match("", $match));
    $this->assertEqual($match, "");
  }

  function testMatchAll() {
    $regex = new WactExpressionLexerParallelRegex(false);
    $regex->addPattern(".*");
    $this->assertTrue($regex->match("Hello", $match));
    $this->assertEqual($match, "Hello");
  }

  function testCaseSensitive() {
    $regex = new WactExpressionLexerParallelRegex(true);
    $regex->addPattern("abc");
    $this->assertTrue($regex->match("abcdef", $match));
    $this->assertEqual($match, "abc");
    $this->assertTrue($regex->match("AAABCabcdef", $match));
    $this->assertEqual($match, "abc");
  }

  function testCaseInsensitive() {
    $regex = new WactExpressionLexerParallelRegex(false);
    $regex->addPattern("abc");
    $this->assertTrue($regex->match("abcdef", $match));
    $this->assertEqual($match, "abc");
    $this->assertTrue($regex->match("AAABCabcdef", $match));
    $this->assertEqual($match, "ABC");
  }

  function testMatchMultiple() {
    $regex = new WactExpressionLexerParallelRegex(true);
    $regex->addPattern("abc");
    $regex->addPattern("ABC");
    $this->assertTrue($regex->match("abcdef", $match));
    $this->assertEqual($match, "abc");
    $this->assertTrue($regex->match("AAABCabcdef", $match));
    $this->assertEqual($match, "ABC");
    $this->assertFalse($regex->match("Hello", $match));
  }

  function testPatternLabels() {
    $regex = new WactExpressionLexerParallelRegex(false);
    $regex->addPattern("abc", "letter");
    $regex->addPattern("123", "number");
    $this->assertIdentical($regex->match("abcdef", $match), "letter");
    $this->assertEqual($match, "abc");
    $this->assertIdentical($regex->match("0123456789", $match), "number");
    $this->assertEqual($match, "123");
  }
}

/**
* @package wact
*/
class TestOfExpressionLexerStateStack extends UnitTestCase {
  function TestOfExpressionLexerStateStack() {
    $this->UnitTestCase();
  }

  function testStartState() {
    $stack = new WactExpressionLexerStateStack("one");
    $this->assertEqual($stack->getCurrent(), "one");
  }

  function testExhaustion() {
    $stack = new WactExpressionLexerStateStack("one");
    $this->assertFalse($stack->leave());
  }

  function testStateMoves() {
    $stack = new WactExpressionLexerStateStack("one");
    $stack->enter("two");
    $this->assertEqual($stack->getCurrent(), "two");
    $stack->enter("three");
    $this->assertEqual($stack->getCurrent(), "three");
    $this->assertTrue($stack->leave());
    $this->assertEqual($stack->getCurrent(), "two");
    $stack->enter("third");
    $this->assertEqual($stack->getCurrent(), "third");
    $this->assertTrue($stack->leave());
    $this->assertTrue($stack->leave());
    $this->assertEqual($stack->getCurrent(), "one");
  }
}

class TestParser {
  function TestParser() {
  }

  function accept() {
  }

  function a() {
  }

  function b() {
  }
}
Mock::generate('TestParser');

class TestOfExpressionLexer extends UnitTestCase {
  function TestOfExpressionLexer() {
    $this->UnitTestCase();
  }

  function testNoPatterns() {
    $handler = new MockTestParser();
    $handler->expectNever("accept");
    $handler->setReturnValue("accept", true);
    $lexer = new WactExpressionLexer($handler);
    $this->assertFalse($lexer->parse("abcdef"));
  }

  function testEmptyPage() {
    $handler = new MockTestParser();
    $handler->expectNever("accept");
    $handler->setReturnValue("accept", true);
    $handler->expectNever("accept");
    $handler->setReturnValue("accept", true);
    $lexer = new WactExpressionLexer($handler);
    $lexer->addPattern("a+");
    $this->assertTrue($lexer->parse(""));
  }

  function testSinglePattern() {
    $handler = new MockTestParser();
    $handler->expectArgumentsAt(0, "accept", array("aaa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(1, "accept", array("x", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(2, "accept", array("a", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(3, "accept", array("yyy", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(4, "accept", array("a", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(5, "accept", array("x", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(6, "accept", array("aaa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(7, "accept", array("z", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectCallCount("accept", 8);
    $handler->setReturnValue("accept", true);
    $lexer = new WactExpressionLexer($handler);
    $lexer->addPattern("a+");
    $this->assertTrue($lexer->parse("aaaxayyyaxaaaz"));
  }

  function testMultiplePattern() {
    $handler = new MockTestParser();
    $target = array("a", "b", "a", "bb", "x", "b", "a", "xxxxxx", "a", "x");
    for ($i = 0; $i < count($target); $i++) {
      $handler->expectArgumentsAt($i, "accept", array($target[$i], '*'));
    }
    $handler->expectCallCount("accept", count($target));
    $handler->setReturnValue("accept", true);
    $lexer = new WactExpressionLexer($handler);
    $lexer->addPattern("a+");
    $lexer->addPattern("b+");
    $this->assertTrue($lexer->parse("ababbxbaxxxxxxax"));
  }
}

class TestOfExpressionLexerModes extends UnitTestCase {
  function TestOfExpressionLexerModes() {
    $this->UnitTestCase();
  }

  function testIsolatedPattern() {
    $handler = new MockTestParser();
    $handler->expectArgumentsAt(0, "a", array("a", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(1, "a", array("b", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(2, "a", array("aa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(3, "a", array("bxb", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(4, "a", array("aaa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(5, "a", array("x", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(6, "a", array("aaaa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(7, "a", array("x", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectCallCount("a", 8);
    $handler->setReturnValue("a", true);
    $lexer = new WactExpressionLexer($handler, "a");
    $lexer->addPattern("a+", "a");
    $lexer->addPattern("b+", "b");
    $this->assertTrue($lexer->parse("abaabxbaaaxaaaax"));
  }

  function testModeChange() {
    $handler = new MockTestParser();
    $handler->expectArgumentsAt(0, "a", array("a", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(1, "a", array("b", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(2, "a", array("aa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(3, "a", array("b", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(4, "a", array("aaa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(0, "b", array(":", EXPRESSION_LEXER_ENTER));
    $handler->expectArgumentsAt(1, "b", array("a", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(2, "b", array("b", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(3, "b", array("a", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(4, "b", array("bb", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(5, "b", array("a", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(6, "b", array("bbb", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(7, "b", array("a", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectCallCount("a", 5);
    $handler->expectCallCount("b", 8);
    $handler->setReturnValue("a", true);
    $handler->setReturnValue("b", true);
    $lexer = new WactExpressionLexer($handler, "a");
    $lexer->addPattern("a+", "a");
    $lexer->addEntryPattern(":", "a", "b");
    $lexer->addPattern("b+", "b");
    $this->assertTrue($lexer->parse("abaabaaa:ababbabbba"));
  }

  function testNesting() {
    $handler = new MockTestParser();
    $handler->setReturnValue("a", true);
    $handler->setReturnValue("b", true);
    $handler->expectArgumentsAt(0, "a", array("aa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(1, "a", array("b", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(2, "a", array("aa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(3, "a", array("b", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(0, "b", array("(", EXPRESSION_LEXER_ENTER));
    $handler->expectArgumentsAt(1, "b", array("bb", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(2, "b", array("a", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(3, "b", array("bb", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(4, "b", array(")", EXPRESSION_LEXER_EXIT));
    $handler->expectArgumentsAt(4, "a", array("aa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(5, "a", array("b", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectCallCount("a", 6);
    $handler->expectCallCount("b", 5);
    $lexer = new WactExpressionLexer($handler, "a");
    $lexer->addPattern("a+", "a");
    $lexer->addEntryPattern("(", "a", "b");
    $lexer->addPattern("b+", "b");
    $lexer->addExitPattern(")", "b");
    $this->assertTrue($lexer->parse("aabaab(bbabb)aab"));
  }

  function testSingular() {
    $handler = new MockTestParser();
    $handler->setReturnValue("a", true);
    $handler->setReturnValue("b", true);
    $handler->expectArgumentsAt(0, "a", array("aa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(1, "a", array("aa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(2, "a", array("xx", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(3, "a", array("xx", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(0, "b", array("b", EXPRESSION_LEXER_SPECIAL));
    $handler->expectArgumentsAt(1, "b", array("bbb", EXPRESSION_LEXER_SPECIAL));
    $handler->expectCallCount("a", 4);
    $handler->expectCallCount("b", 2);
    $lexer = new WactExpressionLexer($handler, "a");
    $lexer->addPattern("a+", "a");
    $lexer->addSpecialPattern("b+", "a", "b");
    $this->assertTrue($lexer->parse("aabaaxxbbbxx"));
  }

  function testUnwindTooFar() {
    $handler = new MockTestParser();
    $handler->setReturnValue("a", true);
    $handler->expectArgumentsAt(0, "a", array("aa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(1, "a", array(")", EXPRESSION_LEXER_EXIT));
    $handler->expectCallCount("a", 2);
    $lexer = new WactExpressionLexer($handler, "a");
    $lexer->addPattern("a+", "a");
    $lexer->addExitPattern(")", "a");
    $this->assertFalse($lexer->parse("aa)aa"));
  }
}

class TestOfExpressionLexerHandlers extends UnitTestCase {
  function TestOfExpressionLexerHandlers() {
    $this->UnitTestCase();
  }

  function testModeMapping() {
    $handler = new MockTestParser();
    $handler->setReturnValue("a", true);
    $handler->expectArgumentsAt(0, "a", array("aa", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(1, "a", array("(", EXPRESSION_LEXER_ENTER));
    $handler->expectArgumentsAt(2, "a", array("bb", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(3, "a", array("a", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectArgumentsAt(4, "a", array("bb", EXPRESSION_LEXER_MATCHED));
    $handler->expectArgumentsAt(5, "a", array(")", EXPRESSION_LEXER_EXIT));
    $handler->expectArgumentsAt(6, "a", array("b", EXPRESSION_LEXER_UNMATCHED));
    $handler->expectCallCount("a", 7);
    $lexer = new WactExpressionLexer($handler, "mode_a");
    $lexer->addPattern("a+", "mode_a");
    $lexer->addEntryPattern("(", "mode_a", "mode_b");
    $lexer->addPattern("b+", "mode_b");
    $lexer->addExitPattern(")", "mode_b");
    $lexer->mapHandler("mode_a", "a");
    $lexer->mapHandler("mode_b", "a");
    $this->assertTrue($lexer->parse("aa(bbabb)b"));
  }
}
?>