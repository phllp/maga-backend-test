<?php

/** @var string $q */ ?>
<form method="get" action="/pessoas" role="search" class="w-full max-w-2xl">
    <label for="q" class="sr-only">Buscar pessoa por nome</label>
    <div class="flex items-stretch gap-2">
        <div class="relative flex-1">
            <input
                id="q"
                name="q"
                type="search"
                placeholder="Buscar por nome…"
                value="<?= htmlspecialchars($q ?? '') ?>"
                class="w-full rounded-md border border-gray-300 bg-white pl-10 pr-3 py-2 text-sm
               placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-black focus:border-black" />
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 opacity-60"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m21 21-4.35-4.35m1.1-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0z" />
            </svg>
        </div>

        <button type="submit" class="btn btn-primary whitespace-nowrap">
            Buscar
        </button>
    </div>

    <?php if (!empty($q)): ?>
        <p class="mt-2 text-xs text-gray-500">Resultados para: <strong><?= htmlspecialchars($q) ?></strong></p>
    <?php else: ?>
        <p class="mt-2 text-xs text-gray-500">Dica: digite um nome e pressione Enter.</p>
    <?php endif; ?>
</form>