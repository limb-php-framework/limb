<?php

lmb_require('limb/view/src/lmbView.class.php');

class lmbBlitzView extends lmbView
{
    private $templateInstance;

    function __call($methodName, $params)
    {
        $tpl = $this->getTemplateInstance();
        if(!method_exists($tpl, $methodName)) {
            throw new lmbException(
                'Wrong template method called', 
                array(
                    'template class' => get_class($tpl),
                    'method' => $methodName,
                )
            );
        }
        return call_user_method_array($methodName, $tpl, $params);        
    }

    function getTemplateInstance()
    {
        if(!$this->templateInstance) {
            if(!$this->hasTemplate()) {
                throw new lmbException('template not defined');
            }
            $this->templateInstance = new Blitz($this->getTemplate());
        }
        return $this->templateInstance;
    }
    
    function render()
    {
        foreach ($this->getVariables() as $name => $value) {
            $this->getTemplateInstance()->set(array($name => $value));
        }
        return $this->getTemplateInstance()->parse();
    }

}