<?php
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../config/auth-check.php';
if (!function_exists('adminEstaLogado') || !adminEstaLogado()) {
    header('Location: login.html');
    exit;
}
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout Assistido - Admin</title>
    <link rel="stylesheet" href="../../public/css/style.css" />
    <style>
        body {
            background: #f5f7fb;
            font-family: Arial, sans-serif;
        }

        header {
            background: #2c3e50;
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-img {
            height: 50px
        }

        .menu {
            display: flex;
            gap: 2rem
        }

        .menu a {
            color: #fff;
            text-decoration: none;
            padding: .5rem 1rem;
            border-radius: 4px
        }

        .menu a:hover {
            background: #34495e
        }

        main {
            max-width: 1000px;
            margin: 2rem auto;
            background: #fff;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .06)
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem
        }

        .card {
            border: 1px solid #e8ebf2;
            border-radius: 10px;
            padding: 1rem
        }

        h2 {
            margin: .25rem 0 1rem
        }

        .field {
            margin-bottom: .75rem
        }

        .field label {
            display: block;
            font-weight: 600;
            margin-bottom: .35rem
        }

        .field input {
            width: 100%;
            padding: .55rem .7rem;
            border: 1px solid #ccd3e0;
            border-radius: 8px
        }

        .btn {
            border: 0;
            padding: .65rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600
        }

        .btn-primary {
            background: #3498db;
            color: #fff
        }

        .btn-secondary {
            background: #95a5a6;
            color: #fff
        }

        .specs {
            display: flex;
            gap: 1rem;
            color: #556;
            font-size: .95rem
        }

        .status {
            font-weight: 700
        }

        .ok {
            color: #27ae60
        }

        .err {
            color: #c0392b
        }

        /* Modal simples */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }

        .modal {
            background: #fff;
            width: min(560px, 92vw);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .2)
        }

        .modal header {
            background: #2c3e50;
            color: #fff;
            padding: .75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal .content {
            padding: 1rem;
        }

        .modal .close {
            background: #e74c3c;
            color: #fff;
            border: 0;
            padding: .4rem .7rem;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <header>
        <figure class="logo"><img src="../../public/images/logo.png" alt="ALUCAR Logo" class="logo-img" /></figure>
        <nav class="menu">
            <a href="index.php">Dashboard</a>
            <a href="vendas.html">Vender</a>
            <a href="../../public/logout.php" style="background:#e74c3c;">Sair</a>
        </nav>
    </header>

    <main>
        <h1>Checkout assistido</h1>
        <div class="grid">
            <section class="card" id="secVeiculo">
                <h2>Veículo</h2>
                <div id="veiculoInfo">Carregando...</div>
            </section>
            <section class="card">
                <h2>Dados do cliente</h2>
                <div class="field">
                    <label for="cpf">CPF do cliente</label>
                    <input id="cpf" type="text" placeholder="Somente números" />
                </div>
                <h2>Período</h2>
                <div class="field">
                    <label for="dias">Dias</label>
                    <input id="dias" type="number" min="1" value="7" />
                </div>
                <div class="field" style="display:flex; align-items:center; gap:.5rem;">
                    <input id="temSeguro" type="checkbox" style="width:auto;" />
                    <label for="temSeguro" style="margin:0;">Incluir seguro (+9,1% do subtotal)</label>
                </div>
                <div style="display:flex; gap:.75rem;">
                    <button class="btn btn-primary" id="btnCriar">Criar reserva</button>
                    <button class="btn btn-secondary" id="btnVoltar">Cancelar</button>
                </div>
                <div id="status" style="margin-top:.5rem;"></div>
            </section>
        </div>
    </main>

    <!-- Modal: Cadastrar cliente -->
    <div class="modal-backdrop" id="mdCliente">
        <div class="modal">
            <header>
                <strong>Cadastrar cliente</strong>
                <button class="close" id="mdClienteClose">Fechar</button>
            </header>
            <div class="content">
                <div class="field"><label>Nome</label><input id="cli_nome" type="text" placeholder="Nome completo" /></div>
                <div class="field"><label>Email</label><input id="cli_email" type="email" placeholder="email@exemplo.com" /></div>
                <div class="field"><label>CPF</label><input id="cli_cpf" type="text" /></div>
                <div class="field"><label>Telefone (opcional)</label><input id="cli_tel" type="text" placeholder="(DDD) 90000-0000" /></div>
                <div style="display:flex; gap:.6rem;">
                    <button class="btn btn-primary" id="btnSalvarCliente">Salvar</button>
                    <button class="btn btn-secondary" id="btnCancelarCliente">Cancelar</button>
                </div>
                <div id="cli_status" style="margin-top:.5rem;"></div>
            </div>
        </div>
    </div>

    <script>
        const ID = <?php echo (int)$id; ?>;
        const veiculoInfo = document.getElementById('veiculoInfo');
        const statusBox = document.getElementById('status');
        const mdBackdrop = document.getElementById('mdCliente');
        const mdClose = document.getElementById('mdClienteClose');
        const btnSalvarCliente = document.getElementById('btnSalvarCliente');
        const btnCancelarCliente = document.getElementById('btnCancelarCliente');
        const cliStatus = document.getElementById('cli_status');
        if (!ID) {
            veiculoInfo.textContent = 'ID do veículo não informado.';
        } else {
            fetch('../../public/api/obter-veiculo.php?id_veiculo=' + encodeURIComponent(ID))
                .then(r => r.json())
                .then(j => {
                    if (!j.success) {
                        veiculoInfo.textContent = 'Erro: ' + (j.error || 'Veículo não encontrado');
                        return;
                    }
                    const v = j.veiculo;
                    veiculoInfo.innerHTML = `
            <div><strong>${v.marca} ${v.nome_modelo}</strong></div>
            <div class="specs">Placa: ${v.placa} • Cor: ${v.cor} • Ano: ${v.ano}</div>
          `;
                })
                .catch(() => {
                    veiculoInfo.textContent = 'Falha ao carregar o veículo.'
                });
        }

        document.getElementById('btnVoltar').addEventListener('click', () => {
            window.location.href = 'vendas.html';
        });
        document.getElementById('btnCriar').addEventListener('click', async () => {
            const cpf = (document.getElementById('cpf').value || '').replace(/\D+/g, '').trim();
            const dias = parseInt(document.getElementById('dias').value, 10) || 0;
            const temSeguro = document.getElementById('temSeguro').checked;
            statusBox.textContent = '';
            if (!ID || !cpf || !dias) {
                statusBox.innerHTML = '<span class="status err">Preencha ID, CPF e dias.</span>';
                return;
            }
            try {
                const url = '/AV2DAW/public/api/adm/criar-locacao.php?_=' + Date.now();
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_veiculo: ID,
                        cpf,
                        dias,
                        tem_seguro: temSeguro
                    })
                });
                const raw = await res.text();
                let j;
                try {
                    j = JSON.parse(raw);
                } catch (parseErr) {
                    const snippet = (raw || '').slice(0, 200).replace(/</g, '&lt;');
                    statusBox.innerHTML = '<span class="status err">Falha inesperada (' + res.status + ').</span><div style="color:#667; margin-top:.35rem; font-size:.9rem;">' + snippet + '</div>';
                    return;
                }
                if (!res.ok || !j.success) {
                    const msg = (j && j.error ? j.error : 'Falha ao criar');
                    statusBox.innerHTML = '<span class="status err">' + msg + '</span>';
                    if (res.status === 404 && /Cliente n[aã]o encontrado/i.test(msg)) {
                        // Oferece cadastro rápido
                        mdBackdrop.style.display = 'flex';
                        document.getElementById('cli_cpf').value = cpf;
                        document.getElementById('cli_nome').focus();
                    }
                    return;
                }
                const det = j.detalhes || {};
                statusBox.innerHTML = '<span class="status ok">Reserva criada (#' + j.id_locacao + ').</span>' +
                    '<div style="margin-top:.5rem; color:#556; font-size:.9rem;">' +
                    'Subtotal: R$ ' + (det.subtotal || 0).toFixed(2) + ' • ' +
                    'Seguro: R$ ' + (det.seguro || 0).toFixed(2) + ' • ' +
                    'Taxa: R$ ' + (det.taxa_locadora || 0).toFixed(2) + ' • ' +
                    '<strong>Total: R$ ' + (j.valor_total || 0).toFixed(2) + '</strong></div>';
            } catch (e) {
                statusBox.innerHTML = '<span class="status err">Erro de rede. Verifique a conexão.</span>';
            }
        });

        // Modal handlers
        mdClose?.addEventListener('click', () => mdBackdrop.style.display = 'none');
        btnCancelarCliente?.addEventListener('click', () => mdBackdrop.style.display = 'none');
        btnSalvarCliente?.addEventListener('click', async () => {
            cliStatus.textContent = '';
            const nome = document.getElementById('cli_nome').value.trim();
            const email = document.getElementById('cli_email').value.trim();
            const cpf = (document.getElementById('cli_cpf').value || '').replace(/\D+/g, '');
            const tel = document.getElementById('cli_tel').value.trim();
            if (!nome || !email || !cpf) {
                cliStatus.innerHTML = '<span class="status err">Preencha nome, email e CPF.</span>';
                return;
            }
            try {
                const res = await fetch('/AV2DAW/public/api/adm/criar_cliente.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nome,
                        email,
                        cpf,
                        telefone: tel
                    })
                });
                const raw = await res.text();
                let j;
                try {
                    j = JSON.parse(raw);
                } catch {
                    j = null;
                }
                if (!j || !res.ok || !j.success) {
                    cliStatus.innerHTML = '<span class="status err">' + (j && j.error ? j.error : 'Falha ao salvar cliente') + '</span>';
                    return;
                }
                cliStatus.innerHTML = '<span class="status ok">Cliente cadastrado.</span>';
                document.getElementById('cpf').value = cpf;
                mdBackdrop.style.display = 'none';
            } catch (e) {
                cliStatus.innerHTML = '<span class="status err">Erro de rede.</span>';
            }
        });
    </script>
</body>

</html>