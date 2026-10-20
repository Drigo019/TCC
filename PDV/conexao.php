<?php

$conn = new mysqli("localhost", "root", "", "containerdoqueijo", 3308);

if ($conn->connect_error){
    die("Erro: " . $conn->connect_error);
}

?>