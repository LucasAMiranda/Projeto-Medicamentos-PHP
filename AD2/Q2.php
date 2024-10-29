<?php
// Exibir erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir a configuração do banco de dados
include "../banco_config.php";

// Verificar a conexão com o banco de dados
$conn = new mysqli($host, $user, $pass, $db); // Certifique-se de que as variáveis estão corretas
if ($conn->connect_error) {
    die("Erro de conexão com o banco de dados: " . $conn->connect_error);
}

// Inicializa a variável de mensagem
$msg = "";

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebendo e validando os valores do formulário
    $tipo = $_POST['tipo'] ?? '';
    $numeroCaixas = (int) $_POST['numeroCaixas'];
    $qtdDoses = (int) $_POST['qtdDoses'];
    $preco = (float) $_POST['preco'];

    // Verificar o número total de tipos de medicamentos no estoque
    $query_total_medicamentos = "SELECT COUNT(DISTINCT tipo) AS total FROM Estoque";
    $result = $conn->query($query_total_medicamentos);

    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['total'] >= 5) {
            $msg = "O estoque está cheio. Não é possível adicionar mais medicamentos.";
        } else {
            if ($numeroCaixas > 10) {
                $msg = "O número de caixas de um medicamento não pode passar de 10 caixas.";
            } else {
                // Preparar a inserção no banco
                $inserir = $conn->prepare("INSERT INTO Estoque (tipo, numeroCaixas, qtdDoses, preco) VALUES (?, ?, ?, ?)");
                if ($inserir) {
                    $inserir->bind_param("siii", $tipo, $numeroCaixas, $qtdDoses, $preco);

                    if ($inserir->execute()) {
                        // Verifica o número total de tipos distintos de medicamentos
                        $query_tipos = "SELECT COUNT(DISTINCT tipo) AS total_tipos FROM Estoque";
                        $result_tipos = $conn->query($query_tipos);

                        if ($result_tipos) {
                            $row_tipos = $result_tipos->fetch_assoc();
                            if ($row_tipos['total_tipos'] == 1) {
                                $msg = "O estoque não pode conter apenas um tipo de medicamento. Adicione medicamentos de outro tipo.";
                            } else {
                                $msg = "Medicamento cadastrado com sucesso.";
                            }
                        } else {
                            $msg = "Erro ao verificar os tipos de medicamentos: " . $conn->error;
                        }
                    } else {
                        $msg = "Erro ao cadastrar o medicamento: " . $conn->error;
                    }

                    $inserir->close();
                } else {
                    $msg = "Erro ao preparar a inserção: " . $conn->error;
                }
            }
        }
    } else {
        $msg = "Erro na consulta ao banco de dados: " . $conn->error;
    }
}

// Exibir a tabela de medicamentos no estoque
$query_estoque = "SELECT * FROM Estoque";
$result_estoque = $conn->query($query_estoque);

if ($result_estoque && $result_estoque->num_rows > 0) {
    echo "<h3>Medicamentos no Estoque:</h3>";
    echo "<table border='1'>
            <tr>
                <th>Tipo</th>
                <th>Número de Caixas</th>
                <th>Quantidade de Doses</th>
                <th>Preço</th>
            </tr>";
    while ($row = $result_estoque->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['tipo']) . "</td>
                <td>" . htmlspecialchars($row['numeroCaixas']) . "</td>
                <td>" . htmlspecialchars($row['qtdDoses']) . "</td>
                <td>" . htmlspecialchars($row['preco']) . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "Nenhum medicamento no estoque.";
}

// Fechar a conexão com o banco de dados ao final
$conn->close();
?>

<!-- Exibir mensagem, se houver -->
<?php if (!empty($msg)) : ?>
    <p><?php echo htmlspecialchars($msg); ?></p>
<?php endif; ?>
