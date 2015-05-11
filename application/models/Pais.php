<?php

class Application_Model_Pais extends Zend_Db_Table_Abstract
{
    protected $_name = 'pais';    
    
    public function consulta(array $campo = null, $where = null, $valor = null, $order = null, $limit = null) {
		
		$select = $this->select()->from($this, $campo)->order($order)->limit($limit);
		if(!is_null($where)){
			$select->where($where, $valor);
		}
		return $this->fetchAll($select)->toArray();
    }
    //PAIS DA INSTITUIÇAO
    public function inserirInstituicao(array $request) {
        //$dao = new Application_Model_Pais();
        $dados = array(
            'nome' => $request['pais-inst']
            
        );

        return $this->insert($dados);
         
        
    }
    //PAIS DO USUÁRIO
    public function inserirUsuario(array $request) {
        //$dao = new Application_Model_Pais();
        $dados = array(
            'nome' => $request['pais-usuario']
            
        );

         return $this->insert($dados);
    }
    
    public function ultimoRegistro(){
        $select = $this->select()
                ->from($this)
                ->order('id DESC')
                ->limit(1);
        
        return $this->fetchRow($select);
        //fetchAll($select)->toArray();
    }
 
}
?>
