<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
* @author	Jason E. Sweat < jsweat_php AT yahoo DOT com >
*/

function math_filter($value, $exp, $file, $line) {
    $rpn = new WactMathRpn();
    $rpn->Filter = 'math';
    $rpn->SourceFile = $file;
    $rpn->StartingLineNo = $line;
    //fix a quirk where RPN does not interpret - as subtraction
    //this only fixes it if it is the first operation of the expression
    if ('-' == substr($exp,0,1)) {
        $exp = '+-1*'.substr($exp,1);
    }
    $calc = '('.$value.')'.$exp;
    return $rpn->calculate($calc, 'deg', false);
}

/**
 * Math::Rpn
 *
 * Purpose:
 *
 *     Change Expression To RPN (Reverse Polish Notation), Calculate RPN Expression
 *
 * Example:
 *
 *     $expression = "(2^3)+sin(30)-(!4)+(3/4)";
 *
 *     $rpn = new RpnConverter(false);
 *     echo $rpn->getValue($expression);
 *
 * Example page:
 *
 *       http://www.maciek.maribex.wizja.net.pl/rpn/
 *
 * 2004-06-11 Jason E. Sweat < jsweat_php AT yahoo DOT com >
 * Modifications for use by WACT:
 * 1) elimination of PEAR.php include requirement
 * 		this was done buy switching the _raiseError method
 * 		to trigger a WACT error instead
 * 2) in several location, undefined indexes were referenced
 * 		this was handled using the error suppression operator
 * 3) corrected error when parsing invalid expressions
 *
 * @author   Maciej Szczytowski <admin@e-rower.pl>
 * @version  1.1
 * @package wact
 * @access   public
 */
class WactMathRpn
{
    /**#@+
     * added for WACT error handling
     * @access protected
     */
    var $Filter;
    var $SourceFile;
    var $StartingLineNo;
    /**#@-*/
    /**
     * Input expression
     *
     * @var    string
     * @access private
     */
    var $_input = '';

    /**
     * Array with input expression
     *
     * @var    array
     * @access private
     */
    var $_input_array = array();

    /**
     * Array with output expression in RPN
     *
     * @var    array
     * @access private
     */
    var $_output = array();

    /**
     * Temporary stack
     *
     * @var    array
     * @access private
     */
    var $_stack = array();

    /**
     * Value of expression
     *
     * @var    float
     * @access private
     */
    var $_value = 0.0;

    /**
     * Angle's unit: rad - true, deg - false
     *
     * @var    boolean
     * @access private
     */
    var $_angle = true;

    /**
     * PEAR Error
     *
     * @var    object PEAR
     * @access private
     */
    var $_error = null;

    /**
     * Timer
     *
     * @var    float
     * @access private
     */
    var $_timer = 0.0;

    /**
     * Array of operators whit priority and math function
     * operator => (name, priority, number of arguments, function)
     *
     * @var    array
     * @access private
     */
    var $_operation = array (
        '('    => array ('left bracket', 0),
        ')'    => array ('right bracket', 1),
        '+'    => array ('sum', 1, 2, '_sum'),
        '-'    => array ('difference', 1, 2, '_difference'),
        '*'    => array ('multiplication', 2, 2, '_multiplication'),
        '/'    => array ('division', 2, 2, '_division'),
        'r'    => array ('root', 3, 2, '_root'),
        '^'    => array ('power', 3, 2, '_power'),
        'sin'  => array ('sine', 3, 1, '_sin'),
        'cos'  => array ('cosine', 3, 1, '_cos'),
        'tan'  => array ('tangent', 3, 1, '_tan'),
        'asin' => array ('asine', 3, 1, '_asin'),
        'acos' => array ('acosine', 3, 1, '_acos'),
        'atan' => array ('atangent', 3, 1, '_atan'),
        'sqrt' => array ('square root', 3, 1, '_sqrt'),
        'exp'    => array ('power of e', 3, 1, '_exp'),
        'log'  => array ('logarithm', 3, 1, '_log'),
        'ln'   => array ('natural logarithm', 3, 1, '_ln'),
        'E'  => array ('power of 10', 3, 1, '_E'),
        'abs'  => array ('absolute value', 3, 1, '_abs'),
        '!'    => array ('factorial', 3, 1, '_factorial'),
        'pi'   => array ('value of pi', 3, 0, '_const_pi'),
        'e'    => array ('value of e', 3, 0, '_const_e'),
        'mod'    => array ('modulo', 3, 2, '_mod'),
        'div'    => array ('integer division', 3, 2, '_div'),
    );

