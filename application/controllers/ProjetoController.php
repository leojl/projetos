<?php

class ProjetoController extends Zend_Controller_Action
{
    

    public function init()
    {
       require_once('../library/fpdf/fpdf.php');
    }

    public function indexAction()
    {
//echo 'Estamos realizando manutenção temporária do site, agradecemos a compreensão';die;        
         //carrega Combobox Pais
        $cbbPais = new Application_Model_Pais();
        $rows = $cbbPais->select();
        $dados = $cbbPais->fetchAll($rows)->toArray();
        $this->view->selectPais=$dados ;
        
        $cbbGrandeArea = new Application_Model_GrandeArea();
        $rows = $cbbGrandeArea->select();
        $dados = $cbbGrandeArea->fetchAll($rows)->toArray();
        $this->view->selectGrandeArea=$dados ;
           
        $modelSoftware = new Application_Model_Software();
        $result = $modelSoftware->select()
                                ->order('nome')
                ;
        $softwares = $modelSoftware->fetchAll($result)->toArray();
        $this->view->softwares = $softwares;
        
    }
    
    public function criarAction(){
        
        $registros = $this->_getAllParams();
        
       
       //CADASTRANDO PAIS DA INSTITUIÇAO
       $modelPaisInst = new Application_Model_Pais();
       
       if($registros['check_paisInst'] == 1){
           $ultimoPaisInst = $modelPaisInst->inserirInstituicao($registros); 
       }
       else{
           $ultimoPaisInst = $registros['pais-inst'];
           
           }            
       //////////////////////////////////////////////////
       
           
       //CADASTRANDO UF DA INSTITUIÇAO
       $modelUFinst = new Application_Model_UnidadeFederativa();
       
       if($registros['check_ufInst'] == 1){
           $ultimaUFinst = $modelUFinst->inserirInstituicao($registros,$ultimoPaisInst); 
       }
       else{
           $ultimaUFinst = $registros['uf-inst'];
           
       }       
       //////////////////////////////////////////////////
       
       //CADASTRANDO INSTITUIÇAO
       $modelInstituicao = new Application_Model_Instituicao();
       
       if($registros['check_nomeInst'] == 1){
            $ultimaInstituicao = $modelInstituicao->inserir($registros,$ultimaUFinst);
       }
       else{
           $ultimaInstituicao = $registros['nome-inst'];
           
       }
       //////////////////////////////////////////////////
       
       //CADASTRANDO DEPARTAMENTO
       $modelDepartamento = new Application_Model_Departamento();
       
       if($registros['check_departamentoInst'] == 1){
            $ultimoDepartamento = $modelDepartamento->inserir($registros, $ultimaInstituicao);
       }
       else{
           $ultimoDepartamento = $registros['departamento-inst'];
           
       }
       
        $hpc="não";
        $proxy="não";
        $bd="não";
        $email="não";
        $web="não";
        $vpn="não";
        
        if($registros['hpc_projeto'] == "sim"){
            $hpc = "sim";
        }
        if($registros['proxy_projeto'] == "sim"){
            $proxy = "sim";
        }
        if($registros['servidorBd_projeto'] == "sim"){
            $bd = "sim";
        }
        if($registros['servidorEmail_projeto'] == "sim"){
            $email = "sim";
        }
        if($registros['servidorWeb_projeto'] == "sim"){
            $web  = "sim";
        }
        if($registros['vpn_projeto'] == "sim"){
            $vpn  = "sim";
        }
        
        $projeto = new Application_Model_Projeto();
        $ultimoProjeto = $projeto->inserir($registros,$ultimoDepartamento, $hpc,$proxy,$bd,$email,$web,$vpn);
        
        //////////CADASTRANDO NOVOS SOFTWARES E RELACIONANDO-OS AOS PROJETO   
         /*   
        $cbSoftwares = $this->_getParam('softwares');
        
        $modelSoftware = new Application_Model_Software();
        $modelprojetoSoftware = new Application_Model_ProjetoSoftware();
        
        foreach ($cbSoftwares as $key => $value):
            $modelprojetoSoftware->inserir($ultimoProjeto,$value);
        endforeach;         
        
        $outros_softwares = $this->_getParam('outros_softwares');
        $versao_software = $this->_getParam('versao_software');
        $links = $this->_getParam('links');
        
        foreach ($outros_softwares as $key => $value):                    
            $ultimoSoftware = $modelSoftware->inserir($outros_softwares[$key], $versao_software[$key], $links[$key]);
            $modelprojetoSoftware->inserir($ultimoProjeto,$ultimoSoftware);                
        endforeach;
                        
        */
        ////////////////////////////////////////////////////////////////////////////////////////
        
        $stringDadosNovoProjeto = 'http://nbcgib.uesc.br/projetos/public/projeto/pdf/id/'.$ultimoProjeto
//                '  
//            
//                Título: '.$registros['titulo_projeto'].'    
//                Descrição: ' .$registros['descricao_projeto']. '
//                Início: ' .$registros['inicio_projeto'].    '
//                Duração(em meses): ' .$registros['duracao_projeto'].   ' 
//                    
//                Para ter acesso completo aos dados acesse o site de cadastro de projetos do NBCGIB e faça o login como administrador.
//                               
//        '
                    ;
        
            $mail = new Zend_Mail('UTF-8');
            $mail->setBodyText($stringDadosNovoProjeto);
            $mail->setSubject('Novo Projeto cadastrado');
            $mail->setFrom('leo.053993@gmail.com', 'Remetente');
            //$mail->setFrom('colocar um e-mail especifico do NBCGIB', 'Remetente');
            $mail->addTo('leo.053993@gmail.com', 'Destinatário');
            //$mail->addTo(' colocar aqui o e-mail da pessoa que cadastrou o projeto ', 'Destinatário');
            
//            if($mail->send()){
            
            session_start();
        
                $_SESSION["id"] = $ultimoProjeto;
                $this->_redirect('/projeto/message');
//        
//            }
        
       
    }
    
    
    public function messageAction(){
            $this->_helper->layout->disableLayout();
           }
    
