<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FastInternship - Liga alunos, professores e empresas para facilitar estágios escolares.">
    <title>FastInternship - Início</title>
    <!-- CSS Global -->
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link rel="stylesheet" href="assets/elements/header.css"> 
    <link rel="stylesheet" href="assets/elements/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <?php require 'assets/elements/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content text-center">
            <h1>Bem-vindo ao <span class="highlight">FastInternship</span></h1>
            <p class="hero-description">Uma plataforma simples para ajudar alunos, professores e empresas a gerir estágios escolares.</p>
            <div class="cta-buttons mt-3">
                <button class="secondary-btn btn btn-primary" onclick="window.location.href='about.php';">Saber Mais</button>
            </div>
        </div>
    </section>

<!-- Funcionalidades -->
<section class="features py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">O que podes fazer aqui?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-user-graduate"></i></div>
                <h3>Alunos</h3>
                <p>Podem ver ofertas de estágio, candidatar-se e acompanhar o estado da candidatura.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <h3>Professores</h3>
                <p>Analisam as candidaturas submetidas pelos alunos e encaminham-nas para validação das empresas.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-building"></i></div>
                <h3>Empresas</h3>
                <p>Validam as candidaturas aprovadas pelos professores e decidem quais alunos pretendem receber.</p>
            </div>
        </div>
    </div>
</section>

    <?php include 'assets/elements/footer.php'; ?>  
</body>
</html>
