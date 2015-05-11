<?php

class Application_Model_ArquivosUsuarios extends Zend_Db_Table_Abstract {
    protected $_name = 'arquivos_usuarios';
    
    public function inserir($email,$caminhoArquivo){
        $dados = array(
            'email' => $email,
            'caminho_arquivo' => $caminhoArquivo
        );
        
        return $this->insert($dados);
        
    }
    
}