    public function pdfAction(){
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
                
        $idProjeto = $this->_getParam('id');
        //$idProjeto = $id['id'];
        
        if( $idProjeto <> null ){
            //O PDF so aparecera caso o usuario logado for o ADM
            $auth = Zend_Auth::getInstance();
            $usuarioLogado = $auth->getIdentity();
            $modelVerificaAdm = new Application_Model_TabelaAdm();
            $admLogado = $modelVerificaAdm->select()
                    ->where('email = ?',$usuarioLogado->email);
            $adm = $modelVerificaAdm->fetchRow($admLogado);
            
            if($adm == NULL){
                $this->_redirect('/login-usuario-comum');
            }
             
        }else{
            session_start();
            $idProjeto = $_SESSION['id'];
        }
                
        $termoCompromisso = 'Não fornecer sua senha (pessoal e intransferível);
Não se afastar do objetivo inicial do projeto ao qual está vinculado;
Respeitar as diretrizes de uso da rede às quais o CACAU está conectado;
Incluir em qualquer trabalho ou publicação, oriunda deste projeto, a frase: "Pesquisa desenvolvida com o auxílio do Núcleo de Biologia Computacional e Gestão de Informações Biotecnológicas - NBCGIB", com recursos FINEP/MCT, CNPQ e FAPESB e da Universidade Estadual de Santa Cruz - UESC” ou o equivalente no idioma da publicação.


O NBCGIB se reserva ao direito de a qualquer momento, a seu exclusivo critério, mudar as diretrizes sem aviso prévio.';
              
        $projeto = array(
            'grupo', 
            'departamento',
            'titulo'  ,
            'descricao' ,
            'duracao'  ,
            'hpc' ,
            'proxy',
            'bd' ,
            'email',
            'web',
            'vpn',
            'outros',
            'necessidades_computacionais',
            'inicio',
            'cacau' ,
            'status',
            'email_contato',
            'grande_area',
            'area' ,
            'sub_area',
            'especialidade',
            'orientador',
            'tipo_projeto'
            
        );
        
        $departamento = array(
            'nomeDepartamento' => 'nome',
            'instituicao'
            
        );
        
        $instituicao = array(
            'nomeInst' => 'nome',
            'endereco',
            'complemento',
            'bairro',
            'cidade',
            'divisao_administrativa',
            'cep',
            'telefone',
            'ramal',
            'fax'
        );
        
        $uf = array(
            'ufProjeto' => 'nome',
            'pais'            
        );
        
        $pais = array(
            'paisProjeto' => 'nome'
            
        );
        
        
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        $rows = $db->select()
                             ->from('projeto',$projeto)
                             ->joinInner('departamento','departamento.id=projeto.departamento',$departamento)
                             ->joinInner('instituicao','instituicao.id=departamento.instituicao',$instituicao)
                             ->joinInner('divisao_administrativa','divisao_administrativa.id=instituicao.divisao_administrativa',$uf)  
                             ->joinInner('pais','pais.id=divisao_administrativa.pais',$pais)
                             ->where('projeto.id='.$idProjeto)
               ;
               
        $projetoPdf = $db->fetchRow($rows);
        
        $duracao = $projetoPdf['duracao'];
        
        if($duracao == 0){
            $duracao = "Indefinida";
        }
        if($projetoPdf['tipo_projeto'] == 'ic' ){ $projetoPdf['tipo_projeto'] = 'Iniciação científica'; }
        if($projetoPdf['tipo_projeto'] == 'tcc' ){ $projetoPdf['tipo_projeto'] = 'Trabalho de conclusão de curso'; }
        
        //GERAR ARQUIVO PDF
              
            $pdf=new FPDF("P","mm","A4");
		$pdf->Open();
		$pdf->SetTitle('Cadastro de projeto');
            $pdf->SetMargins(10,10,5);
            $pdf->AddPage();
            
            $pdf->SetFont('arial','B',18);
            $pdf->Cell(185,8,"Cadastro de projeto",0,1,'C');
            $pdf->Cell(30,8,"",0,1,'C');
            
            $pdf->SetFont('arial','B',15);
            $pdf->Cell(185,8,"Instituição",0,1,'C');
            $pdf->Cell(30,8,"",0,1,'C');
            
         //Pais
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(30,8,"País:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$projetoPdf['paisProjeto'],0,1,'L');
            
        //Divisao administrativa
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Divisão administrativa:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$projetoPdf['ufProjeto'],0,1,'L');
            
            //Nome
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Nome:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$projetoPdf['nomeInst'],0,1,'L');
            
