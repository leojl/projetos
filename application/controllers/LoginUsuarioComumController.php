<?php

class LoginUsuarioComumController extends Zend_Controller_Action
{

    public function init()
    {
        
    }

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
       if(!$auth->hasIdentity()){
           $this->_redirect('/projeto');
         }
         //$this->_helper->layout->setLayout('admin');
         //O EMAIL DO USUARIO E UNIQUE 
         $emailUsuario = $this->view->usuario = $auth->getIdentity();//getDados do usuario logado
         
         $projeto = array(
            'idProjeto' => 'id',
            'titulo'  ,
        );         
         
         $usuario = array(
            'nomeUsuario' => 'nome',            
        );
         
         $projetoUsuario = array(
            'projeto',
        );
        
         
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        $rows = $db->select()
                             ->from('usuario',$usuario)
                             ->joinInner('projeto_usuario','usuario.id=projeto_usuario.usuario',$projetoUsuario) 
                             ->joinInner('projeto','projeto.id=projeto_usuario.projeto',$projeto)
                             ->where('usuario.email = ?',$emailUsuario->email)
               ;
               
        $projetos = $db->fetchAll($rows);//echo var_dump($projetos);die;
        $this->view->projetos = $projetos;//ENVIANDO OS PROJETOS PARA VIEW
        
    }

//    public function alterarSenhaPadraoAction(){
//        $this->_helper->layout->disableLayout(); 
//    }
    
    public function uploadFileAction(){
        $this->_helper->layout->disableLayout();
        $dadosForm = $this->_getAllParams();
        
        $modelProjetos = new Application_Model_Projeto();
        $projetos = $modelProjetos->fetchRow('id ='.$dadosForm['projetos'])->toArray();
        $tituloDoProjeto = str_replace(' ', '', $projetos["titulo"]);
        
        if(isset($dadosForm['check_file'])){
            // criar diretorio caso ainda nao exista
            if(!file_exists("/var/www/html/projetos/public/files/".$tituloDoProjeto)){
                mkdir("/var/www/html/projetos/public/files/".$tituloDoProjeto, 0775, true);
            }

            $upload = new Zend_File_Transfer();
            $upload->setDestination("/var/www/html/projetos/public/files/".$tituloDoProjeto);
            // Retorna as informações referente ao arquivo
            $files = $upload->getFileInfo();

            foreach ($files as $file => $info) {

                if (!$upload->isValid($file)) {
                    $this->_redirect('login-usuario-comum/erro');
                 }

                $nameFile = $upload->getFileName('uploadedfile');//retorna o caminho completo com o nome do arquivo

                if(file_exists($nameFile)){
                    $this->_redirect('login-usuario-comum/erro');
                }

            }
            //Salva caminho do arquivo no banco de dados
                 $modelPublicacoes = new Application_Model_Publicacoes();
                $modelPublicacoes->inserir($dadosForm['projetos'], $dadosForm['titulo'], str_replace(' ',' "\"',$nameFile));
                      
            
            //Envia para o servidor
            $upload->receive();
                    
                
            


    //        // Obtem o nome do arquivo
    //        $names = $upload->getFileName('uploadedfile');
    //
    //        // Retorna o tamanho do arquivo
    //        $size = $upload->getFileSize('uploadedfile');
    //
    //        // Retorna o mimetype do arquivo (Se é jpeg, gif, pdf, etc)
    //        $type = $upload->getMimeType('uploadedfile');
        }
        else{            
            $modelPublicacoes = new Application_Model_Publicacoes();
            $modelPublicacoes->inserir($dadosForm['projetos'], $dadosForm['titulo'], $dadosForm['link']);            
        }
    }
    
    public function erroAction(){
         $this->_helper->layout->disableLayout();
     }

}

