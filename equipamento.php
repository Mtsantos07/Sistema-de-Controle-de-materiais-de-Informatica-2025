equipamento.php
<?php
// equipamento.php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Controle de Equipamentos - Sistema de Materiais</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f8;
        color: #333;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: #0046ff;
        padding: 20px 0;
        color: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    header .container {
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    header h1 {
        font-size: 22px;
    }

    nav a {
        color: #fff;
        text-decoration: none;
        margin-left: 20px;
        font-weight: 600;
        transition: color 0.3s;
    }

    nav a:hover {
        color: #aad4ff;
    }

    nav .logo {
        width: 40px;
        height: 40px;
        background-color: white;
        border-radius: 5px;
        margin-right: 15px;
    }

    main {
        padding: 60px 20px;
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
    }

    h1.page-title {
        text-align: center;
        margin-bottom: 20px;
        color: #0046ff;
    }

    a.btn {
        display: inline-block;
        padding: 8px 15px;
        margin-bottom: 15px;
        text-decoration: none;
        border-radius: 5px;
        color: #fff;
        font-weight: bold;
        transition: 0.3s;
    }

    a.add { background-color: #4CAF50; }
    a.add:hover { background-color: #45a049; }

    a.edit { background-color: #ff9800; }
    a.edit:hover { background-color: #e68a00; }

    a.delete { background-color: #f44336; }
    a.delete:hover { background-color: #d32f2f; }

    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border-radius: 5px;
        overflow: hidden;
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #0046ff;
        color: #fff;
    }

    tr:hover td {
        background-color: #f1f1f1;
    }

    footer {
        text-align: center;
        padding: 20px;
        background-color: #0046ff;
        color: #fff;
        margin-top: 40px;
    }

    @media(max-width:768px){
        header .container {
            flex-direction: column;
            align-items: flex-start;
        }

        nav a {
            margin: 5px 0;
        }

        table, th, td {
            font-size: 14px;
        }
    }
</style>
</head>

<body>
<header>
    <div class="container">
        <div class="logo"></div>
        <h1>Sistema de Controle de Materiais</h1>
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

<main>
    <h1 class="page-title">Gerenciamento de Equipamentos</h1>
    <a href="adicionar.php" class="btn add">Adicionar Novo Equipamento</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Localização</th>
                <th>Data Cadastro</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM equipamentos ORDER BY id DESC");
            if ($stmt->rowCount() == 0) {
                echo "<tr><td colspan='7' style='text-align:center;'>Nenhum equipamento cadastrado.</td></tr>";
            } else {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>".htmlspecialchars($row['id'])."</td>";
                    echo "<td>".htmlspecialchars($row['nome'])."</td>";
                    echo "<td>".htmlspecialchars($row['tipo'])."</td>";
                    echo "<td>".htmlspecialchars($row['status'])."</td>";
                    echo "<td>".htmlspecialchars($row['localizacao'])."</td>";
                    echo "<td>".htmlspecialchars($row['data_cadastro'])."</td>";
                    echo "<td>
                        <a href='editar.php?id=".urlencode($row['id'])."' class='btn edit'>Editar</a>
                        <a href='excluir.php?id=".urlencode($row['id'])."' class='btn delete' onclick='return confirm(\"Confirma exclusão?\")'>Excluir</a>
                    </td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</main>

<footer>
    &copy; <?= date('Y') ?> Sistema de Controle de Materiais de Informática
</footer>
</body>
</html>
