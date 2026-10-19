<?php
require_once __DIR__ . '/../includes/layout.php';
require_admin('/index.php');
$conn = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = trim($_POST['name'] ?? '');
    $em = trim($_POST['email'] ?? '');
    $ad = trim($_POST['address'] ?? '');
    $un = trim($_POST['username'] ?? '');
    $pw = $_POST['password'] ?? '';
    if ($n === '' || $un === '' || $pw === '') {
        toast('OHHH MAAAAMAAAMIAA!!! GAME-OVERR!! Punan ang name, username, password!', 'error');
    } else {
        $h = password_hash($pw, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO customers (name,email,address,username,password) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $n, $em, $ad, $un, $h);
        if ($stmt->execute()) toast('Customer added!', 'success'); else toast('Error: ' . $conn->error, 'error');
        $stmt->close();
    }
    redirect('/admin/customer_list.php');
}
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM customers WHERE id=?");
    $stmt->bind_param("i", $_GET['delete']);
    if ($stmt->execute()) toast('Customer deleted.', 'success'); else toast('Error: ' . $conn->error, 'error');
    $stmt->close();
    redirect('/admin/customer_list.php');
}
$rows = $conn->query("SELECT * FROM customers ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
render_head('Customer List', '/Mario Wallpaper/NSMBWii2.png');
render_navbar();
?>
<div class="max-w-5xl mx-auto p-4">
<div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black p-6 mt-6">
<h2 class="text-2xl font-bold text-center text-yellow-400 mb-4" style="text-shadow:2px 2px #000;">↓ ADD NEW CUSTOMER FORM ↓</h2>
<form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3">
<input type="text" name="name" placeholder="Full Name" required class="px-3 py-2 border-2 border-blue-400 rounded-lg">
<input type="email" name="email" placeholder="Email" class="px-3 py-2 border-2 border-blue-400 rounded-lg">
<input type="text" name="address" placeholder="Address" class="px-3 py-2 border-2 border-blue-400 rounded-lg">
<input type="text" name="username" placeholder="Username" required class="px-3 py-2 border-2 border-blue-400 rounded-lg">
<input type="password" name="password" placeholder="Password" required class="px-3 py-2 border-2 border-blue-400 rounded-lg">
<button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-bold border-2 border-black md:col-span-2">➕ Add Customer</button>
</form></div>

<div class="bg-white/95 rounded-3xl shadow-mario border-4 border-black mt-6 overflow-hidden">
<div class="bg-gradient-to-r from-blue-500 to-blue-400 p-4"><h2 class="text-2xl font-bold text-white text-center">👤 CUSTOMER LIST</h2></div>
<div class="overflow-x-auto"><table class="w-full"><thead class="bg-blue-200"><tr>
<th class="px-4 py-3 text-left text-sm">ID</th><th class="px-4 py-3 text-left text-sm">Name</th>
<th class="px-4 py-3 text-left text-sm">Email</th><th class="px-4 py-3 text-left text-sm">Address</th>
<th class="px-4 py-3 text-left text-sm">Username</th><th class="px-4 py-3 text-left text-sm">Action</th>
</tr></thead><tbody>
<?php if (empty($rows)): ?><tr><td colspan="6" class="text-center py-8 text-gray-500 font-bold">Walang customers.</td></tr>
<?php else: foreach ($rows as $r): ?>
<tr class="border-t border-blue-200 hover:bg-blue-50">
<td class="px-4 py-3 font-bold">ID: <?= (int)$r['id'] ?></td>
<td class="px-4 py-3 font-bold text-blue-700"><?= e($r['name']) ?></td>
<td class="px-4 py-3"><?= e($r['email']) ?></td>
<td class="px-4 py-3"><?= e($r['address']) ?></td>
<td class="px-4 py-3 font-bold"><?= e($r['username']) ?></td>
<td class="px-4 py-3"><button onclick="confirmLink('?delete=<?= (int)$r['id'] ?>','Burahin itong customer?')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-bold">Delete</button></td>
</tr>
<?php endforeach; endif; ?></tbody></table></div></div></div>
<?php render_footer(); ?>