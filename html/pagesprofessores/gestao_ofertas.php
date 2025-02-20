<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'C:/xampp/htdocs/pap/database/mysqli.php';

// Verifica se o usuário está logado e é um professor
if (!isset($_SESSION['id_professor'])) {
    $_SESSION['error'] = "Sessão inválida. Faça login novamente.";
    header("Location: ../formlogin.php");
    exit();
}

$id_professor = $_SESSION['id_professor'];

// Obtém o curso do professor logado
$query = "SELECT id_curso FROM professores WHERE id_professor = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_professor);
$stmt->execute();
$result = $stmt->get_result();
$professor = $result->fetch_assoc();
$id_curso = $professor['id_curso'] ?? null;

if (!$id_curso) {
    die("<p>Erro: O professor não tem um curso associado.</p>");
}

// Consulta para obter as ofertas do curso do professor
$queryOfertas = "
    SELECT 
        ofertas_empresas.*, 
        empresas.nome_empresa, 
        empresas.responsavel 
    FROM 
        ofertas_empresas
    INNER JOIN 
        empresas 
    ON 
        ofertas_empresas.id_empresa = empresas.id_empresas
    WHERE 
        ofertas_empresas.id_curso = ?
";

$stmtOfertas = $conn->prepare($queryOfertas);
$stmtOfertas->bind_param("i", $id_curso);
$stmtOfertas->execute();
$resultOfertas = $stmtOfertas->get_result();
?>

<div class="form-background">
    <div class="form-wrapper">
        <h1 class="users-header">Gerir Ofertas Publicadas</h1>

        <!-- Grade de Ofertas -->
        <div class="users-grid">
            <?php
            if ($resultOfertas->num_rows > 0) {
                while ($oferta = $resultOfertas->fetch_assoc()) {
                    echo "<div class='user-card'>";
                    echo "<h3>" . htmlspecialchars($oferta['titulo']) . "</h3>";
                    echo "<p><strong>Empresa:</strong> " . htmlspecialchars($oferta['nome_empresa']) . "</p>";
                    echo "<p><strong>Responsável:</strong> " . htmlspecialchars($oferta['responsavel']) . "</p>";
                    echo "<p><strong>Descrição:</strong> " . htmlspecialchars($oferta['descricao']) . "</p>";
                    echo "<p><strong>Data Início:</strong> " . htmlspecialchars($oferta['data_inicio']) . "</p>";
                    echo "<p><strong>Data Fim:</strong> " . htmlspecialchars($oferta['data_fim']) . "</p>";
                    echo "<p><strong>Vagas:</strong> " . htmlspecialchars($oferta['vagas']) . "</p>";
                    echo "<div class='user-actions'>";
                    echo "<a href='pagesprofessores/editar_oferta.php?id=" . $oferta['id_oferta'] . "'>Editar</a>";
                    echo "<a href='pagesprofessores/excluir_oferta.php?id=" . $oferta['id_oferta'] . "'>Excluir</a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>Nenhuma oferta encontrada para o seu curso.</p>";
            }
            ?>
        </div>
    </div>
</div>

<?php
// Exibe pop-up se houver mensagem na sessão
if (isset($_SESSION['mensagem_sucesso'])) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.createElement('div');
                toast.className = 'toast-success';
                toast.textContent = '" . $_SESSION['mensagem_sucesso'] . "';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            });
          </script>";
    unset($_SESSION['mensagem_sucesso']); 
}
?>
