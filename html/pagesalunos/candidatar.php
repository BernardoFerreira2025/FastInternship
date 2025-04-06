<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_aluno'])) {
    header("Location: formlogin.php");
    exit();
}

require_once '../database/mysqli.php';

$mapa_perguntas = [
    "experiencia" => "Tem experiência na área?",
    "conhecimentos" => "Tem conhecimentos relevantes para a vaga?",
    "trabalho_em_equipa" => "Sente-se confortável a trabalhar em equipa?"
];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Erro: ID da oferta inválido.";
    exit();
}

$id_oferta = $_GET['id'];
$id_aluno = $_SESSION['id_aluno'];

$sql_check = "SELECT id_candidatura FROM candidaturas WHERE id_aluno = ? AND id_oferta = ?";
if ($stmt = $conn->prepare($sql_check)) {
    $stmt->bind_param("ii", $id_aluno, $id_oferta);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        echo "Já se candidatou a esta oferta.";
        exit();
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $motivacao = isset($_POST['motivacao']) ? trim($_POST['motivacao']) : '';
    $respostas = isset($_POST['respostas']) ? $_POST['respostas'] : [];

    if (empty($motivacao) || empty($respostas['experiencia']) || empty($respostas['conhecimentos']) || empty($respostas['trabalho_em_equipa'])) {
        echo "Por favor, preencha todos os campos.";
        exit();
    }

    $sql_insert = "INSERT INTO candidaturas (id_aluno, id_oferta, status_professor, status_empresa, data_candidatura, carta_motivacao, respostas) 
                    VALUES (?, ?, 'pendente', 'pendente', CURRENT_TIMESTAMP, ?, ?)";
    if ($stmt = $conn->prepare($sql_insert)) {
        $respostas_json = json_encode($respostas);
        $stmt->bind_param("iiss", $id_aluno, $id_oferta, $motivacao, $respostas_json);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: aluno_dashboard.php?page=verofertas&candidatura=sucesso");
            exit();
        } else {
            echo "Erro ao inserir a candidatura: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Erro ao preparar a query: " . $conn->error;
    }
    $conn->close();
} else {
    $sql_curriculo = "SELECT curriculo FROM alunos WHERE id_aluno = ?";
    $stmt = $conn->prepare($sql_curriculo);
    $stmt->bind_param("i", $id_aluno);
    $stmt->execute();
    $stmt->bind_result($curriculo);
    $stmt->fetch();
    $stmt->close();
    ?>
<div class="candidatar-container">
    <h1>Candidatar-se à Oferta</h1>
    <form method="post" onsubmit="return validateForm()">
        <fieldset>
            <legend>Perguntas sobre Competências</legend>
            <?php foreach ($mapa_perguntas as $chave => $texto): ?>
                <label for="pergunta_<?= $chave; ?>"><?= $texto; ?></label>
                <select id="pergunta_<?= $chave; ?>" name="respostas[<?= $chave; ?>]" required>
                    <option value="">Selecione</option>
                    <option value="sim">Sim</option>
                    <option value="nao">Não</option>
                </select>
            <?php endforeach; ?>
        </fieldset>

        <fieldset>
            <legend>Currículo Disponível</legend>
            <?php if (!empty($curriculo)): ?>
                <p>O seu currículo está disponível para ser visualizado por empresas e professores.</p>
            <?php else: ?>
                <p>Ainda não enviou um currículo. Por favor, carregue-o no seu perfil.</p>
            <?php endif; ?>
        </fieldset>

        <label for="motivacao">Carta de Motivação</label>
        <textarea id="motivacao" name="motivacao" rows="4" required></textarea>

        <input type="submit" value="Candidatar-se">
    </form>
</div>
<script>
    function validateForm() {
        const experiencia = document.getElementById('pergunta1').value;
        const conhecimentos = document.getElementById('pergunta2').value;
        const disponibilidade = document.getElementById('pergunta3').value;
        const motivacao = document.getElementById('motivacao').value;

        if (!experiencia || !conhecimentos || !disponibilidade || !motivacao) {
            alert("Por favor, preencha todos os campos obrigatórios.");
            return false;
        }
        return true;
    }
</script>
    <?php
}
?>
