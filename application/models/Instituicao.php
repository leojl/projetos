<?php

class Application_Model_Instituicao extends Zend_Db_Table_Abstract
{
    protected $_name = 'instituicao';  
    
   public function consulta(array $campo = null, $where, $valor = null, $order = null, $limit = null) {
		
		$select = $this->select()->from($this, $campo)->order($order)->limit($limit);
		if(!is_null($where)){
			$select->where($where, $valor);
		}
		return $this->fetchAll($select)->toArray();
    }
    
    public function inserir(array $request,$idUf) {
        //$dao = new Application_Model_DbTable_Instituicao();
        $dados = array(
            'nome' => $request['nome-inst'],
            'endereco' => $request['end-inst'],
            'complemento' => $request['comp-inst'],
            'bairro' => $request['bairro-inst'],
            'cidade' => $request['cidade-inst'],
            'divisao_administrativa' => $idUf,
            'cep' => $request['cep-inst'],
            'telefone' => $request['tel-inst'],
            'ramal' => $request['ramal-inst'],
            'fax' => $request['fax-inst']
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
