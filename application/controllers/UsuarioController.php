<?php

class UsuarioController extends Zend_Controller_Action
{
       
    public function init()
    {
         require_once('../library/fpdf/fpdf.php');
         date_default_timezone_set('Brazil/East');
        
    }
 
    public function indexAction()
    {
//echo 'Estamos realizando manutenção temporária do site, agradecemos a compreensão';die;        
        //Realizar autenticaçao do usuario
         if (isset($_POST['btn_auth'])){
             
                $form = $this->_getAllParams();
                //AUTENTICAÇAO ADM
             if ( $form['radioTipoLogin'] == 'adm' ){
                 
                $login = Application_Model_Login::loginAdm($form['login'],$form['senha']);
                if($login == true) {
                    $this->_redirect('/adm-usuarios');
                }else{
                    $this->_redirect('usuario/falha-autenticacao');
             }
           }
           else{
               //AUTENTICAÇAO USUARIO
               $login = Application_Model_Login::loginUsuario($form['login'],$form['senha']);
                if($login == true) {
                    if( $form['senha'] == 'cacau2014' ){
                        
                        $this->_redirect('/alteracao-senha');//alterar senha
                        
                    }
                    $this->_redirect('login-usuario-comum');
                }else{
                    $this->_redirect('usuario/falha-autenticacao');
             }
               
           }
           
         }         
        ///////////////////////////////////////
        
        
       //carrega SelectBox();
        $cbbInst = new Application_Model_Pais();
        $rows = $cbbInst->select();
        $dados = $cbbInst->fetchAll($rows)->toArray();
        $this->view->selectPais=$dados ;
        
        $cbbGrandeArea = new Application_Model_GrandeArea();
        $rows = $cbbGrandeArea->select();
        $dados = $cbbGrandeArea->fetchAll($rows)->toArray();
        $this->view->selectGrandeArea=$dados ;
                
        $cbbProjetos = new Application_Model_Projeto();
        $rows = $cbbProjetos->select()->order('titulo');
        $dados = $cbbProjetos->fetchAll($rows)->toArray();
        
        //este laço é utilizado para verificar se algum projeto foi expirado e alterar o status do mesmo para inativo
        foreach ($dados as $ln):
           
            $dataHoje = new DateTime('now', new DateTimeZone( 'America/Bahia'));
            $dataInicio =  new DateTime( $ln['inicio'], new DateTimeZone( 'America/Bahia') ) ;
            $dataFim = $dataInicio->add( new DateInterval( "P".$ln['duracao']."M" ) );
		
            if( ($dataFim < $dataHoje) and ($ln['duracao'] <> 0) ){
                $validade = array(
                     'status' => 5            
                    );
                
                $model = new Application_Model_Projeto();
                $model->update($validade, 'id='.$ln['id']);
            }
            /*else{
                $validade = array(
                     'statusvalidade' => 'válido'            
                    );
                
                $model = new Application_Model_Projeto();
                $model->update($validade, 'id='.$ln['id']);
            }
        */
        endforeach;
        
        $this->view->selectProjetos = $dados ;
       
    }
   
    
//ACTION QUE FAZ O CADASTRO NAS TABELAS
    public function criarAction()
    {
        
        $registros = $this->_getAllParams();
        
        //Verifica se ja existe alguem cadastrado com o mesmo CPF
        
         $modelUsuarioVerificaCPF = new Application_Model_Usuario();
         $selectUsuario = $modelUsuarioVerificaCPF->select()
                                ->where('cpf = ?',$registros['cpf-usuario']);
        
            $usuario = $modelUsuarioVerificaCPF->fetchRow($selectUsuario);
            //Aparece mensagem de erro caso o usuario ja tenha se cadastrado antes com este CPF
            if($usuario <> null){
                //$erro = 1;
                $this->redirect("usuario/erro");    
                
            }
            
            
        //Verifica se ja existe alguem cadastrado com o mesmo EMAIL
        
         $modelUsuarioVerificaEmail = new Application_Model_Usuario();
         $selectUsuario = $modelUsuarioVerificaEmail->select()
                                ->where('email = ?',$registros['email-usuario']);
        
            $usuario = $modelUsuarioVerificaEmail->fetchRow($selectUsuario);
            //Aparece mensagem de erro caso o usuario ja tenha se cadastrado antes com este EMAIL
            if($usuario <> null){
                //$erro = 1;
                $this->redirect("usuario/erro");    
                
            }
            
            
                     
            if($registros['tipo_usuario'] === 1 ){
            
            $modelProjetoUsuarioVerificaResp = new Application_Model_ProjetoUsuario();
            $selectResp = $modelProjetoUsuarioVerificaResp->select()
                                ->where('projeto = ?',$registros['comboProjetos'])
                                ->where('tipo_usuario = ?', 1)
                    ;
        
            $usuarioResponsavel = $modelProjetoUsuarioVerificaResp->fetchRow($selectResp);
            
            //Aparece mensagem de erro caso ja exista algum responsavel neste projeto
            if($usuarioResponsavel <> null){
                $this->redirect("usuario/erro");    
                
            }
            
            }
            ////////////////////////////////////////////////////////////////
            
       
       //CADASTRANDO PAIS DA INSTITUIÇAO
       $modelPaisInst = new Application_Model_Pais();
       //
       if( isset($registros['check_paisInst'])){
           $ultimoPaisInst = $modelPaisInst->inserirInstituicao($registros);
       }
       else{
           $ultimoPaisInst = $registros['pais-inst'];
           
           }            
       //////////////////////////////////////////////////
       
           
       //CADASTRANDO UF DA INSTITUIÇAO
       $modelUFinst = new Application_Model_UnidadeFederativa();
       
       if(isset($registros['check_ufInst']) ){
           $ultimaUFinst = $modelUFinst->inserirInstituicao($registros,$ultimoPaisInst); 
       }
       else{
           $ultimaUFinst = $registros['uf-inst'];
           
       }       
       //////////////////////////////////////////////////
       
       //CADASTRANDO INSTITUIÇAO
       $modelInstituicao = new Application_Model_Instituicao();
       
       if(isset($registros['check_nomeInst'])){
            $ultimaInstituicao = $modelInstituicao->inserir($registros,$ultimaUFinst);
       }
       else{
           $ultimaInstituicao = $registros['nome-inst'];
           
       }
       //////////////////////////////////////////////////
       
       //CADASTRANDO DEPARTAMENTO
       $modelDepartamento = new Application_Model_Departamento();
       //Insere um novo departamento
       if(isset($registros['check_departamentoInst'] )){
           $ultimoDepartamento =  $modelDepartamento->inserir($registros, $ultimaInstituicao);
       }
       else{//caso contrario, pega o departamento do selectBox,
            // OBS: a mesma logica vale para os outros
           $ultimoDepartamento = $registros['departamento-inst'] ;
           
       }
       
       
       //CADASTRANDO PAIS DO USUARIO       
       $modelPaisUsuario = new Application_Model_Pais();
      
       if(isset($registros['check_paisUsuario'])){
           if($registros['pais-usuario'] == $registros['pais-inst'] ){
              
               $ultimoPaisusuario = $ultimoPaisInst;
           }
           else{
                $ultimoPaisusuario = $modelPaisUsuario->inserirUsuario($registros);
           }
       }
       else{
           $ultimoPaisusuario = $registros['pais-usuario'];
           
           }  
       //////////////////////////////////////////////////
       
       //CADASTRANDO UF DO USUARIO
       $modelUFusuario = new Application_Model_UnidadeFederativa();
       
       if(isset($registros['check_ufUsuario'])){
           if( $registros['uf-usuario'] == $registros['uf-inst'] ){
              
               $ultimaUFusuario = $ultimaUFinst;
           }
           else{ 
                $ultimaUFusuario = $modelUFusuario->inserirUsuario($registros,$ultimoPaisusuario); 
           }
       }
       else{
           $ultimaUFusuario = $registros['uf-usuario'];
           
       }
       //////////////////////////////////////////////////
       
       //CADASTRANDO USUARIO
            
            $modelUsuario = new Application_Model_Usuario();
            $ultimoUsuario = $modelUsuario->inserir($registros,$ultimoDepartamento, $ultimaUFinst/*,$StatusBuscado['status']*/);
       
       //////////////////////////////////////////////////
       
       //INSERINDO NA TABELA PROJETO_USUARIO
            $modelProjetoUsuario = new Application_Model_ProjetoUsuario();
            $idProjetoUsuario = $modelProjetoUsuario->inserir($registros,$ultimoUsuario);
            
       //////////////////////////////////////////////////////////////////
            $idTipoUsuario = $registros['tipo_usuario'];
            $idProjeto = $registros['comboProjetos'];
            
            $modelProjeto = new Application_Model_Projeto();
            $projeto = $modelProjeto->fetchRow(' id ='.$idProjeto )->toArray();
            $nomeProjeto = $projeto['titulo'];
       
            $modelTipoUsuario = new Application_Model_TipoUsuario();
            $tipoUsuario = $modelTipoUsuario->fetchRow(' id ='.$idTipoUsuario )->toArray();
            $nomeTipoUsuario = $tipoUsuario['tipo'];
       
       //String para enviar os dados para e-mail
            
        $stringDadosNovoUsuario = 'http://nbcgib.uesc.br/projetos/public/usuario/pdf/id/'.$idProjeto

                    ;
        
            $mail = new Zend_Mail('UTF-8');
            $mail->setBodyText($stringDadosNovoUsuario);
            $mail->setSubject('Novo Usuário cadastrado');
            $mail->setFrom('leo.053993@gmail.com', 'Remetente');
            //$mail->setFrom('colocar um e-mail especifico do NBCGIB', 'Remetente');
            $mail->addTo('leo.053993@gmail.com', 'Destinatário');
            //$mail->addTo(' colocar aqui o e-mail da pessoa que cadastrou o projeto ', 'Destinatário');
            
//            if($mail->send()){
//            
//              Obs: foi necessario redirecionar primeiramente para o 'message' e depois
              //redirecionar para gerar o PDF, pois foi uma forma achada para nao dar erro ao
              //gerar o PDF
                session_start();
                
                $_SESSION["id-projeto-usuario"] = $idProjetoUsuario;
                $this->_redirect('usuario/message');
                
//            }
              
      
    }
    
