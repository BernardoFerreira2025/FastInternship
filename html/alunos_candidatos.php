<?php
include '../database/mysqli.php';

$oferta_id = $_GET['oferta_id'];
$query = "SELECT alunos.nome, alunos.email FROM candidaturas 
          JOIN alunos ON candidaturas.aluno_id = alunos.id_aluno 
          WHERE candidaturas.oferta_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $oferta_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos</title>
    <link rel="stylesheet" href="assets/css/allcss.css">
</head>
<body>
    <h1>Candidatos da Oferta</h1>
    <div class="candidates-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<p><strong>Nome:</strong> " . htmlspecialchars($row['nome']) . "<br>";
                echo "<strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
            }
        } else {
            echo "<p>Nenhum candidato encontrado.</p>";
        }
        ?>
    </div>
</body>
</html>
        