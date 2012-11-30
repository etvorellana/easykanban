<?php
	include_once('../connect/connect_vars.php');
	include_once('../sessao_php/inicia_sessao.php');
	
	if (isset($_SESSION['usu_id']) and isset($_GET['pro_id']) and isset($_GET['tip_id']) ) 
	{
		$usu_id = $_SESSION['usu_id'];
		$pro_id = $_GET['pro_id'];
		$permissao = $_GET['tip_id'];
		
		function pegar_usuario_por_projeto( $parametro_pro_id )
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			// Seleciona o banco de dados
			mysqli_select_db($dbc, "easykanban-bd")
				or die ('Erro ao selecionar o Banco de Dados');
			
			// selecioma todos os usu�rios logados ao projeto selecionado
			$query = "SELECT u.`usu_id`, u.`usu_nome`
					  FROM `usuario` u 
					  JOIN `usuario_projeto_tipo` up on up.`usu_id` = u.`usu_id` 
					  JOIN `projeto` p on p.`pro_id` = up.`pro_id`
					  WHERE p.`pro_id`= '$parametro_pro_id'"
			or die ("Erro ao construir a consulta");	
					
			//executa query de inser��o na tabela cep
			$data = mysqli_query($dbc, $query)
				or die('Erro ao executar a inser��o na tabela projeto');
			
			return $data;
			
			// fecha conex�o com bd
			mysqli_close($dbc);	
		} // fim fun��o pegar_usuario_por_projeto
		
	}
	else {
		header( 'Location: ../index.php' ) xor die ;	
	}	
	
?>

<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<title>Documento sem t&iacute;tulo</title>
    
    <link rel="stylesheet" type="text/css" media="all" href="../css/formulario.css">
    <link rel="stylesheet" type="text/css" media="all" href="../fancybox/jquery.fancybox.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.0.6"></script>
    
    <link type="text/css" rel="stylesheet" href="../css/main.css" />
    <link type="text/css" rel="stylesheet" href="../css/candidato.css" />
    
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />
    <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css" />
    <script>	
        $(function() {
            $("#data_inicio").datepicker({ dateFormat: "yy-mm-dd" }).val()
            $("#data_fim").datepicker({ dateFormat: "yy/mm/dd" }).val()
        });
        
        $(function() {
            $( "#data_inicio" ).datepicker();
        });
        
        $(function() {
            $( "#data_fim" ).datepicker();
        });
    </script>

	
    <script type="text/javascript" language="javascript">
		$(document).ready(function(){
			$('.fancybox').fancybox();
			
            $("#contact").submit(function() {  // quando os dados forem submetidos...
                $("#contact").fadeOut("slow", function(){
                    $(this).before("<p><strong>Tarefa inserida com Sucesso!</strong></p>"); // exibe mensagem de confirma��o para o usu�rio
                    setTimeout("$.fancybox.close()", 1000); // fecha caixa de dialogo
                });
            });
			
		});
	</script>
    
	<script>
        function allowDrop(ev)
        {
            ev.preventDefault();
        }
        
        function drag(ev, responsavel )
        {
            var usuario_logado = <?php echo $_SESSION['usu_id'] ?>;
            
            if ( responsavel != usuario_logado ){
                alert('Permiss�o negada!');
                return false;	
            }
            
            //alert("Responsavel" + responsavel);
            ev.dataTransfer.setData("Text",ev.target.id);
        }
        
        // Quando o usu&aacute;rio arrasta sobre um dos pain�is, retornamos 
        // false para que o evento n�o se propague para o navegador, o 
        // que faria com que o conte�do fosse selecionado
        function dragOver(ev) { 
            return false; 
        } 
        
        function drop(ev, numero_tarefas, maximo_tarefas)
        {
            //alert( String(numero_tarefas) + ' >= ' + String(maximo_tarefas) );
            
            if ( numero_tarefas >= maximo_tarefas ){
                return false;
            }
            
            ev.preventDefault();
            var data=ev.dataTransfer.getData("Text");
            ev.target.appendChild(document.getElementById(data));
            
            var sit_id = String(ev.target.id);
            
            var tar_id = String(data);
            
            //alert( sit_id + " " + tar_id );

            // se a tarefa for alocada de posi��o, o servidor � requisitado para fazer a atualiza��o do status da tarefa
            location.href= '<?php echo  'mudar_status_tarefa.php?pro_id=' , $pro_id , '&tip_id=', $permissao, '&action=change_state&tar_id='; ?>' + tar_id + '<?php echo'&sit_id='; ?>' + sit_id + '<?php if (isset($usu_id_selecionado)) echo '&usu_id_selecionado=', $usu_id_selecionado; ?>';
            
        }
    </script>
    
    
