<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	$usu_id =  $_SESSION['usu_id'];
	
	if ( isset($_SESSION['usu_id']) )
	{
		if ( isset($_POST['add_colaborador']) and isset($_POST['checkbox_add_user']) )
		{
			$campo = $_POST['checkbox_add_user']; 

			foreach($campo as $value){
				echo $value.'<br />';
			}
		}
	}
	
	// Quando o usuário submeter os dados de cadastro de nova empresa
	if (isset($_POST['send'])) 
	{
		// conectar ao banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
		die('Erro ao conectar ao BD!');
		
		// recupera os dados digitados no formulário
		$nome = trim ($_POST['nome']);	
		$email = trim($_POST['email']);
		$telefone = trim($_POST['telefone']);
		$cnpj = trim($_POST['cnpj']);
		$cep = trim ($_POST['cep']);	
		$cidade = trim($_POST['cidade']);
		$bairro = trim($_POST['bairro']);
		$uf = trim($_POST['uf']);
		$endereco = trim($_POST['endereco']);
		$numero = trim($_POST['numero']);
		
		if ( !empty($nome) && !empty($email) && !empty($telefone) )
		{
			// primeiro inserimos na tabela cep
			$query = "INSERT INTO cep ( cep_numero, cep_rua, cep_bairro, cep_cidade, cep_uf ) VALUES ('$cep', '$endereco', '$bairro', '$cidade', '$uf' )"
			or die ('Erro ao contruir a consulta');
			
			//execulta query de inserção na tabela cep
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inserção na tabela cep');
			
			// recupera o id do cep inserido e insere na tabela endereco
			$ultimo_id_cep = mysqli_insert_id($dbc);
			
			// contrução da query de inserção na tabela endereço
			$query = "INSERT INTO endereco ( cep_id, end_numero, end_complemento ) VALUES ( '$ultimo_id_cep', '$numero', 'NULL' )";
			
			// execulta query de inserção na tabela endereco
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inserção na tabela endereço');
				
			// recupera o id do endereco inserido e insere na tabela empresa
			$ultimo_id_end = mysqli_insert_id($dbc);
			
			// contrução da query de inserção na empresa
			$query = "INSERT INTO empresa ( end_id, emp_nome, emp_email, emp_tel, emp_descricao, emp_cnpj, emp_logo	) VALUES ( '$ultimo_id_end', '$nome', '$email', '$telefone', 'NULL', '$cnpj', 'NULL' )" ;
			
			// execulta query de inserção na tabela empresa
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inserção na tabela empresa');
				
			// recupera o id da empresa inserida e insere na tabela usuario_emrpesa
			$ultimo_id_emp = mysqli_insert_id($dbc);
			
			$query = "INSERT INTO usuario_empresa ( usu_id, emp_id ) VALUES ( '$usu_id', '$ultimo_id_emp' )"
			or die ('Erro ao criar a consulta');
			
			// execulta query de inserção na tabela usuario_empresa
			$data = mysqli_query($dbc, $query)
				or die('Erro ao execultar a inserção na tabela usuario_empresa');

		}
			
		/* Fecha conexão com o banco */
		mysqli_close($dbc);
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
    <link rel="stylesheet" type="text/css" href="../css/company.css" />
  
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
        <li><a href="empresa.php">Empresas</a></li>
        <li><a href="#">Relatórios</a></li>
        <li><a href="#">Configurações</a></li>
        </ul>
        
        <div id="nova-tarefa" >
            <a id="bug" class="modalbox" href="#inline"> 
                <input class="orange_button" type="submit" value="+ Nova Empresa" > 
            </a>
        </div>
        
      <br style="clear:left"/>
    </div>
    
    <div id="main">
    	<div id="menu_busca">
        	<input type="text" name="busca" id="busca" placeholder="Buscar Empresas" />
        </div>
    	
<?php
		// se a sessão for válida
		if (isset($_SESSION['usu_id'])) 
		{
			// conectar ao banco de dados
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
				die('Erro ao conectar ao BD!');
			
			
			$query =  "SELECT e.emp_id, e.end_id, e.emp_nome, e.emp_email, e.emp_tel, e.emp_descricao, e.emp_cnpj, c.cep_numero, c.cep_rua, c.cep_bairro, c.cep_cidade, c.cep_uf, ed.end_numero" .
					" FROM empresa e" .
					" JOIN endereco ed on ed.end_id = e.end_id"  .
					" JOIN cep c on c.cep_id = ed.cep_id" .
					" JOIN usuario_empresa ue ON ue.emp_id = e.emp_id" .
					" JOIN usuario u on u.usu_id = ue.usu_id" .
					" WHERE ue.usu_id = '$usu_id'"
					or die('Erro ao construir a consulta');
			
			// executa consulta
			$data = mysqli_query($dbc, $query);
			$row = mysqli_num_rows($data);
			
			while ($row = mysqli_fetch_array($data)) 
			{
				echo '<div id="menu_perfil">';
			
				echo '<table id="dados">';
				echo '<tr>';
				
				echo '<td rowspan="6">';
				
				if (empty($row['usu_foto'])) {
					echo '<img src="../nopic.jpg" alt="Profile Picture" />';
				}
				
				echo '</td>';
				echo '</tr>';
				
				echo '<tr> <td> <strong class="nome_titulo">'; echo( $row['emp_nome'] ); echo '</strong> </td> </tr>';
				echo '<tr> <td> <strong> Cnpj: </strong>';     echo( $row['emp_cnpj'] ); echo '</td> </tr>';
				
				echo '<tr> <td> <strong> Endereço: </strong>'; echo( $row['cep_rua'] . ' ' . $row['end_numero'] . ', ' . $row['cep_bairro'] . ', 	' . $row['cep_cidade'] . '-' . $row['cep_uf'] ); echo '</td> </tr>'; 
				  
				echo '<tr> <td> <strong> Telefone: </strong>';  echo( $row['emp_tel'] );   echo '</td> </tr>';
				echo '<tr> <td> <strong> E-mail:   </strong>';  echo( $row['emp_email'] ); echo '</td> </tr>';
				
			
				echo '</table>';
				
				echo '<div id="botoes_empresa">';
					echo '<a href="../projetos/projeto.php?emp_id=' . $row['emp_id'] . ' " class="gray_button">Entrar</a>';
					echo '<a id="config_button" href="#" class="gray_button">Configurações</a>';
				echo '</div>';
				
				echo '</div>';
			}
		
			mysqli_close($dbc);
		}
?>

    	<p id="fim_da_lista"> Não existem mais empresas cadastradas </p>
        
		<div id="dialog-modal" title="Inserir Colaboradores">
<?php	
			// se a sessão do usuário estiver devidamente definida
			if ( isset($_SESSION['usu_id']) ) 
			{	
				// conecta ao banco de dados
				$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
					die('Erro ao conectar ao BD!');
				
				// constroi a query	
				$query = "SELECT DISTINCT u.usu_id, u.usu_nome " .
						 " FROM usuario AS u, empresa AS e, usuario_empresa ue" .
						 " WHERE e.emp_id = 3" .
						 " AND NOT ( ue.usu_id = u.usu_id )" .
						 " AND ( e.emp_id = ue.emp_id )"
						 or die ('Erro ao construir a consulta');
					
					
				// executa consulta
				$data = mysqli_query($dbc, $query) or die ('Erro ao execultar consulta');
				//*$row = mysqli_num_rows($data); */
				
				 
				echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?>" >';
				 
				while ($row = mysqli_fetch_array($data)) 
				{
					echo '<table>';
					echo '<tr> <td> <input type="checkbox" name="checkbox_add_user[]" id="checkbox_add_user" value="'; echo($row['usu_id']); echo'" />'; echo( " " . $row['usu_nome']); echo '<br></td> </tr>';		
					echo '</table>';
					
				}
       
				echo '<input class="orange_button" type="submit" value="Adicionar" name="add_colaborador" id="add_colaborador" />';
				echo '</form>';
				
				mysqli_close($dbc);
			}
?>    
		</div>  
    </div>
    
    
	<!-- invisivel inline form -->
	<div id="inline">
	<h2> Adicionar nova Empresa </h2> <br />
    
	<form id="contact" name="contact" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
            <table class="add_empresa" >
         	
            <tr>
                <td>
                    <table class="add_empresa">
                        <tr> <td>  <label for="nome" class="negrito">Nome:</label> </td> </tr>
                        <tr> <td>  <input type="text" id="nome" name="nome" placeholder="Ex: .CON Computer on Network" required>  </td> </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
            	<td>
                    <table width="375" class="add_empresa">
                        <tr>
                            <td>  <label class="negrito" >Endereço de e-mail:</label> </td>
                            <td>  <label class="negrito" >Confirmar e-mail:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="email" id="email" name="email" placeholder="Ex: con@computer.com" required> </td>
                            <td> <input type="email" id="confirmar_email" name="confirmar_email" required oninput="check_email(this)"> </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_empresa">
                        <tr>
                            <td> <label class="negrito" >Telefone:</label> </td>
                            <td> <label class="negrito" >CNPJ:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="text" id="telefone" name="telefone" placeholder="(73)32589874" required> </td>
                            <td> <input type="text" id="cnpj" name="cnpj" placeholder="Ex: XX.XXX.XXX/XXXX-XX" > </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="add_empresa">
                        <tr>
                            <td width="18%"> <label class="negrito" >CEP:</label> </td>
                            <td width="64%"> <label class="negrito" >Cidade:</label> </td>
                            <td width="18%"> <label class="negrito" >UF:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="text" id="cep" name="cep" placeholder="Ex: XXXXX-XXX" required> </td>
                            <td> <input type="text" id="cidade" name="cidade" placeholder="Ex: Almadina" required > </td>
                            <td> <input type="text" id="uf" name="uf" placeholder="Ex: BA" required > </td>
                        </tr>
                        
                    </table>
               </td>
            </tr>
            
            <tr>
                <td>
                    <table class="add_empresa">
                        <tr>
                            <td width="55%"> <label class="negrito" >Endereço:</label> </td>
                            <td width="10%"> <label class="negrito" >Número:</label> </td>
                            <td width="35%"> <label class="negrito" >Bairro:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="text" id="endereco" name="endereco" placeholder="Ex: Rua Durval Oliveira" required > </td>
                            <td> <input type="text" id="numero" name="numero" placeholder="Ex: 45" required > </td>
                             <td> <input type="text" id="bairro" name="bairro" placeholder="Ex: Centro" required > </td>
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
				$(this).before("<p><strong>Empresa cadastrada com Sucesso!</strong></p>"); // exibe mensagem de confirmação para o usuário
				setTimeout("$.fancybox.close()", 1000); // fecha caixa de dialogo
			});
		});

	});
</script>
    
</body>
</html>




