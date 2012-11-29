<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Documento sem t&iacute;tulo</title>
</head>

<body>	
    	<!-- invisivel inline form -->
        <div id="inline">
        <h2> Editar Projeto </h2> <br />
        
    <?php	
	
		echo  $tar_id_edit;
		
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
                                <tr> <td>  <label for="descricao" class="negrito">Descri&ccedil;&atilde;o:</label> </td> </tr>
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
                                <td> <label class="negrito" >Conclus&atilde;o:</label> </td>
                                <td> <label class="negrito" >Situa&ccedil;&atilde;o:</label> </td>
                            </tr>
                            <tr>
                                <td> <input type="date" disabled="disabled" id="data_inicio" name="data_inicio" value="<?php echo($row['pro_dt_inicio']);?>" required> </td>
                                <td> <input type="date" id="data_fim" name="data_fim" value="<?php echo($row['pro_dt_fim']);?>" required> </td>
                                <td>
                                <select id="tipo_situacao" name="tipo_situacao" required>
                                    <option value="1">Em andamento</option>
                                    <option value="2">Conclu&iacute;do </option>
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