<?php
// Inicia a sessão caso não esteja ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclui a conexão com o banco de dados
include '../database/mysqli.php';

// Verifica se a conexão foi bem-sucedida
if (!$conn) {
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}

// Obtém os cursos disponíveis
$cursos = $conn->query("SELECT id_curso, nome FROM cursos");
if (!$cursos) {
    die("Erro na consulta de cursos: " . $conn->error);
}

// Obtém as ofertas disponíveis
$ofertas = $conn->query("SELECT ofertas_empresas.id_oferta, ofertas_empresas.titulo, ofertas_empresas.descricao, ofertas_empresas.vagas, 
                                cursos.id_curso, cursos.nome AS curso, empresas.nome_empresa 
                         FROM ofertas_empresas 
                         INNER JOIN cursos ON ofertas_empresas.id_curso = cursos.id_curso
                         INNER JOIN empresas ON ofertas_empresas.id_empresa = empresas.id_empresas");
if (!$ofertas) {
    die("Erro na consulta de ofertas: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="ofertas-container">
        <!-- Filtro de Cursos -->
        <div class="filter-buttons">
            <?php while ($curso = $cursos->fetch_assoc()) { ?>
                <button class="filter-btn" onclick="showSection('curso_<?php echo $curso['id_curso']; ?>')">
                    <i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($curso['nome']); ?>
                </button>
            <?php } ?>
        </div>

        <?php
        $cursos->data_seek(0); // Reset curso para reutilizar
        while ($curso = $cursos->fetch_assoc()) {
        ?>
            <div id="curso_<?php echo $curso['id_curso']; ?>" class="section">
                <h2 class="section-header">Ofertas - <?php echo htmlspecialchars($curso['nome']); ?></h2>
                <div class="users-grid">
                    <?php
                    $ofertas->data_seek(0);
                    $hasOffers = false;
                    while ($oferta = $ofertas->fetch_assoc()) {
                        if ($oferta['id_curso'] == $curso['id_curso']) {
                            $hasOffers = true;
                    ?>
                        <div class="user-card">
                            <h3><?php echo htmlspecialchars($oferta['titulo']); ?></h3>
                            <div class="card-content">
                                <p><i class="fas fa-building"></i> <strong>Empresa:</strong> <?php echo htmlspecialchars($oferta['nome_empresa']); ?></p>
                                <p><i class="fas fa-file-alt"></i> <strong>Descrição:</strong> <?php echo htmlspecialchars($oferta['descricao']); ?></p>
                                <p><i class="fas fa-users"></i> <strong>Vagas:</strong> <?php echo htmlspecialchars($oferta['vagas']); ?></p>
                            </div>
                            <div class="user-actions">
                                <a href='editar_oferta.php?id=<?php echo $oferta['id_oferta']; ?>' class="edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href='excluir_oferta.php?id=<?php echo $oferta['id_oferta']; ?>' class="delete">
                                    <i class="fas fa-trash"></i> Excluir
                                </a>
                            </div>
                        </div>
                    <?php
                        }
                    }
                    if (!$hasOffers) {
                        echo "<p class='no-data'>Nenhuma oferta encontrada para este curso.</p>";
                    }
                    ?>
                </div>
            </div>
        <?php } ?>
    </div>

    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(sectionId).classList.add('active');
        }

        document.addEventListener("DOMContentLoaded", function() {
            const firstSection = document.querySelector('.section');
            if (firstSection) {
                firstSection.classList.add('active');
            }
        });
    </script>
</body>
</html>
