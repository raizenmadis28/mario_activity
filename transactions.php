<?php
require_once __DIR__ . '/includes/layout.php';
require_login();
$conn = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $c = trim($_POST['customer_name'] ?? '');
    $p = trim($_POST['product_name'] ?? '');
    $a = (float)($_POST['amount'] ?? 0);
    $pm = trim($_POST['payment_method'] ?? '');
    $cat = trim($_POST['category'] ?? '');
    $img = '';
    if (isset($_FILES['transaction_image']) && $_FILES['transaction_image']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir(__DIR__ . '/uploads')) mkdir(__DIR__ . '/uploads', 0777, true);
        $img = 'uploads/' . time() . '_' . basename($_FILES['transaction_image']['name']);
        move_uploaded_file($_FILES['transaction_image']['tmp_name'], __DIR__ . '/' . $img);
    }
    if ($c === '' || $p === '' || $a <= 0 || $pm === '' || $cat === '') {
        toast('Punan lahat ng fields!', 'error');
    } else {
        $stmt = $conn->prepare("INSERT INTO transactions (customer_name,product_name,amount,payment_method,category,transaction_image) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssdsss", $c, $p, $a, $pm, $cat, $img);
        if ($stmt->execute()) toast('Transaction added!', 'success'); else toast('Error: ' . $conn->error, 'error');
        $stmt->close();
    }
    redirect('/transactions.php');
}
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM transactions WHERE id=?");
    $stmt->bind_param("i", $_GET['delete']);
    if ($stmt->execute()) toast('Transaction deleted.', 'success'); else toast('Error: ' . $conn->error, 'error');
    $stmt->close();
    redirect('/transactions.php');
}
$rows = $conn->query("SELECT * FROM transactions ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
render_head('Transactions', '/Mario Wallpaper/Mario 3D World.jpeg');
render_navbar();
?>
<div class="max-w-6xl mx-auto p-4">
<div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black p-6 mt-6">
<h2 class="text-2xl font-bold text-green-700 mb-4" style="text-shadow:1px 1px #ffeb3b;">➕ Add Transaction</h2>
<form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div><label class="block font-bold mb-1">Customer</label>
<input type="text" name="customer_name" required class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg"></div>
<div><label class="block font-bold mb-1">Product</label>
<input type="text" name="product_name" required class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg"></div>
<div><label class="block font-bold mb-1">Amount (₱)</label>
<input type="number" step="0.01" name="amount" required class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg"></div>
<div><label class="block font-bold mb-1">Payment</label>
<select name="payment_method" required class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg">
<option>Cash on Delivery (COD)</option><option>GCash</option><option>Maya</option><option>Bank Transfer</option></select></div>
<div><label class="block font-bold mb-1">Category</label>
<select name="category" required class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg">
<?php foreach (['Gundam','Code Geass','Transformers','Valorant','Manga','Other'] as $c): ?><option><?= $c ?></option><?php endforeach; ?>
</select></div>
<div><label class="block font-bold mb-1">Receipt Image</label>
<input type="file" name="transaction_image" accept="image/*" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg"></div>
<div class="md:col-span-2"><button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-bold border-2 border-black">💳 Save</button></div>
</form></div>

<div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black mt-6 overflow-hidden">
<div class="bg-gradient-to-r from-green-600 to-green-500 p-4"><h2 class="text-2xl font-bold text-white">💳 Transactions</h2></div>
<div class="overflow-x-auto"><table class="w-full">
<thead class="bg-green-300"><tr>
<th class="px-4 py-3 text-left text-sm">ID</th><th class="px-4 py-3 text-left text-sm">Customer</th>
<th class="px-4 py-3 text-left text-sm">Product</th><th class="px-4 py-3 text-left text-sm">Amount</th>
<th class="px-4 py-3 text-left text-sm">Payment</th><th class="px-4 py-3 text-left text-sm">Image</th>
<th class="px-4 py-3 text-left text-sm">Action</th></tr></thead><tbody>
<?php if (empty($rows)): ?><tr><td colspan="7" class="text-center py-8 text-gray-500 font-bold">Walang transactions.</td></tr>
<?php else: foreach ($rows as $t): ?>
<tr class="border-t border-gray-300 hover:bg-yellow-50">
<td class="px-4 py-3 font-bold"><?= (int)$t['id'] ?></td>
<td class="px-4 py-3 font-bold text-blue-700"><?= e($t['customer_name']) ?></td>
<td class="px-4 py-3"><?= e($t['product_name']) ?></td>
<td class="px-4 py-3 font-bold text-green-600">₱<?= number_format((float)$t['amount'],2) ?></td>
<td class="px-4 py-3"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold"><?= e($t['payment_method']) ?></span></td>
<td class="px-4 py-3"><?php if (!empty($t['transaction_image']) && file_exists(__DIR__ . '/' . $t['transaction_image'])): ?>
<img src="/<?= e($t['transaction_image']) ?>" class="w-10 h-10 object-cover rounded border-2 border-gray-300"><?php else: ?>—<?php endif; ?></td>
<td class="px-4 py-3"><button onclick="confirmLink('?delete=<?= (int)$t['id'] ?>','Burahin itong transaction?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-bold">Delete</button></td>
</tr>
<?php endforeach; endif; ?></tbody></table></div></div></div>
<?php render_footer(); ?>