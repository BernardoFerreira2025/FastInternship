<?php
// Conexão com o banco de dados
include '../database/mysqli.php';
if (session_status() === PHP_SESSION_NONE) 
session_start(); // Certifique-se de iniciar a sessão

// Verifica se foi enviado um filtro de curso
$cursoSelecionado = isset($_GET['curso']) ? $_GET['curso'] : '';

// Consulta para obter as ofertas com base no filtro
if ($cursoSelecionado) {
    $query = "SELECT id_oferta, titulo, descricao, vagas, id_curso, data_inicio, data_fim 
              FROM ofertas_empresas 
              WHERE id_curso = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cursoSelecionado);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT id_oferta, titulo, descricao, vagas, id_curso, data_inicio, data_fim FROM ofertas_empresas";
    $result = $conn->query($query);
}

// Consulta para obter os cursos disponíveis
$cursosQuery = "SELECT DISTINCT id_curso FROM ofertas_empresas";
$cursosResult = $conn->query($cursosQuery);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle</title>
    <link rel="stylesheet" href="../assets/css/allcss.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Título do Painel -->
        <h1 class="dashboard-header">Controlo das Candidaturas</h1>

        <!-- Seção de Ofertas Publicadas -->
        <div class="offers-section">
            <?php
            if ($result->num_rows > 0) {
                // Itera pelas ofertas e exibe como cartões
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='offer-card'>";
                    echo "<h3>" . htmlspecialchars($row['titulo']) . "</h3>";
                    echo "<p><strong>Descrição:</strong> " . htmlspecialchars($row['descricao']) . "</p>";
                    echo "<p><strong>Vagas:</strong> " . htmlspecialchars($row['vagas']) . "</p>";
                    
                    // Substituir id_curso pelo nome do curso real
                    $cursoNomeQuery = "SELECT nome FROM cursos WHERE id_curso = ?";
                    $stmtCurso = $conn->prepare($cursoNomeQuery);
                    $stmtCurso->bind_param("i", $row['id_curso']);
                    $stmtCurso->execute();
                    $cursoNomeResult = $stmtCurso->get_result();
                    $cursoNome = $cursoNomeResult->fetch_assoc()['nome'] ?? 'Curso não encontrado';

                    echo "<p><strong>Curso Relacionado:</strong> " . htmlspecialchars($cursoNome) . "</p>";
                    echo "<p><strong>Início:</strong> " . htmlspecialchars($row['data_inicio']) . "</p>";
                    echo "<p><strong>Fim:</strong> " . htmlspecialchars($row['data_fim']) . "</p>";
                    echo "<a href='alunos_candidatos.php?oferta_id=" . $row['id_oferta'] . "' class='btn-view'>Ver Candidatos</a>";
                    echo "</div>";
                }
            } else {
                echo "<p>Nenhuma oferta publicada.</p>";
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
                    toast.className = 'toast-success';
                    toast.textContent = '" . addslashes($_SESSION['mensagem_sucesso']) . "';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 5000);
                });
              </script>";
        unset($_SESSION['mensagem_sucesso']);
    }

    if (isset($_SESSION['mensagem_erro'])) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    const toast = document.createElement('div');
                    toast.className = 'toast-error';
                    toast.textContent = '" . addslashes($_SESSION['mensagem_erro']) . "';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 5000);
                });
              </script>";
        unset($_SESSION['mensagem_erro']);
    }
    ?>
</body>
</html>
