<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FastInternship - Conecte alunos, professores e empresas para criar oportunidades de estágio.">
    <title>FastInternship - Início</title>
    <!-- CSS Global -->
    <link rel="stylesheet" href="assets/css/allcss.css">
    <link rel="stylesheet" href="assets/elements/header.css"> 
    <link rel="stylesheet" href="assets/elements/footer.css">
</head>
<body>
        <?php require 'assets/elements/header.php'; ?>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content text-center">
            <h1>Bem-vindo ao <span class="highlight">FastInternship</span></h1>
            <p class="hero-description">Conectamos talentos às melhores oportunidades de estágio com suporte de professores e empresas.</p>
            <div class="cta-buttons mt-3">
                <button class="secondary-btn btn btn-primary" onclick="window.location.href='about.php';">Saiba Mais</button>
            </div>
        </div>
    </section>

    <!-- Seção Destaques -->
    <section class="features py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5">Por que escolher o FastInternship?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🚀</div>
                    <h3>Crescimento Rápido</h3>
                    <p>Encontre as oportunidades ideais para o seu futuro profissional.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Rede de Talentos</h3>
                    <p>Professores, alunos e empresas colaborando para o sucesso.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💼</div>
                    <h3>Parcerias Exclusivas</h3>
                    <p>Trabalhamos com empresas líderes para trazer as melhores ofertas.</p>
                </div>
            </div>
        </div>
    </section>
        <?php include 'assets/elements/footer.php'; ?>  
    <!-- Script do Bootstrap sem o atributo integrity e crossorigin -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>