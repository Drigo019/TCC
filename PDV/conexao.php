<?php

$conn = new mysqli("localhost", "root", "", "containerdoqueijo");

if ($conn->connect_error){
    die("Erro: " . $conn->connect_error);
}

?>