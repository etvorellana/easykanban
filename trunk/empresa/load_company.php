<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Documento sem título</title>
</head>

<body>
<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	$usu_id =  $_SESSION['usu_id'];
	
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

		while ($row = mysqli_fetch_array($data)) {
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
				echo '<a href="#" class="gray_button">Entrar</a>';
				echo '<a id="config_button" href="#" class="gray_button">Configurações</a>';
			echo '</div>';
			
			echo '</div>';
		}
	
	mysqli_close($dbc);
	}
?>

</body>
</html>