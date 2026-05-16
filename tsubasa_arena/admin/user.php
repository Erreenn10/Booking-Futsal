<?php
// admin/user.php - Kelola User (hanya lihat, aktif/nonaktif, hapus)
$title = 'Kelola User - Tsubasa Arena';
include '../includes/header_admin.php';
include '../config/database.php';

// Pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

if ($search) {
    $query = "SELECT * FROM users WHERE role = 'user' AND (nama LIKE '%$search%' OR email LIKE '%$search%' OR no_hp LIKE '%$search%') ORDER BY id DESC";
} else {
    $query = "SELECT * FROM users WHERE role = 'user' ORDER BY id DESC";
}
$result = mysqli_query($conn, $query);
?>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .page-header h1 {
        font-size: 24px;
        color: #0a2b4e;
    }
    
    .search-box {
        margin-bottom: 20px;
    }
    
    .search-form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .search-form input {
        flex: 1;
        padding: 12px 18px;
        border: 2px solid #e1e1e1;
        border-radius: 12px;
        font-size: 14px;
    }
    
    .search-form input:focus {
        outline: none;
        border-color: #e63946;
    }
    
    .search-form button {
        padding: 12px 25px;
        background: #0a2b4e;
        color: white;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
    }
    
    .search-form button:hover {
        background: #1e4d7c;
    }
    
    .reset-btn {
        padding: 12px 25px;
        background: #888;
        color: white;
        border-radius: 12px;
        text-decoration: none;
        display: inline-block;
    }
    
    .table-container {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        overflow-x: auto;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th {
        background: linear-gradient(135deg, #0a2b4e 0%, #1e4d7c 100%);
        color: white;
        padding: 14px 12px;
        text-align: left;
        font-size: 13px;
    }
    
    td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
        font-size: 13px;
    }
    
    tr:hover {
        background: #f8f9fa;
    }
    
    .btn-toggle {
        background: #0a2b4e;
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 12px;
        margin-right: 5px;
        display: inline-block;
        transition: all 0.3s;
    }
    
    .btn-toggle:hover {
        background: #1e4d7c;
    }
    
    .btn-activate {
        background: #28a745;
    }
    
    .btn-activate:hover {
        background: #218838;
    }
    
    .btn-deactivate {
        background: #dc3545;
    }
    
    .btn-deactivate:hover {
        background: #c82333;
    }
    
    .btn-delete {
        background: #e63946;
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 12px;
        display: inline-block;
        transition: all 0.3s;
    }
    
    .btn-delete:hover {
        background: #c1121f;
    }
    
    .badge {
        background: #d4edda;
        color: #155724;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    
    .badge-inactive {
        background: #f8d7da;
        color: #721c24;
    }
    
    .empty-data {
        text-align: center;
        padding: 40px;
        color: #888;
    }
</style>

<div class="page-header">
    <h1>👥 Kelola User</h1>
</div>

<!-- Search Box -->
<div class="search-box">
    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Cari user (nama, email, no HP)..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">🔍 Cari</button>
        <?php if ($search): ?>
            <a href="user.php" class="reset-btn">↺ Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-container">
    <?php if (mysqli_num_rows($result) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>No. HP</th>
                <th>Status</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><strong><?= $row['nama'] ?></strong></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['no_hp'] ?: '-' ?></td>
                <td>
                    <?php if ($row['is_active'] == 1): ?>
                        <span class="badge">✅ Aktif</span>
                    <?php else: ?>
                        <span class="badge badge-inactive">❌ Nonaktif</span>
                    <?php endif; ?>
                </td>
                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                <td>
                    <?php if ($row['is_active'] == 1): ?>
                        <a href="toggle_user.php?id=<?= $row['id'] ?>&action=deactivate" class="btn-toggle btn-deactivate" onclick="return confirm('Nonaktifkan user <?= $row['nama'] ?>? User tidak bisa login.')">🔴 Nonaktifkan</a>
                    <?php else: ?>
                        <a href="toggle_user.php?id=<?= $row['id'] ?>&action=activate" class="btn-toggle btn-activate" onclick="return confirm('Aktifkan user <?= $row['nama'] ?>?')">🟢 Aktifkan</a>
                    <?php endif; ?>
                    <a href="hapus_user.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Yakin hapus user <?= $row['nama'] ?>?')">🗑️ Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="empty-data">
        <p>📭 Belum ada user.</p>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer_admin.php'; ?>