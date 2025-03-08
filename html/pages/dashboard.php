<?php
// Conexão com o banco de dados
include '../database/mysqli.php';

// Verifica se foi enviado um filtro de curso
$cursoSelecionado = isset($_GET['curso']) ? $_GET['curso'] : '';

// Consulta para obter as ofertas com base no filtro
if ($cursoSelecionado) {
    $query = "SELECT id_oferta, titulo, descricao, vagas, curso_relacionado, data_inicio, data_fim 
              FROM ofertas_empresas 
              WHERE curso_relacionado = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $cursoSelecionado);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT id_oferta, titulo, descricao, vagas, curso_relacionado, data_inicio, data_fim FROM ofertas_empresas";
    $result = $conn->query($query);
}

// Consulta para obter os cursos disponíveis
$cursosQuery = "SELECT DISTINCT curso_relacionado FROM ofertas_empresas";
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

        <!-- Filtro por Curso -->
        <form method="GET" class="filter-form">
            <label for="curso">Filtrar por Curso:</label>
            <select name="curso" id="curso" onchange="this.form.submit()">
                <option value="">Todos os Cursos</option>
                <?php
                if ($cursosResult->num_rows > 0) {
                    while ($curso = $cursosResult->fetch_assoc()) {
                        $selected = ($curso['curso_relacionado'] === $cursoSelecionado) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($curso['curso_relacionado']) . "' $selected>" . htmlspecialchars($curso['curso_relacionado']) . "</option>";
                    }
                }
                ?>
            </select>
        </form>

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
                    echo "<p><strong>Curso Relacionado:</strong> " . htmlspecialchars($row['curso_relacionado']) . "</p>";
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
    // Exibe o pop-up se houver mensagem na sessão
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
        unset($_SESSION['mensagem_sucesso']); // Remove a mensagem após exibir
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
        unset($_SESSION['mensagem_erro']); // Remove a mensagem após exibir
    }
    ?>
</body>
</html>
