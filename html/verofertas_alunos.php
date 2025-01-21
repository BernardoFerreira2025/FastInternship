<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Ofertas - Aluno</title>
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/elements/header.php">
</head>
<body>
    <!-- Container Principal -->
    <div class="offers-container">
        <h1 class="title">Ofertas Disponíveis</h1>
        <p class="subtitle">Explore as oportunidades disponíveis e candidate-se às que mais interessam a você!</p>

        <!-- Filtro -->
        <div class="filter-bar">
            <input type="text" placeholder="Pesquisar ofertas..." class="search-input">
            <select class="filter-select">
                <option value="">Filtrar por área</option>
                <option value="TI">Tecnologia da Informação</option>
                <option value="Design">Design</option>
                <option value="Marketing">Marketing</option>
                <option value="Engenharia">Engenharia</option>
            </select>
            <button class="btn-filter">Filtrar</button>
        </div>

        <!-- Grade de Ofertas -->
        <div class="offers-grid">
            <!-- Card de Exemplo de Oferta -->
            <div class="offer-card">
                <h2 class="offer-title">Desenvolvedor Web</h2>
                <p class="offer-company">Empresa: Tech Solutions</p>
                <p class="offer-location"><i class="fas fa-map-marker-alt"></i> Lisboa</p>
                <p class="offer-description">Trabalhe em projetos inovadores de desenvolvimento web com uma equipe dinâmica.</p>
                <button class="btn-apply">Candidatar-se</button>
            </div>

            <div class="offer-card">
                <h2 class="offer-title">Designer Gráfico</h2>
                <p class="offer-company">Empresa: Creative Studio</p>
                <p class="offer-location"><i class="fas fa-map-marker-alt"></i> Porto</p>
                <p class="offer-description">Participe na criação de artes digitais e design visual para campanhas publicitárias.</p>
                <button class="btn-apply">Candidatar-se</button>
            </div>

            <!-- Adicione mais cards dinamicamente -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
