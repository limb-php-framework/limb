<?php

class lmbPHPUnitTestCase extends UnitTestCase
{
  function __construct($label = false) {
    parent::UnitTestCase($label);
  }

  function assertEquals($first, $second, $message = '%s')
  {
    return $this->assertEqual($first, $second, $message);
  }


//
//        function assertEquals($first, $second, $message = false) {
//            parent::assert(new EqualExpectation($first), $second, $message);
//        }
//
//        /**
//         *    Simple string equality.
//         *    @param $first          First value.
//         *    @param $second         Comparison value.
//         *    @param $message        Message to display.
//         *    @public
//         */
//        function assertEqualsMultilineStrings($first, $second, $message = false) {
//            parent::assert(new EqualExpectation($first), $second, $message);
//        }
//
//        /**
//         *    Tests a regex match.
//         *    @param $pattern        Regex to match.
//         *    @param $subject        String to search in.
//         *    @param $message        Message to display.
//         *    @public
//         */
//        function assertRegexp($pattern, $subject, $message = false) {
//            parent::assert(new PatternExpectation($pattern), $subject, $message);
//        }
//
//        /**
//         *    Sends an error which we interpret as a fail
//         *    with a different message for compatibility.
//         *    @param $message        Message to display.
//         *    @public
//         */
//        function error($message) {
//            parent::fail("Error triggered [$message]");
//        }
//
//        /**
//         *    Accessor for name.
//         *    @public
//         */
//       function name() {
//            return $this->getLabel();
//        }
//    }
}