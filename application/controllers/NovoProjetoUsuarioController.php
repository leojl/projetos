<?php

class NovoProjetoUsuarioController extends Zend_Controller_Action
{
    

        public function init()
    {
       
       require_once('../library/fpdf/fpdf.php');
	

    }

    public function indexAction()
    {
        
         //carrega Combobox();
        $cbbPais = new Application_Model_Pais();
        $rows = $cbbPais->select();
        $dados = $cbbPais->fetchAll($rows)->toArray();
        $this->view->selectPais=$dados ;        
                
        $cbbProjetos = new Application_Model_Projeto();
        $rows = $cbbProjetos->select()->order('titulo');
        $projetos = $cbbProjetos->fetchAll($rows)->toArray();
        $this->view->selectProjetos = $projetos;
        
    }
    
    public function criarAction(){
        
        $registros = $this->_getAllParams();

        
            $modelUsuario = new Application_Model_Usuario();
            $selectUsuario = $modelUsuario->select()
                                ->where('cpf = ?',$registros['cpf-usuario']);
        
            $usuario = $modelUsuario->fetchRow($selectUsuario);
            
            if($usuario == null){
                $this->redirect('novo-projeto-usuario/erro-cadastro');
            }
            
            else{
                
                $modelProjetoUsuario = new Application_Model_ProjetoUsuario();
                $selectProjetoUsuario = $modelProjetoUsuario->select()
                                  ->where('projeto = ?',$registros['comboProjetos'] )
                                  ->where('usuario = ?',$usuario['id']);
                $projetoUsuario = $modelProjetoUsuario->fetchRow($selectProjetoUsuario);
                
            if($projetoUsuario<>null){
                
                echo"
                    <script LANGUAGE=\"Javascript\">
                    alert(\"O usuário já está cadastrado neste projeto.\");
                    location.href=\"javascript:history.go(-1)\"
                    </script>";
                die;
                
            }
            $dados = array(
            'projeto' => $registros['comboProjetos'],
            'usuario' => $usuario['id'],
            'tipo_usuario' => $registros['tipo_usuario'],
            'status' => 2
            
        );            
        
        $modelProjetoUsuario2 = new Application_Model_ProjetoUsuario();
        $idProjetoUsuario = $modelProjetoUsuario2->insert($dados);
        
        
        
        $idTipoUsuario = $registros['tipo_usuario'];
            $idProjeto = $registros['comboProjetos'];
            
            $modelProjeto = new Application_Model_Projeto();
            $projeto = $modelProjeto->fetchRow(' id ='.$registros['comboProjetos'] )->toArray();
            $nomeProjeto = $projeto['titulo'];
       
            $modelTipoUsuario = new Application_Model_TipoUsuario();
            $tipoUsuario = $modelTipoUsuario->fetchRow(' id ='.$registros['tipo_usuario'] )->toArray();
            $nomeTipoUsuario = $tipoUsuario['tipo'];
       
       
        $stringDadosNovoUsuario = 'http://nbcgib.uesc.br/projetos/public/usuario/pdf/id/'.$idProjetoUsuario
                  
//            
//                Nome do usuário: '.$usuario['nome'].'
//                Tipo de usuário: '.$nomeTipoUsuario.'
//                Cadastrado no projeto: '.$nomeProjeto.'
//
//                Para ter acesso completo aos dados acesse o site de cadastro de projetos do NBCGIB e faça o login como administrador.
//                
//
//        '
            ;
        
            $mail = new Zend_Mail('UTF-8');
            $mail->setBodyText($stringDadosNovoUsuario);
            $mail->setSubject('Usuário cadastrado em novo projeto');
            $mail->setFrom('leo.053993@gmail.com', 'Remetente');
            //$mail->setFrom('colocar um e-mail especifico do NBCGIB', 'Remetente');
            $mail->addTo('leo.053993@gmail.com', 'Destinatário');
            //$mail->addTo(' colocar aqui o e-mail da pessoa que cadastrou o projeto ', 'Destinatário');
            
//            if($mail->send()){
//        
                $this->_redirect('novo-projeto-usuario/message/id-usuario/'.$usuario['id'].'/id-projeto-usuario/'.$idProjetoUsuario);
//        
//            }
          }
    }
    
    
    public function messageAction(){
//            $this->_helper->layout->disableLayout();
//            $this->view->idUsuario = $this->_getParam('id-usuario');
//            $this->view->idProjetoUsuario = $this->_getParam('id-projeto-usuario');
        $this->_helper->layout->disableLayout();
        $this->view->idProjetoUsuario = $this->_getParam('id-projeto-usuario');
           }
           
    public function erroCadastroAction(){
        //$this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    }
    
//    public function pdfAction(){
////        $this->_helper->viewRenderer->setNoRender();
////        $this->_helper->layout->disableLayout();
////                
////        $idUsuario = $this->_getParam('id-usuario');
////        $idProjetoUsuario = $this->_getParam('id-projeto-usuario');
////                
////        $modelProjetoUsuario = new Application_Model_ProjetoUsuario();
////        $projetoUsuario = $modelProjetoUsuario->fetchRow(' id ='.$idProjetoUsuario )->toArray();
////        
////        $modelUsuario = new Application_Model_Usuario();
////        $usuario = $modelUsuario->fetchRow(' id ='.$idUsuario )->toArray();
////        
////        $modelUfUsuario = new Application_Model_UnidadeFederativa();
////        $ufUsuario = $modelUfUsuario->fetchRow(' id ='.$usuario['divisao_administrativa'] )->toArray();
////        
////        $modelPaisUsuario = new Application_Model_Pais();
////        $paisUsuario = $modelPaisUsuario->fetchRow(' id ='.$ufUsuario['pais'] )->toArray();  
////                                        
////        $modelArea = new Application_Model_AreaConhecimento();
////        $area = $modelArea->fetchRow(' id ='.$usuario['area_conhecimento'])->toArray();
////        
////        $modelDepartamento = new Application_Model_Departamento();
////        $departamento = $modelDepartamento->fetchRow(' id ='.$area['departamento'])->toArray();
////        
////        $modelInst = new Application_Model_Instituicao();
////        $instituicao = $modelInst->fetchRow(' id ='.$departamento['instituicao'])->toArray();
////        
////        $modelUf = new Application_Model_UnidadeFederativa();
////        $uf = $modelUf->fetchRow(' id ='.$instituicao['divisao_administrativa'] )->toArray();
////        
////        $modelPais = new Application_Model_Pais();
////        $pais = $modelPais->fetchRow(' id ='.$uf['pais'] )->toArray(); 
////        
////        $modelProjeto = new Application_Model_Projeto();
////        $projeto = $modelProjeto->fetchRow(' id ='.$projetoUsuario['projeto'] )->toArray();
////        
////        $modelTipoUsuario = new Application_Model_TipoUsuario();
////        $tipoUsuario = $modelTipoUsuario->fetchRow(' id ='.$projetoUsuario['tipo_usuario'] )->toArray();
////        
////        
////        //GERAR ARQUIVO PDF
////            $this->_helper->viewRenderer->setNoRender();
////            $this->_helper->layout->disableLayout();   
////            $pdf=new FPDF("P","mm","A4");
////		$pdf->Open();
////		$pdf->SetTitle('Cadastro de usuário');
////            $pdf->SetMargins(10,20,10);
////            $pdf->AddPage();
////            
////            $pdf->SetFont('arial','B',20);
////            $pdf->Cell(185,8,"Usuário",0,1,'C');
////            $pdf->Cell(30,8,"",0,1,'C');
////            
////            $pdf->SetFont('arial','B',15);
////            $pdf->Cell(185,8,"Instituição",0,1,'C');
////            $pdf->Cell(30,8,"",0,1,'C');
////            
////         //Pais
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(30,8,"País:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$pais['nome'],0,1,'L');
////            
////        //Divisao administrativa
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Divisão administrativa:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$uf['nome'],0,1,'L');
////            
////            //Nome
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Nome:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$instituicao['nome'],0,1,'L');
////            
////            //departamento
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Departamento:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$departamento['nome'],0,1,'L');
////            
////            //Área de conhecimento
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Área de conhecimento:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$area['nome'],0,1,'L');
////
////            //Dados do usuário
////            $pdf->Cell(30,8,"",0,1,'C');
////            $pdf->SetFont('arial','B',15);
////            $pdf->Cell(185,5,"Dados do usuário",0,1,'C');
////            $pdf->Cell(30,8,"",0,1,'C');
////            
////            //nome do usuário
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Nome:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['nome'],0,1,'L');
////            
////            //conta
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Conta:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['conta'],0,1,'L');
////            
////            //projeto cadastrado
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Projeto cadastrado:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$projeto['titulo'],0,1,'L');
////            
////            //tipo de usuario
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Tipo do usuário:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$tipoUsuario['tipo'],0,1,'L');
////                    
////            //numero de matricula
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Número de matrícula:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['matricula'],0,1,'L');
////            
////            //e-mail
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"E-mail:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['email'],0,1,'L');
////            
////             //RG
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"RG:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['rg'],0,1,'L');
////            
////            //CPF
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"CPF:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['cpf'],0,1,'L');
////            
////            //endereco
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Endereço:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['endereco'],0,1,'L');
////            
////            //complemento
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Complemento:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['complemento'],0,1,'L');
////            
////            // bairro
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Bairro:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['bairro'],0,1,'L');
////            
////            //cidade
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Cidade:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['cidade'],0,1,'L');
////            
////            //divisao administrativa
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Divisâo administrativa:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$ufUsuario['nome'],0,1,'L');
////            
////            //Pais
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"País:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$paisUsuario['nome'],0,1,'L');
////            
////            //CEP
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"CEP:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['cep'],0,1,'L');
////            
////            //telefone
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Telefone:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['telefone'],0,1,'L');
////            
////            //celular
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Celular:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['celular'],0,1,'L');
////            
////            //fax
////            $pdf->SetFont('arial','B',12);
////            $pdf->Cell(50,8,"Fax:",0,0,'L');
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,$usuario['fax'],0,1,'L');
////            
////            //ASSINATURA
////            $pdf->setFont('arial','',12);
////            $pdf->Cell(0,8,'___________________________________',0,1,'R');
////            $pdf->setFont('arial','',9);
////            $pdf->Cell(0,8,'Assinatura do responsável                     ',0,1,'R');
////          
////            $pdf->Close();
////            ob_clean();        
////            $pdf->Output('cadastro.pdf','I');
//
//        $idProjetoUsuario = $this->_getParam('id-projeto-usuario');        
//        
//        $modelProjetoUsuario = new Application_Model_ProjetoUsuario();
//        $projetoUsuario = $modelProjetoUsuario->fetchRow(' id ='.$idProjetoUsuario )->toArray();
//        
//        $modelUsuario = new Application_Model_Usuario();
//        $usuario = $modelUsuario->fetchRow(' id ='.$projetoUsuario['usuario'] )->toArray();
//        
//        $modelUfUsuario = new Application_Model_UnidadeFederativa();
//        $ufUsuario = $modelUfUsuario->fetchRow(' id ='.$usuario['divisao_administrativa'] )->toArray();
//        
//        $modelPaisUsuario = new Application_Model_Pais();
//        $paisUsuario = $modelPaisUsuario->fetchRow(' id ='.$ufUsuario['pais'] )->toArray();  
//                                        
//        $modelArea = new Application_Model_AreaConhecimento();
//        $area = $modelArea->fetchRow(' id ='.$usuario['area_conhecimento'])->toArray();
//        
//        $modelDepartamento = new Application_Model_Departamento();
//        $departamento = $modelDepartamento->fetchRow(' id ='.$area['departamento'])->toArray();
//        
//        $modelInst = new Application_Model_Instituicao();
//        $instituicao = $modelInst->fetchRow(' id ='.$departamento['instituicao'])->toArray();
//        
//        $modelUf = new Application_Model_UnidadeFederativa();
//        $uf = $modelUf->fetchRow(' id ='.$instituicao['divisao_administrativa'] )->toArray();
//        
//        $modelPais = new Application_Model_Pais();
//        $pais = $modelPais->fetchRow(' id ='.$uf['pais'] )->toArray(); 
//        
//        $modelProjeto = new Application_Model_Projeto();
//        $projeto = $modelProjeto->fetchRow(' id ='.$projetoUsuario['projeto'] )->toArray();
//        
//        $modelTipoUsuario = new Application_Model_TipoUsuario();
//        $tipoUsuario = $modelTipoUsuario->fetchRow(' id ='.$projetoUsuario['tipo_usuario'] )->toArray();
//        
//        
//        //GERAR ARQUIVO PDF
//            $this->_helper->viewRenderer->setNoRender();
//            $this->_helper->layout->disableLayout();   
//            $pdf=new FPDF("P","mm","A4");
//		$pdf->Open();
//		$pdf->SetTitle('Cadastro de usuário');
//            $pdf->SetMargins(10,20,10);
//            $pdf->AddPage();
//            
//            $pdf->SetFont('arial','B',20);
//            $pdf->Cell(185,8,"Usuário",0,1,'C');
//            $pdf->Cell(30,8,"",0,1,'C');
//            
//            $pdf->SetFont('arial','B',15);
//            $pdf->Cell(185,8,"Instituição",0,1,'C');
//            $pdf->Cell(30,8,"",0,1,'C');
//            
//         //Pais
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(30,8,"País:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$pais['nome'],0,1,'L');
//            
//        //Divisao administrativa
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Divisão administrativa:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$uf['nome'],0,1,'L');
//            
//            //Nome
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Nome:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$instituicao['nome'],0,1,'L');
//            
//            //departamento
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Departamento:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$departamento['nome'],0,1,'L');
//            
//            //Área de conhecimento
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Área de conhecimento:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$area['nome'],0,1,'L');
//
//            //Dados do usuário
//            $pdf->Cell(30,8,"",0,1,'C');
//            $pdf->SetFont('arial','B',15);
//            $pdf->Cell(185,5,"Dados do usuário",0,1,'C');
//            $pdf->Cell(30,8,"",0,1,'C');
//            
//            //nome do usuário
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Nome:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['nome'],0,1,'L');
//            
//            //conta
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Conta:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['conta'],0,1,'L');
//            
//            //projeto cadastrado
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Projeto cadastrado:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$projeto['titulo'],0,1,'L');
//            
//            //tipo de usuario
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Tipo do usuário:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$tipoUsuario['tipo'],0,1,'L');
//                    
//            //numero de matricula
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Número de matrícula:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['matricula'],0,1,'L');
//            
//            //e-mail
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"E-mail:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['email'],0,1,'L');
//            
//             //RG
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"RG:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['rg'],0,1,'L');
//            
//            //CPF
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"CPF:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['cpf'],0,1,'L');
//            
//            //endereco
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Endereço:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['endereco'],0,1,'L');
//            
//            //complemento
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Complemento:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['complemento'],0,1,'L');
//            
//            // bairro
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Bairro:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['bairro'],0,1,'L');
//            
//            //cidade
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Cidade:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['cidade'],0,1,'L');
//            
//            //divisao administrativa
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Divisâo administrativa:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$ufUsuario['nome'],0,1,'L');
//            
//            //Pais
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"País:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$paisUsuario['nome'],0,1,'L');
//            
//            //CEP
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"CEP:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['cep'],0,1,'L');
//            
//            //telefone
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Telefone:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['telefone'],0,1,'L');
//            
//            //celular
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Celular:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['celular'],0,1,'L');
//            
//            //fax
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Fax:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$usuario['fax'],0,1,'L');
//            
//            //ASSINATURA
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,'___________________________________',0,1,'R');
//            $pdf->setFont('arial','',9);
//            $pdf->Cell(0,8,'Assinatura do responsável                     ',0,1,'R');
//          
//            $pdf->Close();
//            ob_clean();        
//            $pdf->Output('cadastro.pdf','I');
//        
//    }
    
}

