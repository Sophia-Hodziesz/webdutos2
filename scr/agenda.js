const corpoTabela = document.getElementById('corpoTabela');

async function carregarAgenda() {
    const lista = await (await fetch('endereco.php')).json();
    corpoTabela.innerHTML = '';
    
    lista.forEach(function (item) {
        const endereco = [item.RUA, item.NUMERO, item.BAIRRO, item.CEP].filter(Boolean).join(', ');
        const cidade = (item.NOME_CIDADE || '') + ' - ' + (item.ESTADO || '');
        
        const tr = document.createElement('tr');
        tr.innerHTML =
            '<td>' + (item.NOME_PESSOA || '') + '</td>' +
            '<td>' + endereco + '</td>' +
            '<td>' + cidade + '</td>' +
            '<td>' + (item.TELEFONE || '') + '</td>' +
            '<td>' +
            '<a href="cadastro.html?id_endereco=' + item.ID_ENDERECO + '" style="background-color: #329542; color: white; padding: 5px 0; border-radius: 4px; text-decoration: none; display: block; text-align: center; font-size: 12px; width: 75px;">Editar</a>' +
            '<button type="button" onclick="excluir(' + item.ID_ENDERECO + ',' + item.ID_PESSOA + ')" style="background-color: #E1111A; color: white; padding: 5px 0; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; width: 75px; margin-top: 5px;">Excluir</button>' +
            '</td>';
        corpoTabela.appendChild(tr);
    });
}

async function excluir(idEndereco, idPessoa) {
    if (!confirm('Deseja excluir este contato?')) return;
    
    await fetch('endereco.php?id=' + idEndereco, { method: 'DELETE' });
    await fetch('telefone_excluir.php?id_pessoa=' + idPessoa, { method: 'DELETE' });
    await fetch('pessoa.php?id=' + idPessoa, { method: 'DELETE' });
    
    carregarAgenda();
}

carregarAgenda();