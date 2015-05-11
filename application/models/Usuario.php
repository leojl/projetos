<?php

class Application_Model_Usuario extends Zend_Db_Table_Abstract
{
    protected $_name = 'usuario';    
    
    public function consulta(array $campo = null, $where = null, $valor = null, $order = null, $limit = null) {
		
		$select = $this->select()->from($this, $campo)->order($order)->limit($limit);
		if(!is_null($where)){
			$select->where($where, $valor);
		}
		return $this->fetchAll($select)->toArray();
    }
    
    public function inserir(array $request,$idDepartamento,$idUf/*,$status */) {
                
        if( $request['subAreaInst'] == 0 ) $request['subAreaInst'] = null;
        if( $request['especialidadeInst'] == 0 ) $request['especialidadeInst'] = null;
        
        $dados = array(
            'nome' => $request['nome-usuario'],
            'matricula' => $request['matricula-usuario'],
            'departamento' => $idDepartamento,
            'email' => $request['email-usuario'],
            'conta' => $request['conta-usuario'],
            'rg' => $request['rg-usuario'],
            'cpf' => $request['cpf-usuario'],
            'endereco' => $request['endereco-usuario'],
            'complemento' => $request['complemento-usuario'],
            'bairro' => $request['bairro-usuario'],
            'cidade' => $request['cidade-usuario'],
            'divisao_administrativa' => $idUf,
            'cep' => $request['cep-usuario'],
            'telefone' => $request['tel-usuario'],
            'celular' => $request['cel-usuario'],
            'fax' => $request['fax-usuario'],
            'passaporte' => $request['passaporte-usuario'],
            'lattes' => $request['lattes-usuario'],            
            'grande_area' => $request['grandeAreaInst'],
            'area' => $request['areaInst'],
            'sub_area' => $request['subAreaInst'],
            'especialidade' => $request['especialidadeInst'],
            'senha' => 'b64f8b559d03ebb1aaab0638dbd2d72f5c987778',//senha padrao criptografada -> cacau2014
            'codigo_alteracao_senha' => null
        );

        return $this->insert($dados);
    }
 
}

