
<?php
require_once 'config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$stmt = $pdo->prepare(
    'SELECT * FROM orders
     WHERE user_id = ?
     ORDER BY created_at DESC'
);

$stmt->execute([(int) $_SESSION['user']['id']]);
$orders = $stmt->fetchAll();

$pageTitle = 'Pesanan Saya';
require 'partials/header.php';
?>

<h1 class="mb-5 text-2xl font-bold">Riwayat pesanan</h1>

<?php if (!$orders): ?>
    <p class="border border-stone-400 bg-white p-5">
        Belum ada pesanan.
    </p>
<?php else: ?>
    <div class="overflow-x-auto border border-stone-400 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-stone-400 bg-stone-200">
                <tr>
                    <th class="p-3">No.</th>
                    <th class="p-3">Produk</th>
                    <th class="p-3">Jumlah</th>
                    <th class="p-3">Diskon</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Waktu</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr class="border-b border-stone-200">
                        <td class="p-3">#<?= (int) $order['id'] ?></td>
                        <td class="p-3"><?= e($order['product_name']) ?></td>
                        <td class="p-3"><?= (int) $order['quantity'] ?></td>
                        <td class="p-3"><?= rupiah($order['discount_amount']) ?></td>
                        <td class="p-3 font-bold"><?= rupiah($order['total_price']) ?></td>
                        <td class="p-3"><?= e($order['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require 'partials/footer.php'; ?>
