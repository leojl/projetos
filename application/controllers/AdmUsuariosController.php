<?php

class AdmUsuariosController extends Zend_Controller_Action
{

    public function init()
    {
        
        require_once('../library/fpdf/fpdf.php');
    }

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
       if(!$auth->hasIdentity()){
           $this->_helper->FlashMessenger(array('erro'=>'Acesso negado'));
           $this->_redirect('/usuario');
         }
         //VERIFICA SE QUEM ESTA LOGADO É O ADM OU UM USUARIO COMUM
         $usuarioLogado = $auth->getIdentity();
            $modelVerificaAdm = new Application_Model_TabelaAdm();
            $admLogado = $modelVerificaAdm->select()
                    ->where('email = ?',$usuarioLogado->email);
            $adm = $modelVerificaAdm->fetchRow($admLogado);
            if($adm == NULL){
                $this->_redirect('/login-usuario-comum/');
            }
         $this->_helper->layout->setLayout('admin');
         $this->view->usuario = $usuarioLogado;
        
        
        $arrayUsuario = array(
           'id',
           'nomeUsuario' => 'nome',
            );
//        
//        $arrayArea = array(
//           'nomeAreaConhecimento' => 'nome'  
//            );
        
        $arrayProjeto_usuario = array( 
            'idProjetoUsuario' => 'id',
            'projeto',
            'tipo_usuario' 
             );
        
        $arrayTipo_usuario = array('tipo');
        
        $arrayProjeto = array( 'titulo');
        
        $arrayStatusProjetoUsuario = array(
            'idStatus' => 'id',
            'statusProjetoUsuario' => 'status'
            );      
        
        $arrayDepartamento = array(
            
            'nomeDepart' => 'nome'
        );
        
        $arrayInstituicao = array(
            
            'nomeInst' => 'nome'
        );
                
        if(  (isset($_POST['Enviar'])) && ($this->_getParam('status_usuario_consulta') <> 0)  ){
            $form = $this->_getAllParams();
            
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
            $rows = $db->select()
                             ->from('projeto_usuario',$arrayProjeto_usuario)
                             ->joinInner('usuario','projeto_usuario.usuario=usuario.id',$arrayUsuario)
                             ->joinInner('departamento','usuario.departamento=departamento.id',$arrayDepartamento)
                             ->joinInner('instituicao','departamento.instituicao=instituicao.id',$arrayInstituicao)
                             ->joinInner('tipo_usuario','tipo_usuario.id=projeto_usuario.tipo_usuario',$arrayTipo_usuario)
                             ->joinInner('projeto','projeto.id=projeto_usuario.projeto',$arrayProjeto)
                             ->joinInner('status','status.id=projeto_usuario.status',$arrayStatusProjetoUsuario)
                             ->where('projeto_usuario.status = ?',$form['status_usuario_consulta'])
                             ->order('usuario.nome')
               ;
          $result = $db->fetchAll($rows);
          $this->view->dados = $result;
        }
        
        
        
        else{
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
            $rows = $db->select()
                             ->from('projeto_usuario',$arrayProjeto_usuario)
                             ->joinInner('usuario','projeto_usuario.usuario=usuario.id',$arrayUsuario)                             
                             ->joinInner('departamento','usuario.departamento=departamento.id',$arrayDepartamento)
                             ->joinInner('instituicao','departamento.instituicao=instituicao.id',$arrayInstituicao)
                             ->joinInner('tipo_usuario','tipo_usuario.id=projeto_usuario.tipo_usuario',$arrayTipo_usuario)
                             ->joinInner('projeto','projeto.id=projeto_usuario.projeto',$arrayProjeto)
                             ->joinInner('status','status.id=projeto_usuario.status',$arrayStatusProjetoUsuario)
                             ->order('usuario.nome')                             
               ;
          $result = $db->fetchAll($rows);
          $this->view->dados = $result;
        }
        
    }
    
    public function atualizaAction(){
        
        //Verificar usuario que esta atualizando as informaçoes;
        $auth = Zend_Auth::getInstance();
        $usuarioLogado = $auth->getIdentity()->email;//getDados do usuario logado
        
        $alteracao = '';//Variavel que armazena as alteraçoes realizadas pelo usuario
        
        if(isset($_POST['checkbox_id'])){
           
            $dados = $this->_getAllParams();
            $model = new Application_Model_ProjetoUsuario();
            //O 'if' verifica se o selectbox enviado tem um valor valido, no caso ele deve ser diferente de zero
            if($dados['status_projeto_usuario'] <> 0){
                foreach ($_POST['checkbox_id'] as $key => $value): 
                    //Array com os campos que eu desejo atualizar da tabela projeto
                    
                    $arrayProjetoUsuario = array(
                        'status' => $dados['status_projeto_usuario']
                    );
                
                //Atualizando status dos usuarios
                    if( $model->update($arrayProjetoUsuario,'id ='. $value) ){
                        
                        
                        //ENVIAR E-MAIL PARA OS USUARIOS
                        $modelProjetoUsuario = new Application_Model_ProjetoUsuario();
                        $projetoUsuario = $modelProjetoUsuario->fetchRow(' id ='.$value)->toArray();  
                        
                        $modelUsuario = new Application_Model_Usuario();
                        $usuario = $modelUsuario->fetchRow(' id ='. $projetoUsuario['usuario'])->toArray();
                        
                        $modelProjeto = new Application_Model_Projeto();
                        $projeto = $modelProjeto->fetchRow(' id ='.$projetoUsuario['projeto'] )->toArray();

                        $modelStatus = new Application_Model_Status();
                        $status = $modelStatus->fetchRow(' id ='.$projetoUsuario['status'] )->toArray();
                        
                        $stringDados =  
            
                $usuario['nome']. ', o status do projeto "' . $projeto['titulo'] . '" que você se cadastrou foi alterado para "'. $status['status'] . '" ,
                    Entrar em contato com o NBCGIB para mais informações.
                
        ';
        
                $mail = new Zend_Mail('UTF-8');
                $mail->setBodyText($stringDados);
                $mail->setSubject('Status alterado');
                $mail->setFrom('leo.053993@gmail.com', 'Remetente');//ALTERAR PARA E-MAIL DO NBCGIB
                //$mail->setFrom('colocar um e-mail especifico do NBCGIB', 'Remetente');
                $mail->addTo($usuario['email'], 'Destinatário');//ALTERAR PARA E-MAIL DO USUARIO -> usuario['email']
                //$mail->addTo(' colocar aqui o e-mail da pessoa que cadastrou o projeto ', 'Destinatário');
            
                //$mail->send();
                        
                    }
                
                    $alteracao = $alteracao.'Alteração do status do usuário: '. $usuario['nome']. ', para '. $status['status'].'    '   ;
                    
                endforeach;
            
                }
        }
        //Array com os dados das alteraçoes do usuario
        $dataHoje = new DateTime('now', new DateTimeZone( 'America/Bahia'));
        
        $request = array(
            'nome' => $usuarioLogado,
            'alteracao' => $alteracao,
            'data' => $dataHoje->format( "d/m/Y H:i:s" )
        ); 
        $modelHistorico = new Application_Model_Historico();
        
        if( $modelHistorico->inserir($request) ){
        
            $this->redirect('/adm-usuarios');
        }
    }
}

