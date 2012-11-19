<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if( isset($_SESSION['usu_id']) )
		$usu_id = $_SESSION['usu_id'];
	else
		printf("Voc� precisa realizar Login");
	
	if ( isset($_GET['emp_id']) )
		$emp_id = $_GET['emp_id'];
	else
		printf("N�o!");
		
		
	if (isset($_SESSION['usu_id'])) 
	{
		// Quando o usu�rio submeter os dados de cadastro de nova empresa
		if (isset($_POST['send'])) 
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			// recupera os dados digitados no formul�rio
			$nome = trim ($_POST['nome']);	
			$descri��o = trim($_POST['descricao']);
			$data_inicio = trim($_POST['data_inicio']);
			$data_fim = trim($_POST['data_fim']);
			$tip_situacao = trim ($_POST['tipo_situacao']);	
			
			echo "$nome";
			echo "$descri��o";
			echo "$data_inicio";
			echo "$data_fim";
			echo "$tip_situacao";
			
			if ( !empty($nome) && !empty($data_inicio) && !empty($data_fim) && !empty($tip_situacao) )
			{
				// criando query de inser��o na tabela projeto
				$query = "INSERT INTO projeto ( emp_id, tip_id, pro_nome, pro_descricao, pro_dt_inicio, pro_dt_fim ) VALUES ('$emp_id', '$tip_situacao', '$nome', '$descri��o', '$data_inicio', '$data_fim' )"
				or die ('Erro ao contruir a consulta');
				
				echo "$query";
				
				//execulta query de inser��o na tabela cep
				$data = mysqli_query($dbc, $query)
					or die('Erro ao execultar a inser��o na tabela projeto');
	
			}
				
			/* Fecha conex�o com o banco */
			mysqli_close($dbc);
		}
	}
	
?>

<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"><head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>easykanban</title>

    <link rel="stylesheet" type="text/css" media="all" href="../css/formulario.css">
    <link rel="stylesheet" type="text/css" media="all" href="../fancybox/jquery.fancybox.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.0.6"></script>
    
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link rel="stylesheet" type="text/css" href="../css/projeto.css" />
  
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
        <ul>
        <li><a href="#">Home</a></li>
        <li><a href="../empresa/empresa.php">Empresas</a></li>
        <li><a href="#">Relat�rios</a></li>
        <li><a href="#">Configura��es</a></li>
        </ul>
        
        <div id="nova-tarefa" >
            <a id="bug" class="modalbox" href="#inline"> 
                <input class="orange_button" type="submit" value="+ Novo Projeto" > 
            </a>
        </div>
        
    <br style="clear:left"/>
    </div>
    
    <div id="main">
    	<div id="menu_busca">
        	<input type="text" name="busca" id="busca" placeholder="Buscar Projetos" />
        </div>
    	
		<?php
		// se a sess�o estiver devidamente definida
		if (isset($_SESSION['usu_id'])) 
		{
			// conecta ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
				die('Erro ao conectar ao BD!');
				
			
			// Se a empresa foi selecionada, selecionamos todos os projetos pertencentes a devida empresa, se n�o selecinamos todos os projetos
			if ( isset($_GET['emp_id'] ) ){
				$query = "SELECT pro_id, emp_id, tip_id, pro_nome, pro_descricao, pro_dt_inicio, pro_dt_fim" .
						 " FROM projeto WHERE emp_id =" . $_GET['emp_id'] 
						 or die ('Erro ao contruir a consulta');
			}else{
				$query = "SELECT pro_id, emp_id, tip_id, pro_nome, pro_descricao, pro_dt_inicio, pro_dt_fim FROM" .
						 " projeto " 
						 or die ('Erro ao contruir a consulta');
			}
			
			
			// executa consulta
			$data = mysqli_query($dbc, $query) or die ('Erro ao execultar consulta');
			//*$row = mysqli_num_rows($data); */
		
			while ($row = mysqli_fetch_array($data)) 
			{
				echo '<div id="menu_perfil">';
				echo '<table id="dados">';
				
				echo '<tr> <td> <strong class="nome_titulo">'; echo( $row['pro_nome'] ); echo '</strong> </td> </tr>';
				echo '<tr> <td> <strong> Descri��o: </strong>';     echo( $row['pro_descricao'] ); echo '</td> </tr>';
				  
				echo '<tr> <td> <strong> Data de In�cio: </strong>';  echo( $row['pro_dt_inicio'] );   echo '</td>';
				echo '<td> <strong> Previs�o de T�rmino:   </strong>';  echo( $row['pro_dt_fim'] ); echo '</td> </tr>';
				
			
				echo '</table>';
				
				echo '<div id="botoes_empresa">';
					echo '<a href="../quadro_kanban/quadro.html?emp_id=' . $row['pro_id'] . ' " class="gray_button">Entrar</a>';
					echo '<a id="config_button" href="#" class="gray_button">Configura��es</a>';
				echo '</div>';
				
				echo '</div>';
			}
			mysqli_close($dbc);
		}
	?>
    
	<p id="fim_da_lista"> N�o existem mais projetos cadastradas </p>
        
    </div>
    
    
	<!-- invisivel inline form -->
	<div id="inline">
	<h2> Adicionar novo Projeto </h2> <br />
    
	<form id="contact" name="contact" method="post" action="<?php echo ( $_SERVER['PHP_SELF'] . "?emp_id=" . $emp_id ); ?>" >
         <table class="add_projeto" >
         	
            <tr>
                <td>
                    <table class="add_projeto">
                        <tr> <td>  <label for="nome" class="negrito">Nome:</label> </td> </tr>
                        <tr> <td>  <input type="text" id="nome" name="nome" placeholder="Ex: .Projeto de Contabilidade P�blica" required>  </td> </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                       	 	<tr> <td>  <label for="descricao" class="negrito">Descri��o:</label> </td> </tr>
                        	<tr> <td>  <textarea id="descricao" rows="6" maxlength="250" name="descricao" placeholder="Pequena descri��o sobre o Projeto" ></textarea>  </td> </tr>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                            <td> <label class="negrito" >Inicio:</label> </td>
                            <td> <label class="negrito" >Conclus�o:</label> </td>
                            <td> <label class="negrito" >Situa��o:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="date" id="data_inicio" name="data_inicio" required> </td>
                            <td> <input type="date" id="data_fim" name="data_fim" required> </td>
                            <td>
                            <select name="tipo_situacao" required>
                                <option value="1">Parado</option>
                                <option value="2">Em andamento</option>
                                <option value="3">Conclu�do</option>
                             </select>
                             </td>
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
		$(".modalbox").fancybox(); //inicia a caixa de dialogo com o formul�rio
		
		// fazer valida��o javascript
		//
		//
		
		sleep( 1000 );					// espera 115 minutos para o usu�rio poder ler a mensagem na tela		
		$("#contact").submit(function() {  // quando os dados forem submetidos...
			$("#contact").fadeOut("slow", function(){
				$(this).before("<p><strong>Projeto cadastrado com Sucesso!</strong></p>"); // exibe mensagem de confirma��o para o usu�rio
				setTimeout("$.fancybox.close()", 1000); // fecha caixa de dialogo
			});
		});

	});
</script>
    
  </body>
</html>




