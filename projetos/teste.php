<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');

		
	if (isset($_SESSION['usu_id']) and isset($_GET['pro_id'])) 
	{
		$usu_id = $_SESSION['usu_id'];
		$pro_id = $_GET['pro_id'];
	}
	else {
		$home = "../index.php";
		header("Location:" . $home );	
	}


?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>easykanban</title>
        
        
        <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.0.6"></script>
        <link rel="stylesheet" type="text/css" media="all" href="../css/formulario.css">
        <link rel="stylesheet" type="text/css" media="all" href="../fancybox/jquery.fancybox.css">
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

        
        <link type="text/css" rel="stylesheet" href="../css/main.css" />
		<link type="text/css" rel="stylesheet" href="../css/quadro.css" />
        
		<script>
			function allowDrop(ev)
			{
				ev.preventDefault();
			}
		
			function drag(ev)
			{
				// Quando o usuário inicia um drag, guardamos no dataset do evento 
				// o id do objeto sendo arrastado
				ev.dataTransfer.setData("ID",ev.target.id);
			}
		
			// Quando o usuário arrasta sobre um dos painéis, retornamos 
			// false para que o evento não se propague para o navegador, o 
			// que faria com que o conteúdo fosse selecionado. 30 
			function dragOver(ev) { return false; } 
		
			function drop(ev)
			{
				ev.preventDefault();
				var data=ev.dataTransfer.getData("ID");
				ev.target.appendChild(document.getElementById(data));
			}
        </script>
        
	</head>
	
	<body>
    	
        <div id="container-cabecalho">
        <header>
            <div id="nome_usuario" class="menu_acesso_rapido">
                <label> <?php echo ( $_SESSION['usu_nome']) ?> </label>
            </div>
            
            <div id="acessiobilidade" >
                <label > <a class="menu_acesso_rapido" href="../logout.php"> logout </a> </label>
            </div>

        </header>
        
        
        </div>
        
        <div id="container-menu">
            <div id="nova-tarefa" >
                <a id="bug" class="modalbox" href="#inline"> 
                    <input class="orange_button" type="submit" value="+ Novo Projeto" > 
                </a>
            </div>
    	</div>
        
        <div id="options_menu">
        	<table width="50%">
           		<tr> 
                	<td> Mostrar somente minhas tarefas: <input type="checkbox" name="mostrar_tarefas" value="Bike"> </td>
            		<td> Mostrar somente tarefas atrasadas:  <input type="checkbox" name="mostrar_tarefas" value="Car"> </td>
                    <td> Mostrar Tarefas de: </td>
                </tr>
            </table>
        </div>
        
        <div id="main">
        
		<div id="div_quadro">
			
            <div id="div_scroll_quadro">
            
                <div id="div_reserva" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                	<label class="texto" > BACKLOG </label> 
                    <div id="tarefa" draggable="true" ondragstart="drag(event)" > </div>
                    <div id="tarefa1" draggable="true" ondragstart="drag(event)" > </div>
                </div>
                
                <div id="div_solicitado" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                	<label class="texto"> REQUISITADO </label>
                    
                </div>
                
                <div id="div_processo" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                	<label class="texto"> EM PROCESSO </label>
                </div>
                
                <div id="div_feito" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                	<label class="texto"> CONCLUIDO </label>
                </div>
                
                <div id="div_arquivado" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                	<label class="texto"> ARQUIVADO </label>
                </div>
     		</div>
            
		</div>
    </div>
        
	<!-- invisivel inline form -->
	<div id="inline">
	<h2> Adicionar novo Projeto </h2> <br />
    
	<form id="contact" name="contact" method="post" action="<?php echo ( $_SERVER['PHP_SELF'] ); ?>" >
		<table class="add_projeto" >
         	
            <tr>
                <td>
                    <table class="add_projeto">
                        <tr> <td>  <label for="nome" class="negrito">Título:</label> </td> </tr>
                        <tr> <td>  <input type="text" id="nome" name="nome" placeholder="Ex: Criação de Relatórios" required>  </td> </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                       	 	<tr> <td>  <label for="descricao" class="negrito">Descrição:</label> </td> </tr>
                        	<tr> <td>  <textarea id="descricao" rows="3" maxlength="250" name="descricao" placeholder="Pequena descrição sobre a terafa" ></textarea>  </td> </tr>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                       	 	<tr> <td>  <label for="descricao" class="negrito">Comentário:</label> </td> </tr>
                        	<tr> <td>  <textarea id="descricao" rows="3" maxlength="250" name="descricao" placeholder="Comentários sobre a tarefa" ></textarea>  </td> </tr>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                            <td> <label class="negrito" >Inicio:</label> </td>
                            <td> <label class="negrito" >Conclusão:</label> </td>
                            <td> <label class="negrito" >Tempo Estimado:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="date" id="data_inicio" name="data_inicio" required> </td>
                            <td> <input type="date" id="data_fim" name="data_fim" required> </td>
                            <td> <input type="text" id="data_fim" name="data_fim" placeholder="Tempo em Horas. Ex: 2:00" required> </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                            <td width="70%" > <label class="negrito" >Alocado para:</label> </td>
                            <td width="30%" > <label class="negrito" >Prioridade:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="date" id="data_inicio" name="data_inicio" required> </td>
                            <td> <input type="date" id="data_fim" name="data_fim" required> </td>
                        </tr>
                    </table>
               </td>
            </tr>

            <tr>
            <td> <input class="blue_button" type="submit" value="Cadastrar" name="send" id="send" /> </td>
            </tr>
            
         </table>
	</form>
	</div>

    <!-- basic fancybox setup -->
    <script type="text/javascript">
    
        function validateEmail(email) { 
            var reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return reg.test(email);
        }
        
        function sleep(milliseconds) {
          var start = new Date().getTime();
          for (var i = 0; i < 1e7; i++) {
            if ((new Date().getTime() - start) > milliseconds){
              break;
            }
          }
        }
    
        $(document).ready(function() {
            $(".modalbox").fancybox(); //inicia a caixa de dialogo com o formulário
            
            // fazer validação javascript
            //
            //
            
            sleep( 1000 );					// espera 115 minutos para o usuário poder ler a mensagem na tela		
            $("#contact").submit(function() {  // quando os dados forem submetidos...
                $("#contact").fadeOut("slow", function(){
                    $(this).before("<p><strong>Projeto cadastrado com Sucesso!</strong></p>"); // exibe mensagem de confirmação para o usuário
                    setTimeout("$.fancybox.close()", 1000); // fecha caixa de dialogo
                });
            });
    
        });
    </script>

	</body>
</html>
