<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if ( isset($_SESSION['usu_id']) and isset($_GET['pro_id']) )
	{
		$usu_id = $_SESSION['usu_id'];
		$usu_nome = $_SESSION['usu_nome'];
		$pro_id = $_GET['pro_id'];
		
				// Quando o usu�rio submeter os dados de cadastro de nova empresa
		if (isset($_POST['edit'])) 
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
			// recupera os dados digitados no formul�rio
			$pro_nome = trim ($_POST['nome']);	
			$pro_descri��o = trim($_POST['descricao']);
			$data_fim = trim($_POST['data_fim']);
			$tip_situacao = trim ($_POST['tipo_situacao']);	

			if ( !empty($pro_nome) && !empty($pro_descri��o) && !empty($data_fim) && !empty($tip_situacao) )
			{
				$por_usu_criador = $usu_id;
				
				// criando query de inser��o na tabela projeto
				$query = "UPDATE projeto SET
						  tip_id = '$tip_situacao',
			              pro_nome = '$pro_nome',
			              pro_descricao= '$pro_descri��o',
			              pro_dt_fim = '$data_fim'
						  WHERE pro_id = '$pro_id' "
				or die ('Erro ao contruir a consulta');
				
				// alimenta os parametros da conculta
				$query = sprintf($query, $tip_situacao, $pro_nome, $pro_descri��o, $data_fim, $pro_id );	
				
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
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>easykanban</title>

    <link rel="stylesheet" type="text/css" media="all" href="../css/formulario.css">
    <link rel="stylesheet" type="text/css" media="all" href="../fancybox/jquery.fancybox.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="../fancybox/jquery.fancybox.js?v=2.0.6"></script>
    
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link rel="stylesheet" type="text/css" href="../css/config_company.css" />
  	
    <script type="text/javascript" src="../js/table_row.js"></script>
    
	</head> 

  	<body>
    
	<div id="container-cabecalho">
    <header>
		<div id="nome_usuario" class="menu_acesso_rapido">
        	<label> <?php echo ( $usu_nome ) ?> </label>
    	</div>
    	
        <div id="logout" class="config_logout">
        	<label > <a class="menu_acesso_rapido" href="../logout.php"> logout </a> </label>
        </div>
    </header>
    </div>
    
	<div id="container-menu">
    <ul>
        <li><a href="../home/home.php">Home</a></li>
        <li class="atual"><a href="projeto.php">Projetos</a></li>
        <li><a href="#">Relat�rios</a></li>
        <li><a href="#">Configura��es</a></li>
    </ul>
        
    </div>
    
    <div id="main">
<?php
        // conectar ao banco de dados
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
            die('Erro ao conectar ao BD!');
                
        $query = 'SELECT p.pro_id, ts.tip_situacao, p.pro_nome, p.pro_descricao, p.pro_dt_inicio, p.pro_dt_fim, p.pro_dt_criacao, p.pro_usu_criador, up.tip_id
                  FROM projeto p
                  JOIN usuario_projeto_tipo up on up.pro_id = p.pro_id
                  JOIN usuario u on u.usu_id = up.usu_id 
                  JOIN tipo_situacao ts on ts.tip_id = p.tip_id
                  WHERE u.usu_id=%s AND p.pro_id=%s'
                 or die ('Erro ao contruir a consulta');
                 
        // alimenta os parametros da conculta
        $query = sprintf($query, $usu_id, $pro_id ); 	
       
        // executa consulta
        $data = mysqli_query($dbc, $query) or die ('Erro ao execultar consulta');
    
        while ($row = mysqli_fetch_array($data)) 
        {
            echo '<div class="projeto_info_hover" id="menu_perfil">
            	  <table width="100%" class="border_space">';
            
            
            echo '<tr> <td> <strong class="nome_titulo">', $row['pro_nome'], '</strong> </td> </tr>
            	  <tr> <td>  <strong> Descri��o: </strong>', $row['pro_descricao'], '</td> </tr>';
              
            echo '<tr> <td width="60%"> <strong> Data de In�cio: </strong>', $row['pro_dt_inicio'], '</td>
                  <td> <strong> Previs�o de T�rmino:   </strong>', $row['pro_dt_fim'], '</td> </tr>';
            
			echo '<tr> <td> <strong> Situa��o do Projeto: </strong>', $row['tip_situacao'], '</td>
            <td> <strong> Sua Fun��o: </strong>'; if ( $row['pro_usu_criador'] == $usu_id ) echo'Criador/Administradar'; else if ($row['tip_id'] == 1 ) echo'Administrador'; else echo'Colaborador'; echo '</td> </tr>';
        
            echo '</table>
            
                  <div id="botoes_projeto">
                  	<a id="botao_editar" class="modalbox" href="#inline" > Editar Projeto </a>
            	  </div>
            
            </div>';
        }
        mysqli_close($dbc);
 ?>
        
        <div id="colaboradores" class="info" >
            <strong class="label_titulo" > Colaboradores n�o vinculados ao Projeto </strong> 
            
            <div class="css_colaboradores_usuarios">
<?php	
			// se a sess�o do usu�rio estiver devidamente definida
			if ( isset($_SESSION['usu_id']) ) 
			{	
				// conecta ao banco de dados
				$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
					die('Erro ao conectar ao BD!');
				
				// selecioma todos os usu�rios que n�o est�o ligados ao projeto selecionado	
				$query = 'SELECT u.usu_id, u.usu_nome
						  FROM usuario u
						  WHERE u.usu_id NOT 
						  IN ( SELECT u.usu_id
						  FROM usuario u
						  JOIN usuario_projeto_tipo upt ON upt.usu_id = u.usu_id
						  JOIN projeto p ON p.pro_id = upt.pro_id
						  WHERE p.pro_id = %s )'
							  or die ('Erro ao construir a consulta');
			
				// alimenta os parametros da conculta
				$query = sprintf($query, $pro_id );	
				
				// executa consulta
				$data = mysqli_query($dbc, $query) or die ('Erro ao executar consulta');
				 
				echo '<table class="tabela_zebrada" > 
				<thead>
				<tr>
					<th>Nome</th>
					<th>Inserir</th>
				</tr>
				</thead> ';
				
				while ($row = mysqli_fetch_array($data)) 
				{
					echo '<tr>
						  <td width="100%">',
					      $row['usu_nome'], '<br>
						  </td>
						
						  <td align="center" >
							  	<a href="inserir_remover_usuario_projeto.php?insert_user=' . $row['usu_id'] . "&pro_id=" . $pro_id . '&action=inserir"> <img class="images" src="../images/add.png" title="Inserir"/> </a>
						  </td>
						
					      </tr>';		
				}
				echo '</table>';
				
				mysqli_close($dbc);
			}
?>    
            </div>
            
        </div>
        
        <div id="usuarios" class="info">   
            <strong class="label_titulo" > Usu�rios </strong>	
            <div = class="css_colaboradores_usuarios">
<?php	
			// se a sess�o do usu�rio estiver devidamente definida
			if ( isset($_SESSION['usu_id']) ) 
			{	
				// conecta ao banco de dados
				$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
					die('Erro ao conectar ao BD!');
					
				$query = 'SELECT u.usu_id, u.usu_nome, up.tip_id
						  FROM usuario u
						  jOIN usuario_projeto_tipo up on up.usu_id = u.usu_id
						  JOIN projeto p on p.pro_id = up.pro_id
						  WHERE p.pro_id = %s'
					         or die ('Erro ao construir a consulta');
			
				// alimenta os parametros da conculta
				$query = sprintf($query, $pro_id );	
				
				// executa consulta
				$data = mysqli_query($dbc, $query) or die ('Erro ao executar consulta');
				 
				echo '<table class="tabela_zebrada" > 
				<thead>
				<tr>
					<th>Nome</th>
					<th>Admin.</th>
					<th>Remover</th>
				</tr>
				</thead> ';
				 
				while ($row = mysqli_fetch_array($data)) 
				{
					echo '<tr>
						  <td width="90%">',
					      $row['usu_nome'], '<br>
						  </td>';

						echo '<td align="center" >';
							echo '<a href="inserir_remover_usuario_projeto.php?edit_usu_id=', $row['usu_id'], "&pro_id=" . $pro_id, '&tip_id=' . $row['tip_id'] . '&action=changetype"> <img class="images" src="../images/'; if( $row['tip_id'] == ADMIN ) echo'checked.png'; else echo'unchecked.png'; echo '" /> </a>';
						echo '</td>';
						
						echo '<td align="center">
								<a href="inserir_remover_usuario_projeto.php?remove_user=', $row['usu_id'], "&pro_id=", $pro_id, '&action=remove"> <img src="../images/del.png" title="Inserir"/> </a>';
						echo '</td>
						
					      </tr>';		
				}
				echo '</table>';
				
				mysqli_close($dbc);
			}
?>    
           
            </div>
        </div>
    </div>
    
	<!-- invisivel inline form -->
	<div id="inline">
	<h2> Editar Projeto </h2> <br />
    
<?php
        // conectar ao banco de dados
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
            die('Erro ao conectar ao BD!');
                
        $query = 'SELECT p.pro_id, ts.tip_situacao, p.pro_nome, p.pro_descricao, p.pro_dt_inicio, p.pro_dt_fim, p.pro_dt_criacao, p.pro_usu_criador, up.tip_id
                  FROM projeto p
                  JOIN usuario_projeto_tipo up on up.pro_id = p.pro_id
                  JOIN usuario u on u.usu_id = up.usu_id 
                  JOIN tipo_situacao ts on ts.tip_id = p.tip_id
                  WHERE u.usu_id=%s AND p.pro_id=%s'
                 or die ('Erro ao contruir a consulta');
                 
        // alimenta os parametros da conculta
        $query = sprintf($query, $usu_id, $pro_id ); 	
        
        // executa consulta
        $data = mysqli_query($dbc, $query);
    
        $row = mysqli_fetch_array($data);
     
        mysqli_close($dbc);
 ?>
 
	<form id="contact" name="contact" method="post" action="<?php echo $_SERVER['PHP_SELF'], '?pro_id=', $pro_id; ?>" >
         <table class="add_projeto" >
         	
            <tr>
                <td>
                    <table class="add_projeto">
                        <tr> <td>  <label for="nome" class="negrito">Nome:</label> </td> </tr>
                        <tr> <td>  <input type="text" id="nome" name="nome" value="<?php echo($row['pro_nome']);?>" required>  </td> </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                       	 	<tr> <td>  <label for="descricao" class="negrito">Descri��o:</label> </td> </tr>
                        	<tr> <td>  <textarea id="descricao" rows="6" maxlength="250" name="descricao" required><?php echo $row['pro_descricao'] ?> </textarea>  </td> </tr>
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
                            <td> <input type="date" disabled="disabled" id="data_inicio" name="data_inicio" value="<?php echo($row['pro_dt_inicio']);?>" required> </td>
                            <td> <input type="date" id="data_fim" name="data_fim" value="<?php echo($row['pro_dt_fim']);?>" required> </td>
                            <td>
                            <select id="tipo_situacao" name="tipo_situacao" required>
                                <option value="1">Em andamento</option>
                                <option value="2">Conclu�do </option>
                                <option value="3">Parado</option>
                             </select>
                             </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_projeto">
                        <tr>
                        <td> <input class="blue_button" type="submit" value="Salvar" name="edit" id="edit" /> </td>
                        </tr>
                    </table>
               </td>
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



