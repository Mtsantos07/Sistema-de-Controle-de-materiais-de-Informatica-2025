<?php
include 'config.php';
session_start();

$erro = '';
$sucesso = '';
$editar_usuario = null;

// =========================
// LOGIN
// =========================
if(isset($_POST['acao']) && $_POST['acao'] == 'login'){
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = trim($_POST['senha'] ?? '');

    if(!$usuario || !$senha){
        $erro = "Preencha todos os campos.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nome = ?");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user && password_verify($senha, $user['senha'])){
            $_SESSION['usuario_id'] = $user['id_usuario'];
            $_SESSION['usuario_nome'] = $user['nome'];
            header("Location: reservas.php");
            exit;
        } else {
            $erro = "Usuário ou senha inválidos.";
        }
    }
}

// =========================
// CRUD DE USUÁRIOS
// =========================
if(isset($_GET['edit'])){
    $id_edit = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_edit]);
    $editar_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}

if(isset($_POST['acao']) && ($_POST['acao'] == 'salvar' || $_POST['acao'] == 'editar')){
    $id_usuario = $_POST['id_usuario'] ?? null;
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if(!$nome || !$email || (!$id_usuario && !$senha)){
        $erro = "Preencha todos os campos.";
    } else {
        try{
            if($id_usuario){
                if($senha){
                    $hash = password_hash($senha, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=?, senha=? WHERE id_usuario=?");
                    $stmt->execute([$nome, $email, $hash, $id_usuario]);
                } else {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=? WHERE id_usuario=?");
                    $stmt->execute([$nome, $email, $id_usuario]);
                }
                $sucesso = "Usuário atualizado com sucesso!";
            } else {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
                $stmt->execute([$nome, $email, $hash]);
                $sucesso = "Usuário cadastrado com sucesso!";
            }
            header("Location: login.php");
            exit;
        } catch(Exception $e){
            $erro = "Erro: " . $e->getMessage();
        }
    }
}

if(isset($_GET['delete'])){
    $id_delete = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_delete]);
    header("Location: login.php");
    exit;
}

$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login e CRUD de Usuários</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; font-family: 'Roboto', sans-serif; margin:0; padding:0; }
body { background:#f4f7f9; color:#333; padding-top: 80px; }

/* ===== HEADER ===== */
header {
    width: 100%;
    background-color: #0046ff;
    color: #fff;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 100;
    box-shadow:0 3px 8px rgba(0,0,0,0.1);
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

header nav a:hover { color:#bbdefb; }

@media (max-width:768px){
    .header-container { flex-direction: column; align-items: flex-start; padding: 10px 20px; }
    header nav { width: 100%; justify-content:flex-start; gap:15px; margin-top:10px; }
}

/* ===== FORM ===== */
h1 { text-align:center; color:#0D47A1; margin-bottom:25px; font-weight:700; }

form { max-width:400px; margin:20px auto; display:flex; flex-direction:column; gap:12px; background:#fff; padding:25px; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.08);}
input { padding:12px; border-radius:8px; border:1px solid #ccc; width:100%; font-size:15px; }
input:focus { border-color:#0D47A1; outline:none; box-shadow:0 0 8px rgba(13,71,161,0.2);}
button { padding:12px; border:none; border-radius:8px; background:#0D47A1; color:#fff; font-weight:600; font-size:16px; cursor:pointer; transition:0.3s;}
button:hover { background:#073270; }

.error { color:#d8000c; background:#ffebee; padding:12px; border-radius:8px; text-align:center; border:1px solid #d32f2f; }
.success { color:#0D47A1; background:#e3f2fd; padding:12px; border-radius:8px; text-align:center; border:1px solid #0D47A1; }

/* ===== TABELA ===== */
table { width:100%; border-collapse: collapse; margin-top:30px; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 6px 18px rgba(0,0,0,0.06);}
th, td { padding:14px; text-align:left; }
th { background:#0D47A1; color:#fff; font-weight:500; }
tr { transition:0.2s; }
tr:hover { background:#e3f2fd; }
td { border-bottom:1px solid #eee; }

a.btn { padding:8px 14px; border-radius:6px; text-decoration:none; font-size:14px; font-weight:500; margin-right:5px; transition:0.3s; }
a.edit { background:#0D47A1; color:#fff; }
a.delete { background:#e53935; color:#fff; }
a.btn:hover { opacity:0.85; }

@media(max-width:600px){
    body { padding:15px; }
    form { padding:20px; }
    th, td { font-size:14px; padding:10px; }
}
</style>
</head>
<body>

<!-- HEADER -->
<header>
    <div class="header-container">
        <div class="logo">Login</div>
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

<!-- LOGIN FORM -->
<h1>Login</h1>
<form method="POST">
    <input type="hidden" name="acao" value="login">
    <input type="text" name="usuario" placeholder="Usuário" required>
    <input type="password" name="senha" placeholder="Senha" required>
    <button type="submit">Entrar</button>
    <?php if($erro && $_POST['acao']=='login'): ?><div class="error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
</form>

<hr style="margin:40px 0; border:none; border-top:1px solid #ccc;">

<h1>Usuários</h1>
<?php if($usuarios): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($usuarios as $u): ?>
        <tr>
            <td><?= $u['id_usuario'] ?></td>
            <td><?= htmlspecialchars($u['nome']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td>
                <a href="?edit=<?= $u['id_usuario'] ?>" class="btn edit">Editar</a>
                <a href="?delete=<?= $u['id_usuario'] ?>" class="btn delete" onclick="return confirm('Confirma exclusão?')">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p style="text-align:center; margin-top:20px;">Nenhum usuário cadastrado.</p>
<?php endif; ?>

</body>
</html>
