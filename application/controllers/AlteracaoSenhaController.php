<?php

class AlteracaoSenhaController extends Zend_Controller_Action
{

    public function init()
    {
        
    }

    public function indexAction()
    {
       //Caso o usuario possua a senha padrao, um codigo sera enviado para ele via e-mail para alterar a senha
        $this->view->dados = $this->_getAllParams();
        
    }
    
    public function enviarCodigoAction(){
        
        $email = $this->_getParam('email');
        
        $codigo = AlteracaoSenhaController::gerarChave(30); //gera um codigo com 30 digitos
        
        $modelUsuarioVerificaEmail = new Application_Model_Usuario();
         $selectUsuario = $modelUsuarioVerificaEmail->select()
                                ->where('email = ?',$email);
        
            $usuario = $modelUsuarioVerificaEmail->fetchRow($selectUsuario);
            //Aparece mensagem de erro caso o usuario nao exista
            if($usuario == null){
                
                $this->redirect("alteracao-senha/erro");    
                
            }
            
            $dadosAlterar = array( 'codigo_alteracao_senha' => $codigo );
            
            $modelLoginUsuario = new Application_Model_Usuario();
            $modelLoginUsuario->update( $dadosAlterar, 'id='.$usuario['id'] );
            
        $mensagem = 'Caso tenha solicitado alterar a senha do login do NBCGIB click no link abaixo,'
                . 'caso contrário ignore esta mensagem.'
                . 'click aqui para alterar sua senha: http://nbcgib.uesc.br/projetos/public/alteracao-senha/form-alterar-senha/email/'.$email.'/codigo/'.$codigo;
            
         $mail = new Zend_Mail('UTF-8');
            $mail->setBodyText($mensagem);
            $mail->setSubject('Alteração de senha NBCGIB');
            $mail->setFrom('leo.053993@gmail.com', 'Remetente');
            $mail->addTo($email, 'Destinatário');
            
            if($mail->send()){
                $this->redirect("alteracao-senha/message");   
            }
    }

    public function formAlterarSenhaAction(){
        
        $dados = $this->_getAllParams();
         $modelUsuarioVerificaEmail = new Application_Model_Usuario();
         $selectUsuario = $modelUsuarioVerificaEmail->select()
                                ->where('email = ?',$dados['email'])
                                ->where('codigo_alteracao_senha = ?',$dados['codigo'])
                    ;
        
            $usuario = $modelUsuarioVerificaEmail->fetchRow($selectUsuario)->toArray();
            //var_dump($usuario);die;
            //Aparece mensagem de erro caso o usuario nao exista
            if($usuario == null){                
                $this->redirect("alteracao-senha/erro");                   
            }
            
            if($usuario['codigo_alteracao_senha'] <> $dados['codigo']){
                $this->redirect("alteracao-senha/erro"); 
            }
            
            $this->view->dados=$usuario;
    }
    
    public function alterarSenhaAction(){
        $usuario = $this->_getAllParams();
            if( $usuario['senha_atual'] <> sha1($usuario['senha_antiga']) ){
                $this->redirect("alteracao-senha/erro-alteracao-senha");
            }
            $dadosAlterar = array(
                'codigo_alteracao_senha' => null ,
                'senha' => sha1($usuario['senha1'])
                    );
            
            $modelLoginUsuario = new Application_Model_Usuario();
            $modelLoginUsuario->update( $dadosAlterar, 'id='.$usuario['id_usuario'] );
            
            $this->redirect("alteracao-senha/message2");  
    }


        public function erroAlteracaoSenhaAction(){
        $this->_helper->layout->disableLayout();
    }
    
    public function erroAction(){
        $this->_helper->layout->disableLayout();
    }
    
    public function messageAction(){
        $this->_helper->layout->disableLayout();
    }
    
    public function message2Action(){
        $this->_helper->layout->disableLayout();
    }
            
    function gerarChave($digitos) {
	    $chave = '';
	    $sopaLetras = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    srand((double)microtime()*1000000);
	    for($i=0; $i < $digitos; $i++) {
	        $chave .= $sopaLetras[rand()%strlen($sopaLetras)];
	    }
	    return $chave;
	}
    
}

