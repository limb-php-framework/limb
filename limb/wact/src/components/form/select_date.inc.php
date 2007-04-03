<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: select_date.inc.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/components/form/form.inc.php';
require_once 'select.inc.php';

/**
 * extends WactSelectSingleComponent with new methods for month handling
 */
class WactSelectMonth extends WactSelectSingleComponent
{
    /**
     * @var string strftime() parameter for option tag contents
     */
    var $format = '&B';  // == long

    /**
     * @var string strftime() parameter for option tag values
     */
    var $valueFormat = '%m'; // == numeric

    /**
     * translate human-readable string in strftime() parameter
     */
    function getFormat($format) {
        switch (strtolower($format)) {
            case 'numeric':
                return '%m';
            case 'short':
                return '%b';
            case 'long':
            default:
                return '%B';
        }
    }

    /**
     * Allowed options are 'numeric', 'long', 'short'.
     * @param string
     */
    function setValueFormat($format='numeric') {
        $this->valueFormat = $this->getFormat($format);
    }

    /**
     * Allowed options are 'numeric', 'long', 'short'.
     * @param string
     */
    function setFormat($format='long') {
        $this->format = $this->getFormat($format);
    }

    /**
     * set option tag choices
     */
    function fillChoices() {
        $months = array();
        for ($i=1; $i<=12; $i++) {
            $months[strftime($this->valueFormat, mktime(0, 0, 0, $i, 1, 2000))]
                = strftime($this->format, mktime(0, 0, 0, $i, 1, 2000));
        }
        $this->setChoices($months);
    }

    /**
     * set selection
     */
    function setSelectedMonth($m) {
        $this->setSelection(strftime($this->valueFormat, mktime(0, 0, 0, $m, 1, 2000)));
    }
}
//--------------------------------------------------------------------------------

/**
 * Runtime form:selectdate API
 * @todo EXPERIMENTAL
 * @package wact
 */
class WactFormSelectDateComponent extends WactFormComponent
{
    var $selectYear = null;
    var $selectMonth = null;
    var $selectDay = null;
    var $selectedTime = array();

    var $setDefaultSelection = false;
    var $asArray = false;
    var $groupName;

    /**
     * the compiler complains if not defined...
     */
    function isVisible() {
        return true;
    }

    /**
     * @param string 'name' attribute of the form:selectdate tag
     */
    function setGroupName($name) {
        $this->groupName = $name;
    }

    function setAsArray() {
        $this->asArray = $this->getAttribute('asArray');
    }

    /**
     * @param mixed int|string unix timestamp or ISO-8601 timestamp
     * @param WactCodeWriter
     * @return array
     * @access private
     */
    function parseTime($time=null)
    {
        if (is_integer($time)) {
            //$time = unix timestamp
            return array(
                'year'  => date('Y', $time),
                'month' => date('m', $time),
                'day'   => date('d', $time)
            );
        }
        $len = (is_string($time)) ? strlen($time) : 0;
        if ($len == 14) {
            //$time = mysql timestamp YYYYMMDDHHMMSS
            return array(
                'year'  => (int)substr($time, 0, 4),
                'month' => (int)substr($time, 4, 2),
                'day'   => (int)substr($time, 6, 2),
            );
        }

        if ($len == 10 || $len == 19) {
            //$time = ISO-8601 timestamp YYYY-MM-DD or YYYY-MM-DD HH:MM:SS
            return array(
                'year'  => (int)substr($time, 0, 4),
                'month' => (int)substr($time, 5, 2),
                'day'   => (int)substr($time, 8, 2),
            );
        }
        //if everything failed, try with strtotime
        if (empty($time)) {
            $time = 'now';
        }
        $time = strtotime($time);
        if (!is_numeric($time) || $time == -1) {
            $time = strtotime('now');
        }
        return array(
            'year'  => date('Y', $time),
            'month' => date('m', $time),
            'day'   => date('d', $time),
        );
    }

