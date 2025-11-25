criar-conta.php
<?php
include 'config.php';
session_start();

$erro = '';
$sucesso = '';

// ===== CRIAR USUÁRIO =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'criar') {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $tipo  = $_POST['tipo'] ?? 'usuario';

    if (!$nome || !$email || !$senha) {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        // Verifica se email já existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $erro = "Este email já está cadastrado.";
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nome, $email, $senhaHash, $tipo])) {
                $sucesso = "Conta criada com sucesso!";
            } else {
                $erro = "Erro ao criar a conta.";
            }
        }
    }
}

// ===== EXCLUIR USUÁRIO =====
if (isset($_GET['delete'])) {
    $id_delete = (int)$_GET['delete'];

    // Verifica se o usuário possui reservas
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservas WHERE id_usuario = ?");
    $stmt->execute([$id_delete]);
    $reservas_count = $stmt->fetchColumn();

    if ($reservas_count > 0) {
        $erro = "Não é possível excluir este usuário, pois ele possui reservas vinculadas.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        if ($stmt->execute([$id_delete])) {
            $sucesso = "Usuário excluído com sucesso!";
            header("Location: criar-conta.php");
            exit;
        } else {
            $erro = "Erro ao excluir usuário.";
        }
    }
}

// ===== LISTAR USUÁRIOS =====
$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY id_usuario DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRUD de Contas</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
/* ===== RESET ===== */
* { box-sizing: border-box; font-family: 'Roboto', sans-serif; margin:0; padding:0; }
body { background:#f4f7f9; color:#333; padding-top: 80px; }

/* ===== HEADER ===== */
header {
    width: 100%;
    background-color: #0046ff;
    color: #fff;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    position: fixed;
    top: 0;
    left: 0;
    z-index: 100;
}

.header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

header .logo {
    font-size: 22px;
    font-weight: bold;
    color: #fff;
}

header nav {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

header nav a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s;
}

header nav a:hover {
    color: #bbdefb;
}

/* Ajuste responsivo do header */
@media (max-width: 768px) {
    .header-container {
        flex-direction: column;
        align-items: flex-start;
        padding: 10px 20px;
    }
    header nav {
        width: 100%;
        justify-content: flex-start;
        gap: 15px;
        margin-top: 10px;
    }
}

/* ===== FORM ===== */
form {
    max-width: 500px;
    margin: 20px auto;
    display:flex;
    flex-direction:column;
    gap:10px;
    background:#fff;
    padding:20px;
    border-radius:8px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

input, select {
    padding:10px;
    border-radius:5px;
    border:1px solid #ccc;
    width:100%;
    font-size:14px;
}

button {
    padding:12px;
    border:none;
    border-radius:5px;
    background:#0046ff;
    color:#fff;
    font-weight:bold;
    cursor:pointer;
    font-size:15px;
    transition:.2s;
}

button:hover { background:#0031bb; }

.error {
    color:#d8000c;
    background:#ffdddd;
    padding:10px;
    text-align:center;
    border-radius:6px;
    border:1px solid #d8000c;
}

.success {
    color:#155724;
    background:#d4edda;
    padding:10px;
    text-align:center;
    border-radius:6px;
    border:1px solid #c3e6cb;
}

/* ===== TABELA ===== */
table {
    width:100%;
    border-collapse: collapse;
    margin:30px auto;
    background:#fff;
    border-radius:8px;
    overflow:hidden;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

th, td {
    padding:12px;
    border-bottom:1px solid #ddd;
    text-align:left;
}

th {
    background:#0046ff;
    color:#fff;
    font-size:14px;
}

tr:hover td { background:#f5f5f5; }

a.btn {
    padding:6px 12px;
    text-decoration:none;
    border-radius:5px;
    color:#fff;
    font-size:14px;
    font-weight:bold;
    margin-right:4px;
    transition:0.2s;
}

a.edit { background:#ff9800; }
a.delete { background:#f44336; }
a.btn:hover { opacity:0.85; }

@media(max-width:600px){
    form, table { width:100%; font-size:13px; }
    th, td { padding:8px; }
}
</style>
</head>
<body>

<!-- HEADER -->
<header>
    <div class="header-container">
        <div class="logo">Criar Conta</div>
        <nav>
             <a href="home.html">Home</a>
                <a href="equipamento.php">Equipamento</a>
                <a href="painel_estoque.php">Estoque</a>
                <a href="associados.html">Associados</a>
                <a href="reservas.php">Reservas</a>
                <a href="feedback.php">Feedback</a>
                <a href="login.php">Login</a>
                <a href="criar-conta.php">Criar Conta</a>
        </nav>
    </div>
</header>

<!-- FORMULARIO -->
<form method="POST">
    <input type="hidden" name="acao" value="criar">
    <?php if($erro): ?><div class="error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php if($sucesso): ?><div class="success"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>
    <input type="text" name="nome" placeholder="Nome" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="senha" placeholder="Senha" required>
    <select name="tipo">
        <option value="usuario">Usuário</option>
        <option value="admin">Administrador</option>
    </select>
    <button type="submit">Criar Conta</button>
</form>

<!-- TABELA DE USUÁRIOS -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($usuarios)): ?>
            <tr><td colspan="5" style="text-align:center;">Nenhum usuário cadastrado.</td></tr>
        <?php else: ?>
            <?php foreach($usuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['id_usuario']) ?></td>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['tipo']) ?></td>
                    <td>
                        <a href="editar-conta.php?id=<?= $u['id_usuario'] ?>" class="btn edit">Editar</a>
                        <a href="?delete=<?= $u['id_usuario'] ?>" class="btn delete" onclick="return confirm('Confirma exclusão?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
