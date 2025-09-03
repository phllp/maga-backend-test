<section class="max-w-md">
    <h1 class="text-xl font-semibold mb-4">Cadastrar Pessoa</h1>


    <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 border-l-4 border-red-600 bg-red-50 text-red-800 text-sm">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>


    <form method="post" action="/pessoas/create" class="space-y-4 bg-white p-4 rounded-md border">
        <div>
            <label class="block text-sm mb-1" for="nome">Nome</label>
            <input id="nome" name="nome" type="text" class="w-full border rounded-md px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm mb-1" for="cpf">CPF</label>
            <input id="cpf" name="cpf" type="text" class="w-full border rounded-md px-3 py-2" required>
        </div>
        <button class="btn btn-primary" type="submit">Salvar</button>
    </form>
</section>