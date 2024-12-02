<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/cadastro.css">
</head>
<body>
    <h2 class="page-title">Cadastro de Usuário</h2>
    
    <div class="container">
        <div id="mensagem" role="alert" aria-live="polite"></div>

        <form id="formCadastro" method="POST" aria-label="Formulário de cadastro">
            <div class="form-group">
                <label for="nome" id="nomeLabel">Nome:</label>
                <input type="text" 
                       id="nome" 
                       name="nome" 
                       required 
                       minlength="3" 
                       placeholder="Digite seu nome completo"
                       aria-labelledby="nomeLabel"
                       aria-describedby="nomeErro"
                       aria-required="true">
                <div class="campo-obrigatorio" id="nomeErro" role="alert"></div>
            </div>
            
            <div class="form-group">
                <label for="email" id="emailLabel">E-mail:</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required 
                       placeholder="Digite seu e-mail"
                       aria-labelledby="emailLabel"
                       aria-describedby="emailErro"
                       aria-required="true">
                <div class="campo-obrigatorio" id="emailErro" role="alert"></div>
            </div>
            
            <div class="form-group">
                <label for="senha" id="senhaLabel">Senha:</label>
                <div class="password-container">
                    <input type="password" 
                           id="senha" 
                           name="senha" 
                           required 
                           placeholder="Digite sua senha"
                           aria-labelledby="senhaLabel"
                           aria-describedby="senhaErro"
                           aria-required="true">
                    <i class="fas fa-eye-slash toggle-password" 
                       onclick="togglePassword()" 
                       role="button" 
                       aria-label="Mostrar/Ocultar senha"></i>
                </div>
                <div class="campo-obrigatorio" id="senhaErro" role="alert"></div>
            </div>

            <button type="submit" class="btn-cadastrar">Cadastrar</button>
        </form>
    </div>

    <footer class="footer">
        <div class="footer-link">
            By <a href="https://github.com/felipevieir4dev" target="_blank">Felipe <img src="/assets/icon/bear.png" alt="Urso"> Vieira</a>
        </div>
        <a href="/listar" class="btn-footer">Ver Cadastros</a>
    </footer>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('senha');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }

        function validarSenha(senha) {
            const temNumero = /[0-9]/.test(senha);
            const temMaiuscula = /[A-Z]/.test(senha);
            const temMinuscula = /[a-z]/.test(senha);
            const temEspecial = /[!@#$%^&*]/.test(senha);
            
            let mensagens = [];
            
            if (senha.length < 8) {
                mensagens.push("A senha deve ter no mínimo 8 caracteres");
            }
            if (!temNumero) {
                mensagens.push("Deve conter pelo menos um número");
            }
            if (!temMaiuscula) {
                mensagens.push("Deve conter pelo menos uma letra maiúscula");
            }
            if (!temMinuscula) {
                mensagens.push("Deve conter pelo menos uma letra minúscula");
            }
            if (!temEspecial) {
                mensagens.push("Deve conter pelo menos um caractere especial (!@#$%^&*)");
            }
            
            return mensagens;
        }

        document.getElementById('senha').addEventListener('input', function() {
            const erros = validarSenha(this.value);
            const senhaErro = document.getElementById('senhaErro');
            
            if (erros.length > 0) {
                senhaErro.innerHTML = erros.join('<br>');
                this.setCustomValidity('Senha inválida');
            } else {
                senhaErro.textContent = '';
                this.setCustomValidity('');
            }
        });

        document.getElementById('formCadastro').addEventListener('submit', function(e) {
            e.preventDefault();
            
            document.querySelectorAll('.campo-obrigatorio').forEach(campo => campo.textContent = '');
            const mensagemElement = document.getElementById('mensagem');
            mensagemElement.className = '';
            mensagemElement.textContent = '';
            
            const senha = document.getElementById('senha').value;
            const errosSenha = validarSenha(senha);
            if (errosSenha.length > 0) {
                document.getElementById('senhaErro').innerHTML = errosSenha.join('<br>');
                return;
            }
            
            const formData = new FormData(this);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if(data.status === 'success') {
                    mensagemElement.className = 'mensagem sucesso';
                    mensagemElement.textContent = data.message;
                    document.getElementById('formCadastro').reset();
                } else {
                    mensagemElement.className = 'mensagem erro';
                    mensagemElement.textContent = data.message || 'Erro ao processar a requisição';
                }
            })
            .catch(error => {
                mensagemElement.className = 'mensagem erro';
                mensagemElement.textContent = 'Erro ao processar a requisição. Por favor, tente novamente.';
                console.error('Erro:', error);
            });
        });
    </script>
</body>
</html>
