<?php

//FUNÇOES PARA FAZER LOGIN  
class Application_Model_Login{
   
   //Funçao para fazer login do usuario
   public static function loginAdm($login,$senha){
       $model = new self;
       //Criptografando a senha
       $senhaSha1 = sha1($senha);
       
       $db = Zend_Db_Table_Abstract::getDefaultAdapter();
       //O USUARIO DE LOGIN ESTA COMO 'EMAIL' MAS PODE SER OUTRO LOGIN QUALQUER
       $adapter = new Zend_Auth_Adapter_DbTable($db,
               'login_adm',//nome da tabela
               'email',
               'senha'
               //'SHA1(?) AND active = "TRUE" '
               );
       
       $adapter
               ->SetIdentity($login)
               ->SetCredential($senhaSha1);
      //////////////////
       $auth = Zend_Auth::getInstance();
       $result = $auth->authenticate($adapter);
       
       if($result->isValid()){
           $data = $adapter->getResultRowObject(null,'senha');
           $auth->getStorage()->write($data);
           
           return TRUE;
       }else{
           return $model->getMessages($result);
       }
       
       $this->view->$adapter = $adapter;
   }
   
   public static function loginUsuario($login,$senha){
       $model = new self;
       //Criptografando a senha
       $senhaSha1 = sha1($senha);
       
       $db = Zend_Db_Table_Abstract::getDefaultAdapter();
       //O USUARIO DE LOGIN ESTA COMO 'EMAIL' MAS PODE SER OUTRO LOGIN QUALQUER
       $adapter = new Zend_Auth_Adapter_DbTable($db,
               'usuario',//nome da tabela
               'email',
               'senha'
               //'SHA1(?) AND active = "TRUE" '
               );
       
       $adapter
               ->SetIdentity($login)
               ->SetCredential($senhaSha1);
      //////////////////
       $auth = Zend_Auth::getInstance();
       $result = $auth->authenticate($adapter);
       
       if($result->isValid()){
           $data = $adapter->getResultRowObject(null,'senha');
           $auth->getStorage()->write($data);
           
           return TRUE;
       }else{
           return $model->getMessages($result);
       }
       
       $this->view->$adapter = $adapter;
   }
   
   
   public function getMessages(Zend_Auth_Result $result){
       switch($result->getCode()){
           
           case $result::FAILURE_IDENTITY_NOT_FOUND:
               $msg = 'Login nao encontrado';
               break;
           case $result::FAILURE_IDENTITY_AMBIGUOUS:
               $msg = 'Login em duplicidade';
               break;
           case $result::FAILURE_CREDENTIAL_INVALID:
               $msg = 'Senha nao corresponde';
               break;
           case $result::FAILURE;
           case $result::FAILURE_UNCATEGORIZED;
               $msg = 'Login e/ou senha nao encontrados';
           
       }
   }
    
    
}