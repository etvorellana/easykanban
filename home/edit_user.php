<?php
	require_once('../connect/connect_vars.php');
	require_once('../sessao_php/inicia_sessao.php');
	
	$usu_id =  $_SESSION['usu_id'];

	// conectar ao banco de dados
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
		die('Erro ao conectar ao BD!');
	
	
	// consulta que retorna todos os dados do usuário logado no sistema
	$query = 'SELECT u.usu_id, tu.tip_descricao, u.usu_nome, u.usu_email, u.usu_senha, u.usu_dt_cadastro, u.usu_foto
			  FROM usuario u
			  NATURAL JOIN tipo_usuario tu
			  WHERE usu_id =%s'
			 or die ('Erro ao construir a query');
	
	// alimenta os parametros da conculta
	$query = sprintf($query, $usu_id ); 	
	
	
	// executa consulta
	$data = mysqli_query($dbc, $query);
	$row = mysqli_num_rows($data);
	
	// verifica se foi retornado apenas um registro do banco
	if ( $row == 1) 
	{
		// captura os dadas deste registro
		$row = mysqli_fetch_array($data);
		
		if ( $row != NULL ) 
		{
			// recupera os dados
			$usu_nome = $row['usu_nome'];
			$usu_tipo = $row['tip_descricao'];
			$usu_email = $row['usu_email'];
		}
	}

	/* Fecha conexão com o banco */
	mysqli_close($dbc);
	
	// Quando o usuário submeter os dados de cadastro de novo usuario
	if (isset($_POST['edit'])) 
	{
		// conectar ao banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
		die('Erro ao conectar ao BD!');
		
		$nome = trim ($_POST['nome']);	
		$email = trim($_POST['email']);
		$senha = trim($_POST['senha']);
		$tipo = trim($_POST['tipo']);
		$data_hora = date("d/m/Y h:i:s");
		
		if ( !empty($nome) && !empty($email) && !empty($senha) && !empty($tipo) )
		{
			$query = "UPDATE INTO usuario (tip_id, usu_nome, usu_email, usu_senha, usu_dt_cadastro ) VALUES ('$tipo', '$nome', '$email', SHA('$senha'), '$data_hora' )" or 
				die ('Erro ao contruir a consulta');
			
			$result = mysqli_query($dbc, $query)
				or die('Erro ao execultar a consulta');
		}
			
	/* Fecha conexão com o banco */
	mysqli_close($dbc);
	}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Documento sem título</title>
</head>
    <div id="formulario">
	<form id="contact" name="contact" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
         <table class="border_space" >
            <tr>
            	<td>
                	<table class="border_space" >
                    	<tr> <td>  <label for="nome" class="negrito">Nome completo:</label> </td> </tr>
                    	<tr> <td>  <input type="text" id="nome" name="nome" placeholder="Ex: Thalles Santos Silva" required>  </td> </tr>
                    </table>
                </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="border_space">
                        <tr>
                            <td>  <label class="negrito" >Endereço de e-mail:</label> </td>
                            <td>  <label </label> </td>
                            <td>  <label class="negrito" >Confirmar e-mail:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="email" id="email" name="email" placeholder="Ex: thalles@easykanban.com" required> </td>
                            <td>  <label </label> </td>
                            <td> <input type="email" id="confirmar_email" name="confirmar_email" required oninput="check_email(this)"> </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
            <tr>
            	<td>
                    <table class="border_space">
                        <tr>
                            <td> <label class="negrito" >Senha:</label> </td>
                            <td>  <label </label> </td>
                            <td> <label class="negrito" >Confirmar Senha:</label> </td>
                        </tr>
                        <tr>
                            <td> <input type="password" id="senha" name="senha" placeholder="" required> </td>
                            <td>  <label </label> </td>
                            <td> <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="" oninput="check_senha(this)" required> </td>
                        </tr>
                    </table>
               </td>
            </tr>
            
         	<tr> <td>  <label class="negrito" >Tipo:</label> </td> </tr>
            
            <tr>
            	<td>
                    <table class="border_space">
                        <tr>
                            <td> 
                           		Administrador: <input name="tipo" type="radio" value="1" required />
                            </td>
                           	
                            <td>
                            	Colaborador: <input name="tipo" type="radio" value="2" required />
                            </td>
                        </tr>
                    </table>
                    <br />	
               </td>
            </tr>
            
            <tr>
            <td> <input class="blue_button" type="submit" value="Cadastrar" name="send" id="send" /> </td>
            </tr>
            
         </table>
	</form>
    </div>
</html>