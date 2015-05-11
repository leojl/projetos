<?php

class Application_Model_UnidadeFederativa extends Zend_Db_Table_Abstract
{
    protected $_name = 'divisao_administrativa';    
    
    public function consulta(array $campo = null, $where = null, $valor = null, $order = null, $limit = null) {
		
		$select = $this->select()->from($this, $campo)->order($order)->limit($limit);
		if(!is_null($where)){
			$select->where($where, $valor);
		}
		return $this->fetchAll($select)->toArray();
    }
    
    public function inserirInstituicao(array $request, $idPais) {
        //$dao = new Application_Model_DbTable_Unidade_federativa();
        $dados = array(
            'nome' => $request['uf-inst'],
            'pais' => $idPais
            
        );

        return $this->insert($dados);
    }
    
    public function inserirUsuario(array $request, $idPais) {
        //$dao = new Application_Model_DbTable_Unidade_federativa();
        $dados = array(
            'nome' => $request['uf-usuario'],
            'pais' => $idPais
            
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
