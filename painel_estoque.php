<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Painel de Estoque - Sistema de Controle de Materiais</title>
<style>
/* RESET E ESTILO GERAL */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: #f0f2f5;
}

header {
    background: #0046ff;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 100;
}

header h1 {
    margin: 0;
    font-size: 20px;
}

nav a {
    color: white;
    margin-left: 15px;
    text-decoration: none;
    font-weight: 600;
}

nav a:hover {
    text-decoration: underline;
}

.container {
    max-width: 1200px;
    margin: 30px auto;
    padding: 0 20px;
}

.section-title {
    font-size: 24px;
    font-weight: bold;
    color: #0046ff;
    text-align: center;
    margin-bottom: 20px;
}

/* GRID DE CARTÕES */
.grid-items {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
}

.card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    text-align: center;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.2);
}

.card-icon {
    font-size: 40px;
    margin-bottom: 10px;
    color: #0046ff;
}

.card-label {
    font-weight: bold;
    font-size: 16px;
    color: #333;
}

/* RESPONSIVO */
@media(max-width:600px){
    header {
        flex-direction: column;
        align-items: flex-start;
    }
    nav {
        margin-top: 10px;
    }
}
</style>
</head>
<body>

<header>
    <h1>Painel de Estoque</h1>
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
    <div class="section-title">Estoque</div>
    <div class="grid-items">
        <div class="card" tabindex="0">
            <div class="card-icon">⌨️</div>
            <div class="card-label">Teclados</div>
        </div>
        <div class="card" tabindex="0">
            <div class="card-icon">📽️</div>
            <div class="card-label">Projetores</div>
        </div>
        <div class="card" tabindex="0">
            <div class="card-icon">🔊</div>
            <div class="card-label">Caixas de Som</div>
        </div>
        <div class="card" tabindex="0">
            <div class="card-icon">🖱️</div>
            <div class="card-label">Mouse</div>
        </div>
        <div class="card" tabindex="0">
            <div class="card-icon">💻</div>
            <div class="card-label">Computadores</div>
        </div>
        <div class="card" tabindex="0">
            <div class="card-icon">🔌</div>
            <div class="card-label">Cabos</div>
        </div>
        <div class="card" tabindex="0">
            <div class="card-icon">🎧</div>
            <div class="card-label">Headset</div>
        </div>
        <div class="card" tabindex="0">
            <div class="card-icon">🖥️</div>
            <div class="card-label">Monitores</div>
        </div>
        <div class="card" tabindex="0">
            <div class="card-icon">🗄️</div>
            <div class="card-label">Gabinetes</div>
        </div>
    </div>
</div>

</body>
</html>
