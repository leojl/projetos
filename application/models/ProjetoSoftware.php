<?php

class Application_Model_ProjetoSoftware extends Zend_Db_Table_Abstract
{
    protected $_name = 'projeto_software';
    
    public function consulta(array $campo = null, $where = null, $valor = null, $order = null, $limit = null) {
		
		$select = $this->select()->from($this, $campo)->order($order)->limit($limit);
		if(!is_null($where)){
			$select->where($where, $valor);
		}
		return $this->fetchAll($select)->toArray();
    }
    
    public function inserir($projeto,$software) {
        
      $dados = array(
            'projeto' => $projeto,
            'software' => $software
            
        );

        return $this->insert($dados);
    }
 
    public function ultimoRegistro(){
        $select = $this->select()
                ->from($this)
                ->order('id DESC')
                ->limit(1);
        
        return $this->fetchRow($select);
    }
}
?>
