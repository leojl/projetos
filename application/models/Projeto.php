<?php

class Application_Model_Projeto extends Zend_Db_Table_Abstract
{
    protected $_name = 'projeto';
    protected $_primary = 'id';
    
   public function consulta(array $campo = null, $where = null, $valor = null, $order = null, $limit = null) {
		
		$select = $this->select()->from($this, $campo)->order($order)->limit($limit);
		if(!is_null($where)){
			$select->where($where, $valor);
		}
		return $this->fetchAll($select)->toArray();
    }
    
    public function inserir(array $request, $idDepartamento,$hpc,$proxy,$bd,$email,$web,$vpn) {
        
        if( $request['subAreaInst'] == 0 ) $request['subAreaInst'] = null;
        if( $request['especialidadeInst'] == 0 ) $request['especialidadeInst'] = null;
        
        $dados = array(
            'grupo' => $request['grupo_projeto'],
            'departamento' => $idDepartamento,
            'titulo' => $request['titulo_projeto'],
            'descricao' => $request['descricao_projeto'],
            'duracao' => $request['duracao_projeto'],
            'hpc' => $hpc,
            'proxy' => $proxy,
            'bd' => $bd,
            'email' => $email,
            'web' => $web,
            'vpn' => $vpn,
            'outros' => $request['outrosRecursos_projeto'],
            'necessidades_computacionais' => $request['descricaoNecessidades_projeto'],
            'inicio' => $request['inicio_projeto'],
            'cacau' => $request['radiocadastroCacau_projeto'],
            'status' => 2,
            'email_contato' => $request['email-projeto'],
            'grande_area' => $request['grandeAreaInst'],
            'area' => $request['areaInst'],
            'sub_area' => $request['subAreaInst'],
            'especialidade' => $request['especialidadeInst'],
            'orientador' => $request['orientador_projeto'],
            'tipo_projeto' => $request['radioTipoProjeto'] ,
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
