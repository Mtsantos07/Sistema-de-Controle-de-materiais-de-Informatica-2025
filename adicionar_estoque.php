adicionar_estoque.php

<?php
include 'config.php';
session_start();
if(!isset($_SESSION['user_id'])) header("Location: login.php");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nome = $_POST['nome'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $quantidade = (int)($_POST['quantidade'] ?? 0);
    $local = $_POST['localizacao'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO estoque (nome,tipo,quantidade,localizacao) VALUES (?,?,?,?)");
    if($stmt->execute([$nome,$tipo,$quantidade,$local])){
        header("Location: painel_estoque.php");
        exit;
    } else {
        $erro = "Erro ao adicionar item.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Adicionar Item</title>
<style>
body{ font-family:Arial; margin:20px; background:#f0f2f5; }
form{ max-width:400px; margin:auto; display:flex; flex-direction:column; gap:10px; background:#fff; padding:20px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
input{ padding:10px; border-radius:5px; border:1px solid #ccc; width:100%; }
button{ padding:10px; border:none; border-radius:5px; background:#4CAF50; color:#fff; cursor:pointer; }
button:hover{ background:#45a049; }
.error{ color:red; text-align:center; }
a{ display:block; text-align:center; margin-top:10px; color:#4CAF50; text-decoration:none; }
</style>
</head>
<body>
<h1>Adicionar Item ao Estoque</h1>
<?php if(isset($erro)) echo "<div class='error'>".htmlspecialchars($erro)."</div>"; ?>
<form method="POST">
    <input type="text" name="nome" placeholder="Nome do item" required>
    <input type="text" name="tipo" placeholder="Tipo do item" required>
    <input type="number" name="quantidade" placeholder="Quantidade" required>
    <input type="text" name="localizacao" placeholder="Localização">
    <button type="submit">Adicionar</button>
</form>
<a href="painel_estoque.php">Voltar</a>
</body>
</html>