    /**
     * Return a WACT error
     *
     * @return object WACT error
     * @access private
     */
    function _raiseError ($error)
    {
      throw new WactException('An Interal Error occoured while processing the filter',
                              array('error' => $error,
                                    'filter' => $this->Filter,
                                    'file' => $this->SourceFile,
                                    'line' => $this->StartingLineNo));
    }

    /**
     * Return a operator's array
     *
     * @return array Array with operator's name, priority, arguments, function's name and syntax
     * @access public
     */
    function getOperators () {
        $return = array();
        while(list($key, $val) = each($this->_operation)) {

            if ($val[2] == 2) {
                $syntax = 'A ' . $key . ' B';
            } elseif ($val[2] == 1) {
                $syntax = $key . ' A';
            } else {
                $syntax = $key;
            }

            $return[] = array (
                'operator' => $key,
                'name' => $val[0],
                'priority' => $val[1],
                'arguments' => $val[2],
                'function' => $val[3],
                'syntax' => $syntax
            );
        }

        return $return;
    }

    /**
     * Add new operator
     *
     * @param string $operator New operator
     * @param string $function Function name
     * @param integer $priority New operator's priority
     * @param integer $no_of_arg Number of function's arguments
     * @param string $text New operator's description
     * @access public
     */
    function addOperator ($operator, $function_name, $priority = 3, $no_of_arg = 0, $text = '') {
        if(preg_match("/^([\W\w]+)\:\:([\W\w]+)$/",$function_name,$match)) {
            $class = $match[1];
            $method = $match[2];
            $function = array (
               'type' => 'userMethod',
               'class' => $class,
               'method' => $method
            );
        } else {
            $function = array (
               'type' => 'userFunction',
               'function' => $function_name
            );
        }

        $this->_operation[$operator] = array ($text, $priority, $no_of_arg, $function);
    }

    /**
     * Calculate the $input expression
     *
     * @param mixed $input Infix expression string or RPN expression array
     * @param string $angle Angle's unit - 'rad' or 'deg'
     * @param boolean $is_rpn True if $input is RPN expression or false if $input is infix expression
     * @return mixed Value of $input expression or a PEAR error
     * @access public
     */
    function calculate($input = '', $angle = 'rad', $is_rpn = false) {
        if($angle = 'deg') $this->_angle = false;
        else $this->_angle = true;

        if($input == '') {
            $this->_error = $this->_raiseError('Empty input expression');
            return $this->_error;
        }

        if(!$is_rpn) {
            $this->_input = $input;

            $this->_stringToArray ();
            if($this->_error <> null) return $this->_error;

            $this->_arrayToRpn();
            if($this->_error <> null) return $this->_error;
        } else {
            if(!is_array($input)) {
                $this->_error = $this->_raiseError('Wrong input expression');
                return $this->_error;
            }
            $this->_input = implode(' ',$input);
            $this->_input_array = $input;
            $this->_output = $input;
        }

        $this->_rpnToValue();
        if($this->_error <> null) return $this->_error;

        return $this->_value;
    }

    /**
     * Return a input array
     *
     * @return array Input array
     * @access public
     */
    function getInputArray() {
        return $this->_input_array;
    }

    /**
     * Return a RPN array
     *
     * @return array RPN array
     * @access public
     */
    function getRpnArray() {
        return $this->_output;
    }

    /**
     * Return a counting time in second
     *
     * @return float Counting time in seconds
     * @access public
     */
    function getTimer() {
        return $this->_timer;
    }

    /**
     * Check that $key is a key of $array (conformity to php<4.1.0)
     *
     * @param string $key
     * @param array $array
     * @param integer $type 0 - return true if $key is $array's key, 1 - return true if $key is $array's key and there isn't any occurrence of $key in another $array's key
     * @return boolean true when $key is a key of $array, or false
     * @access private
     */

    function _keyExists($key,$array,$type) {
        $keys = array_keys($array);

        if($type == 1) {
            $count = 0;
            while (list($keys_key, $keys_val) = each($keys)) {
                if(is_integer(strpos($keys_val, $key)) && (strpos($keys_val, $key)==0)) $count++;
            }
            if(($count==1) && in_array($key,$keys)) return true;
            else return false;
        } else {
            if(in_array($key,$keys)) return true;
            else return false;
        }
    }

