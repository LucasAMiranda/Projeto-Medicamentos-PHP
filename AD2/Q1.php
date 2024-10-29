<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-size: 14px;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            font-size: 14px;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #5cb85c;
        }

        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #4cae4c;
        }

        select {
            appearance: none;
            cursor: pointer;
        }

        @media (max-width: 600px) {
            form {
                padding: 20px;
            }
            input, select {
                font-size: 12px;
            }
            input[type="submit"] {
                padding: 12px;
                font-size: 14px;
            }
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            font-size: 14px;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            font-size: 14px;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #5cb85c;
        }

        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #4cae4c;
        }

        select {
            appearance: none;
            cursor: pointer;
        }

        @media (max-width: 600px) {
            form {
                padding: 20px;
            }
            input, select {
                font-size: 12px;
            }
            input[type="submit"] {
                padding: 12px;
                font-size: 14px;
            }
        }

    </style>
</head>
<body>
    <form method="POST" action="Q2.php">
        Tipo:
        <select name="tipo" required>
        <option value="anti-inflamatório">Anti-inflamatório</option>
        <option value="antibiótico">Antibiótico</option>
    </select>

        <br/>
        Número de Caixas: <input  type="number" name="numeroCaixas" required /><br/>
        Quantidade de Doses: <input type="number" name="qtdDoses" required /><br/>
        Preço: <input type="text" name="preco" required /><br/>
        <input type="submit" value="Cadastrar Medicamento"/>
    </form>
</body>