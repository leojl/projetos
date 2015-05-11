<?php

class Application_Model_AreaConhecimento extends Zend_Db_Table_Abstract
{
    protected $_name = 'area_conhecimento';
    
    public function consulta(array $campo = null, $where = null, $valor = null, $order = null, $limit = null) {
		
		$select = $this->select()->from($this, $campo)->order($order)->limit($limit);
		if(!is_null($where)){
			$select->where($where, $valor);
		}
		return $this->fetchAll($select)->toArray();
    }
    
    public function inserir(array $request,$idDepartamento) {
        //$dao = new Application_Model_DbTable_Area_conhecimento();
        $dados = array(
            'nome' => $request['area-conhecimento'],
            'departamento' => $idDepartamento
            
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
