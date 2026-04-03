<?php
    require("conexao.php");
    require("Tela_cadastro.html");

    if ($_SERVER["REQUEST_METHOD"] === "POST")
        {
            INSERT INTO usuario ('id_usuario', 'nome', 'email', 'senha', 'ativo') VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]')
        }
