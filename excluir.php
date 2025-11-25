excluir.php
<?php
include 'config.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("ID inválido.");
}

$id = (int) $id;

// Verifica existência do equipamento
$stmt = $pdo->prepare("SELECT * FROM equipamentos WHERE id = ?");
$stmt->execute([$id]);
$equipamento = $stmt->fetch();

if (!$equipamento) {
    die("Equipamento não encontrado.");
}

try {
    // Tenta excluir
    $delete = $pdo->prepare("DELETE FROM equipamentos WHERE id = ?");
    $delete->execute([$id]);

    header("Location: equipamento.php ?msg=excluido");
    exit;

} catch (PDOException $e) {

    // Se for erro por existir reservas usando o equipamento
    if ($e->getCode() == "23000") {
        die("Não é possível excluir este equipamento porque há reservas associadas a ele.");
    }

    // Outros erros
    die("Erro ao excluir: " . $e->getMessage());
}
