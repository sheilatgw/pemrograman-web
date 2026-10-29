
<?php
$pageTitle = $pageTitle ?? 'KopiKita';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | KopiKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-stone-100 text-stone-900">

<header class="border-b border-stone-300 bg-white">
    <nav class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-3 px-4 py-4">
        <a href="index.php" class="text-xl font-bold">
            KopiKita
        </a>

        <div class="flex flex-wrap items-center gap-4 text-sm">
            <a href="index.php" class="underline">Menu</a>

            <?php if (is_logged_in()): ?>
                <a href="orders.php" class="underline">Pesanan saya</a>
                <span><?= e($_SESSION['user']['name']) ?></span>
                <a href="logout.php" class="underline">Keluar</a>

            <?php elseif (is_admin()): ?>
                <a href="admin.php" class="underline">Admin</a>
                <a href="logout.php" class="underline">Keluar</a>

            <?php else: ?>
                <a href="login.php" class="underline">Login</a>
                <a href="register.php" class="underline">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main class="mx-auto max-w-5xl px-4 py-8">
