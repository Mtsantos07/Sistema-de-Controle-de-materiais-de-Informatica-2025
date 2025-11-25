Aluno.php
<?php
require_once __DIR__ . '/conec.php';
// AÇÕES
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$acao = $_POST['acao'] ?? '';
$matricula = trim($_POST['matricula'] ?? '');
$nome = trim($_POST['nome'] ?? '');
$original = $_POST['original'] ?? '';
if ($acao === 'adicionar' && $matricula !== '' && $nome
!== '') {
$stmt = $pdo->prepare("INSERT INTO aluno
(matricula, nome) VALUES (:m, :n)");
$stmt->execute([':m' => $matricula, ':n' =>
$nome]);
}
if ($acao === 'atualizar' && $original !== '' &&
$matricula !== '' && $nome !== '') {
if ($original !== $matricula) {
$chk = $pdo->prepare("SELECT 1 FROM aluno
WHERE matricula = :m");
$chk->execute([':m' => $matricula]);
if ($chk->fetch()) {
header('Location: index.php?erro=duplicidade');
exit;}
$up = $pdo->prepare("UPDATE aluno SET
matricula=:m, nome=:n WHERE matricula=:o");
$up->execute([':m'=>$matricula, ':n'=>$nome,
':o'=>$original]);
} else {
$up = $pdo->prepare("UPDATE aluno SET
nome=:n WHERE matricula=:m");
$up->execute([':n'=>$nome, ':m'=>$matricula]);
}
}
}
if (($_GET['acao'] ?? '') === 'excluir') {
$m = $_GET['matricula'] ?? '';
if ($m !== '') {
$del = $pdo->prepare("DELETE FROM aluno
WHERE matricula = :m");
$del->execute([':m' => $m]);
}
}
// MODO EDIÇÃO
$editando = false;
$alunoEdit = ['matricula'=>'', 'nome'=>''];
if (($_GET['acao'] ?? '') === 'editar') {
$m = $_GET['matricula'] ?? '';
if ($m !== '') {
$s = $pdo->prepare("SELECT matricula, nome
FROM aluno WHERE matricula = :m");
$s->execute([':m' => $m]);
if ($row = $s->fetch(PDO::FETCH_ASSOC)) {
$editando = true; $alunoEdit = $row; }
}
}
// LISTA
$lista = $pdo->query("SELECT matricula, nome
FROM aluno ORDER BY
nome")->fetchAll(PDO::FETCH_ASSOC);
// MENSAGEM
$msg = '';
if (isset($_GET['erro']) &&
$_GET['erro']==='duplicidade') {
$msg = '<div class="alert alert-error">Matrícula já
existente.</div>';
}
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>CRUD Simples de Alunos</title>
<meta name="viewport"
content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css" >
</head><body>
<header>
<div class="container">
<h1>Alunos</h1>
<nav><a href="Aluno.php"
class="btn">Novo</a></nav>
</div>
</header>
<main class="container">
<?= $msg ?>
<!-- TABELA -->
<table class="table">
<thead>
<tr>
<th>Matrícula</th>
<th>Nome</th>
<th>Ações</th>
</tr>
</thead>
<tbody>
<?php if (empty($lista)): ?>
<tr><td colspan="3">Nenhum aluno
cadastrado.</td></tr>
<?php else: ?>
<?php foreach ($lista as $a): ?>
<tr>
<td><?= htmlspecialchars($a['matricula'])
?></td>
<td><?= htmlspecialchars($a['nome']) ?></td>
<td class="acao">
<a class="btn btn-secondary"
href="?acao=editar&matricula=<?=
urlencode($a['matricula']) ?>">Editar</a>
<a class="btn btn-danger"
href="?acao=excluir&matricula=<?=
urlencode($a['matricula']) ?>"
onclick="return confirm('Excluir este
aluno?');">Excluir</a>
</td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
<!-- FORMULÁRIO -->
<form class="form-card" method="post">
<h2><?= $editando ? 'Editar Aluno' : 'Adicionar
Aluno' ?></h2>
<?php if ($editando): ?>
<input type="hidden" name="original" value="<?=
htmlspecialchars($alunoEdit['matricula']) ?>">
<?php endif; ?><div class="form-row">
<div class="form-group">
<label for="matricula">Matrícula (máx.
10)</label>
<input type="text" id="matricula"
name="matricula" maxlength="10" required
value="<?=
htmlspecialchars($alunoEdit['matricula']) ?>">
</div>
<div class="form-group">
<label for="nome">Nome (máx. 100)</label>
<input type="text" id="nome" name="nome"
maxlength="100" required
value="<?=
htmlspecialchars($alunoEdit['nome']) ?>">
</div>
</div>
<div class="form-actions">
<?php if ($editando): ?>
<input type="hidden" name="acao"
value="atualizar">
<button class="btn" type="submit">Salvar
Alterações</button>
<a class="btn btn-secondary"
href="index.php">Cancelar</a>
<?php else: ?>
<input type="hidden" name="acao"
value="adicionar">
<button class="btn"
type="submit">Adicionar</button>
<?php endif; ?>
</div>
</form>
</main>
<footer>
<div class="container">
<small>&copy; <?= date('Y') ?> — Sistema
Escola</small>
</div>
</footer>
</body>
</html>
