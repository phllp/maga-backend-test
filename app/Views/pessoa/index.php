<section class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Pessoas</h1>
        <a class="btn btn-primary" href="/pessoas/create">Cadastrar</a>
    </div>

    <!-- Barra de busca -->
    <?php $q = $q ?? '';
    require __DIR__ . '/../components/search-bar.php'; ?>

    <?php if (empty($pessoas)): ?>
        <p class="text-gray-600">Nenhuma pessoa cadastrada.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left p-2 border w-full">Nome</th>
                        <th class="text-left p-2 border min-w-40">CPF</th>
                        <th class="text-left p-2 border min-w-80">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pessoas as $p): ?>
                        <tr>
                            <td class="p-2 border"><?= htmlspecialchars($p->getNome()) ?></td>
                            <td class="p-2 border"><?= htmlspecialchars(format_cpf($p->getCpf())) ?></td>
                            <td class="p-2">
                                <span class="flex gap-4">
                                    <a class="btn btn-secondary" href="/pessoas/edit?id=<?= htmlspecialchars($p->getId()) ?>">Alterar</a>
                                    <button
                                        type="button"
                                        class="btn btn-secondary js-open-contatos"
                                        data-pessoa-id="<?= htmlspecialchars((string)$p->getId()) ?>"
                                        data-pessoa-nome="<?= htmlspecialchars($p->getNome()) ?>">
                                        Contatos
                                    </button>
                                    <form method="post" action="/pessoas/delete"
                                        onsubmit="return confirm('Tem certeza que deseja excluir esta pessoa?');">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars((string)$p->getId()) ?>">
                                        <button type="submit" class="btn btn-danger">Excluir</button>
                                    </form>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Modal de Contatos -->
            <div id="contatosModal" class="fixed inset-0 z-50 hidden">
                <!-- overlay -->
                <div class="absolute inset-0 bg-black/40"></div>

                <!-- dialog -->
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="w-full max-w-2xl rounded-xl bg-white shadow-xl">
                        <div class="flex items-center justify-between border-b px-5 py-3">
                            <h2 id="contatosTitle" class="text-lg font-semibold">Contatos</h2>
                            <button id="contatosClose" class="rounded p-1 hover:bg-gray-100" aria-label="Close">✕</button>
                        </div>

                        <div class="px-5 py-4 space-y-4">
                            <div id="contatosAlert" class="hidden rounded-md border-l-4 p-3 text-sm"></div>

                            <div id="contatosTableWrap" class="overflow-x-auto">
                                <!-- tabela vai ser injetada aqui -->
                            </div>

                            <!-- Novo contato -->
                            <form id="contatoForm" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                                <!-- Campo hidden para guardar referencia do contato sendo editado -->
                                <input type="hidden" name="id" id="contatoId">
                                <!-- Campo hidden para guardar referência da pessoa cujos contatos estão sendo manipulados -->
                                <input type="hidden" name="pessoa_id" id="contatoPessoaId">
                                <div>
                                    <label for="contatoTipo" class="block text-sm mb-1">Tipo</label>
                                    <select id="contatoTipo" name="tipo" class="w-full border rounded-md px-3 py-2">
                                        <option value="1">Email</option>
                                        <option value="2">Telefone</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="contatoDescricao" class="block text-sm mb-1">Descrição</label>
                                    <input id="contatoDescricao" name="descricao" type="text"
                                        class="w-full border rounded-md px-3 py-2" placeholder="ex: (48) 99999-9999, email@dominio.com">
                                </div>
                                <div class="sm:col-span-3 flex gap-2">
                                    <button type="submit" class="btn btn-primary">Adicionar contato</button>
                                    <button type="button" id="contatosCancel" class="btn">Fechar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>
</section>

