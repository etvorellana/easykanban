<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	if ( isset($_SESSION['usu_id']) and isset($_GET['pro_id']) )
	{
		$usu_id = $_SESSION['usu_id'];
		$usu_nome = $_SESSION['usu_nome'];
		$pro_id = $_GET['pro_id'];

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
        	<label> <?php echo ( $usu_nome ) ?> </label>
    	</div>
    	
        <div id="acessiobilidade" >
        	<label > <a class="menu_acesso_rapido" href="../logout.php"> logout </a> </label>
        </div>
    </header>
    </div>
    
	<div id="container-menu">
        <ul>
        <li><a href="../home/home.php">Home</a></li>
        <li><a href="empresa.php">Empresas</a></li>
        <li><a href="#">Relatórios</a></li>
        <li><a href="#">Configurações</a></li>
        </ul>
        
    </div>
    
    <div id="main">
	<?php
        // conectar ao banco de dados
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
            die('Erro ao conectar ao BD!');
                
        $query = 'SELECT p.pro_id, ts.tip_situacao, p.pro_nome, p.pro_descricao, p.pro_dt_inicio, p.pro_dt_fim, p.pro_dt_criacao
                  FROM projeto p
                  JOIN usuario_projeto up on up.pro_id = p.pro_id
                  JOIN usuario u on u.usu_id = up.usu_id 
                  JOIN tipo_situacao ts on ts.tip_id = p.tip_id
                  WHERE u.usu_id=%s AND p.pro_id=%s'
                 or die ('Erro ao contruir a consulta');
                 
        // alimenta os parametros da conculta
        $query = sprintf($query, $usu_id, $pro_id ); 	
        
        // executa consulta
        $data = mysqli_query($dbc, $query);
        
        // executa consulta
        $data = mysqli_query($dbc, $query) or die ('Erro ao execultar consulta');
    
        while ($row = mysqli_fetch_array($data)) 
        {
            echo '<div class="projeto_info_hover" id="menu_perfil">';
            echo '<table width="100%" class="border_space">';
            
            
            echo '<tr> <td> <strong class="nome_titulo">'; echo( $row['pro_nome'] ); echo '</strong> </td> </tr>';
            echo '<tr> <td>  <strong> Descrição: </strong>';     echo( $row['pro_descricao'] ); echo '</td> </tr>';
              
            echo '<tr> <td width="60%"> <strong> Data de Início: </strong>';  echo( $row['pro_dt_inicio'] );   echo '</td>';
            echo '<td> <strong> Previsão de Término:   </strong>';  echo( $row['pro_dt_fim'] ); echo '</td> </tr>';
            
            echo '<tr> <td> <strong> Situação do Projeto: </strong>';     echo( $row['tip_situacao'] ); echo '</td> </tr>';
        
            echo '</table>';
            
            echo '<div id="botoes_projeto">';
                echo '<a href="../quadro_kanban/quadro.php?pro_id=' , $row['pro_id'] , ' " class="gray_button">Editar Projeto</a>';
            echo '</div>';
            
            echo '</div>';
        }
        mysqli_close($dbc);
    ?>
        
        <div id="colaboradores" class="info" >
            <strong class="label_titulo" > Colaboradores não vinculados ao Projeto </strong> 
            
            <div class="css_colaboradores_usuarios">
<?php	
			// se a sessão do usuário estiver devidamente definida
			if ( isset($_SESSION['usu_id']) ) 
			{	
				// conecta ao banco de dados
				$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
					die('Erro ao conectar ao BD!');
				
				// selecioma todos os usuários que não estão ligados ao projeto selecionado	
				$query = 'SELECT u.usu_id, u.usu_nome
						FROM usuario u 
						JOIN usuario_projeto up on NOT (up.usu_id = u.usu_id)  
						JOIN projeto p on p.pro_id = up.pro_id 
						WHERE p.pro_id=%s'
				or die ('Erro ao construir a consulta');
			
				// alimenta os parametros da conculta
				$query = sprintf($query, $pro_id );	
				
				// executa consulta
				$data = mysqli_query($dbc, $query) or die ('Erro ao executar consulta');
				 
				echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?>" >';
				 
				echo '<table class="tabela_zebrada" width="100%">'; 
				 
				while ($row = mysqli_fetch_array($data)) 
				{
					echo '<tr> ';
						echo '<td width="90%">';
					    echo( $row['usu_nome'] . '<br>' );
						echo '</td>';
						
						echo '<td width="10%">';
							echo '<a href="inserir_remover_usuario_projeto.php?insert_user=' . $row['usu_id'] . "&pro_id=" . $pro_id . '&action=inserir"> <img src="../images/add.png" title="Inserir"/> </a>';
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
				
				$query = 'SELECT u.usu_id, u.usu_nome
						FROM usuario u
						jOIN usuario_projeto up on up.usu_id = u.usu_id
						JOIN projeto p on p.pro_id = up.pro_id
						WHERE p.pro_id = %s'
					     or die ("Erro ao construir a consulta");
			
				// alimenta os parametros da conculta
				$query = sprintf($query, $pro_id );	
				
				// executa consulta
				$data = mysqli_query($dbc, $query) or die ('Erro ao executar consulta');
				 
				echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?>" >';
				echo '<table>';
				
				while ($row = mysqli_fetch_array($data)) 
				{
					echo '<tr> <td> <input type="checkbox" name="checkbox_add_user[]" id="checkbox_add_user" value="'; echo($row['usu_id']); echo'" />'; echo( " " . $row['usu_nome']); echo '<br></td> </tr>';		
					
				}
				echo '</table>';
				echo '</form>';
				
				mysqli_close($dbc);
			}
?>                
            </div>
        </div>
        
		<div id="inserir_colaborador">
			<tr> <td> <input class="blue_button" type="submit" value="Adiconar ao Projeto" name="send" id="send" />  </td> </tr>
		</div>  
    </div>
    
</body>
</html>

<?php
	}
?>



