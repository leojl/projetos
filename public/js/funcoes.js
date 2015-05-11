 //MASCARAS NOS CAMPOS
 $(document).ready(function(){
            $("#cpf").mask("999.999.999-99");
            $("#tel").mask("(99)9999-9999?9");
            $("#cel").mask("(99)9999-9999?9");
            //$("#cep").mask("99.999-999");
            $("#tel-inst").mask("(99)9999-9999?9");
            $("#cep-inst").mask("99.999-999");
            $("#matricula-usuario").mask("?999999999");
            $("#espacoDisco_projeto").mask("?99999999");
            $("#duracao_projeto").mask("?999");
            $("#conta-usuario").mask("?***************");
            $("#rg-usuario").mask("?***********");
            $("#grupo_projeto").mask("?********");
        });
        
        ////////////////////////////////////////////////////////////
        //CAMPO DATA NO FORMULARIO
        $(document).ready(function(){
        $('#data').focus(function(){
          $(this).calendario({
            target:'#data'
          });
        });
    });
////////////////////////////////////////////////////////////

        function desbloquear(cb,tb,combo){
            
            if(cb.checked == true){
                tb.disabled = false;
                combo.disabled = true;
                combo.value = 0;
            }else{
        
                tb.disabled = true;
                tb.value = "";
                combo.disabled = false;
           }

        }




        function cadastroInst(cb){
             
            
            if(cb.checked === true){
                document.getElementById('cidade-inst').disabled = false;
                document.getElementById('end.inst').disabled = false;
                document.getElementById('comp.inst').disabled = false;
                document.getElementById('bairro.inst').disabled = false;
                document.getElementById('cep-inst').disabled = false;
                document.getElementById('tel-inst').disabled = false;
                document.getElementById('ramal-inst').disabled = false;
                document.getElementById('fax-inst').disabled = false;
                
            }else{
                document.getElementById('cidade-inst').disabled = true;
                document.getElementById('end.inst').disabled = true;
                document.getElementById('comp.inst').disabled = true;
                document.getElementById('bairro.inst').disabled = true;
                document.getElementById('cep-inst').disabled = true;
                document.getElementById('tel-inst').disabled = true;
                document.getElementById('ramal-inst').disabled = true;
                document.getElementById('fax-inst').disabled = true;
           }

        }
        


    //FUNÇOES EM JQUERY PARA SELECTBOX DINAMICAS
  
     
        //COMBO DINAMICO PAIS E UF DA INSTITUIÇAO
        $(document).ready(function(){

            $("select[name=pais-inst]").change(function(){
                $("select[name=uf-inst]").html('<option value="0">Carregando...</option>');//o comando desta linha 'html' é usado para inserir algo na combobox

                $.post( '  http://nbcgib.uesc.br/projetos/public/usuario/verifica2 ',//campo para o qual vou enviar meus dados do 'post' do Jquery
                    {nome:$(this).val()},//'nome' é uma variavel qualquer. esta linha tbm poderia ser assim --> {inst:("select[name=cbbDepartamento]").val()},
                    function(valor){
                        $("select[name=uf-inst]").html(valor);
                    }
                );

            });
        });

    
        
    
  
     
        //COMBO DINAMICO PAIS E UF DA INSTITUIÇAO
        $(document).ready(function(){

            $("select[name=uf-inst]").change(function(){
                $("select[name=nome-inst]").html('<option value="0">Carregando...</option>');//o comando desta linha 'html' é usado para inserir algo na combobox

                $.post( '  http://nbcgib.uesc.br/projetos/public/usuario/verifica3 ',//campo para o qual vou enviar meus dados do 'post' do Jquery
                    {nome:$(this).val()},//'nome' é uma variavel qualquer. esta linha tbm poderia ser assim --> {inst:("select[name=cbbDepartamento]").val()},
                    function(valor){
                        $("select[name=nome-inst]").html(valor);
                    }
                );

            });
        });

    
        
  
     
        
        $(document).ready(function(){

            $("select[name=nome-inst]").change(function(){
                $("select[name=departamento-inst]").html('<option value="0">Carregando...</option>');//o comando desta linha 'html' é usado para inserir algo na combobox

                $.post( '  http://nbcgib.uesc.br/projetos/public/usuario/verifica ',//campo para o qual vou enviar meus dados do 'post' do Jquery
                    {inst:$(this).val()},//'inst' é uma variavel qualquer. esta linha tbm poderia ser assim --> {inst:("select[name=cbbDepartamento]").val()},
                    function(valor){
                        $("select[name=departamento-inst]").html(valor);
                    }
                );

            });
        });

    
        
  // SELECTBOX DINAMICO DAS AREAS DE CONHECIMENTO DO CNPQ
     
        // grandeArea -> Area
        
        $(document).ready(function(){

            $("select[name=grandeAreaInst]").change(function(){
                $("select[name=areaInst]").html('<option value="0">Carregando...</option>');

                $.post( '  http://nbcgib.uesc.br/projetos/public/usuario/verifica4 ',
                    {id:$(this).val()},
                    function(valor){
                        $("select[name=areaInst]").html(valor);
                    }
                );

            });
        });

    
        //Area -> SubArea
        $(document).ready(function(){

            $("select[name=areaInst]").change(function(){
                $("select[name=subAreaInst]").html('<option value="0">Carregando...</option>');//o comando desta linha 'html' é usado para inserir algo na combobox

                $.post( '  http://nbcgib.uesc.br/projetos/public/usuario/verifica5 ',//campo para o qual vou enviar meus dados do 'post' do Jquery
                    {id:$(this).val()},//'id' é uma variavel qualquer. esta linha tbm poderia ser assim --> {inst:("select[name=cbbDepartamento]").val()},
                    function(valor){
                        $("select[name=subAreaInst]").html(valor);
                    }
                );

            });
        });

    
        //SubArea -> especialidade
        $(document).ready(function(){

            $("select[name=subAreaInst]").change(function(){
                $("select[name=especialidadeInst]").html('<option value="0">Carregando...</option>');//o comando desta linha 'html' é usado para inserir algo na combobox

                $.post( '  http://nbcgib.uesc.br/projetos/public/usuario/verifica6 ',//campo para o qual vou enviar meus dados do 'post' do Jquery
                    {id:$(this).val()},//'id' é uma variavel qualquer. esta linha tbm poderia ser assim --> {inst:("select[name=cbbDepartamento]").val()},
                    function(valor){
                        $("select[name=especialidadeInst]").html(valor);
                    }
                );

            });
        });

    
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
  
  
  
  
     
        //COMBO DINAMICO PAIS E UF DO USUARIO
        $(document).ready(function(){

            $("select[name=pais-usuario]").change(function(){
                $("select[name=uf-usuario]").html('<option value="0">Carregando...</option>');//o comando desta linha 'html' é usado para inserir algo na combobox

                $.post( '  http://nbcgib.uesc.br/projetos/public/usuario/verifica2 ',//campo para o qual vou enviar meus dados do 'post' do Jquery
                    {nome:$(this).val()},//'inst' é uma variavel qualquer. esta linha tbm poderia ser assim --> {inst:("select[name=cbbDepartamento]").val()},
                    function(valor){
                        $("select[name=uf-usuario]").html(valor);
                    }
                );

            });
        });

    
    ////////////////////////////////////////////////////////////////////////////////////////////////
       
  function validarUsuario(form){

      var paisUsu = form.combo_paisUsuario.value;
      var ufUsu = form.combo_ufUsuario.value;
      var paisIsnt = form.combo_paisInst.value;
      var ufInst = form.combo_ufInst.value;
      var nomeInst = form.combo_nomeInst.value;
      var depart = form.combo_departamentoInst.value;
      var projeto = form.comboProjetos.value;
      var tipoUsuario = form.tipo_usuario.value;
      var area = form.areaInst.value;
      
      var cPaisUsu = form.check_paisUsuario.checked;
      var cUfUsu = form.check_ufUsuario.checked;
      var cPaisInst = form.check_paisInst.checked;
      var cUfInst = form.check_ufInst.checked;
      var cNomeInst = form.check_nomeInst.checked;
      var cDepart = form.check_departamentoInst.checked;
     
     if( projeto == 0 ){
        alert("selecione um projeto.");
        form.comboProjetos.focus();
        return false;
  }
  
  if( tipoUsuario == 0 ){
        alert("selecione um tipo de usuario.");
        form.tipo_usuario.focus();
        return false;
  }
     
    if( (paisUsu == 0) && (cPaisUsu==false) ){
        alert("selecione um país ou cadastre outro.");
        form.combo_paisUsuario.focus();
        return false;
  }
  
   if(ufUsu == 0 && (cUfUsu==false)){
    alert("selecione uma divisão administrativa ou cadastre outra.");
    form.combo_ufUsuario.focus();
    return false;
  }
   if(paisIsnt == 0 && (cPaisInst==false)){
    alert("selecione um país ou cadastre outro.");
    form.combo_paisInst.focus();
    return false;
  }
   if(ufInst == 0 && (cUfInst==false) ){
    alert("selecione uma divisão administrativa ou cadastre outra.");
    form.combo_ufInst.focus();
    return false;
  }
   if(nomeInst == 0 && (cNomeInst==false) ){
    alert("selecione uma instituição ou cadastre outra.");
    form.combo_nomeInst.focus();
    return false;
  }
   if(depart == 0 && (cDepart==false) ){
    alert("selecione o departamento ou cadastre outro.");
    form.combo_departamentoInst.focus();
    return false;
  }
  
  if( area == 0 ){
        alert("Para prosseguir com o cadastro a grande área e área devem estar selecionados.");
        form.areaInst.focus();
        return false;
  }
    
    return true;
}
 
  
     
  function validarProjeto(form){

      var paisIsnt = form.combo_paisInst.value;
      var ufInst = form.combo_ufInst.value;
      var nomeInst = form.combo_nomeInst.value;
      var depart = form.combo_departamentoInst.value;
      var area = form.areaInst.value;
      
      var cPaisInst = form.check_paisInst.checked;
      var cUfInst = form.check_ufInst.checked;
      var cNomeInst = form.check_nomeInst.checked;
      var cDepart = form.check_departamentoInst.checked;
      //var cArea = form.check_areaInst.checked;
      
   if(paisIsnt == 0 && (cPaisInst==false)){
    alert("selecione um país ou cadastre outro.");
    form.combo_paisInst.focus();
    return false;
  }
   if(ufInst == 0 && (cUfInst==false) ){
    alert("selecione uma divisão administrativa ou cadastre outra.");
    form.combo_ufInst.focus();
    return false;
  }
   if(nomeInst == 0 && (cNomeInst==false) ){
    alert("selecione uma instituição ou cadastre outra.");
    form.combo_nomeInst.focus();
    return false;
  }
   if(depart == 0 && (cDepart==false) ){
    alert("selecione o departamento ou cadastre outro.");
    form.combo_departamentoInst.focus();
    return false;
  }
  
//   if(area == 0 && (cArea==false) ){
//    alert("selecione a  Área de conhecimento ou cadastre outra.");
//    form.combo_areaInst.focus();
//    return false;
//  }
  
  if( area == 0 ){
        alert("Para prosseguir com o cadastro a grande área e área devem estar selecionados.");
        form.areaInst.focus();
        return false;
  }
    
    return true;
}


