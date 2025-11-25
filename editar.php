editar.php
<?php
include 'config.php';
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) die("ID inválido.");

$id = (int)$id;

$stmt = $pdo->prepare("SELECT * FROM equipamentos WHERE id = ?");
$stmt->execute([$id]);
$equip = $stmt->fetch();
if (!$equip) die("Equipamento não encontrado.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $status = $_POST['status'] ?? '';
    $local = $_POST['localizacao'] ?? '';

    $update = $pdo->prepare("UPDATE equipamentos SET nome=?, tipo=?, status=?, localizacao=? WHERE id=?");
    $update->execute([$nome, $tipo, $status, $local, $id]);
    header("Location: equipamento.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Editar Equipamento</title>
</head>
<body>
<h1>Editar Equipamento</h1>
<form method="POST">
Nome:<br>
<input type="text" name="nome" value="<?= htmlspecialchars($equip['nome']) ?>" required><br><br>
Tipo:<br>
<input type="text" name="tipo" value="<?= htmlspecialchars($equip['tipo']) ?>" required><br><br>
Status:<br>
<input type="text" name="status" value="<?= htmlspecialchars($equip['status']) ?>" required><br><br>
Localização:<br>
<input type="text" name="localizacao" value="<?= htmlspecialchars($equip['localizacao']) ?>" required><br><br>
<button type="submit">Salvar</button>
</form>
<br>
<a href="index.php">Voltar</a>
</body>
</html>
