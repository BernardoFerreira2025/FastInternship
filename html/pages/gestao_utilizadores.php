<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../database/mysqli.php';

// Verifica se há mensagem de toast na sessão
$toast_message = "";
if (isset($_SESSION['toast_message'])) {
    $toast_message = $_SESSION['toast_message'];
    unset($_SESSION['toast_message']); // Remove a mensagem da sessão após exibição
}

// Consulta para obter empresas
$empresas = $conn->query("SELECT id_empresas, nome_empresa, email, telefone, responsavel, morada, cod_postal, Localidade, foto, id_curso FROM empresas");

// Consulta para obter alunos
$alunos = $conn->query("SELECT id_aluno, Nome, Email, id_curso, Turma, Curriculo, foto FROM alunos");

// Consulta para obter professores
$professores = $conn->query("SELECT id_professor, nome, email, id_curso, foto FROM professores");
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/allcss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Gestão de Utilizadores</title>
</head>
<body>
    <!-- Toast de sucesso -->
    <?php if (!empty($toast_message)) { ?>
        <div id="toast" class="toast-message toast-success"><?php echo htmlspecialchars($toast_message); ?></div>
    <?php } ?>

    <div class="users-container">
        <h2 class="users-header">Gestão de Utilizadores</h2>

        <!-- Filtro de Seções -->
        <div class="filter-buttons">
            <button class="filter-btn" onclick="showSection('alunos')"><i class="fas fa-user-graduate"></i> Alunos</button>
            <button class="filter-btn" onclick="showSection('professores')"><i class="fas fa-chalkboard-teacher"></i> Professores</button>
            <button class="filter-btn" onclick="showSection('empresas')"><i class="fas fa-building"></i> Empresas</button>
        </div>

        <!-- Seção Alunos -->
        <div id="alunos" class="section active">
            <h2 class="section-header">Alunos</h2>
            <div class="users-grid">
                <?php while ($aluno = $alunos->fetch_assoc()) { ?>
                    <div class="user-card">
                        <div class="profile-pic-container">
                            <img src="<?php echo !empty($aluno['foto']) ? '../images/'.$aluno['foto'] : '../images/student_default.png'; ?>" alt="Foto do Aluno">
                        </div>
                        <h3><?php echo htmlspecialchars($aluno['Nome']); ?></h3>
                        <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?php echo htmlspecialchars($aluno['Email']); ?></p>
                        <p><i class="fas fa-users"></i> <strong>Turma:</strong> <?php echo htmlspecialchars($aluno['Turma']); ?></p>
                        
                        <!-- Link para ver o currículo -->
                        <p><i class="fas fa-file-pdf"></i> <strong>Currículo:</strong> 
                            <?php if (!empty($aluno['Curriculo'])): ?>
                                <a href="../uploads/<?php echo htmlspecialchars($aluno['Curriculo']); ?>" target="_blank" class="view-cv">Ver Currículo</a>
                            <?php else: ?>
                                <span class="no-cv">Nenhum currículo enviado</span>
                            <?php endif; ?>
                        </p>

                        <!-- Formulário para alterar o currículo -->
                        <form action="pages/atualizar_curriculo.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id_aluno" value="<?php echo $aluno['id_aluno']; ?>">
                            <input type="file" name="novo_curriculo" accept=".pdf" required>
                            <button type="submit" class="btn-upload">Alterar Currículo</button>
                        </form>

                        <div class="user-actions">
                            <a href='pages/editar_aluno.php?id=<?php echo $aluno['id_aluno']; ?>' class="edit"><i class="fas fa-edit"></i> Editar</a>
                            <a href='pages/excluir_aluno.php?id=<?php echo $aluno['id_aluno']; ?>' class="delete"><i class="fas fa-trash"></i> Excluir</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Seção Empresas -->
        <div id="empresas" class="section">
            <h2 class="section-header">Empresas</h2>
            <div class="users-grid">
                <?php while ($empresa = $empresas->fetch_assoc()) { ?>
                    <div class="user-card">
                        <div class="profile-pic-container">
                            <img src="<?php echo !empty($empresa['foto']) ? '../images/'.$empresa['foto'] : '../images/company_default.png'; ?>" alt="Foto da Empresa">
                        </div>
                        <h3><?php echo htmlspecialchars($empresa['nome_empresa']); ?></h3>
                        <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?php echo htmlspecialchars($empresa['email']); ?></p>
                        <p><i class="fas fa-phone"></i> <strong>Telefone:</strong> <?php echo htmlspecialchars($empresa['telefone']); ?></p>
                        <p><i class="fas fa-map-marker-alt"></i> <strong>Localidade:</strong> <?php echo htmlspecialchars($empresa['Localidade']); ?></p>

                        <div class="user-actions">
                            <a href='pages/editar_empresa.php?id=<?php echo $empresa['id_empresas']; ?>' class="edit"><i class="fas fa-edit"></i> Editar</a>
                            <a href='pages/excluir_empresa.php?id=<?php echo $empresa['id_empresas']; ?>' class="delete"><i class="fas fa-trash"></i> Excluir</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Seção Professores -->
        <div id="professores" class="section">
            <h2 class="section-header">Professores</h2>
            <div class="users-grid">
                <?php while ($professor = $professores->fetch_assoc()) { ?>
                    <div class="user-card">
                        <div class="profile-pic-container">
                            <img src="<?php echo !empty($professor['foto']) ? '../images/'.$professor['foto'] : '../images/professor_default.png'; ?>" alt="Foto do Professor">
                        </div>
                        <h3><?php echo htmlspecialchars($professor['nome']); ?></h3>
                        <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?php echo htmlspecialchars($professor['email']); ?></p>

                        <div class="user-actions">
                            <a href='pages/editar_professor.php?id=<?php echo $professor['id_professor']; ?>' class="edit"><i class="fas fa-edit"></i> Editar</a>
                            <a href='pages/excluir_professor.php?id=<?php echo $professor['id_professor']; ?>' class="delete"><i class="fas fa-trash"></i> Excluir</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.section').forEach(section => section.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');
        }

        // Exibir Toast ao carregar a página se houver mensagem
        document.addEventListener("DOMContentLoaded", function() {
            let toast = document.getElementById("toast");
            if (toast) {
                setTimeout(function() {
                    toast.style.display = "none";
                }, 4000);
            }
        });
    </script>
</body>
</html>
