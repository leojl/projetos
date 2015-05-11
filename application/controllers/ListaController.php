<?php



class ListaController extends Zend_Controller_Action
{

    public function init()
    {
       require_once('../library/fpdf/fpdf.php');
    }

    public function indexAction()
    {
        
        $arrayProjetoUsuario = array(
            'id',
            'status',
            'projeto',
            'usuario',
            'tipo_usuario'            
        );
        
        $arrayProjeto = array(
            'grupo',
            'titulo',
            'descricao',
            'duracao' ,
            'hpc',
            'proxy',
            'bd',
            'email',
            'web',
            'vpn',
            'outros',
            'necessidades_computacionais',
            'inicio',
            'cacau',
            'status',
            'grande_area',
              );
        
        $arrayUsuario = array(
            'nome',
            'email',
            'conta',
            'cidade',
            'divisao_administrativa',            
            'lattes',
            'grande_area',            
        );

        
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        $rows = $db->select()
                             ->from('projeto_usuario',$arrayProjetoUsuario)
                             ->joinInner('projeto','projeto_usuario.projeto=projeto.id',$arrayProjeto)
                             ->joinInner('usuario','projeto_usuario.usuario=usuario.id',$arrayUsuario)
                             ->where('projeto_usuario.tipo_usuario=1')
                             ->orWhere('projeto_usuario.tipo_usuario=3')
               ;
                
        $result = $db->fetchAll($rows);
        $this->view->dados = $result;
           
    }
   
    
    public function dadosAction(){
            
        $this->_helper->layout->disableLayout(); 
        
        $arrayProjetoUsuario = array(
            'id',
            'status',
            'projeto',
            'usuario',
            'tipo_usuario'            
        );
        
        $arrayProjeto = array(
            'grupo',
            'titulo',
            'descricao',
            'duracao' ,
            'necessidades_computacionais',
            'inicio',
            'cacau',
            'status',
            'grande_area',
              );
        
        $arrayUsuario = array(
            'nome',
            'email',
            'conta',
            'cidade',
            'divisao_administrativa',            
            'lattes',
            'grande_area',            
        );
        
        $idProjetoUsuario = $this->_getParam('id-projeto-usuario');
        
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        
        $rows = $db->select()
                             ->from('projeto_usuario',$arrayProjetoUsuario)
                             ->joinInner('projeto','projeto_usuario.projeto=projeto.id',$arrayProjeto)
                             ->joinInner('usuario','projeto_usuario.usuario=usuario.id',$arrayUsuario)
                             ->where('projeto_usuario.id='.$idProjetoUsuario)
               ;
               
        $result = $db->fetchAll($rows);
        $this->view->dados = $result;
    
    }

    
}
 