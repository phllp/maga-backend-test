<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>📝 Contatos</title>
    <link rel="stylesheet" href="/assets/app.css">
</head>

<body class="min-h-screen bg-gray-100 text-gray-900 flex flex-col">
    <header class="bg-slate-800 text-slate-100">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/" class="font-semibold">Lista de Contatos</a>
            <nav class="flex items-center gap-4 text-sm">
                <a class="hover:underline" href="/">Início</a>
                <a class="hover:underline" href="/pessoas">Contatos</a>
            </nav>
        </div>
    </header>
    <main class="mx-auto px-4 py-8  flex flex-col flex-1 h-full max-w-5xl w-full">
        <?= $content ?? '' ?>
    </main>
    <footer class="bg-slate-800 text-slate-100 text-center text-sm py-6 align-bottom">Felipe Beiger ©</footer>
</body>

</html>