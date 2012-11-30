<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Documento sem t&iacute;tulo</title>
</head>

<body>	
    	<!-- invisivel inline form -->
        <div id="inline">
        <h2> Editar Quadro Kanban </h2> <br />
        
    <?php	
		
		// conectar ao banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or
			die('Erro ao conectar ao BD!');
			
		mysqli_select_db($dbc, "easykanban-bd")
			or die ('Erro ao selecionar o Banco de Dados');
				
		// seleciona o limite de tarefa de cada uma das colunas do quadro kanban
		$query = 'SELECT s.`sit_id`, l.`lin_limite`
					FROM `limite_tarefa` l
					JOIN `projeto` p on p.`pro_id` = l.`pro_id`
					JOIN `situacao` s on s.`sit_id` = l.`sit_id`
					WHERE p.`pro_id` = %s'
						or die ("Erro ao construir a consulta");

		// alimenta os parametros da conculta
		$query = sprintf($query, $pro_id );	
		
		// executa consulta
		$data = mysqli_query($dbc, $query);

		// fecha conexão com o bd
		mysqli_close($dbc);
	
     ?>
     
        <form id="contact" name="contact" method="post" action="editar_quadro.php<?php echo '?pro_id=', $pro_id, '&tip_id=', $permissao; ?>" >
             <table class="add_projeto" >
                <tr>
                    <td>
                        <table class="add_projeto">
                            <tr>
                                <td> <label class="negrito" >Backlog:</label> </td>
                                <td> <label class="negrito" >Requisitado:</label> </td>
                                <td> <label class="negrito" >Em Processo:</label> </td>
								<td> <label class="negrito" >Concluido:</label> </td>
								<td> <label class="negrito" >Arquivado:</label> </td>
                            </tr>
                            <tr>
								<?php
									while ( $row = mysqli_fetch_array($data) ) {
										echo '<td> <input type="text" id="coluna', $row['sit_id'], '" name="coluna', $row['sit_id'], '"  value="', $row['lin_limite'], '" required> </td>';
									}
								?>
                        </table>
                   </td>
                </tr>
                
                <tr>
                    <td>
                        <table class="add_projeto">
                            <tr>
                            <td> <input class="blue_button" type="submit" value="Editar" name="edit" id="edit" /> </td>
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