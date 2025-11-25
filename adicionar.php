adicionar.php
<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $status = $_POST['status'] ?? '';
    $local = $_POST['localizacao'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO equipamentos (nome,tipo,status,localizacao,data_cadastro) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$nome, $tipo, $status, $local]);

    header("Location: equipamento.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Adicionar Equipamento</title>
</head>
<body>
<h1>Adicionar Equipamento</h1>
<form method="POST">
Nome:<br>
<input type="text" name="nome" required><br><br>
Tipo:<br>
<select name="tipo" required>
<option value="">Selecione</option>
<option value="Projetor">Projetor</option>
<option value="Computador">Computador</option>
<option value="Notebook">Notebook</option>
<option value="Impressora">Impressora</option>
<option value="Outro">Outro</option>
</select><br><br>
Status:<br>
<select name="status" required>
<option value="Disponível">Disponível</option>
<option value="Em Uso">Em Uso</option>
<option value="Manutenção">Manutenção</option>
</select><br><br>
Localização:<br>
<input type="text" name="localizacao"><br><br>
<button type="submit">Adicionar</button>
</form>
<br>
<a href="index.php">Voltar</a>
</body>
</html>