    public function messageAction(){
        $this->_helper->layout->disableLayout();
    }
    
    public function pdfAction(){
        
                        
        $idProjetoUsuario = $this->_getParam('id-projeto-usuario');
        //$idProjetoUsuario = $id['id-projeto-usuario'];
        
        if( $idProjetoUsuario <> null ){
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
            $idProjetoUsuario = $_SESSION["id-projeto-usuario"];
        }
        
        //ARRAYS PARA FAZER INNER JOIN
         
        //$idProjetoUsuario = $this->_getParam('id-projeto-usuario');  
        
        $termoCompromisso = 'Não fornecer sua senha (pessoal e intransferível);
Não se afastar do objetivo inicial do projeto ao qual está vinculado;
Respeitar as diretrizes de uso da rede às quais o CACAU está conectado;
Incluir em qualquer trabalho ou publicação, oriunda deste projeto, a frase: "Pesquisa desenvolvida com o auxílio do Núcleo de Biologia Computacional e Gestão de Informações Biotecnológicas - NBCGIB", com recursos FINEP/MCT, CNPQ e FAPESB e da Universidade Estadual de Santa Cruz - UESC” ou o equivalente no idioma da publicação.


O NBCGIB se reserva ao direito de a qualquer momento, a seu exclusivo critério, mudar as diretrizes sem aviso prévio.';
              
        $projetoUsuario = array(
            'projeto',
            'usuario',
            'tipo_usuario',
            'status'
            
        );
        
        $usuario = array(
            'nomeUsuario' => 'nome',
            'matricula',
            'departamentoUsuario' => 'departamento',
            'email',
            'conta',
            'rg',
            'cpf',
            'endereco',
            'complemento',
            'bairro',
            'cidade',
            'divisao_administrativaIdUsuario' => 'divisao_administrativa',
            'cep',
            'telefone',
            'celular',
            'fax',
            'passaporte',
            'lattes', 
            'grande_area',
            'area',
            'sub_area',
            'especialidade',
        );
        
         $ufUsuario = array(
            'ufUsuario' => 'nome',
            'paisIdUsuario' => 'pais'         
        );
        
        $paisUsuario = array(
            'paisUsuario' => 'nome'
            
        );
        
        $departamento = array(
            'nomeDepartamento' => 'nome',
            'instituicao'            
        );
        
        $instituicao = array(
            'nomeInst' => 'nome',
            'divisao_administrativaIdInst' => 'divisao_administrativa',
            
        );       
        
         $ufInst = array(
            'ufInst' => 'nome',
            'paisIdInst' => 'pais'
        );
         
        $paisInst = array(
            'paisInst' => 'nome'
            
        );
        
        
        
        $projeto = array(
            'titulo'  ,
        );
       
        $tipoUsuario = array(
            'tipo'
            
        );
        
        ////////////////////////////////////////////////////////////////////////////////
        
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        $rows = $db->select()
                             ->from('projeto_usuario',$projetoUsuario)
                             ->joinInner('usuario','usuario.id=projeto_usuario.usuario',$usuario)
                             ->joinInner('divisao_administrativa','divisao_administrativa.id=usuario.divisao_administrativa',$ufUsuario)
                             ->joinInner('pais','pais.id=divisao_administrativa.pais',$paisUsuario)  
                             ->joinInner('departamento','departamento.id=usuario.departamento',$departamento)
                             ->joinInner('instituicao','instituicao.id=departamento.instituicao',$instituicao)
                             ->joinInner('divisao_administrativa','divisao_administrativa.id=instituicao.divisao_administrativa',$ufInst)
                             ->joinInner('pais','pais.id=divisao_administrativa.pais',$paisInst)
                             ->joinInner('tipo_usuario','tipo_usuario.id=projeto_usuario.tipo_usuario',$tipoUsuario)
                             ->joinInner('projeto','projeto.id=projeto_usuario.projeto',$projeto)
                             ->where('projeto_usuario.id='.$idProjetoUsuario)
               ;
               
        $usuarioPdf = $db->fetchRow($rows);
        
        ///////////////Verificando as Areas de conhecimento se estao 'null' ou nao, porque para fazer uma consulta ao BD
       // o ID nao pode ser NULL

        if($usuarioPdf['grande_area'] == null) {
           $grandeArea = array (
               'nome' => 'Indefinido'
               );
        }else{
        $modelGrandeArea = new Application_Model_GrandeArea();
        $grandeArea = $modelGrandeArea->fetchRow('id='.$usuarioPdf['grande_area'])->toArray();
        }
        
        if($usuarioPdf['area'] == null) {
           $area = array (
               'nome' => 'Indefinido'
               );
        }else{
        $modelArea = new Application_Model_Area();
        $area = $modelArea->fetchRow('id='.$usuarioPdf['area'])->toArray();
        }
        
        if($usuarioPdf['sub_area'] == null) {
           $subArea = array (
               'nome' => 'Indefinido'
               );
        }else{
        $modelSubArea = new Application_Model_SubArea();
        $subArea = $modelSubArea->fetchRow('id='.$usuarioPdf['sub_area'])->toArray();
        }
        
        if($usuarioPdf['especialidade'] == null) {
           $especialidade = array (
               'nome' => 'Indefinido'
               );
        }else{
        $modelEspecialidade = new Application_Model_Especialidade();
        $especialidade = $modelEspecialidade->fetchRow('id='.$usuarioPdf['especialidade'])->toArray();
        }
        
        
        ////////////////////////////////////////////////////////////////////////////////////////////////////
        
        //GERAR ARQUIVO PDF
        //
            //OBS: Para gerar um PDF é necessario desativar a view e o layout, 
            //caso contrario nao é possivel gera-lo no zend.
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout(); 
            ///////////////////////////////////////////////
            $pdf=new FPDF("P","mm","A4");
		$pdf->Open();
		$pdf->SetTitle('Cadastro de usuario');
            $pdf->SetMargins(5,5,10);
            $pdf->AddPage();
            
            $pdf->SetFont('arial','B',20);
            $pdf->Cell(185,8,"Cadastro de Usuário",0,1,'C');
            $pdf->Cell(30,8,"",0,1,'C');
            
            $pdf->SetFont('arial','B',15);
            $pdf->Cell(185,8,"Instituição",0,1,'C');
            $pdf->Cell(30,8,"",0,1,'C');
            
         //Pais
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(30,8,"País:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['paisInst'],0,1,'L');
            
        //Divisao administrativa
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Divisão administrativa:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['ufInst'],0,1,'L');
            
            //Nome
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Nome:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['nomeInst'],0,1,'L');
            