    /**
     * Check that $value is nan (conformity to php<4.2.0)
     *
     * @param float $value checking value
     * @return boolean true when $value is nan, or false
     * @access private
     */
    function _isNan($value) {
        if(function_exists('is_nan')) {
            return is_nan($value);
        } else {
            if((substr($value,-3) == 'IND') || (substr($value,-3) == 'NAN')) return true;
            else return false;
        }
    }

    /**
     * Check that $value is infinite (conformity to php<4.2.0)
     *
     * @param float $value checking value
     * @return boolean true when $value is infinite, or false
     * @access private
     */
    function _isInfinite($value) {
        if(function_exists('is_finite')) {
            return !is_finite($value);
        } else {
            if(substr($value,-3) == 'INF') return true;
            else return false;
        }
    }

    /**
     * Change input expression into array
     *
     * @return array Input expression changed into array
     * @access private
     */
    function _stringToArray () {
        $temp_operator = null;
        $temp_value = null;

        for($i = 0; $i < strlen($this->_input); $i++) {
            if ($this->_input[$i] == ' ') {
                if ($temp_operator != null) {
                    array_push($this->_input_array, $temp_operator);
                    $temp_operator = null;
                }
                if ($temp_value != null) {
                    array_push($this->_input_array, $temp_value);
                    $temp_value = null;
                }
            } elseif (
                ($temp_value == null)
                && (@$this->_operation[$temp_operator][2]>0
                    || !@isset($this->_operation[$temp_operator][2]))
                && ($this->_input[$i] == '-')
            ) {
                if ($temp_operator != null) {
                    array_push($this->_input_array, $temp_operator);
                    $temp_operator = null;
                }

                array_push($this->_input_array, '-1');
                array_push($this->_input_array, '*');
            } elseif ((is_numeric($this->_input[$i])) || ($this->_input[$i] == '.')) {
                if ($temp_operator != null) {
                    array_push($this->_input_array, $temp_operator);
                    $temp_operator = null;
                }

                $temp_value .= $this->_input[$i];
            } else {
                if ($this->_keyExists($temp_operator, $this->_operation, 1)) {
                    array_push($this->_input_array, $temp_operator);
                    $temp_operator = null;
                }

                if ($temp_value != null) {
                    array_push($this->_input_array, $temp_value);
                    $temp_value = null;
                }

                $temp_operator .= $this->_input[$i];
            }
        }

        if ($temp_operator != null) {
            array_push($this->_input_array, $temp_operator);
        } else {
            array_push($this->_input_array, $temp_value);
        }

        $this->_testInput();

        return $this->_input_array;
    }

    /**
     * Check input array and return correct array or a PEAR Error
     *
     * @return object Null or a PEAR Error
     * @access private
     */
    function _testInput() {
        if (!count($this->_input_array)) {
            $this->_input_array = null;
            $this->_error = $this->_raiseError('Undefined input array');
            return $this->_error;
        }

        $bracket = 0;
        for($i = 0; $i <= count($this->_input_array); $i++) if (@$this->_input_array[$i] == '(') $bracket++;
        for($i = 0; $i <= count($this->_input_array); $i++) if (@$this->_input_array[$i] == ')') $bracket--;

        if ($bracket <> 0) {
                $this->_input_array = null;
                $this->_error = $this->_raiseError('Syntax error');
                return $this->_error;
        }

        for($i = 0; $i < count($this->_input_array); $i++) {
            if ((!is_numeric($this->_input_array[$i])) && (!$this->_keyExists($this->_input_array[$i], $this->_operation, 0))) {
                $error_operator = $this->_input_array[$i];
                $this->_input_array = null;
                $this->_error = $this->_raiseError('Undefined operator \''. $error_operator.'\'');
                return $this->_error;
            }
        }

        $this->_error = null;
        return $this->_error;
    }

    /**
     * Add value to the end of stack
     *
     * @param string $value Value to add into stack
     * @access private
     */
    function _stackAdd($value) {
        array_push($this->_stack, $value);
    }

    /**
     * Delete and return value from the end of stack
     *
     * @return string Value deleted from stack
     * @access private
     */
    function _stackDelete() {
        return array_pop($this->_stack);
    }

