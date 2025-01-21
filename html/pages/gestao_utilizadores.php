<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Inicia a sessão apenas se nenhuma sessão estiver ativa
}
include 'C:/xampp/htdocs/pap/database/mysqli.php';

// Consulta para obter as empresas
$empresas = $conn->query("SELECT id_empresas, nome_empresa, email, telefone, responsavel, morada, cod_postal, Localidade FROM empresas");

// Consulta para obter os alunos
$alunos = $conn->query("SELECT id_aluno, Nome, Email, Curso, Turma FROM alunos");
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Utilizadores</title>
    <link rel="stylesheet" href="../assets/css/allcss.css">
</head>
<body>
    <div class="users-container">
        <h1 class="users-header">Gerir Utilizadores</h1>

        <!-- Filtro -->
        <div class="filter-buttons">
            <button onclick="showSection('empresas')">Empresas</button>
            <button onclick="showSection('alunos')">Alunos</button>
        </div>

        <!-- Section de Empresas -->
        <div id="empresas" class="section active">
            <h2 class="section-header">Empresas</h2>
            <div class="users-grid">
                <?php
                if ($empresas->num_rows > 0) {
                    while ($empresa = $empresas->fetch_assoc()) {
                        echo "<div class='user-card'>";
                        echo "<h3>" . htmlspecialchars($empresa['nome_empresa']) . "</h3>";
                        echo "<p><strong>Responsável:</strong> " . htmlspecialchars($empresa['responsavel']) . "</p>"; // Exibindo responsável
                        echo "<p><strong>Email:</strong> " . htmlspecialchars($empresa['email']) . "</p>";
                        echo "<p><strong>Telefone:</strong> " . htmlspecialchars($empresa['telefone']) . "</p>"; // Exibindo telefone
                        echo "<p><strong>Morada:</strong> " . htmlspecialchars($empresa['morada']) . "</p>";
                        echo "<p><strong>Código Postal:</strong> " . htmlspecialchars($empresa['cod_postal']) . "</p>";
                        echo "<p><strong>Localidade:</strong> " . htmlspecialchars($empresa['Localidade']) . "</p>";
                        echo "<div class='user-actions'>";
                        echo "<a href='pages/editar_empresa.php?id=" . $empresa['id_empresas'] . "'>Editar</a>";
                        echo "<a href='pages/excluir_empresa.php?id=" . $empresa['id_empresas'] . "'>Excluir</a>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Nenhuma empresa encontrada.</p>";
                }
                 ?>
            </div>
        </div>


        <!-- Section de Alunos -->
        <div id="alunos" class="section">
            <h2 class="section-header">Alunos</h2>
            <div class="users-grid">
                <?php
                if ($alunos->num_rows > 0) {
                    while ($aluno = $alunos->fetch_assoc()) {
                        echo "<div class='user-card'>";
                        echo "<h3>" . htmlspecialchars($aluno['Nome']) . "</h3>";
                        echo "<p><strong>Email:</strong> " . htmlspecialchars($aluno['Email']) . "</p>";
                        echo "<p><strong>Curso:</strong> " . htmlspecialchars($aluno['Curso']) . "</p>";
                        echo "<p><strong>Turma:</strong> " . htmlspecialchars($aluno['Turma']) . "</p>";
                        echo "<div class='user-actions'>";
                        echo "<a href='pages/editar_aluno.php?id=" . $aluno['id_aluno'] . "'>Editar</a>";
                        echo "<a href='pages/excluir_aluno.php?id=" . $aluno['id_aluno'] . "'>Excluir</a>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Nenhum aluno encontrado.</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <?php
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


    <script>
        function showSection(sectionId) {
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(sectionId).classList.add('active');
        }
    </script>
</body>
</html>
