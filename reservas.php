<?php
include 'config.php';

// Inicializa variáveis
$erro = '';
$sucesso = '';

// Buscar usuários e equipamentos para selects
$usuarios = $pdo->query("SELECT id_usuario, nome FROM usuarios ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
$equipamentos = $pdo->query("SELECT id, nome FROM equipamentos ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

// Adicionar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario     = $_POST['id_usuario'] ?? null;
    $id_equipamento = $_POST['equipamento_id'] ?? null;
    $data_reserva   = $_POST['data_reserva'] ?? null;
    $data_uso       = $_POST['data_uso'] ?? null;
    $hora_inicio    = $_POST['hora_inicio'] ?? null;
    $status         = $_POST['status'] ?? 'ativa';

    if (!$id_usuario || !$id_equipamento || !$data_reserva || !$data_uso || !$hora_inicio) {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        try {
            $stmtUser = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE id_usuario = ?");
            $stmtUser->execute([$id_usuario]);
            if ($stmtUser->fetchColumn() == 0) throw new Exception("Usuário inválido.");

            $stmtEquip = $pdo->prepare("SELECT COUNT(*) FROM equipamentos WHERE id = ?");
            $stmtEquip->execute([$id_equipamento]);
            if ($stmtEquip->fetchColumn() == 0) throw new Exception("Equipamento inválido.");

            $stmt = $pdo->prepare("INSERT INTO reservas (id_usuario, id_equipamento, data_reserva, data_uso, hora_inicio, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_usuario, $id_equipamento, $data_reserva, $data_uso, $hora_inicio, $status]);
            $sucesso = "Reserva cadastrada com sucesso!";
        } catch (Exception $e) {
            $erro = "Erro ao cadastrar a reserva: " . $e->getMessage();
        }
    }
}

// Deletar reserva
if (isset($_GET['delete'])) {
    $id_delete = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM reservas WHERE id_reserva = ?");
    $stmt->execute([$id_delete]);
    header("Location: reservas.php");
    exit;
}

// Buscar reservas
$reservas = $pdo->query("
    SELECT 
        r.id_reserva,
        u.nome AS usuario_nome,
        e.nome AS equipamento_nome,
        r.data_reserva,
        r.data_uso,
        r.hora_inicio,
        r.status
    FROM reservas r
    JOIN usuarios u ON r.id_usuario = u.id_usuario
    JOIN equipamentos e ON r.id_equipamento = e.id
    ORDER BY r.data_reserva DESC, r.hora_inicio ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRUD de Reservas</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
body { background: #eef1f5; color: #333; padding-top: 80px; }

/* ===== HEADER FIXO ===== */
header {
    width: 100%;
    background: #0046ff;
    color: #fff;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 100;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

.header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

header .logo {
    font-size: 22px;
    font-weight: bold;
    color: #fff;
}

header nav a {
    margin-left: 15px;
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s;
}

header nav a:hover { color:#bbdefb; }

/* ===== FORMULÁRIO ===== */
form { max-width: 500px; margin: 20px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: flex; flex-direction: column; gap: 12px; }
input, select { padding: 12px; border-radius: 6px; border: 1px solid #bbb; font-size: 15px; }
input:focus, select:focus { border-color: #0046ff; outline: none; box-shadow: 0 0 4px rgba(0,70,255,0.4); }
button { padding: 12px; border: none; border-radius: 6px; background: #0046ff; color: #fff; font-weight: bold; cursor: pointer; transition: .2s; }
button:hover { background: #0033aa; }

/* ===== MENSAGENS ===== */
.error { color: #d8000c; background: #ffdddd; padding: 10px; text-align: center; border-radius: 6px; border: 1px solid #d8000c; }
.success { color: #0D47A1; background: #e3f2fd; padding: 10px; text-align: center; border-radius: 6px; border: 1px solid #0D47A1; }

/* ===== TABELA ===== */
table { width: 100%; border-collapse: collapse; margin-top: 35px; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
th { background: #0046ff; color: #fff; font-size: 15px; }
tr:hover td { background: #f5f5f5; }

/* ===== BOTÕES DE AÇÃO ===== */
a.btn { padding: 6px 12px; text-decoration: none; border-radius: 5px; color: #fff; font-size: 14px; font-weight: bold; margin-right: 4px; }
a.edit { background: #ff9800; }
a.delete { background: #f44336; }
a.btn:hover { opacity: 0.85; }

/* RESPONSIVO */
@media(max-width:768px){
    .header-container { flex-direction: column; align-items: flex-start; }
    header nav a { margin: 8px 0; }
}
@media(max-width:600px){
    form { width: 100%; padding: 15px; }
    th, td { font-size: 13px; padding: 8px; }
}
</style>
</head>
<body>

<header>
    <div class="header-container">
        <div class="logo">Reservas</div>
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

<form method="POST">
    <?php if($erro): ?><div class="error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php if($sucesso): ?><div class="success"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>

    <select name="id_usuario" required>
        <option value="">Selecione o usuário</option>
        <?php foreach($usuarios as $u): ?>
            <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nome']) ?></option>
        <?php endforeach; ?>
    </select>

    <select name="equipamento_id" required>
        <option value="">Selecione o equipamento</option>
        <?php foreach($equipamentos as $e): ?>
            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
        <?php endforeach; ?>
    </select>

    <input type="date" name="data_reserva" required>
    <input type="date" name="data_uso" required>
    <input type="time" name="hora_inicio" required>

    <select name="status">
        <option value="ativa">Ativa</option>
        <option value="cancelada">Cancelada</option>
        <option value="concluida">Concluída</option>
    </select>

    <button type="submit">Reservar Equipamento</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuário</th>
            <th>Equipamento</th>
            <th>Data Reserva</th>
            <th>Data Uso</th>
            <th>Hora Início</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($reservas)): ?>
            <tr><td colspan="8" style="text-align:center;">Nenhuma reserva cadastrada.</td></tr>
        <?php else: ?>
            <?php foreach($reservas as $r): ?>
                <tr>
                    <td><?= $r['id_reserva'] ?></td>
                    <td><?= htmlspecialchars($r['usuario_nome']) ?></td>
                    <td><?= htmlspecialchars($r['equipamento_nome']) ?></td>
                    <td><?= $r['data_reserva'] ?></td>
                    <td><?= $r['data_uso'] ?></td>
                    <td><?= $r['hora_inicio'] ?></td>
                    <td><?= ucfirst($r['status']) ?></td>
                    <td>
                        <a href="editar-reserva.php?id=<?= $r['id_reserva'] ?>" class="btn edit">Editar</a>
                        <a href="?delete=<?= $r['id_reserva'] ?>" class="btn delete" onclick="return confirm('Confirma exclusão?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
