<?php
// Habilitar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurações do banco de dados
$servername = "localhost";
$username = "root"; // Substitua pelo seu usuário
$password = ""; // Substitua pela sua senha
$dbname = "hospital_db"; // Nome do seu banco de dados

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$errors = [];
$leitosOcupados = 4; // Total de leitos
$leitosOcupadosCount = 0;

// Verifica a contagem de pacientes
$result = $conn->query("SELECT COUNT(*) AS total FROM Pacientes");
if ($result) {
    $row = $result->fetch_assoc();
    $leitosOcupadosCount = $row['total'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar os dados
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $idade = $_POST['idade']; 
    $peso = $_POST['peso']; 
    $medicamentos = [
        ['id' => $_POST['medicamento1'], 'dose' => $_POST['dose1'], 'dias' => $_POST['dias1']],
        ['id' => $_POST['medicamento2'], 'dose' => $_POST['dose2'], 'dias' => $_POST['dias2']],
    ];

    // Validações
    if ($leitosOcupadosCount >= $leitosOcupados) {
        $errors[] = "Todos os leitos estão ocupados!";
    }

    if (strlen($cpf) != 11 || !ctype_digit($cpf)) {
        $errors[] = "O CPF deve conter 11 dígitos.";
    }

    $result = $conn->query("SELECT * FROM Pacientes WHERE cpf = '$cpf'");
    if ($result && $result->num_rows > 0) {
        $errors[] = "Paciente já está internado.";
    }

    // Validar medicamentos
    $tipo1 = null;
    if (!empty($medicamentos[0]['id'])) {
        $result1 = $conn->query("SELECT tipo FROM Estoque WHERE id = " . $medicamentos[0]['id']);
        if ($result1) {
            $tipo1 = $result1->fetch_assoc()['tipo'];
        }
    }

    if (!empty($medicamentos[1]['id'])) {
        $result2 = $conn->query("SELECT tipo FROM Estoque WHERE id = " . $medicamentos[1]['id']);
        if ($result2) {
            $tipo2 = $result2->fetch_assoc()['tipo'];
            if ($tipo1 === $tipo2) {
                $errors[] = "Os medicamentos precisam ser de tipos diferentes.";
            }
        }
    }

    // Verificar estoque
    foreach ($medicamentos as $med) {
        if (!empty($med['id'])) {
            $estoque = $conn->query("SELECT qtdDoses FROM Estoque WHERE id = " . $med['id'])->fetch_assoc()['qtdDoses'];
            $totalDoses = $med['dose'] * $med['dias'];
            if ($estoque < $totalDoses) {
                $errors[] = "Estoque insuficiente para o medicamento ID " . $med['id'];
            }
        }
    }

    // Se não houver erros, insere os dados
    if (empty($errors)) {
        $conn->query("INSERT INTO Pacientes (nome, cpf, idade, peso) VALUES ('$nome', '$cpf', $idade, $peso)");
        $paciente_id = $conn->insert_id;

        foreach ($medicamentos as $med) {
            if (!empty($med['id'])) {
                $conn->query("INSERT INTO Medicamentos (paciente_id, medicamento_id, doseDiaria, diasTratamento) VALUES ($paciente_id, " . $med['id'] . ", " . $med['dose'] . ", " . $med['dias'] . ")");
                $conn->query("UPDATE Estoque SET qtdDoses = qtdDoses - (" . $med['dose'] . " * " . $med['dias'] . ") WHERE id = " . $med['id']);
            }
        }

        echo "<p>Paciente cadastrado com sucesso!</p>";
        exit; // Evitar que o formulário seja processado novamente
    }
}

// Fechar conexão no final
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Pacientes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        h3 {
            margin-top: 20px;
            color: #333;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .success {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Cadastro de Pacientes</h1>

    <form method="POST">
        <label for="nome">Nome: </label>
        <input type="text" id="nome" name="nome" required />

        <label for="cpf">CPF: </label>
        <input type="text" id="cpf" name="cpf" required />

        <label for="idade">Idade: </label>
        <input type="number" id="idade" name="idade" required />

        <label for="peso">Peso: </label>
        <input type="number" step="0.01" id="peso" name="peso" required />

        <h3>Medicamentos</h3>

        <div>
            <label for="medicamento1">Medicamento 1: </label>
            <select name="medicamento1" required>
                <option value="">Selecione</option>
                <?php 
                $conn = new mysqli($servername, $username, $password, $dbname);
                $result = $conn->query("SELECT * FROM Estoque");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['tipo'] . "</option>";
                }
                $conn->close();
                ?>
            </select>
            Dose Diária: <input type="number" name="dose1" required />
            Dias: <input type="number" name="dias1" required />
        </div>

        <div>
            <label for="medicamento2">Medicamento 2: </label>
            <select name="medicamento2">
                <option value="">Selecione</option>
                <?php 
                $conn = new mysqli($servername, $username, $password, $dbname);
                $result = $conn->query("SELECT * FROM Estoque");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['tipo'] . "</option>";
                }
                $conn->close();
                ?>
            </select>
            Dose Diária: <input type="number" name="dose2" />
            Dias: <input type="number" name="dias2" />
        </div>

        <input type="submit" value="Cadastrar" />
    </form>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?> 
</body>
</html>
