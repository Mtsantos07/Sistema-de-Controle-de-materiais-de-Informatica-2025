editar_usuario.php
<?php
include 'config.php';
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) die("Usuário não encontrado.");

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $tipo = $_POST['tipo'] ?? 'usuario';
    $senha = $_POST['senha'] ?? '';

    if ($nome && $email) {
        if ($senha) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=?, tipo=?, senha=? WHERE id_usuario=?");
            $stmt->execute([$nome,$email,$tipo,$senha_hash,$id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=?, tipo=? WHERE id_usuario=?");
            $stmt->execute([$nome,$email,$tipo,$id]);
        }
        header("Location: listar_usuarios.php");
        exit;
    } else {
        $erro = "Preencha os campos obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Editar Usuário</title>
<style>
body { font-family: Arial; background:#f0f2f5; padding:20px; }
form { max-width:400px; margin:auto; display:flex; flex-direction:column; gap:10px; background:#fff; padding:20px; border-radius:8px; }
input, select { padding:10px; border-radius:5px; border:1px solid #ccc; width:100%; }
button { padding:10px; border:none; border-radius:5px; background:#ff9800; color:#fff; cursor:pointer; }
button:hover { background:#e68900; }
.error { color:red; text-align:center; }
</style>
</head>
<body>
<h2>Editar Usuário</h2>
<?php if ($erro): ?><div class="error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
<form method="POST">
    <label>Nome:</label>
    <input type="text" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required>
    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    <label>Senha (deixe em branco para não alterar):</label>
    <input type="password" name="senha">
    <label>Tipo:</label>
    <select name="tipo">
        <option value="usuario" <?= $user['tipo']=='usuario'?'selected':'' ?>>Usuário</option>
        <option value="admin" <?= $user['tipo']=='admin'?'selected':'' ?>>Administrador</option>
    </select>
    <button type="submit">Atualizar</button>
</form>
</body>
</html>