</head>

<body>
    <!-- Cabe�alho -->
    <div id="container-cabecalho">
    <header>
        <div id="nome_usuario" class="menu_acesso_rapido" >
            <label> <?php echo ( $_SESSION['usu_nome'] ) ?> </label>
        </div>
        
        <div id="logout" class="config_logout">
            <label> <a class="menu_acesso_rapido" href="../logout.php"> logout </a> </label>
        </div>
        
        <?php
        if ( $permissao == ADMIN )
        echo '
            <div id="config" class="config_logout">
                <label> <a class="menu_acesso_rapido" href="config_tarefas.php?pro_id=', $pro_id ,'&tip_id=', $permissao, '"> Configura��es </a> </label>
            </div>';
        ?> 
    </header>
    </div>
    <!-- Fim cabe�alho -->
    
    <!-- Barra de Menu -->
    <div id="container-menu-tarefas">
    <?php
        if ($permissao == ADMIN ) {
            echo '
                <div id="nova-tarefa" >
                    <a id="bug" class="fancybox" href="#inline"> 
                        <input class="orange_button" type="submit" value="+ Nova Tarefa" > 
                    </a>
                </div>
            ';
        }
    ?>
    </div>
    <!-- Fim barra de Menu -->
    
    <!-- menu de op��es -->
    <nav id="options_menu">
    <ul>
        <li> Mostrar somente minhas tarefas: <input type="checkbox" name="mostrar_tarefas" value="Bike"> </li>
        <li> Mostrar somente tarefas atrasadas:  <input type="checkbox" name="mostrar_tarefas" value="Car"> </li>
        <li>
            Mostrar Tarefas de:
            <select id="mostrar_tarefas" name="mostrar_tarefas" required>      
            <?php
               $pegar_usuario_por_projeto = pegar_usuario_por_projeto( $_GET['pro_id'] );
			   
				while ($row = mysqli_fetch_array($pegar_usuario_por_projeto)) {
					echo '<option value="' , $row['usu_id'] , '"> ' , $row['usu_nome'] , '</option>';
				}
				
				mysqli_data_seek($pegar_usuario_por_projeto, 0);
            ?>    
            </select>                     
        </li>
    </ul>
    </nav>
    <!-- Fim menu de op��es -->


    
    <!-- Quadro de Kanban -->
    <div id="div_quadro">
    
        <div id="div_scroll_quadro">
        
            <?php 
                // conectar ao banco de dados
                $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
                die('Erro ao conectar ao BD!');
            
                // Seleciona o banco de dados
                mysqli_select_db($dbc, "easykanban-bd")
                    or die ('Erro ao selecionar o Banco de Dados');
            
                $query = "SELECT s.`sit_id`, t.`tar_id`, t.`tar_titulo`, r.`usu_id`
                        FROM `tarefa` t
                        JOIN `projeto` p ON p.`pro_id` = t.`pro_id`
                        JOIN `situacao` s ON s.`sit_id` = t.`sit_id`
                        JOIN `responsavel` r on r.`tar_id` = t.`tar_id` 
                        JOIN `usuario` u on u.`usu_id` = r.`usu_id`
                        WHERE t.`pro_id` = '$pro_id'"
                or die ("Erro ao construir a consulta");		
                        
                //executa query de consulta na tabela tarefa
                $data = mysqli_query($dbc, $query)
                    or die('Erro ao executar a consulta na tabela tarefa');

				
				// seleciona o limite de tarefa de cada uma das colunas do quadro kanban
				$query = 'SELECT s.`sit_id`, l.`lin_limite`
						  FROM `limite_tarefa` l
						  JOIN `projeto` p on p.`pro_id` = l.`pro_id`
						  JOIN `situacao` s on s.`sit_id` = l.`sit_id`
						  WHERE p.`pro_id` =%s'
								or die ("Erro ao construir a consulta");
				
				// alimenta os parametros da conculta
				$query = sprintf($query, $pro_id );	
						
				//executa query na tabela limite_tarefa
				$data_limite_tarefas = mysqli_query($dbc, $query)
					or die('Erro ao execultar a inser��o na tabela projeto');
	
                mysqli_close($dbc);
                
				$row_limite_tarefa = mysqli_fetch_array($data_limite_tarefas);
				
                $linhas = mysqli_num_rows($data);
            
				$GLOBALS['numero_tarefas_backlog'] = mysqli_num_rows($data_limite_tarefas);
				$GLOBALS['maximo_tarefas_backlog'] = $row_limite_tarefa['lin_limite'];
            ?>  
            
            <div id="1" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                <label class="texto" > BACKLOG </label> 
                <label class="texto" > [ <?php echo $GLOBALS['numero_tarefas_backlog'], ' / ', $GLOBALS['maximo_tarefas_backlog'] ?> ]  </label> <br>
                <?php
                    $row_tarefas = mysqli_fetch_array($data);
                    $linha = 0;
                    
                    if ( $row_tarefas['sit_id'] == 1 ){
                        do {
                            echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="true" ondragstart="drag(event, ', $row_tarefas['usu_id'], ')" > ID:' , $row_tarefas['tar_id'] , '</div>';
                            
                            $linha++;
                            $row_tarefas = mysqli_fetch_array($data);

                        }while ( $row_tarefas['sit_id'] == 1 );
                        
                        mysqli_data_seek( $data, $linha);
                        
                        if ( $data != NULL )
                            $row_tarefas = mysqli_fetch_array($data);
                    }
                ?>
            </div>
            
            

            <div id="2" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                <label class="texto"> REQUISITADO </label>
                <?php
                    if ( $row_tarefas['sit_id'] == 2 ){
                        do {
                            echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="true" ondragstart="drag(event, ', $row_tarefas['usu_id'], ')" > ID:' , $row_tarefas['tar_id'] , '</div>';
                            
                            $linha++;
                            $row_tarefas = mysqli_fetch_array($data);

                        }while ( $row_tarefas['sit_id'] == 2 );
                        
                        mysqli_data_seek( $data, $linha);
                        
                        if ( $data != NULL )
                            $row_tarefas = mysqli_fetch_array($data);
                    }
                ?>
            </div>
            
            

            <div id="3" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                <label class="texto"> EM PROCESSO </label>
                <?php
                    
                        if ( $row_tarefas['sit_id'] == 3 ){
                            do {
                                echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="true" ondragstart="drag(event, ', $row_tarefas['usu_id'], ')" > ID:' , $row_tarefas['tar_id'] , '</div>';
                                
                                $linha++;
                                $row_tarefas = mysqli_fetch_array($data);
    
                            }while ( $row_tarefas['sit_id'] == 3 );
                            
                            mysqli_data_seek( $data, $linha);
                            
                            if ( $data != NULL )
                                $row_tarefas = mysqli_fetch_array($data);
                        }
                    
                ?>
            </div>
            


            <div id="4" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                <label class="texto"> CONCLUIDO </label>
                <?php
                    
                        if ( $row_tarefas['sit_id'] == 4 ){
                            do {
                                echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="true" ondragstart="drag(event, ', $row_tarefas['usu_id'], ')" > ID:' , $row_tarefas['tar_id'] , '</div>';
                                
                                $linha++;
                                $row_tarefas = mysqli_fetch_array($data);
    
                            }while ( $row_tarefas['sit_id'] == 4 );
                            
                            mysqli_data_seek( $data, $linha);
                            
                            if ( $data != NULL )
                                $row_tarefas = mysqli_fetch_array($data);
                        }
                    
                ?>
            </div>
            
         
            <div id="5" class="quadro" ondrop="drop(event)" ondragover="allowDrop(event)" >
                <label class="texto"> ARQUIVADO </label>
                <?php
            
                        if ( $row_tarefas['sit_id'] == 5 ){
                            do {
                                echo '<div id="', $row_tarefas['tar_id'], '" class="tarefa" draggable="true" ondragstart="drag(event, ', $row_tarefas['usu_id'], ')" > ID:' , $row_tarefas['tar_id'] , '</div>';
                                
                                $linha++;
                                $row_tarefas = mysqli_fetch_array($data);
    
                            }while ( $row_tarefas['sit_id'] == 5 );
                            
                            mysqli_data_seek( $data, $linha);
                            
                            if ( $data != NULL )
                                $row_tarefas = mysqli_fetch_array($data);
                        }
                    
                ?>
            </div>
        </div>
    
    </div>
	<!-- invisivel inline form -->
	<div id="inline">
	<h2> Adicionar novo Projeto </h2> <br />
    
	<form id="contact" name="contact" method="post" action="add_tarefa.php?pro_id=<?php echo $pro_id, '&tip_id=', $permissao; ?>" >
		<table class="add_projeto" >
         	
            <tr>
                <td>
                    <table class="add_projeto">
                        <tr> <td>  <label for="titulo" class="negrito">T&iacute;tulo:</label> </td> </tr>
                        <tr> <td>  <input type="text" id="titulo" name="titulo" placeholder="Ex: Cria&ccedil;&atilde;o de Relat&oacute;rios" required>  </td> </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                       	 	<tr> <td>  <label for="descricao" class="negrito">Descri&ccedil;&atilde;o:</label> </td> </tr>
                        	<tr> <td>  <textarea id="descricao" rows="3" maxlength="250" name="descricao" placeholder="Pequena descri&ccedil;&atilde;o sobre a terafa" ></textarea>  </td> </tr>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                       	 	<tr> <td>  <label for="comentario" class="negrito">Coment&aacute;rio:</label> </td> </tr>
                        	<tr> <td>  <textarea id="comentario" rows="3" maxlength="250" name="comentario" placeholder="Coment&aacute;rios sobre a tarefa" ></textarea>  </td> </tr>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                            <td> <label class="negrito" >Inicio:</label> </td>
                            <td> <label class="negrito" >Conclus&atilde;o:</label> </td>
                            <td> <label class="negrito" >Tempo Estimado:</label> </td>
                        </tr>
                        <tr>
                            <td> <input class="selector" type="text" id="data_inicio" name="data_inicio" /> </td>
                            <td> <input class="selector" type="text" id="data_fim" name="data_fim" /> </td>
                           
                            <td> <input type="text" id="tempo_estimado" name="tempo_estimado" placeholder="Tempo em Horas. Ex: 2:00" required> </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                            <td> <label class="negrito" >Alocado para:</label> </td>
                            <td> <label class="negrito" >Tipo:</label> </td>
                            <td> <label class="negrito" >Prioridade:</label> </td>
                        </tr>
                        <tr>
                            <td>     
							<select class="tipo_situacao" name="tipo_situacao" required>      
                            <?php
								while ($row = mysqli_fetch_array($pegar_usuario_por_projeto)) {
									echo '<option value="' , $row['usu_id'] , '"> ' , $row['usu_nome'] , '</option>';
								}
							?>    
                            </select>          
                            </td>
                            
                            <td>									
                            <select class="tipo_situacao" name="tip_tarefa" required>
								<option value="1"> Tarefa </option>
                                <option value="2"> Nova Caracter�stica </option>
                                <option value="3"> Defeito  </option>
                                <option value="4"> Melhoria  </option>
							</select>
                            </td>
                            
                            <td>									
                            <select class="tipo_situacao" name="prioridade" required>
								<option value="1">  Baixa </option>
                                <option value="2">  M�dia </option>
                                <option value="3">  Alta  </option>
							</select>
                            </td>
                        </tr>
                    </table>
               </td>
            </tr>

            <tr>
            	<td> 
                	<table class="add_projeto" >
                    	<tr> <td> <br> </tr> </td>
                		<tr> <td> <input class="blue_button" type="submit" value="Cadastrar" name="send" id="send" />  </td> </tr>
                    </table>
                </td>
            </tr>
            
         </table>
	</form>
	</div>   

</body>

</html>