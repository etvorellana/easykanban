
<h2> Adicionar Novo Usuário </h2><br />
<form id="editar_usuario_formulario" name="editar_usuario_formulario" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
<table class="border_space" >
<tr>
    <td>
        <table class="border_space" >
            <tr> <td>  <label for="nome" class="negrito">Nome completo:</label> </td> </tr>
            <tr> <td>  <input type="text" id="nome" name="nome" value="<?php echo $row['usu_nome'] ?>" required>  </td> </tr>
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
                <td> <input type="email" id="email" name="email" value="<?php echo $row['usu_email'] ?>" required> </td>
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
                <td> <input type="password" id="senha" name="senha" value="<?php echo $row['usu_nome'] ?>" required> </td>
                <td>  <label </label> </td>
                <td> <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirmar senha" onChange="return validarSenha();" required> </td>
            </tr>
        </table>
   </td>
</tr>

<tr>
    <td>
        <table class="border_space">
            <tr>
                <tr>
                <td> <input class="blue_button" type="submit" value="Cadastrar" name="send" id="send" /> </td>
                </tr>
            </tr>
        </table>
   </td>
</tr>
   
</table>
</form>

