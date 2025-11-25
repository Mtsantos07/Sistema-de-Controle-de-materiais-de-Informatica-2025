listar_usuarios.php
<?php
include 'config.php';
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Listar Usuários</title>
<style>
body { font-family: Arial; margin: 20px; background:#f4f4f4;}
table { width:100%; border-collapse: collapse; background:#fff; color:#000; }
th, td { padding:8px; border:1px solid #000; text-align:left; }
th { background:#4CAF50; color:#fff; }
a.btn { padding:5px 10px; text-decoration:none; color:#fff; border-radius:4px; margin:2px; }
a.edit { background:#ff9800; }
a.delete { background:#f44336; }
</style>
</head>
<body>
<h1>Usuários Cadastrados</h1>
<a href="criar_conta.php" class="btn" style="background:#4CAF50;">Adicionar Novo</a>

<table>
<thead>
<tr>
<th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th><th>Data Cadastro</th><th>Ações</th>
</tr>
</thead>
<tbody>
<?php
$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id_usuario DESC");
if ($stmt->rowCount() == 0) {
    echo "<tr><td colspan='6' style='text-align:center;'>Nenhum usuário cadastrado.</td></tr>";
} else {
    while($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>".htmlspecialchars($row['id_usuario'])."</td>";
        echo "<td>".htmlspecialchars($row['nome'])."</td>";
        echo "<td>".htmlspecialchars($row['email'])."</td>";
        echo "<td>".htmlspecialchars($row['tipo'])."</td>";
        echo "<td>".htmlspecialchars($row['data_cadastro'])."</td>";
        echo "<td>
            <a href='editar_usuario.php?id=".urlencode($row['id_usuario'])."' class='btn edit'>Editar</a>
            <a href='excluir_usuario.php?id=".urlencode($row['id_usuario'])."' class='btn delete' onclick='return confirm(\"Confirma exclusão?\")'>Excluir</a>
        </td>";
        echo "</tr>";
    }
}
?>
</tbody>
</table>
</body>
</html>
