<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../database/mysqli.php';

// Verifica se o usuário está logado e é uma empresa
if (!isset($_SESSION['id_empresas'])) {
    $_SESSION['error'] = "Sessão inválida. Faça login novamente.";
    header("Location: ../formlogin.php");
    exit();
}

$id_empresas = $_SESSION['id_empresas'];

// Consulta para as ofertas associadas SÓ à empresa logada
$queryOfertas = "
    SELECT 
        ofertas_empresas.*, 
        empresas.nome_empresa, 
        empresas.responsavel,
        cursos.nome AS curso_nome
    FROM 
        ofertas_empresas
    INNER JOIN 
        empresas ON ofertas_empresas.id_empresa = empresas.id_empresas
    INNER JOIN 
        cursos ON ofertas_empresas.id_curso = cursos.id_curso
    WHERE 
        ofertas_empresas.id_empresa = ?
";

$stmtOfertas = $conn->prepare($queryOfertas);
$stmtOfertas->bind_param("i", $id_empresas);
$stmtOfertas->execute();
$resultOfertas = $stmtOfertas->get_result();
?>

<div class="oferta-container">
    <h2 class="oferta-titulo">Gerir Ofertas Publicadas</h2>

    <!-- Grade de Ofertas -->
    <div class="oferta-grid">
    <?php
    if ($resultOfertas->num_rows > 0) {
        while ($oferta = $resultOfertas->fetch_assoc()) {
            $data_inicio = date('d-m-Y', strtotime($oferta['data_inicio']));
            $data_fim = date('d-m-Y', strtotime($oferta['data_fim']));
            // Abreviação TGPSI
            $curso_nome = ($oferta['curso_nome'] === 'Técnico(a) de Gestão e Programação de Sistemas Informáticos') ? 'TGPSI' : htmlspecialchars($oferta['curso_nome']);

            echo "<div class='oferta-card'>";
            echo "<h3><i class='fas fa-briefcase'></i> " . htmlspecialchars($oferta['titulo']) . "</h3>";
            echo "<p><i class='fas fa-user-tie'></i> <strong>Responsável:</strong> " . htmlspecialchars($oferta['responsavel']) . "</p>";
            echo "<p><i class='fas fa-align-left'></i> <strong>Descrição:</strong> " . htmlspecialchars($oferta['descricao']) . "</p>";
            echo "<p><i class='fas fa-calendar-alt'></i> <strong>Data Início:</strong> " . $data_inicio . "</p>";
            echo "<p><i class='fas fa-calendar-check'></i> <strong>Data Fim:</strong> " . $data_fim . "</p>";
            echo "<p><i class='fas fa-users'></i> <strong>Vagas:</strong> " . htmlspecialchars($oferta['vagas']) . "</p>";
            echo "<p><i class='fas fa-graduation-cap'></i> <strong>Curso:</strong> " . $curso_nome . "</p>";

            echo "<div class='oferta-actions'>";
            echo "<a href='empresa_dashboard.php?page=editar_oferta&id=" . $oferta['id_oferta'] . "' class='btn-editar'>";
            echo "<i class='fas fa-pen-to-square'></i> Editar</a>";
            echo "<a href='pagesempresas/excluir_oferta.php?id=" . $oferta['id_oferta'] . "' class='btn-excluir'>";
            echo "<i class='fas fa-trash'></i> Excluir</a>";
            echo "</div>";

            echo "</div>";
        }
    } else {
        echo "<p style='color: yellow;'>Nenhuma oferta publicada pela sua empresa.</p>";
    }
    ?>
    </div>
</div>

<?php
// Exibe mensagens de sucesso ou erro
if (isset($_SESSION['mensagem_sucesso'])) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.createElement('div');
                toast.className = 'toast-message toast-success';
                toast.textContent = '" . addslashes($_SESSION['mensagem_sucesso']) . "';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            });
          </script>";
    unset($_SESSION['mensagem_sucesso']);
}

if (isset($_SESSION['mensagem_erro'])) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.createElement('div');
                toast.className = 'toast-message toast-error';
                toast.textContent = '" . addslashes($_SESSION['mensagem_erro']) . "';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            });
          </script>";
    unset($_SESSION['mensagem_erro']);
}
?>
