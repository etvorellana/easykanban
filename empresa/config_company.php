<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if ( isset($_SESSION['usu_id']) and isset($_GET['emp_id']) )
	{
		$usu_id = $_SESSION['usu_id'];
		$emp_id = $_GET['emp_id'];
	}
	else
		echo 'Sessão Indefinida';
	
?>

<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"><head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>easykanban</title>

    <link rel="stylesheet" type="text/css" media="all" href="../css/formulario.css">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="../css/main.css" />
    <link rel="stylesheet" type="text/css" href="../css/config_company.css" />
  
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
        
    </div>
    
    <div id="main">
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
					" WHERE ue.usu_id =" . $usu_id 
					or die('Erro ao construir a consulta');
			
			// executa consulta
			$data = mysqli_query($dbc, $query);
			$row = mysqli_num_rows($data);
			
			while ($row = mysqli_fetch_array($data)) 
			{
				echo '<div id="menu_perfil">';
			
				echo '<table>';
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
					echo '<a id="config_button" href="config_company.php?emp_id=' . $row['emp_id'] . ' " class="gray_button">Editar Configurações</a>';
				echo '</div>';
				
				echo '</div>';
			}
		
			mysqli_close($dbc);
		}
?>
        
        <div id="colaboradores" class="info" >
            <strong class="label_titulo" > Colaboradores </strong> 
            <div class="css_colaboradores_usuarios">
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
						 " WHERE e.emp_id =" . $emp_id .
						 " AND ( ue.usu_id = u.usu_id )" .
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
				echo '</form>';
				
				mysqli_close($dbc);
			}
?>    
            </div>
            
        </div>
        
        <div id="usuarios" class="info">   
            <strong class="label_titulo" > Usuários </strong>	
            <div = class="css_colaboradores_usuarios">
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
						 " WHERE e.emp_id =" . $emp_id .
						 " AND NOT ( ue.usu_id = u.usu_id )" .
						 " AND ( e.emp_id = ue.emp_id )"
						 or die ('Erro ao construir a consulta');
					
				// executa consulta
				$data = mysqli_query($dbc, $query) or die ('Erro ao execultar consulta');
				//*$row = mysqli_num_rows($data); */
				
				 
				echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?>" >';
				 
				echo '<table class="tabela_zebrada" width="100%">'; 
				 
				while ($row = mysqli_fetch_array($data)) 
				{
					echo '<tr> ';
						echo '<td width="90%">';
					    echo( $row['usu_nome'] . '<br>' );
						echo '</td>';
						
						echo '<td width="10%">';
							echo '<a href="inserir_remover_usuario_empresa.php?usu_id=' . $row['usu_id'] . "&emp_id=" . $emp_id . '&haction=inserir"> <img src="../images/add.png" title="Inserir"/> </a>';
						echo '</td>';
						
					echo '</tr>';		
				}
				echo '</table>';
				
				echo '</form>';
				
				mysqli_close($dbc);
			}
?>    
            </div>
        </div>
        
		<div id="dialog-modal" title="Inserir Colaboradores">

		</div>  
    </div>
    
</body>
</html>




