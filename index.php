
<?php
require_once 'config.php';

$pageTitle = 'Menu Kopi';

$products = $pdo->query(
    'SELECT * FROM products
     WHERE is_active = 1
     ORDER BY id DESC'
)->fetchAll();

require 'partials/header.php';
?>

<section class="mb-8 border border-stone-400 bg-white p-6">
    <p class="mb-2 text-sm">MENU KEDAI</p>

    <h1 class="mb-3 text-3xl font-bold">
        Pilih kopi favoritmu.
    </h1>

    <p class="text-stone-900">
        Pesan kopi langsung dari website.
    </p>

    <?php if (is_logged_in()): ?>
        <p class="mt-4 border-l-4 border-green-700 pl-3 text-sm">
            Kamu mendapat diskon 10%.
        </p>
    <?php else: ?>
        <p class="mt-4 border-l-4 border-stone-500 pl-3 text-sm">
            <a href="login.php" class="font-bold underline">Login</a>
            atau
            <a href="register.php" class="font-bold underline">Register</a>
            untuk mendapat diskon 10%.
        </p>
    <?php endif; ?>
</section>

<?php if (!$products): ?>
    <p class="border border-stone-400 bg-white p-5">
        Belum ada produk tersedia.
    </p>
<?php else: ?>

<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

    <?php foreach ($products as $product): ?>
        <article class="border border-stone-400 bg-white">

            <?php if ($product['image']): ?>
                <img
                    src="uploads/<?= e($product['image']) ?>"
                    alt="<?= e($product['name']) ?>"
                    class="h-44 w-full border-b border-stone-300 object-cover"
                >
            <?php else: ?>
                <div class="flex h-44 items-center justify-center border-b border-stone-300 bg-stone-200">
                    Tidak ada gambar
                </div>
            <?php endif; ?>

            <div class="space-y-3 p-4">
                <h2 class="text-lg font-bold">
                    <?= e($product['name']) ?>
                </h2>

                <p class="text-sm text-stone-700">
                    <?= e($product['description']) ?>
                </p>

                <p class="font-bold">
                    <?= rupiah($product['price']) ?>
                </p>

                <form action="order.php" method="POST" class="space-y-3">
                    <input
                        type="hidden"
                        name="product_id"
                        value="<?= (int)$product['id'] ?>"
                    >

                    <label class="block text-sm">
                        Jumlah
                        <input
                            type="number"
                            name="quantity"
                            min="1"
                            max="20"
                            value="1"
                            required
                            class="mt-1 w-full border border-stone-400 px-3 py-2"
                        >
                    </label>

                    <button
                        type="submit"
                        class="w-full border border-stone-900 bg-stone-900 px-4 py-2 text-white hover:bg-stone-700"
                    >
                        Pesan kopi
                    </button>
                </form>
            </div>
        </article>
    <?php endforeach; ?>

</div>
<?php endif; ?>

<?php require 'partials/footer.php'; ?>