    /**
     * Return priority of value
     *
     * @param string $value Value to get priority
     * @return integer Priority
     * @access private
     */
    function _priority($value) {
        return $this->_operation[$value][1];
    }
    /**
     * Return priority of value from the end of stack
     *
     * @return integer Priority of operator from stack's top
     * @access private
     */
    function _stackPriority() {
        $value = $this->_stackDelete();
        $this->_stackAdd($value);
        return $this->_priority($value);
    }

    /**
     * Return true whene the stack is empty
     *
     * @return boolean Stack is empty (true) or not (false)
     * @access private
     */
    function _stackEmpty() {
        if (count($this->_stack)) {
            return false;
        }
        else return true;
    }

    /**
     * Add value into output array
     *
     * @param string $value Value to add into output array
     * @access private
     */
    function _outputAdd($value) {
        if ($value<>'(') {
            array_push($this->_output, $value);
        }
    }

    /**
     * Change input array into RPN array
     *
     * @return array Array with RPN expression
     * @access private
     */
    function _arrayToRpn() {

        if ($this->_error <> null) {
            $this->_output = array();
            return $this->_output;
        }

        for($i = 0; $i < count($this->_input_array); $i++) {

            $temp = $this->_input_array[$i];

            if (is_numeric($temp)) {
                $this->_outputAdd($temp);
            } else {
                if ($temp == ')') {
                    while(!$this->_stackEmpty() && ($this->_stackPriority() >= 1)) {
                        $this->_outputAdd($this->_stackDelete());
                    }
                    if (!$this->_stackEmpty()) {
                        $this->_stackDelete();
                    }

                } elseif ($temp=='(') {
                    $this->_stackAdd($temp);
                } elseif (($this->_stackEmpty()) || (($this->_priority($temp) > $this->_stackPriority()))) {
                   $this-> _stackAdd($temp);
                } else {
                    while(!$this->_stackEmpty() && ($this->_priority($temp) <= $this->_stackPriority())) {
                        $this->_outputAdd($this->_stackDelete());
                    }
                    $this->_stackAdd($temp);
                }

            }

        }

        while(!$this->_stackEmpty()) {
            $this->_outputAdd($this->_stackDelete());
        }

        return $this->_output;
    }

    /**
     * Return position of the first operator in array
     *
     * @param array $array Temporary array
     * @return integer Position of the first operator
     * @access private
     */
    function _nextOperator($array) {
        $pos = 0;
        while(is_numeric($array[$pos])) {
            $pos++;
            if ($pos >= count($array)) {
                return -1;
            }
        }
        return $pos;

    }

