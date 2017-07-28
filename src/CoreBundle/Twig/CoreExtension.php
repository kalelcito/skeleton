<?php

namespace CoreBundle\Twig;

class CoreExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'core_extension';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sino', array($this, 'sinoFilter'), array('is_safe' => array('html'))),
        );
    }

    public function sinoFilter($value)
    {
        $result='';
        if($value){
            $result='<i class="fa fa-check-circle" style="color: green;" ></i>';
        }else{
            $result='<i class="fa fa-times-circle" style="color: red;" ></i>';
        }
        return $result;
    }
}