            //departamento
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Departamento:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$projetoPdf['nomeDepartamento'],0,1,'L');
       
            //DADOS DO PROJETO
            $pdf->Cell(30,8,"",0,1,'C');
            $pdf->SetFont('arial','B',15);
            $pdf->Cell(185,5,"Dados do projeto",0,1,'C');
            $pdf->Cell(30,8,"",0,1,'C');
            
            //grupo
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Grupo do projeto:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$projetoPdf['grupo'],0,1,'L');
            
            //Titulo
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Título:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->MultiCell(0,8,$projetoPdf['titulo'].".",0,1);
            
            //Orientador
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Orientador:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->MultiCell(0,8,$projetoPdf['orientador'].".",0,1);
            
            //Tipo de projeto
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Tipo de projeto:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->MultiCell(0,8,$projetoPdf['tipo_projeto'].".",0,1);
            
            //descrição
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Descrição:",0,1,'L');
            $pdf->setFont('arial','',12);
            $pdf->MultiCell(0,5,$projetoPdf['descricao'],0,1);            
//             
//            //Espaço em disco
//            $pdf->SetFont('arial','B',12);
//            $pdf->Cell(50,8,"Espaço em disco:",0,0,'L');
//            $pdf->setFont('arial','',12);
//            $pdf->Cell(0,8,$projetoPdf['espaco_disco']." MB",0,1,'L');
//            
            //hpc
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"HPC:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$projetoPdf['hpc'],0,1,'L');
            
            //proxy
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"proxy:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$projetoPdf['proxy'],0,1,'L');
            
            // BD
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Servidor BD:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$projetoPdf['bd'],0,1,'L');
            
            //email
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Servidor e-mail:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$projetoPdf['email'],0,1,'L');
            
            //web
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Servidor Web:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$projetoPdf['web'],0,1,'L');
            
            //vpn
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"VPN:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$projetoPdf['vpn'],0,1,'L');
            
            //Outros
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Outros recursos:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$projetoPdf['outros'],0,1,'L');
            
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Descrição das necessidades computacionais para o projeto:",0,1,'L');
            $pdf->setFont('arial','',12);
            $pdf->MultiCell(0,5,$projetoPdf['necessidades_computacionais'],0,1);
            
            //Cadastro no cacau ou nbcgib
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Projeto cadastrado no ",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$projetoPdf['cacau'],0,1,'L');
            
             //Data início
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Início do projeto:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,date('d/m/Y', strtotime($projetoPdf['inicio'])),0,1,'L');
            
            //Duração
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Duração(em meses):",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$duracao,0,1,'L');
                        
            $pdf->ln(100);
            //
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"O usuário se compromete a:",0,1,'L');
            $pdf->setFont('arial','',10);
            $pdf->MultiCell(0,5,$termoCompromisso,0,1);
            $pdf->ln(10);
            //ASSINATURA
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,'___________________________________',0,1,'R');
            $pdf->setFont('arial','',9);
            $pdf->Cell(0,8,'Assinatura do responsável                     ',0,1,'R');
               
            $pdf->Close();
            ob_clean();        
            $pdf->Output('cadastro.pdf','I');
            
            
        
    }
    

}
