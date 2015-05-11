<?php

class Application_Model_Status extends Zend_Db_Table_Abstract
{
    protected $_name = 'status'; 
    
    public function consulta(array $campo = null, $where = null, $valor = null, $order = null, $limit = null) {
		
		$select = $this->select()->from($this, $campo)->order($order)->limit($limit);
		if(!is_null($where)){
			$select->where($where, $valor);
		}
		return $this->fetchAll($select)->toArray();
    }
    
    public function inserir(array $request) {
        
        $dados = array(
            'status' => $request['']
            
        );

        return $this->insert($dados);
    }
 
}
?>
