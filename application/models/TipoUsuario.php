<?php

class Application_Model_TipoUsuario extends Zend_Db_Table_Abstract
{
    protected $_name = 'tipo_usuario';
    
    public function consulta(array $campo = null, $where = null, $valor = null, $order = null, $limit = null) {
		
		$select = $this->select()->from($this, $campo)->order($order)->limit($limit);
		if(!is_null($where)){
			$select->where($where, $valor);
		}
		return $this->fetchAll($select)->toArray();
    }
    
    public function inserir(array $request) {
        
        $dados = array(
            'tipo' => $request['tipo_usuario']
            
        );

        return $this->insert($dados);
    }
 
}
?>
