<?php
    require("conexao.php");

    if ($_SERVER["REQUEST_METHOD"] === "POST") 
        {
            $nome = $_POST["nome"];
            $id_usuario = $_POST["id_usuario"];
            $email = $_POST["email"];
            $senha = $_POST["senha"];
            $ativo = "ativo";

            $query = "SELECT * FROM usuario WHERE $id_usuario = ? AND $email = ? AND $senha = ?";

            var_dump($pdo);
            $stm = $pdo -> prepare($query);
            $stm -> bindValue(1, $nome);
            $stm -> bindValue(2, $email);
            $stm -> bindValue(3, $senha);
            $stm -> bindValue(4, $id_usuario);


            if ($stm -> execute()) 
                {
                    $res = $stm -> fetch(PDO::FETCH_ASSOC);
                    $rlog  = "logado";
                    $ruser_id = $res['id_usuario'];
                    $ruser_name = $res['user_name'];
                    $ruser_email = $res['user_email'];
                    $ruser_senha = $res['user_senha'];
                    $rmens = 'Usuário logado com sucesso'; 
                }
            else 
                {
                    $rlog = "notloged";
                    $user_id = null;
                    $user_name = "notname";
                    $ruser_email = "notemail";
                    $ruser_senha = "notsenha";    
                    $rmens = "Não foi possivel conclir o cadastro";
                }

        }
    header("Location: Tela_Login.html?criado=sucesso");