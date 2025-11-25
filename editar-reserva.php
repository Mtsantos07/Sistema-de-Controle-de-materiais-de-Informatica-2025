editar-reserva.php
<?php
include 'config.php';

// Inicializa variáveis
$erro = '';
$sucesso = '';

// Buscar usuários e equipamentos para os selects
$usuarios = $pdo->query("SELECT id_usuario, nome FROM usuarios ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
$equipamentos = $pdo->query("SELECT id, nome FROM equipamentos ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

// =========================
//  PROCESSAR EDIÇÃO
// =========================
$editar_reserva = null;
if(isset($_GET['edit'])){
    $id_edit = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM reservas WHERE id_reserva = ?");
    $stmt->execute([$id_edit]);
    $editar_reserva = $stmt->fetch(PDO::FETCH_ASSOC);
}

// =========================
//  PROCESSAR FORMULÁRIO (ADICIONAR OU ATUALIZAR)
// =========================
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id_reserva     = $_POST['id_reserva'] ?? null;
    $id_usuario     = $_POST['id_usuario'] ?? null;
    $id_equipamento = $_POST['equipamento_id'] ?? null;
    $data_reserva   = $_POST['data_reserva'] ?? null;
    $data_uso       = $_POST['data_uso'] ?? null;
    $hora_inicio    = $_POST['hora_inicio'] ?? null;
    $status         = $_POST['status'] ?? 'ativa';

    if(!$id_usuario || !$id_equipamento || !$data_reserva || !$data_uso || !$hora_inicio){
        $erro = "Todos os campos são obrigatórios.";
    } else {
        try{
            if($id_reserva){ // Atualizar reserva
                $stmtUpdate = $pdo->prepare("
                    UPDATE reservas SET
                        id_usuario = ?,
                        id_equipamento = ?,
                        data_reserva = ?,
                        data_uso = ?,
                        hora_inicio = ?,
                        status = ?
                    WHERE id_reserva = ?
                ");
                $stmtUpdate->execute([$id_usuario, $id_equipamento, $data_reserva, $data_uso, $hora_inicio, $status, $id_reserva]);
                $sucesso = "Reserva atualizada com sucesso!";
            } else { // Inserir nova reserva
                $stmtInsert = $pdo->prepare("
                    INSERT INTO reservas (id_usuario, id_equipamento, data_reserva, data_uso, hora_inicio, status)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmtInsert->execute([$id_usuario, $id_equipamento, $data_reserva, $data_uso, $hora_inicio, $status]);
                $sucesso = "Reserva cadastrada com sucesso!";
            }
            header("Location: reservas.php"); // evita reenvio do formulário
            exit;
        } catch(Exception $e){
            $erro = "Erro: " . $e->getMessage();
        }
    }
}

// =========================
//  DELETAR RESERVA
// =========================
if(isset($_GET['delete'])){
    $id_delete = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM reservas WHERE id_reserva = ?");
    $stmt->execute([$id_delete]);
    header("Location: reservas.php");
    exit;
}

// =========================
//  BUSCAR RESERVAS
// =========================
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
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRUD de Reservas</title>
<style>
/* ===== RESET ===== */
* {margin:0; padding:0; box-sizing:border-box;}
body {font-family:Arial,sans-serif; background:#eef1f5; padding:30px; color:#333;}
h1 {text-align:center; margin-bottom:25px; color:#222; font-size:28px;}
form {max-width:500px; margin:auto; display:flex; flex-direction:column; gap:12px; background:#fff; padding:25px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
input, select {padding:12px; border-radius:6px; border:1px solid #bbb; width:100%; font-size:15px; color:#333; transition:.2s;}
input::placeholder {color:#888;}
input:focus, select:focus {border-color:#4CAF50; outline:none; box-shadow:0 0 4px rgba(76,175,80,0.4);}
button {padding:12px; border:none; border-radius:6px; background:#4CAF50; color:#fff; font-weight:bold; font-size:15px; cursor:pointer; transition:.2s;}
button:hover {background:#3e8e41;}
.error {color:#d8000c; background:#ffdddd; padding:10px; text-align:center; border-radius:6px; border:1px solid #d8000c;}
.success {color:#155724; background:#d4edda; padding:10px; text-align:center; border-radius:6px; border:1px solid #c3e6cb;}
table {width:100%; border-collapse:collapse; margin-top:35px; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.1);}
th, td {padding:12px; border-bottom:1px solid #ddd;}
th {background:#4CAF50; color:#fff; text-align:left; font-size:15px;}
tr:hover td {background:#f5f5f5;}
a.btn {padding:6px 12px; text-decoration:none; border-radius:5px; color:#fff; font-size:14px; font-weight:bold;}
a.edit {background:#ff9800;}
a.delete {background:#f44336;}
a.btn:hover {opacity:0.8;}
</style>
</head>
<body>

<h1>CRUD de Reservas de Equipamentos</h1>

<form method="POST">
    <?php if($erro): ?><div class="error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php if($sucesso): ?><div class="success"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>

    <input type="hidden" name="id_reserva" value="<?= $editar_reserva['id_reserva'] ?? '' ?>">

    <select name="id_usuario" required>
        <option value="">Selecione o usuário</option>
        <?php foreach($usuarios as $u): ?>
            <option value="<?= $u['id_usuario'] ?>" <?= ($editar_reserva && $editar_reserva['id_usuario']==$u['id_usuario'])?'selected':'' ?>>
                <?= htmlspecialchars($u['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="equipamento_id" required>
        <option value="">Selecione o equipamento</option>
        <?php foreach($equipamentos as $e): ?>
            <option value="<?= $e['id'] ?>" <?= ($editar_reserva && $editar_reserva['id_equipamento']==$e['id'])?'selected':'' ?>>
                <?= htmlspecialchars($e['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="date" name="data_reserva" value="<?= $editar_reserva['data_reserva'] ?? '' ?>" required>
    <input type="date" name="data_uso" value="<?= $editar_reserva['data_uso'] ?? '' ?>" required>
    <input type="time" name="hora_inicio" value="<?= $editar_reserva['hora_inicio'] ?? '' ?>" required>

    <select name="status">
        <option value="ativa" <?= ($editar_reserva && $editar_reserva['status']=='ativa')?'selected':'' ?>>Ativa</option>
        <option value="cancelada" <?= ($editar_reserva && $editar_reserva['status']=='cancelada')?'selected':'' ?>>Cancelada</option>
        <option value="concluida" <?= ($editar_reserva && $editar_reserva['status']=='concluida')?'selected':'' ?>>Concluída</option>
    </select>

    <button type="submit"><?= $editar_reserva ? 'Atualizar Reserva' : 'Adicionar Reserva' ?></button>
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
                        <a href="?edit=<?= $r['id_reserva'] ?>" class="btn edit">Editar</a>
                        <a href="?delete=<?= $r['id_reserva'] ?>" class="btn delete" onclick="return confirm('Confirma exclusão?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
