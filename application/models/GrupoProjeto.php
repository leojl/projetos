<?php

class Application_Model_GrupoProjeto extends Zend_Db_Table_Abstract
{
    protected $_name = 'grupo_projeto';    
    
    public function consulta(array $campo = null, $where = null, $valor = null, $order = null, $limit = null) {
		
		$select = $this->select()->from($this, $campo)->order($order)->limit($limit);
		if(!is_null($where)){
			$select->where($where, $valor);
		}
		return $this->fetchAll($select)->toArray();
    }
    
    public function insert(array $request) {
        
        $dados = array(
            'nome' => $request['']
            
        );

        return $this->insert($dados);
    }
 
}
?>
