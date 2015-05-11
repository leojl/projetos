<?php

class Application_Model_Departamento extends Zend_Db_Table_Abstract
{
    protected $_name = 'departamento';    
    
   public function consulta(array $campo = null, $where = null, $valor = null, $order = null, $limit = null) {
		
		$select = $this->select()->from($this, $campo)->order($order)->limit($limit);
		if(!is_null($where)){
			$select->where($where, $valor);
		}
		return $this->fetchAll($select)->toArray();
    }
    
    public function inserir(array $request,$idInstituicao) {
        //$dao = new Application_Model_DbTable_Departamento();
        $dados = array(
            'nome' => $request['departamento-inst'],
            'instituicao' => $idInstituicao
            
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
