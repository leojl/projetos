<?php

class Application_Model_Historico extends Zend_Db_Table_Abstract
{
    
    protected $_name = 'historico_alteracoes';
    
    public function inserir(array $request){
        
        $dados = array(
            
            'nome' => $request['nome'],
            'alteracao' => $request['alteracao'],
            'data' => $request['data'],
            
        );
        
        return $this->insert($dados);
        
    }

}

