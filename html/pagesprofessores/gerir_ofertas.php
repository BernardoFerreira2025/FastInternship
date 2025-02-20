<?php
include '../database/mysqli.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado e é um professor
$id_professor = $_SESSION['id_professor'] ?? null;

if (!$id_professor) {
    $_SESSION['error'] = "Sessão inválida. Faça login novamente.";
    header("Location: ../formlogin.php");
    exit();
}

// Obtém o curso do professor
$query = "SELECT c.id_curso, c.nome AS curso_nome 
          FROM professores p
          JOIN cursos c ON p.id_curso = c.id_curso
          WHERE p.id_professor = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_professor);
$stmt->execute();
$result = $stmt->get_result();
$professor = $result->fetch_assoc();
$id_curso = $professor['id_curso'] ?? null;
$curso_nome = $professor['curso_nome'] ?? "Curso não encontrado";

if (!$id_curso) {
    die("Erro: Curso do professor não encontrado.");
}
?>

<div class="form-background">
    <div class="form-wrapper">
        <h1>Inserir Nova Oferta</h1>

        <form action="geriroferta_handler.php" method="POST" id="ofertaForm">
            <div class="form-group">
                <label for="titulo">Título da Oferta:</label>
                <input type="text" id="titulo" name="titulo" placeholder="Ex: Nome da Empresa" required>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" placeholder="Descreva os detalhes da oferta..." required></textarea>
            </div>

            <div class="form-group">
                <label for="requisitos">Requisitos:</label>
                <textarea id="requisitos" name="requisitos" placeholder="Liste os requisitos necessários..." required></textarea>
            </div>

            <div class="form-group">
                <label for="id_empresa">Empresa:</label>
                <select id="id_empresa" name="id_empresa" required>
                    <option value="">Selecione uma empresa</option>
                    <?php
                    // Buscar empresas SOMENTE do mesmo curso do professor
                    $queryEmpresas = "SELECT id_empresas, nome_empresa FROM empresas WHERE id_curso = ?";
                    $stmtEmpresas = $conn->prepare($queryEmpresas);
                    $stmtEmpresas->bind_param("i", $id_curso);
                    $stmtEmpresas->execute();
                    $resultEmpresas = $stmtEmpresas->get_result();

                    while ($empresa = $resultEmpresas->fetch_assoc()) {
                        echo "<option value='{$empresa['id_empresas']}'>{$empresa['nome_empresa']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="vagas">Número de Vagas:</label>
                <input type="number" id="vagas" name="vagas" min="1" placeholder="Ex: 5" required>
            </div>

            <div class="form-group">
                <label for="curso_relacionado">Curso Relacionado:</label>
                <input type="text" id="curso_relacionado" name="curso_relacionado" value="<?php echo $curso_nome; ?>" readonly>
            </div>

            <div class="form-group">
                <label for="data_inicio">Data de Início:</label>
                <input type="date" id="data_inicio" name="data_inicio" required>
            </div>

            <div class="form-group">
                <label for="data_fim">Data de Fim:</label>
                <input type="date" id="data_fim" name="data_fim" required>
            </div>

            <button type="submit" class="btn-submit">Salvar Oferta</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('ofertaForm').addEventListener('submit', function (event) {
        const dataInicio = new Date(document.getElementById('data_inicio').value);
        const dataFim = new Date(document.getElementById('data_fim').value);
        const titulo = document.getElementById('titulo').value.trim();
        const descricao = document.getElementById('descricao').value.trim();
        const requisitos = document.getElementById('requisitos').value.trim();
        const idEmpresa = document.getElementById('id_empresa').value.trim();
        const vagas = document.getElementById('vagas').value.trim();

        if (!titulo || !descricao || !requisitos || !idEmpresa || !vagas) {
            showToast('Por favor, preencha todos os campos.', 'error');
            event.preventDefault();
            return;
        }

        if (dataFim < dataInicio) {
            showToast('Tente Novamente, verifique as datas.', 'error');
            event.preventDefault();
            return;
        }
    });

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast-${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }
</script>
