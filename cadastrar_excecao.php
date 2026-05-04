<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lançar Exceção na Agenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container bg-white p-4 rounded shadow-sm">
        <h2>Lançar Exceção / Alteração de Culto</h2>
        <form action="process/processar_excecao.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Data do Culto</label>
                <input type="date" name="exception_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Período</label>
                <select name="period" class="form-select">
                    <option value="Manhã">Manhã</option>
                    <option value="Tarde">Tarde</option>
                    <option value="Noite">Noite</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo de Culto (ex: Culto de Oração, Reunião de Junta)</label>
                <input type="text" name="service_name" class="form-control" placeholder="Ex: Culto de Oração" required>
            </div>
            <button type="submit" class="btn btn-success">Registrar Exceção</button>
        </form>
    </div>
</body>
</html>