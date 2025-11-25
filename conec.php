conec.php
<?php 
// Dados de conexão ao banco de dados 
//PASTA DO PROJETO DENTRO DE c:\XAMPP\HTDOCS 
$host = 'localhost'; 
$dbname = 'escola'; 
$username = 'root'; 
$password = ''; 
// Tentativa de conexão 
try { 
    // Criação da instância do PDO 
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password); 
    
    } 
    catch (PDOException $e) { 
        // Em caso de erro na conexão, exibir mensagem de erro 
        die('Erro ao conectar: '.$e->getMessage()); 
    } 
  
    ?>
   
   