<script type="module">
    import {
        formatBrPhone
    } from '/js/formatters.js';

    (function() {
        const modal = document.getElementById('contatosModal');
        const titleEl = document.getElementById('contatosTitle');
        const closeBtn = document.getElementById('contatosClose');
        const cancelBtn = document.getElementById('contatosCancel');
        const tableWrap = document.getElementById('contatosTableWrap');
        const alertBox = document.getElementById('contatosAlert');
        const form = document.getElementById('contatoForm');
        const pessoaIdInput = document.getElementById('contatoPessoaId');
        const contatoIdInput = document.getElementById('contatoId');
        const tipoSelect = document.getElementById('contatoTipo');
        const descInput = document.getElementById('contatoDescricao');

        let currentPessoaId = null;
        let editingId = null;

        function openModal(pessoaId, pessoaNome) {
            currentPessoaId = pessoaId;
            pessoaIdInput.value = pessoaId;
            titleEl.textContent = `Contatos — ${pessoaNome}`;
            modal.classList.remove('hidden');
            loadContatos(pessoaId);
        }

        function closeModal() {
            modal.classList.add('hidden');
            tableWrap.innerHTML = '';
            form.reset();
            alertBox.classList.add('hidden');
            contatoIdInput.value = '';
            editingId = null;
            form.querySelector('button[type="submit"]').textContent = 'Adicionar contato';
        }

        function setAlert(ok, msg) {
            alertBox.textContent = msg;
            alertBox.classList.remove('hidden');
            alertBox.classList.toggle('border-green-600', ok);
            alertBox.classList.toggle('bg-green-50', ok);
            alertBox.classList.toggle('text-green-800', ok);
            alertBox.classList.toggle('border-red-600', !ok);
            alertBox.classList.toggle('bg-red-50', !ok);
            alertBox.classList.toggle('text-red-800', !ok);
        }

        // associa o botão de update a cada registro respectivamente
        function bindRowUpdateAction() {
            tableWrap.querySelectorAll('.js-edit').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const id = btn.getAttribute('data-id');
                    const tipo = btn.getAttribute('data-tipo');
                    const descricao = btn.getAttribute('data-descricao');

                    editingId = id;
                    contatoIdInput.value = id;
                    tipoSelect.value = tipo;
                    descInput.value = descricao;


                    form.querySelector('button[type="submit"]').textContent = 'Salvar alterações';
                });
            });
        }

        // associa o botão de delete a cada registro respectivamente
        function bindRowDeleteAction() {
            tableWrap.querySelectorAll('.js-del').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    const id = btn.getAttribute('data-id');
                    if (!id) return;
                    if (!confirm('Excluir este contato?')) return;

                    const body = new FormData();
                    body.append('id', id);

                    const res = await fetch('/contatos/delete', {
                        method: 'POST',
                        body
                    });
                    const json = await res.json();

                    if (json && json.ok) {
                        setAlert(true, 'Contato excluído.');
                        // Remove a linha da tabela
                        const tr = tableWrap.querySelector(`tr[data-id="${id}"]`);
                        if (tr) tr.remove();
                        // Se a tabela estiver vazia, renderiza novamente com "Nenhum contato."
                        if (!tableWrap.querySelector('tbody tr')) renderTable({
                            contatos: []
                        });
                    } else {
                        setAlert(false, json?.error || 'Falha ao excluir.');
                    }
                });
            });
        }

        function renderTable(data) {
            const rows = (data.contatos ?? []).map(c => `
                <tr data-id="${c.id}">
                    <td class="p-2 border">${escapeHtml(c.tipo.label)}</td>
                    <td class="p-2 border">${formatBrPhone(escapeHtml(c.descricao))}</td>
                    <td class="p-2 border flex gap-2">
                        <button 
                            class="btn btn-secondary btn-xs js-edit"
                            data-id="${c.id}"
                            data-tipo="${c.tipo.value}"
                            data-descricao="${escapeHtml(c.descricao)}">Editar
                        </button>
                        <button class="btn btn-danger btn-xs js-del" data-id="${c.id}">Excluir</button>
                    </td>
                </tr>
            `).join('');

            tableWrap.innerHTML = `
                <table class="min-w-full text-sm border bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left p-2 border w-24">Tipo</th>
                            <th class="text-left p-2 border">Descrição</th>
                            <th class="text-left p-2 border w-28">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows || `<tr><td class="p-3 text-gray-600 border" colspan="4">Nenhum contato.</td></tr>`}
                    </tbody>
                </table>
            `;

            bindRowUpdateAction()
            bindRowDeleteAction()
        }

        async function loadContatos(pessoaId) {
            tableWrap.innerHTML = `<div class="p-3 text-sm text-gray-500">Carregando…</div>`;
            try {
                const res = await fetch(`/contatos?pessoaId=${encodeURIComponent(pessoaId)}`);
                const json = await res.json();
                if (json.error) throw new Error(json.error);
                renderTable(json);
            } catch (err) {
                tableWrap.innerHTML = `<div class="p-3 text-sm text-red-700 bg-red-50 border-l-4 border-red-600">${escapeHtml(err.message)}</div>`;
            }
        }

        // O evento de submit pode ser Update ou Create
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = new FormData(form);
            const isUpdate = !!editingId;
            const url = isUpdate ? '/contatos/update' : '/contatos';

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    body
                });
                const json = await res.json();
                if (!json.ok) throw new Error(json.error || 'Falha na operação.');

                setAlert(true, isUpdate ? 'Contato atualizado.' : 'Contato adicionado.');
                // Reset form back to create mode
                editingId = null;
                contatoIdInput.value = '';
                form.querySelector('button[type="submit"]').textContent = 'Adicionar contato';
                form.reset();

                // Reload the list
                loadContatos(currentPessoaId);
            } catch (err) {
                setAlert(false, err.message);
            }
        });

        // Openers
        document.querySelectorAll('.js-open-contatos').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const pessoaId = btn.getAttribute('data-pessoa-id');
                const pessoaNome = btn.getAttribute('data-pessoa-nome') || 'Pessoa';
                openModal(pessoaId, pessoaNome);
            });
        });

        // Closers
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal.firstElementChild) closeModal(); // fecha o overlay
        });

        function escapeHtml(s) {
            return String(s).replace(/[&<>"']/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            } [m]));
        }
    })();
</script>