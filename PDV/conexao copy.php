<?php

$conn = new mysqli("localhost", "root", " ", "pdv");

if ($conn->connect_error){
    die("Erro: " . $conn->connect_error);
}

?>