<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../database/mysqli.php';

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

<div class="oferta-container">
    <h2 class="oferta-titulo">Gerir Ofertas Publicadas</h2>
    <div class="oferta-grid">
        <?php
        if ($resultOfertas->num_rows > 0) {
            while ($oferta = $resultOfertas->fetch_assoc()) {
                $data_inicio = date('d-m-Y', strtotime($oferta['data_inicio']));
                $data_fim = date('d-m-Y', strtotime($oferta['data_fim']));

                echo "<div class='oferta-card'>";
                echo "<h3>" . htmlspecialchars($oferta['titulo']) . "</h3>";
                echo "<p><strong>Empresa:</strong> " . htmlspecialchars($oferta['nome_empresa']) . "</p>";
                echo "<p><strong>Responsável:</strong> " . htmlspecialchars($oferta['responsavel']) . "</p>";
                echo "<p><strong>Descrição:</strong> " . htmlspecialchars($oferta['descricao']) . "</p>";
                echo "<p><strong>Data Início:</strong> " . $data_inicio . "</p>";
                echo "<p><strong>Data Fim:</strong> " . $data_fim . "</p>";
                echo "<p><strong>Vagas:</strong> " . htmlspecialchars($oferta['vagas']) . "</p>";
                echo "<div class='oferta-actions'>";
                echo "<a href='professor_dashboard.php?page=editar_oferta&id=" . $oferta['id_oferta'] . "' class='btn-editar'>";
                echo "<i class='fas fa-pen-to-square'></i> Editar</a>";
                echo "<a href='pagesprofessores/excluir_oferta.php?id=" . $oferta['id_oferta'] . "' class='btn-excluir'>";
                echo "<i class='fas fa-trash'></i> Excluir</a>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>Nenhuma oferta encontrada para o seu curso.</p>";
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