function validarProjetoUsuario(form){
    
    var tipoUsuario = form.tipo_usuario.value;
    var projetos = form.comboProjetos.value;
    
    if(tipoUsuario === '0'){
        alert("selecione um tipo de usuário!");
        form.tipo_usuario.focus();
        return false;
  }
    if(projetos === '0'){
        alert("selecione um projeto!");
        form.comboProjetos.focus();
        return false;
  }
  
  return true;
    
}
//Função em JQuery para adicionar dinamicamente mais campos no formulário
$(document).ready(function(){  
 
    var linhaHorizontal = '<hr width=80%><br>';
    var input1 = '<label class="lbl-default" >Outro software:</label><input type="text"  class="txb-default" name="outros_softwares[]" ><br><br>';
    var input2 = '<label class="lbl-default" >Versão:</label><input type="text"  class="txb-default" name="versao_software[]"  ><br><br>';
    var input3 = '<label class="lbl-default" >Links da documentação:</label><textarea name="links[]"  rows="3" cols="40"></textarea><br><br>';
    
    $("input[name='add']").click(function( e ){  
        $('#mais_inputs').append( linhaHorizontal );
        $('#mais_inputs').append( input1 );
        $('#mais_inputs').append( input2 );  
        $('#mais_inputs').append( input3 );  
    });  
 
    /*$('#inputs_adicionais').delegate('a','click',function( e ){  
        e.preventDefault();  
        $( this ).parent('label').remove();  
    });  */
 
}); 

