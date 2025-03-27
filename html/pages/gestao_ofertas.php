<?php
// Inicia a sessão caso não esteja ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Inclui a conexão com o banco de dados
require_once '../database/mysqli.php';

$toast_message = "";
if (isset($_SESSION['toast_message'])) {
    $toast_message = $_SESSION['toast_message'];
    unset($_SESSION['toast_message']);
}
// Verifica se a conexão foi bem-sucedida
if (!$conn) {
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}

// Definição de abreviações para os cursos
$curso_abreviacoes = [
    "Técnico(a) de Multimédia" => "Multimédia",
    "Técnico(a) de Turismo" => "Turismo",
    "Técnico(a) de Gestão e Programação de Sistemas Informáticos" => "TGPSI"
];

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
    <title>Gestão de Ofertas</title>
</head>
<body>
    <?php if (!empty($toast_message)) : ?>
        <div id="toast" class="toast-message toast-success">
            <?= htmlspecialchars($toast_message) ?>
        </div>
    <?php endif; ?>
    <div class="users-container">
        <h2 class="users-header">Gestão de Ofertas</h2>

        <!-- Filtro de Cursos -->
        <div class="filter-buttons">
            <?php 
            $first = true;
            while ($curso = $cursos->fetch_assoc()) { 
                $abreviacao = $curso_abreviacoes[$curso['nome']] ?? $curso['nome']; 
            ?>
                <button class="filter-btn <?php echo $first ? 'active' : ''; ?>" 
                        onclick="showSection('curso_<?php echo $curso['id_curso']; ?>', this)">
                    <i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($abreviacao); ?>
                </button>
                <?php $first = false; ?>
            <?php } ?>
        </div>

        <?php
        $cursos->data_seek(0); // Reset para reutilizar
        $first = true;
        while ($curso = $cursos->fetch_assoc()) {
        ?>
            <div id="curso_<?php echo $curso['id_curso']; ?>" class="section <?php echo $first ? 'active' : ''; ?>">
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
    <a href='admin_dashboard.php?page=editar_oferta&id=<?php echo $oferta['id_oferta']; ?>' class="edit">
        <i class="fas fa-pen-to-square action-icon"></i> Editar
    </a>
    <a href='pages/excluir_oferta.php?id=<?php echo $oferta['id_oferta']; ?>' class="delete">
        <i class="fas fa-trash action-icon"></i> Excluir
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
        <?php 
        $first = false;
        } 
        ?>
    </div>

    <script>
        function showSection(sectionId, btn) {
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(sectionId).classList.add('active');

            // Remover classe ativa dos botões e ativar o clicado
            document.querySelectorAll('.filter-btn').forEach(button => {
                button.classList.remove('active');
            });
            btn.classList.add('active');
        }

        document.addEventListener("DOMContentLoaded", function() {
            const firstSection = document.querySelector('.section.active');
            if (!firstSection) {
                document.querySelector('.section').classList.add('active');
            }
        });
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const toast = document.getElementById("toast");
        if (toast) {
            setTimeout(() => {
                toast.style.display = "none";
            }, 4000); // 4 segundos
        }
    });
</script>

</body>
</html>
