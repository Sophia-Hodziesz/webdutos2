const selectCidade = document.getElementById('cidade');
const modal = document.getElementById('modalCidade');
const urlParams = new URLSearchParams(window.location.search);
const idEndereco = urlParams.get('id_endereco');

let idPessoaEdit = null;

async function carregarCidades() {
    const cidades = await (await fetch('cidade_listar.php')).json();
    selectCidade.innerHTML = '<option value="">Selecione uma cidade</option>';
    cidades.forEach(function (c) {
        const opt = document.createElement('option');
        opt.value = c.ID_CIDADE;
        opt.textContent = c.CIDADE + ' - ' + c.ESTADO;
        selectCidade.appendChild(opt);
    });

    const nova = document.createElement('option');
    nova.value = 'nova';
    nova.textContent = '➕ Nova cidade...';
    selectCidade.appendChild(nova);
}

async function iniciar() {
    await carregarCidades();

    if (idEndereco) {
        const res = await fetch('endereco.php');
        const lista = await res.json();
        const e = lista.find(item => item.ID_ENDERECO == idEndereco);

        if (e) {
            document.getElementById('nome').value = e.NOME_PESSOA || '';
            document.getElementById('cep').value = e.CEP || '';
            document.getElementById('rua').value = e.RUA || '';
            document.getElementById('bairro').value = e.BAIRRO || '';
            document.getElementById('numero').value = e.NUMERO || '';
            selectCidade.value = e.ID_CIDADE || '';
            idPessoaEdit = e.ID_PESSOA;
            document.getElementById('telefone').value = e.TELEFONE || '';
        }
    }
}

selectCidade.addEventListener('change', function () {
    if (selectCidade.value === 'nova') {
        document.getElementById('novaCidade').value = '';
        document.getElementById('novoEstado').value = '';
        modal.style.display = 'flex';
    }
});

document.getElementById('btnOkCidade').addEventListener('click', async function () {
    const cidadeNome = document.getElementById('novaCidade').value.trim();
    const estado = document.getElementById('novoEstado').value.trim().toUpperCase();
    if (!cidadeNome || !estado) { alert('Preencha cidade e estado.'); return; }

    const dados = new FormData();
    dados.append('cidade', cidadeNome);
    dados.append('estado', estado);

    const cidade = await (await fetch('cidade_criar.php', { method: 'POST', body: dados })).json();

    if (!selectCidade.querySelector('option[value="' + cidade.ID_CIDADE + '"]')) {
        const opt = document.createElement('option');
        opt.value = cidade.ID_CIDADE;
        opt.textContent = cidade.CIDADE + ' - ' + cidade.ESTADO;
        selectCidade.insertBefore(opt, selectCidade.querySelector('option[value="nova"]'));
    }
    selectCidade.value = cidade.ID_CIDADE;
    modal.style.display = 'none';
});

document.getElementById('btnCadastrar').addEventListener('click', async function () {
    const nome = document.getElementById('nome').value.trim();
    const cep = document.getElementById('cep').value.trim();
    const rua = document.getElementById('rua').value.trim();
    const bairro = document.getElementById('bairro').value.trim();
    const numero = document.getElementById('numero').value.trim();
    const idCidade = selectCidade.value;
    const telefone = document.getElementById('telefone').value.trim();

    if (!nome || !cep || !telefone || idCidade === 'nova' || !idCidade) {
        alert('Preencha Nome, CEP, Telefone e Cidade.');
        return;
    }

    try {
        if (idEndereco) {
            await fetch('pessoa.php?id=' + idPessoaEdit, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nome: nome })
            });

            await fetch('endereco.php?id=' + idEndereco, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_cidade: idCidade,
                    cep: cep,
                    rua: rua,
                    bairro: bairro,
                    numero: numero
                })
            });

            await fetch('telefone_excluir.php?id_pessoa=' + idPessoaEdit, { method: 'DELETE' });
            const dt = new FormData();
            dt.append('id_pessoa', idPessoaEdit);
            dt.append('numero', telefone);
            await fetch('telefone_criar.php', { method: 'POST', body: dt });

        } else {
            const dp = new FormData();
            dp.append('nome', nome);
            const pessoa = await (await fetch('pessoa.php', { method: 'POST', body: dp })).json();
            const idPessoa = pessoa.ID_PESSOA;

            const dt = new FormData();
            dt.append('id_pessoa', idPessoa);
            dt.append('numero', telefone);
            await fetch('telefone_criar.php', { method: 'POST', body: dt });

            const de = new FormData();
            de.append('id_pessoa', idPessoa);
            de.append('id_cidade', idCidade);
            de.append('cep', cep);
            de.append('rua', rua);
            de.append('bairro', bairro);
            de.append('numero', numero);
            await fetch('endereco.php', { method: 'POST', body: de });
        }

        location.href = 'agenda.html';
    } catch (err) {
        alert('Erro ao salvar: ' + err.message);
    }
});

iniciar();