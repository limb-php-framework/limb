<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactToDateFilterTest extends WactTemplateTestCase
{
  function testSimpleUse()
  {
    $template = '{$test|todate}';
    $this->registerTestingTemplate('/filters/core/todate/var.html', $template);

    $page = $this->initTemplate('/filters/core/todate/var.html');
    $page->set('test', 'April 3, 1970');
    $output = $page->capture();
    $check = mktime(0,0,0,4,3,1970);
    $this->assertEqual($output, $check);
  }

  function testEmptyDate()
  {
    $template = '{$test|todate}';
    $this->registerTestingTemplate('/filters/core/todate/empty_date.html', $template);

    $page = $this->initTemplate('/filters/core/todate/empty_date.html');
    $output = $page->capture();
    $this->assertEqual($output, '');
  }
}

/*
per http://oss.software.ibm.com/icu/apiref/classSimpleDateFormat.html



  Symbol   Meaning                 Presentation        Example
 ------   -------                 ------------        -------
 G        era designator          (Text)              AD
 y        year                    (Number)            1996
 Y        year (week of year)     (Number)            1997
 u        extended year           (Number)            4601
 M        month in year           (Text & Number)     July & 07
 d        day in month            (Number)            10
 h        hour in am/pm (1~12)    (Number)            12
 H        hour in day (0~23)      (Number)            0
 m        minute in hour          (Number)            30
 s        second in minute        (Number)            55
 S        fractional second       (Number)            978
 E        day of week             (Text)              Tuesday
 e        day of week (local 1~7) (Number)            2
 D        day in year             (Number)            189
 F        day of week in month    (Number)            2 (2nd Wed in July)
 w        week in year            (Number)            27
 W        week in month           (Number)            2
 a        am/pm marker            (Text)              PM
 k        hour in day (1~24)      (Number)            24
 K        hour in am/pm (0~11)    (Number)            0
 z        time zone               (Text)              Pacific Standard Time
 Z        time zone (RFC 822)     (Number)            -0800
 g        Julian day              (Number)            2451334
 A        milliseconds in day     (Number)            69540000
 '        escape for text         (Delimiter)         'Date='
 ''       single quote            (Literal)           'o''clock'




The count of pattern letters determine the format.

(Text): 4 or more, use full form, <4, use short or abbreviated form if it exists. (e.g., "EEEE" produces "Monday", "EEE" produces "Mon")

(Number): the minimum number of digits. Shorter numbers are zero-padded to this amount (e.g. if "m" produces "6", "mm" produces "06"). Year is handled specially; that is, if the count of 'y' is 2, the Year will be truncated to 2 digits. (e.g., if "yyyy" produces "1997", "yy" produces "97".) Unlike other fields, fractional seconds are padded on the right with zero.

(Text & Number): 3 or over, use text, otherwise use number. (e.g., "M" produces "1", "MM" produces "01", "MMM" produces "Jan", and "MMMM" produces "January".)

Any characters in the pattern that are not in the ranges of ['a'..'z'] and ['A'..'Z'] will be treated as quoted text. For instance, characters like ':', '.', ' ', '#' and '@' will appear in the resulting time text even they are not embraced within single quotes.

A pattern containing any invalid pattern letter will result in a failing UErrorCode result during formatting or parsing.

Examples using the US locale:

    Format Pattern                         Result
    --------------                         -------
    "yyyy.MM.dd G 'at' HH:mm:ss z"    ->>  1996.07.10 AD at 15:08:56 PDT
    "EEE, MMM d, ''yy"                ->>  Wed, July 10, '96
    "h:mm a"                          ->>  12:08 PM
    "hh 'o''clock' a, zzzz"           ->>  12 o'clock PM, Pacific Daylight Time
    "K:mm a, z"                       ->>  0:00 PM, PST
    "yyyyy.MMMMM.dd GGG hh:mm aaa"    ->>  1996.July.10 AD 12:08 PM


*/