            //departamento
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Departamento:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['nomeDepartamento'],0,1,'L');
                       
            //grande area
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Grande Área:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$grandeArea['nome'],0,1,'L');

            
            //area
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Área:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$area['nome'],0,1,'L');

            
            //subarea
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Subárea:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$subArea['nome'],0,1,'L');

            
            //especialidade
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Especialidade:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$especialidade['nome'],0,1,'L');


            //Dados do usuário
            $pdf->Cell(30,8,"",0,1,'C');
            $pdf->SetFont('arial','B',15);
            $pdf->Cell(185,5,"Dados do usuário",0,1,'C');
            $pdf->Cell(30,8,"",0,1,'C');
            
            //nome do usuário
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Nome:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['nomeUsuario'],0,1,'L');
            
            //conta
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Conta:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['conta'],0,1,'L');
            
            //projeto cadastrado
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Projeto cadastrado:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->MultiCell(0,8,$usuarioPdf['titulo'].".",0,1);
            //$pdf->Cell(0,8,$projeto['titulo'],0,1,'L');
            
            //tipo de usuario
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Tipo do usuário:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['tipo'],0,1,'L');
                    
            //numero de matricula
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Número de matrícula:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['matricula'],0,1,'L');
            
            //e-mail
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"E-mail:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['email'],0,1,'L');
            
             //RG
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"RG:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['rg'],0,1,'L');
            
            //CPF
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"CPF:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['cpf'],0,1,'L');
            
            //endereco
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Endereço:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['endereco'],0,1,'L');
            
            //complemento
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Complemento:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['complemento'],0,1,'L');
            
            // bairro
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Bairro:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['bairro'],0,1,'L');
            
            //cidade
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Cidade:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['cidade'],0,1,'L');
            
            //divisao administrativa
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Divisâo administrativa:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['ufUsuario'],0,1,'L');
            
            //Pais
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"País:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['paisUsuario'],0,1,'L');
            
            //telefone
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Telefone:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['telefone'],0,1,'L');
            
            //celular
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Celular:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,$usuarioPdf['celular'],0,1,'L');
                        
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
    
    
    

    //TODAS AS ACTIONS ABAIXO 'VERIFICA' SAO UTILIZADAS PARA FAZER AS 'SELECTBOX' ALTERAREM DINAMICAMENTE
    //UTILIZANDO JQUERY
    
    //ACTION FAZ COMBO DINAMICO ENTRE INSTITUIÇAO E DEPARTAMENTO
    public function verificaAction(){
        //SO FUNCIONA SE DESABILITAR O LAYOUT
        $this->_helper->layout->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(); 
        
        $inst =  $this->_getAllParams();
        
        echo '<option value="0"> Selecione o departamento </option>';  

        $sql = new Application_Model_Departamento();    //$select->where("id_uf = ?", $uf);
        $qr = $sql->select()
                  ->where("instituicao = ?",$inst['inst'])
                ;
        
        
        $dados = $sql->fetchAll($qr)->toArray();
              foreach ($dados as $ln):
                echo '<option value="'.$ln['id'].'">'.$ln['nome'].'</option>';
              endforeach;
    
    }
    
    
    //ACTION FAZ COMBO DINAMICO ENTRE PAIS E UF
    public function verifica2Action(){
        //SO FUNCIONA SE DESABILITAR O LAYOUT
        $this->_helper->layout->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(); 
        
        $inst =  $this->_getAllParams();
        
        echo '<option value="0"> Selecione a divisão administrativa </option>';  

        $sql = new Application_Model_UnidadeFederativa();    //$select->where("id_uf = ?", $uf);
        $qr = $sql->select()
                  ->where("pais = ?",$inst['nome'])
                ;
        
        
        $dados = $sql->fetchAll($qr)->toArray();
              foreach ($dados as $ln):
                echo '<option value="'.$ln['id'].'">'.$ln['nome'].'</option>';
              endforeach;
    }
    
    
    //ACTION FAZ COMBO DINAMICO ENTRE UF E INSTITUIÇAO
    public function verifica3Action(){
        //SO FUNCIONA SE DESABILITAR O LAYOUT
        $this->_helper->layout->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(); 
        
        $inst =  $this->_getAllParams();
        
        echo '<option value="0"> Selecione a Instituição </option>';  

        $sql = new Application_Model_Instituicao();    //$select->where("id_uf = ?", $uf);
        $qr = $sql->select()
                  ->where("divisao_administrativa = ?",$inst['nome'])
                ;
        
        
        $dados = $sql->fetchAll($qr)->toArray();
              foreach ($dados as $ln):
                echo '<option value="'.$ln['id'].'">'.$ln['nome'].'</option>';
              endforeach;
    }
    
    
    
    
    
    //ACTION FAZ COMBO DINAMICO ENTRE GRANDEAREA E AREA, E ASSIM SUCESSIVAMENTE
    
    public function verifica4Action(){
        //SO FUNCIONA SE DESABILITAR O LAYOUT
        $this->_helper->layout->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(); 
        
        $id =  $this->_getAllParams();
        
        echo '<option value="0"> Selecione a área </option>';  

        $sql = new Application_Model_Area();    //$select->where("id_uf = ?", $uf);
        $qr = $sql->select()
                  ->where("grande_area = ?",$id['id'])
                ;
        
        
        $dados = $sql->fetchAll($qr)->toArray();
              foreach ($dados as $ln):
                echo '<option value="'.$ln['id'].'">'.$ln['nome'].'</option>';
              endforeach;
    }
    
    
    //area -> subArea
    
    public function verifica5Action(){
        //SO FUNCIONA SE DESABILITAR O LAYOUT
        $this->_helper->layout->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(); 
        
        $id =  $this->_getAllParams();
        
        echo '<option value="0"> Selecione a subárea </option>';  

        $sql = new Application_Model_SubArea();    //$select->where("id_uf = ?", $uf);
        $qr = $sql->select()
                  ->where("area = ?",$id['id'])
                ;
        
        
        $dados = $sql->fetchAll($qr)->toArray();
              foreach ($dados as $ln):
                echo '<option value="'.$ln['id'].'">'.$ln['nome'].'</option>';
              endforeach;
    }
    
    
    //subArea -> especialidade
    
    public function verifica6Action(){
        //SO FUNCIONA SE DESABILITAR O LAYOUT
        $this->_helper->layout->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(); 
        
        $id =  $this->_getAllParams();
        
        echo '<option value="0"> Selecione a especialidade </option>';  

        $sql = new Application_Model_Especialidade();    //$select->where("id_uf = ?", $uf);
        $qr = $sql->select()
                  ->where("sub_area = ?",$id['id'])
                ;
        
        
        $dados = $sql->fetchAll($qr)->toArray();
              foreach ($dados as $ln):
                echo '<option value="'.$ln['id'].'">'.$ln['nome'].'</option>';
              endforeach;
    }
    
    
    ////////////////////////////////////////////////////////////////////////////////////
    
    public function falhaAutenticacaoAction() {
        $this->_helper->layout->disableLayout();
    }
    
     //Fazer Logout
    public function sairAction(){
        $this->_helper->layout->setLayout('admin');
         $auth = Zend_Auth::getInstance();
         $auth->clearIdentity();
         $this->_redirect('/usuario');
         
     }
//////////////////
     public function erroAction(){
         $this->_helper->layout->disableLayout();
     }
    
     
}
