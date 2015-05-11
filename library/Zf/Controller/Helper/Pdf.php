<?php


class Zend_Controller_Action_Helper_Pdf extends Zend_Controller_Action_Helper_Abstract
{
     
	public function __construct() {
        
    }

        public function relatorio($dados){
            //GERAR ARQUIVO PDF
            //$this->_helper->viewRenderer->setNoRender();
            //$this->_helper->layout->disableLayout();   
            $pdf=new FPDF("P","mm","A4");
		$pdf->Open();
		$pdf->SetTitle('Salve ou imprima este arquivo e encaminhe ao NBCGIB');
            $pdf->SetMargins(10,20,10);
            $pdf->AddPage();
            
            $pdf->SetFont('arial','B',15);
            $pdf->Cell(185,8,"Instituição",0,1,'C');
            $pdf->Cell(30,8,"",0,1,'C');
            
         //Pais
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(30,8,"País:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"País",0,1,'L');
            
        //Divisao administrativa
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Divisão administrativa:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"Divisão administrativa",0,1,'L');
            
            //Nome
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Nome:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"Nome",0,1,'L');
            
            //departamento
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Departamento:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"Departamento",0,1,'L');
            
            //Área de conhecimento
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Área de conhecimento:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"Área de conhecimento",0,1,'L');

            //DADOS DO PROJETO
            $pdf->Cell(30,8,"",0,1,'C');
            $pdf->SetFont('arial','B',15);
            $pdf->Cell(185,5,"Dados do projeto",0,1,'C');
            $pdf->Cell(30,8,"",0,1,'C');
            
            //grupo
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Grupo do projeto:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"Grupo do projeto",0,1,'L');
            
            //Titulo
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Título:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"Título",0,1,'L');
            
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Descrição:",0,1,'L');
            $pdf->setFont('arial','',12);
            $pdf->MultiCell(0,8,"Aqui vai um texto longo,para que possa ser feito a quebra de linha,como você pode ver isso acontecendo aqui, essa forma é boa para textos longo, para textos curtos é preferível a utilização da função 'cell'. a quebra de linha pode ser feito com o  OK",0,1);
            
             //Duração
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Duração(em meses):",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"4",0,1,'L');
            
            //Espaço em disco
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Espaço em disco:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"Espaço em disco",0,1,'L');
            
            //hpc
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"HPC:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"sim",0,1,'L');
            
            //proxy
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"proxy:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"sim",0,1,'L');
            
            // BD
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Servidor BD:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"sim",0,1,'L');
            
            //email
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Servidor e-mail:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"sim",0,1,'L');
            
            //web
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Servidor Web:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"sim",0,1,'L');
            
            //vpn
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"VPN:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"sim",0,1,'L');
            
            //Outros
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Outros recursos:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"",0,1,'L');
            
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Descrição das necessidades computacionais para o projeto:",0,1,'L');
            $pdf->setFont('arial','',12);
            $pdf->MultiCell(0,8,"Aqui vai um texto longo,para que possa ser feito a quebra de linha,como você pode ver isso acontecendo aqui, essa forma é boa para textos longo, para textos curtos é preferível a utilização da função 'cell'. a quebra de linha pode ser feito com o  OK",0,1);
            
            //Cadastro no cacau
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Cadastro no cacau:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"Sim",0,1,'L');
            
             //Responsável
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Responsável:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"",0,1,'L');
            
             //Data início
            $pdf->SetFont('arial','B',12);
            $pdf->Cell(50,8,"Início do projeto:",0,0,'L');
            $pdf->setFont('arial','',12);
            $pdf->Cell(0,8,"_/_/_",0,1,'L');
          
            $pdf->Close();
            ob_clean();        
            $pdf->Output('cadastro.pdf','D');
        }
        
}

?>