///////////////////////////////////////////////////////////////////////////

function checkAll(){
   for (i=0;i<document.form.elements.length;i++){
       
      if(document.form.elements[i].type === "checkbox"){
          
          if(document.form.elements[i].checked === true){
             document.form.elements[i].checked = 0;
          }
          
          else{
              document.form.elements[i].checked = 1;
          }
      }
   }
} 

function verificarSenhas(senha1, senha2){
    if(senha1 === senha2){
        return true
        
    } else return false;
}

function link_artigo(checkbox,link,artigo){
    if(checkbox.checked === true){
        link.disabled = true;
        artigo.disabled = false;
    }else{        
        link.disabled = false;
        artigo.disabled = true;
        
    }
    
}

//Funçao para consultar CEP e retornar o logradouro
//TEMPORARIAMENTE DESATIVADA 
//$(document).ready(function(){
//                //Preenche os campos na a&#231;&#227;o "Blur" (mudar de campo)
//                $("#cep").blur(function(){
//                        $("#endereco-usuario").val("procurando endereco...")//Rua
//                $("#bairro-usuario").val("procurando bairro...")
//            $("#cidade-usuario").val("procurando cidade...")
//                //$("#uf").val("...")
//        
//            // seta a variavel requisitada no campo cep
//        consulta = $("#cep").val()
//                
//                //Realiza a consulta
//                /*Realiza a consulta atrav&#233;s do toolsweb passando o cep como parametro
//                  e informando que vamos consultar no tipo javascript
//                */
//                $.getScript("http://www.toolsweb.com.br/webservice/clienteWebService.php?cep="+consulta+"&formato=javascript", function(){
//                        
//                        //unescape - Decodifica uma string codificada com o m&#233;todo escape.
//                        rua=unescape(resultadoCEP.logradouro)
//                        bairro=unescape(resultadoCEP.bairro)
//                        cidade=unescape(resultadoCEP.cidade)
//                        //uf=unescape(resultadoCEP.uf)
//                        
//                        // preenche os campos
//                        $("#endereco-usuario").val(rua)
//                        $("#bairro-usuario").val(bairro)
//                        $("#cidade-usuario").val(cidade)
//                        //$("#uf").val(uf)
//        
//                        });
//                });
//        });