    /**
     * build SelectSimpleComponent object and set options for years
     */
    function prepareYear()
    {
        $this->selectYear  = new WactSelectSingleComponent(); //SelectYear
        $this->addChild($this->selectYear);

        $start = ($this->hasAttribute('startYear') ? $this->getAttribute('startYear') : date('Y'));
        $end   = ($this->hasAttribute('endYear') ? $this->getAttribute('endYear') : date('Y'));
        if ((strpos($start.'', '+') !== false) || (strpos($start.'', '-') !== false)) {
            $start += date('Y');
        }
        if ((strpos($end.'', '+') !== false) || (strpos($end.'', '-') !== false)) {
            $end += date('Y');
        }

        $years = array();
        for ($i=$start; $i<=$end; $i++) {
            $years[$i] = $i;
        }
        $this->selectYear->setChoices($years);
        if ($this->setDefaultSelection) {
            $this->selectYear->setSelection($this->selectedTime['year']);
        }

        //maintain selection through pages
        $form_component = $this->findParentByClass('WactFormComponent');

        if ($y = $form_component->getValue($this->groupName.'_Year'))
            $this->selectYear->setSelection($y);

        if ($date = $form_component->getValue($this->groupName))
        {
            if (is_array($date) && array_key_exists('Year', $date))
                $this->selectYear->setSelection($date['Year']);
            else
            {
              $this->selectedTime = $this->parseTime($date);
              $this->selectYear->setSelection($this->selectedTime['year']);
            }
        }
    }

    /**
     * build SelectSimpleComponent object and set options for months
     */
    function prepareMonth()
    {
        $this->WactSelectMonth = new WactSelectMonth();
        $this->addChild($this->WactSelectMonth);

        if ($this->hasAttribute('monthValueFormat')) {
            $this->WactSelectMonth->setValueFormat($this->getAttribute('monthValueFormat'));
        }
        if ($this->setDefaultSelection) {
            $this->WactSelectMonth->setSelectedMonth($this->selectedTime['month']);
        }

        //maintain selection through pages
        $FormComponent = &$this->findParentByClass('WactFormComponent');
        if ($m = $FormComponent->getValue($this->groupName.'_Month')) {
            $this->WactSelectMonth->setSelection($m);
        }
        if ($date = $FormComponent->getValue($this->groupName)) {
            if (is_array($date) && array_key_exists('Month', $date)) {
                $this->WactSelectMonth->setSelection($date['Month']);
            } else {
                $this->selectedTime = $this->parseTime($date);
                $this->WactSelectMonth->setSelectedMonth($this->selectedTime['month']);
            }
        }
    }

    /**
     * build SelectSimpleComponent object and set options for days
     */
    function prepareDay()
    {

        $this->selectDay = new WactSelectSingleComponent(); // new SelectDay
        $this->addChild($this->selectDay);

        $days = array();
        for ($i=1; $i<=31; $i++) {
            $days[$i] = $i;
        }
        $this->selectDay->setChoices($days);
        if ($this->setDefaultSelection) {
            $this->selectDay->setSelection($this->selectedTime['day']);
        }

        //maintain selection through pages
        $FormComponent = &$this->findParentByClass('WactFormComponent');
        if ($d = $FormComponent->getValue($this->groupName.'_Day')) {
            $this->selectDay->setSelection($d);
        }
        if ($date = $FormComponent->getValue($this->groupName)) {
            if (is_array($date) && array_key_exists('Day', $date)) {
                $this->selectDay->setSelection($date['Day']);
            } else {
                $this->selectedTime = $this->parseTime($date);
                $this->selectDay->setSelection($this->selectedTime['day']);
            }
        }
    }

    /**
     * override default behaviour when onInitial() is called
     */
    function setSelection($time=null) {
        if (is_null($time)) {
            $time = time();
        }
        $this->selectedTime = $this->parseTime($time);
        $this->setDefaultSelection = true;
    }

    /**
     * @return WactSelectSingleComponent object
     * @access protected
     */
    function getYear() {
        return $this->selectYear;
    }

    /**
     * @return WactSelectSingleComponent object
     * @access protected
     */
    function getMonth() {
        return $this->WactSelectMonth;
    }

    /**
     * @return WactSelectSingleComponent object
     * @access protected
     */
    function getDay() {
        return $this->selectDay;
    }
}
?>
