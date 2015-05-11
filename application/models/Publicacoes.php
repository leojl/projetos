<?php

class Application_Model_Publicacoes extends Zend_Db_Table_Abstract {
    protected $_name = 'publicacao';
    
    public function inserir($projeto,$titulo,$link){
        $dados = array(
            'projeto' => $projeto,
            'titulo' => $titulo,
            'links' => $link
        );
        
        return $this->insert($dados);
        
    }
    
}