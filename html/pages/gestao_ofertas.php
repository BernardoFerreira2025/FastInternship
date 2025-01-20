<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia a sessão apenas se nenhuma sessão estiver ativa
}
include 'C:/xampp/htdocs/pap/database/mysqli.php';

// Consulta para obter as ofertas junto com os dados das empresas
$ofertas_empresas = $conn->query("
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
");
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Ofertas</title>
    <link rel="stylesheet" href="../assets/css/allcss.css">
</head>
<body>
    <div class="users-container">
        <h1 class="users-header">Gerir Ofertas Publicadas</h1>

        <!-- Grade de Ofertas -->
        <div class="users-grid">
            <?php
            if ($ofertas_empresas->num_rows > 0) {
                while ($oferta = $ofertas_empresas->fetch_assoc()) {
                    echo "<div class='user-card'>";
                    echo "<h3>" . htmlspecialchars($oferta['titulo']) . "</h3>";
                    echo "<p><strong>Empresa:</strong> " . htmlspecialchars($oferta['nome_empresa']) . "</p>";
                    echo "<p><strong>Responsável:</strong> " . htmlspecialchars($oferta['responsavel']) . "</p>";
                    echo "<p><strong>Descrição:</strong> " . htmlspecialchars($oferta['descricao']) . "</p>";
                    echo "<p><strong>Data Início:</strong> " . htmlspecialchars($oferta['data_inicio']) . "</p>";
                    echo "<p><strong>Data Fim:</strong> " . htmlspecialchars($oferta['data_fim']) . "</p>";
                    echo "<p><strong>Vagas:</strong> " . htmlspecialchars($oferta['vagas']) . "</p>";
                    echo "<div class='user-actions'>";
                    echo "<a href='pages/editar_oferta.php?id=" . $oferta['id_oferta'] . "'>Editar</a>";
                    echo "<a href='pages/excluir_oferta.php?id=" . $oferta['id_oferta'] . "'>Excluir</a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>Nenhuma oferta encontrada.</p>";
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
                    toast.textContent = '" . $_SESSION['mensagem_sucesso'] . "';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                });
              </script>";
        unset($_SESSION['mensagem_sucesso']); // Remove a mensagem após exibir
    }
    ?>
</body>
</html>
