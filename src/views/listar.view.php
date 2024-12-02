<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuários Cadastrados</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/listar.css">
</head>

<body>
    <div class="container">
        <h1 class="page-title">Usuários Cadastrados</h1>

        <div id="mensagem" role="alert" aria-live="polite"></div>

        <?php if (count($usuarios) > 0): ?>
            <div class="table-responsive" role="region" aria-label="Lista de usuários">
                <table aria-label="Usuários cadastrados">
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Email</th>
                            <th scope="col">Data de Cadastro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td data-label="Nome"><?= htmlspecialchars($usuario['nome']) ?></td>
                                <td data-label="Email"><?= htmlspecialchars($usuario['email']) ?></td>
                                <td data-label="Data de Cadastro"><?= date('d/m/Y H:i', strtotime($usuario['data_cadastro'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button id="limparRegistros" class="btn-limpar" onclick="confirmarLimpeza()">Limpar Registros</button>
            </div>
        <?php else: ?>
            <div class="sem-registros" role="alert">Nenhum usuário cadastrado ainda.</div>
        <?php endif; ?>

        <?php if (isset($erro)): ?>
            <div class="mensagem-erro" role="alert">Erro ao listar usuários: <?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="footer-link">
            By <a href="https://github.com/felipevieir4dev" target="_blank">Felipe <img src="/assets/icon/bear.png" alt="Urso"> Vieira</a>
        </div>
        <a href="/" class="btn-footer">Novo Cadastro</a>
    </footer>

    <script>
        function confirmarLimpeza() {
            if (confirm('Tem certeza que deseja limpar todos os registros? Esta ação não pode ser desfeita.')) {
                limparRegistros();
            }
        }

        function limparRegistros() {
            fetch('/src/limpar.php', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    const mensagemElement = document.getElementById('mensagem');

                    if (data.status === 'success') {
                        mensagemElement.className = 'mensagem sucesso';
                        mensagemElement.textContent = data.message;

                        // Recarrega a página após 1.5 segundos
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        mensagemElement.className = 'mensagem erro';
                        mensagemElement.textContent = data.message;
                    }
                })
                .catch(error => {
                    const mensagemElement = document.getElementById('mensagem');
                    mensagemElement.className = 'mensagem erro';
                    mensagemElement.textContent = 'Erro ao processar a requisição';
                    console.error('Erro:', error);
                });
        }
    </script>
</body>

</html>