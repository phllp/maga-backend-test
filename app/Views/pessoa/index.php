<section class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Pessoas</h1>
        <a class="btn btn-primary" href="/pessoas/create">Cadastrar</a>
    </div>


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
                            <td class="p-2 border"><?= htmlspecialchars($p->getCpf()) ?></td>
                            <td class="p-2">
                                <span class="flex gap-4">
                                    <a class="btn btn-secondary" href="/pessoas/create">Alterar</a>
                                    <a class="btn btn-secondary" href="/pessoas/create">Contatos</a>
                                    <a class="btn btn-secondary" href="/pessoas/create">Visualizar</a>
                                    <a class="btn btn-danger" href="/pessoas/create">Excluir</a>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>