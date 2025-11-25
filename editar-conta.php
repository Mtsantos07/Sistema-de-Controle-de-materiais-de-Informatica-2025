editar-conta.php
<?php
include 'config.php';

$erro = '';
$sucesso = '';

// Pega o ID do usuário
$id_usuario = $_GET['id'] ?? null;

if (!$id_usuario) {
    die("ID do usuário não fornecido.");
}

// Buscar dados do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuário não encontrado.");
}

// Atualizar usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];

    if (!$nome || !$email) {
        $erro = "Nome e email são obrigatórios.";
    } else {
        // Verifica se o email já existe em outro usuário
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ? AND id_usuario != ?");
        $stmtCheck->execute([$email, $id_usuario]);
        if ($stmtCheck->fetchColumn() > 0) {
            $erro = "Este email já está cadastrado para outro usuário.";
        } else {
            // Atualiza senha apenas se foi preenchida
            if ($senha) {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmtUpdate = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = ?, tipo = ? WHERE id_usuario = ?");
                $stmtUpdate->execute([$nome, $email, $senhaHash, $tipo, $id_usuario]);
            } else {
                $stmtUpdate = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, tipo = ? WHERE id_usuario = ?");
                $stmtUpdate->execute([$nome, $email, $tipo, $id_usuario]);
            }
            $sucesso = "Usuário atualizado com sucesso!";
            // Atualiza a variável $usuario para preencher o formulário novamente
            $usuario['nome'] = $nome;
            $usuario['email'] = $email;
            $usuario['tipo'] = $tipo;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Editar Usuário</title>
<style>
body { font-family: Arial, sans-serif; background:#f0f2f5; padding:20px; }
h1 { text-align:center; color:#333; }
form { max-width:450px; margin:auto; display:flex; flex-direction:column; gap:10px; background:#fff; padding:20px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
input, select { padding:10px; border-radius:5px; border:1px solid #ccc; width:100%; }
button { padding:10px; border:none; border-radius:5px; background:#4CAF50; color:#fff; font-weight:bold; cursor:pointer; }
button:hover { background:#45a049; }
.error { color:red; text-align:center; }
.success { color:green; text-align:center; }
</style>
</head>
<body>

<h1>Editar Usuário</h1>

<form method="POST">
    <?php if($erro): ?><div class="error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php if($sucesso): ?><div class="success"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>

    <input type="text" name="nome" placeholder="Nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
    <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
    <input type="password" name="senha" placeholder="Nova senha (deixe em branco para manter)">
    <select name="tipo">
        <option value="usuario" <?= $usuario['tipo'] === 'usuario' ? 'selected' : '' ?>>Usuário</option>
        <option value="admin" <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
    </select>
    <button type="submit">Atualizar Usuário</button>
</form>

</body>
</html>
