<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        
    }

    public function indexAction()
    {
        $this->_redirect('http://nbcgib.uesc.br/projetos/public/projeto');
    }

    
}



