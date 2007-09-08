<?php
/*
* Limb PHP Framework
*
* @link http://limb-project.com
* @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
* @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/
lmb_require('limb/view/src/lmbBlitzView.class.php');

class lmbBlitzViewTest extends UnitTestCase
{
    private function _createTemplateFile($name, $source)
    {
        file_put_contents($path = LIMB_VAR_DIR . $name, $source);
        return $path;
    }

    function testRenderSimpleVars()
    {
        $template = '{{$hello}}{{$again}}';
        $path = $this->_createTemplateFile('/simple.bhtml', $template);

        $view = new lmbBlitzView($path);
        $view->set('hello', 'Hello message!');
        $view->set('again', 'Hello again!');

        $this->assertEqual($view->render(), 'Hello message!Hello again!');
    }
    
    function testManualTemplateFunctionCall()
    {
        $template = '{{BEGIN foo}}{{END}}';
        $path = $this->_createTemplateFile('/simple.bhtml', $template);

        $view = new lmbBlitzView($path);        
        $this->assertTrue($view->hasContext('foo'));
        $this->assertFalse($view->hasContext('bar'));
    }

    function testRenderIteratedTemplates()
    {
        $template = 
            '{{ BEGIN outer }}o'
                .'{{ $outer_var }}'
                .'{{ BEGIN inner }}i'
                    .'{{ $inner_var }}'
                .'{{ END inner }}'
            .'{{ END }}';

        $data = array (
                array(
                    'outer_var' => 'a',
                    'inner' => array(
                        array('inner_var' => '1'),
                        array('inner_var' => '2'),
                        array('inner_var' => '3'),
                        ),
                array(
                    'outer_var' => 'b',
                    'inner' => array(
                        array('inner_var' => '4'),
                        array('inner_var' => '5'),
                        array('inner_var' => '6'),
                        ),
                )
                )
        );
        
        $out = 'oai1i2i3obi4i5i6';
        
        $path = $this->_createTemplateFile('/iteration.bhtml', $template);

        $view = new lmbBlitzView($path);
        $view->set('outer', $data);

        $this->assertEqual($view->render(), $out);
    }
    
}
