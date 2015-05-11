<?php

class Application_Model_ProjetoUsuario extends Zend_Db_Table_Abstract
{
     protected $_name = 'projeto_usuario';
    
    public function consulta(array $campo = null, $where = null, $valor = null, $order = null, $limit = null) {
		
		$select = $this->select()->from($this, $campo)->order($order)->limit($limit);
		if(!is_null($where)){
			$select->where($where, $valor);
		}
		return $this->fetchAll($select)->toArray();
    }
    
    public function inserir(array $request, $usuario) {
        
        $dados = array(
            'projeto' => $request['comboProjetos'],
            'usuario' => $usuario,
            'tipo_usuario' => $request['tipo_usuario'],
            'status' => 2
            
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
