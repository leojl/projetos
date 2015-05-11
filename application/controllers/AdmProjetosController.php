<?php

class AdmProjetosController extends Zend_Controller_Action
{
/*
$date = new DateTime();
echo $date->getTimestamp();
 *  */
    public function init()
    {
       require_once('../library/fpdf/fpdf.php');
    }

    public function indexAction()
    {
       $auth = Zend_Auth::getInstance();
       if(!$auth->hasIdentity()){
           $this->_redirect('/projeto');
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
         $this->view->usuario = $usuarioLogado;//getDados do usuario logado
                 
        $arrayProjeto = array(
            'id',
            'departamento',
            'titulo',            
            'status'
              );
        
        $arrayStatusProjeto = array(
            
            'status',                       
        );
        $arrayDepartamento = array(
            
            'nome'
        );
        
        $arrayInstituicao = array(
            
            'nomeInst' => 'nome'
        );
        
        if(  (isset($_POST['Enviar'])) && ($this->_getParam('status_projeto_consulta') <> 0)  ){
            $form = $this->_getAllParams();
            
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
            $rows = $db->select()
                             ->from('projeto',$arrayProjeto)
                             ->joinInner('status','status.id=projeto.status',$arrayStatusProjeto)
                             ->joinInner('departamento','projeto.departamento=departamento.id',$arrayDepartamento)
                             ->joinInner('instituicao','departamento.instituicao=instituicao.id',$arrayInstituicao)
                             ->where('projeto.status = ?',$form['status_projeto_consulta'])
                             ->order('projeto.titulo')
               ;
            $result = $db->fetchAll($rows);
            $this->view->dados = $result;
            //echo var_dump($result);
            
        }
       
        else{
        
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        $rows = $db->select()
                             ->from('projeto',$arrayProjeto)
                             ->joinInner('status','status.id=projeto.status',$arrayStatusProjeto)
                             ->joinInner('departamento','projeto.departamento=departamento.id',$arrayDepartamento)
                             ->joinInner('instituicao','departamento.instituicao=instituicao.id',$arrayInstituicao)
                             ->order('projeto.titulo')
               ;
        $result = $db->fetchAll($rows);
        $this->view->dados = $result;
        //echo var_dump($result);
        } 
    }
    
    
    public function atualizaAction(){
      
        //Verificar usuario que esta atualizando as informaçoes;
        $auth = Zend_Auth::getInstance();
        $usuarioLogado = $auth->getIdentity()->email;//getDados do usuario logado
        
        $alteracao = '';//Variavel que armazena as alteraçoes realizadas pelo usuario
        
        if(isset($_POST['checkbox_id'])){//VERIFICO SE AS CHECKBOX FORAR SELECIONADAS
           
            $dados = $this->_getAllParams();
            $cb = $_POST['checkbox_id'];//armazeno o array de checkbox na variavel $cb
            
            $model = new Application_Model_Projeto();
            
            //O 'if' verifica se o selectbox enviado tem um valor valido, no caso ele deve ser diferente de zero
            if($dados['status_projeto'] <> 0){
                
                foreach ($cb as $key => $value): 
                    //Array com os campos que eu desejo atualizar da tabela projeto
                    $arrayProjeto = array(
                        'status' => $dados['status_projeto']
                    );
                
                                
                //Atualizando status dos projetos
                    if($model->update($arrayProjeto,'id='.$value)){
                        
                        //ENVIAR EMAIL                        
                        
                        $modelProjeto = new Application_Model_Projeto();
                        $projeto = $modelProjeto->fetchRow(' id ='.$value )->toArray();
                        
                        $modelStatus = new Application_Model_Status();
                        $status = $modelStatus->fetchRow(' id ='.$projeto['status'] )->toArray();                        
                        
                         $stringDados =  
                        
                        'O status do projeto( ' .$projeto['titulo']. ' ) cadastrado por você foi alterado para "' .$status['status']. '" .
                         Entrar em contato com o NBCGIB para mais informações.
                
        ';
        
                $mail = new Zend_Mail('UTF-8');
                $mail->setBodyText($stringDados);
                $mail->setSubject('Status alterado');
                $mail->setFrom('leo.053993@gmail.com', 'Remetente');//ALTERAR PARA E-MAIL DO NBCGIB
                //$mail->setFrom('colocar um e-mail especifico do NBCGIB', 'Remetente');
                $mail->addTo($projeto['email_contato'], 'Destinatário');//ALTERAR PARA E-MAIL DO USUARIO -> usuario['email']
                //$mail->addTo(' colocar aqui o e-mail da pessoa que cadastrou o projeto ', 'Destinatário');
            
                        
                    //$mail->send();
                        
                    }
                    
                    $alteracao = $alteracao.'Alteração do status do projeto: '. $projeto['titulo']. ', para '. $status['status'].'    '
                               ;
                
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
        
            $this->redirect('/adm-projetos');
            
        }
        
        
    }
    
    function teste(){
        echo 'teste';
    }
    
}
