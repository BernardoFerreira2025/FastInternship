<?php
// Conexão com o banco de dados
include '../database/mysqli.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o professor está logado e obtém o ID do curso ao qual ele pertence
if (!isset($_SESSION['id_professor'])) {
    die("Acesso negado.");
}

$id_professor = $_SESSION['id_professor'];

// Busca o id_curso do professor logado
$queryCurso = "SELECT id_curso FROM professores WHERE id_professor = ?";
$stmtCurso = $conn->prepare($queryCurso);
$stmtCurso->bind_param("i", $id_professor);
$stmtCurso->execute();
$resultCurso = $stmtCurso->get_result();
$curso = $resultCurso->fetch_assoc();

if (!$curso) {
    die("Erro ao obter curso do professor.");
}

$id_curso = $curso['id_curso'];

// Consulta para obter apenas as ofertas do curso do professor logado
$query = "SELECT id_oferta, titulo, descricao, vagas, id_curso, data_inicio, data_fim 
          FROM ofertas_empresas 
          WHERE id_curso = ? AND data_fim >= CURDATE()";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="form-background">
    <div class="form-wrapper">
        <!-- Título do Painel -->
        <h1 class="dashboard-header">Controlo das Candidaturas</h1>

        <!-- Seção de Ofertas Publicadas -->
        <div class="offers-section">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='offer-card'>";
                    echo "<h3>" . htmlspecialchars($row['titulo']) . "</h3>";
                    echo "<p><strong>Descrição:</strong> " . htmlspecialchars($row['descricao']) . "</p>";
                    echo "<p><strong>Vagas:</strong> " . htmlspecialchars($row['vagas']) . "</p>";

                    // Obter o nome do curso
                    $cursoNomeQuery = "SELECT nome FROM cursos WHERE id_curso = ?";
                    $stmtCursoNome = $conn->prepare($cursoNomeQuery);
                    $stmtCursoNome->bind_param("i", $row['id_curso']);
                    $stmtCursoNome->execute();
                    $cursoNomeResult = $stmtCursoNome->get_result();
                    $cursoNome = $cursoNomeResult->fetch_assoc()['nome'] ?? 'Curso não encontrado';

                    echo "<p><strong>Curso Relacionado:</strong> " . htmlspecialchars($cursoNome) . "</p>";
                    echo "<p><strong>Início:</strong> " . htmlspecialchars($row['data_inicio']) . "</p>";
                    echo "<p><strong>Fim:</strong> " . htmlspecialchars($row['data_fim']) . "</p>";
                    echo "<a href='alunos_candidatos.php?oferta_id=" . $row['id_oferta'] . "' class='btn-view'>Ver Candidatos</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>Nenhuma oferta publicada para o seu curso.</p>";
            }
            ?>
        </div>
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

