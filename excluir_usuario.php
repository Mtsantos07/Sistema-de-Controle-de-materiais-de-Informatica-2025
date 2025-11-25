excluir_usuario.php
<?php
include 'config.php';
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario=?");
    $stmt->execute([$id]);
}
header("Location: listar_usuarios.php");
exit;
