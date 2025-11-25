<?php
include 'config.php';

$erro = '';
$sucesso = '';
$id_editar = $_GET['edit'] ?? null;

// Buscar feedbacks
$feedbacks = $pdo->query("SELECT * FROM feedbacks ORDER BY data_envio DESC")->fetchAll(PDO::FETCH_ASSOC);

// Se estiver editando, buscar dados do feedback
$feedback_editar = null;
if ($id_editar) {
    $stmt = $pdo->prepare("SELECT * FROM feedbacks WHERE id = ?");
    $stmt->execute([$id_editar]);
    $feedback_editar = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$feedback_editar) {
        $erro = "Feedback não encontrado para edição.";
        $id_editar = null;
    }
}

// Inserir ou atualizar feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $mensagem = trim($_POST['mensagem'] ?? '');

    if (!$usuario || !$mensagem) {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        try {
            if ($id_editar) {
                $stmt = $pdo->prepare("UPDATE feedbacks SET usuario = ?, mensagem = ? WHERE id = ?");
                $stmt->execute([$usuario, $mensagem, $id_editar]);
                $sucesso = "Feedback atualizado com sucesso!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO feedbacks (usuario, mensagem, data_envio) VALUES (?, ?, NOW())");
                $stmt->execute([$usuario, $mensagem]);
                $sucesso = "Feedback enviado com sucesso!";
            }
            $feedbacks = $pdo->query("SELECT * FROM feedbacks ORDER BY data_envio DESC")->fetchAll(PDO::FETCH_ASSOC);
            $_POST = [];
            $id_editar = null;
        } catch (Exception $e) {
            $erro = "Erro ao salvar feedback: " . $e->getMessage();
        }
    }
}

// Deletar feedback
if (isset($_GET['delete'])) {
    $id_delete = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM feedbacks WHERE id = ?");
    $stmt->execute([$id_delete]);
    header("Location: feedback.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Feedback - Sistema de Controle de Materiais</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; font-family: 'Roboto', sans-serif; margin:0; padding:0; }
body { background:#f4f7f9; color:#333; }

/* ===== HEADER ===== */
header { display:flex; justify-content: space-between; align-items:center; padding:15px 30px; background:#0D47A1; color:#fff; box-shadow:0 3px 8px rgba(0,0,0,0.1);}
header h1 { font-size:24px; font-weight:700; }
header nav a { margin-left:20px; color:#fff; text-decoration:none; font-weight:500; transition:0.3s; }
header nav a:hover { text-decoration:underline; color:#bbdefb; }

/* ===== CONTAINER ===== */
.container { max-width:800px; margin:50px auto; padding:30px; background:#fff; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.08); }

/* ===== TITULO ===== */
.section-title { font-size:26px; font-weight:700; color:#0D47A1; margin-bottom:25px; text-align:center; }

/* ===== FORM ===== */
form { display:flex; flex-direction:column; }
input, textarea { width:100%; padding:14px; border:1px solid #ccc; border-radius:8px; font-size:16px; margin-bottom:15px; transition:0.3s; }
input:focus, textarea:focus { border-color:#0D47A1; box-shadow:0 0 8px rgba(13,71,161,0.2); outline:none; }
button { padding:14px; border:none; border-radius:8px; background:#0D47A1; color:#fff; font-weight:600; font-size:16px; cursor:pointer; transition:0.3s; }
button:hover { background:#073270; }

/* ===== MENSAGENS ===== */
.error { color:#d8000c; background:#ffebee; padding:12px; border-radius:8px; text-align:center; margin-bottom:15px; border:1px solid #d32f2f; }
.success { color:#0D47A1; background:#e3f2fd; padding:12px; border-radius:8px; text-align:center; margin-bottom:15px; border:1px solid #0D47A1; }

/* ===== TABELA ===== */
table { width:100%; border-collapse: collapse; margin-top:40px; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
th, td { padding:15px; text-align:left; }
th { background:#0D47A1; color:#fff; font-weight:500; font-size:15px; }
tr { transition:0.2s; }
tr:hover { background:#e3f2fd; }
td { border-bottom:1px solid #eee; }

/* ===== BOTOES ===== */
a.btn { padding:8px 14px; border-radius:6px; text-decoration:none; font-size:14px; font-weight:500; transition:0.3s; }
a.edit { background:#0D47A1; color:#fff; }
a.delete { background:#e53935; color:#fff; }
a.btn:hover { opacity:0.85; }

/* ===== RESPONSIVO ===== */
@media(max-width:600px){
    header { flex-direction:column; align-items:flex-start; padding:15px; }
    header nav { margin-top:10px; }
    .container { margin:20px 15px; padding:20px; }
    th, td { font-size:14px; padding:10px; }
}
</style>
</head>
<body>

<header>
    <h1>Feedback</h1>
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
</header>

<div class="container">
    <div class="section-title"><?= $id_editar ? "Editar Feedback" : "Envie seu Feedback" ?></div>
    <form method="POST">
        <?php if($erro): ?><div class="error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <?php if($sucesso): ?><div class="success"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>
        <input type="text" name="usuario" placeholder="Seu nome" required value="<?= htmlspecialchars($_POST['usuario'] ?? $feedback_editar['usuario'] ?? '') ?>">
        <textarea name="mensagem" placeholder="Sua mensagem" rows="5" required><?= htmlspecialchars($_POST['mensagem'] ?? $feedback_editar['mensagem'] ?? '') ?></textarea>
        <button type="submit"><?= $id_editar ? "Atualizar Feedback" : "Enviar Feedback" ?></button>
    </form>

    <div class="section-title" style="margin-top:50px;">Feedbacks Recebidos</div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Mensagem</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($feedbacks)): ?>
                <tr><td colspan="5" style="text-align:center;">Nenhum feedback enviado.</td></tr>
            <?php else: ?>
                <?php foreach($feedbacks as $f): ?>
                    <tr>
                        <td><?= $f['id'] ?></td>
                        <td><?= htmlspecialchars($f['usuario']) ?></td>
                        <td><?= htmlspecialchars($f['mensagem']) ?></td>
                        <td><?= $f['data_envio'] ?></td>
                        <td>
                            <a href="?edit=<?= $f['id'] ?>" class="btn edit">Editar</a>
                            <a href="?delete=<?= $f['id'] ?>" class="btn delete" onclick="return confirm('Confirma exclusão?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
