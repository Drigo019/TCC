<?php
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    if ( $email == "rcarvalho15022009@gmail.com" and $senha = "Drigo")
        {
            echo "Login correto";
        }
    else 
        {
            echo "Login incorreto";
        }
?>