    /**
     * Delete from array operator [posision $pos] and its argument and insert new value
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of the last operator
     * @param integer $arg Number of last operator's arguments
     * @param float $result Last operation result
     * @return array New temporary array
     * @access private
     */
    function _refresh($temp, $pos, $arg, $result) {
        $temp1 = array_slice($temp, 0, $pos-$arg);
        $temp1[] = $result;
        $temp2 = array_slice($temp, $pos+1);
        return array_merge($temp1, $temp2);
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _sum($temp, $pos) {
        return $temp[$pos-2]+$temp[$pos-1];
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _difference($temp, $pos) {
        return $temp[$pos-2]-$temp[$pos-1];
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _multiplication($temp, $pos) {
        return $temp[$pos-2]*$temp[$pos-1];
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _division($temp, $pos) {
        if ($temp[$pos-1]==0) {
            $this->_error = $this->_raiseError('Division by 0');
            $this->_value = null;
            return $this->value;
        }
        return $temp[$pos-2]/$temp[$pos-1];
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _root($temp, $pos) {
        return pow($temp[$pos-1], (1/$temp[$pos-2]));
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _power($temp, $pos) {
        return pow($temp[$pos-2], $temp[$pos-1]);
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _sin($temp, $pos) {
        if ($this->_angle) {
            $angle = $temp[$pos-1];
        } else {
            $angle = deg2rad($temp[$pos-1]);
        }
        return sin($angle);
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _cos($temp, $pos) {
        if ($this->_angle) {
            $angle = $temp[$pos-1];
        } else {
            $angle = deg2rad($temp[$pos-1]);
        }
        return cos($angle);
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _tan($temp, $pos) {
        if ($this->_angle) {
            $angle = $temp[$pos-1];
        } else {
            $angle = deg2rad($temp[$pos-1]);
        }
        return tan($angle);
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _asin($temp, $pos) {
        $angle = asin($temp[$pos-1]);
        if (!$this->_angle) {
            $angle = rad2deg($angle);
        }
        return $angle;
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _acos($temp, $pos) {
        $angle = acos($temp[$pos-1]);
        if (!$this->_angle) {
            $angle = rad2deg($angle);
        }
        return $angle;
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _atan($temp, $pos) {
        $angle = atan($temp[$pos-1]);
        if (!$this->_angle) {
            $angle = rad2deg($angle);
        }
        return $angle;
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _sqrt($temp, $pos) {
        return sqrt($temp[$pos-1]);
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _exp($temp, $pos) {
        return exp($temp[$pos-1]);
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _log($temp, $pos) {
        return log10($temp[$pos-1]);
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _ln($temp, $pos) {
        return log($temp[$pos-1]);
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _const_pi($temp, $pos) {
        return M_PI;
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _const_e($temp, $pos) {
        return M_E;
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _E($temp, $pos) {
        return pow(10, $temp[$pos-1]);
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _factorial($temp, $pos) {
        $factorial = 1;
        for($i=1;$i<=$temp[$pos-1];$i++) {
            $factorial *= $i;
        }
        return $factorial;
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _abs($temp, $pos) {
        return abs($temp[$pos-1]);
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _mod($temp, $pos) {
        return $temp[$pos-2]%$temp[$pos-1];
    }

    /**
     * Math function
     *
     * @param array $temp Temporary array
     * @param integer $pos Position of operator
     * @return float Function's relult
     * @access private
     */
    function _div($temp, $pos) {
        return floor($temp[$pos-2]/$temp[$pos-1]);
    }

    /**
     * Calculate RPN Expression and return value
     *
     * @return float Result of input expression
     * @access private
     */
    function _rpnToValue() {

        $time1 = $this->_getMicroTime();

        if ($this->_error <> null) {
            $this->_value = null;
            return $this->_value;
        }

        $this->_value = 0;
        $temp = $this->_output;

        do {
            $pos = $this->_nextOperator($temp);

            if ($pos == -1) {
                $this->_error = $this->_raiseError('Syntax error');
                $this->_value = null;
                return $this->_value;
            }

            $operator = $this->_operation[$temp[$pos]];
            $arg = $operator[2];
            $function = $operator[3];

            if (($arg==2) && ((!is_numeric($temp[$pos-1])) || (!is_numeric($temp[$pos-2])))) {
                $this->_error = $this->_raiseError('Syntax error');
                $this->_value = null;
                return $this->_value;
            } elseif (($arg==1) && (!is_numeric($temp[$pos-1]))) {
                $this->_error = $this->_raiseError('Syntax error');
                $this->_value = null;
                return $this->_value;
            }

            if(is_array($function)) {

                if($arg==2) $arg_array = array($temp[$pos-2],$temp[$pos-1]);
                elseif($arg==1) $arg_array = array($temp[$pos-1]);
                else $arg_array = array();

                if($function['type'] == 'userFunction') {
                    $this->_value = call_user_func_array($function['function'], $arg_array);
                } else {
                    $function_array = array(&$function['class'], $function['method']);
                    $this->_value = call_user_func_array($function_array, $arg_array);
                }
            } else {
                if (method_exists($this, $function)) {
                    $this->_value = $this->$function($temp, $pos);
                } else {
                    $this->_raiseError("invalid function '$function' temp '$temp' pos '$pos'");
                }
            }

            if ($this->_isNan($this->_value)) {
                $this->_error = $this->_raiseError('NAN value');
                $this->_value = null;
                return $this->_value;
            } elseif ($this->_isInfinite($this->_value)) {
                $this->_error = $this->_raiseError('Infinite value');
                $this->_value = null;
                return $this->_value;
            } elseif (is_null($this->_value)) {
                return $this->_value;
            }

            $temp = $this->_refresh($temp, $pos, $arg, $this->_value);
        } while(count($temp) > 1);

        $this->_value = $temp[0];

        $time2 = $this->_getMicroTime();

        $this->_timer = $time2 - $time1;

        return $this->_value;
    }

    /**
     * Return a time in second
     *
     * @return float Current time in seconds
     * @access private
     */
    function _getMicroTime() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

}


