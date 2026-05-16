<?php
// admin/lapangan.php - Kelola Lapangan dengan Pagination (5 per halaman)
$title = 'Kelola Lapangan - Tsubasa Arena';
include '../includes/header_admin.php';
include '../config/database.php';

// ========== PAGINATION (5 per halaman) ==========
$limit = 5; // jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$countQuery = "SELECT COUNT(*) as total FROM lapangan";
$countResult = mysqli_query($conn, $countQuery);
$totalData = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalData / $limit);

// Query dengan pagination
$query = "SELECT * FROM lapangan ORDER BY id DESC LIMIT $limit OFFSET $offset";
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
    
    .btn-tambah {
        background: #e63946;
        color: white;
        padding: 12px 24px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-tambah:hover {
        background: #c1121f;
        transform: translateY(-2px);
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
    
    .btn-edit {
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
    
    .btn-edit:hover {
        background: #1e4d7c;
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
    
    .status-active {
        background: #d4edda;
        color: #155724;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        display: inline-block;
    }
    
    .status-inactive {
        background: #f8d7da;
        color: #721c24;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        display: inline-block;
    }
    
    .empty-data {
        text-align: center;
        padding: 40px;
        color: #888;
    }
    
    /* Pagination Style */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 25px;
        flex-wrap: wrap;
    }
    
    .pagination a, .pagination span {
        padding: 8px 12px;
        border-radius: 8px;
        text-decoration: none;
        color: #0a2b4e;
        background: white;
        border: 1px solid #ddd;
        transition: 0.2s;
    }
    
    .pagination a:hover {
        background: #e63946;
        color: white;
        border-color: #e63946;
    }
    
    .pagination .active {
        background: #e63946;
        color: white;
        border-color: #e63946;
    }
    
    .pagination .disabled {
        opacity: 0.5;
        pointer-events: none;
    }
</style>

<div class="page-header">
    <h1>🏟️ Kelola Lapangan Futsal</h1>
    <a href="tambah_lapangan.php" class="btn-tambah">➕ Tambah Lapangan</a>
</div>

<div class="table-container">
    <?php if (mysqli_num_rows($result) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Nama Lapangan</th>
                <th>Harga / Jam</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>
                    <?php if ($row['foto'] && file_exists('../assets/uploads/lapangan/' . $row['foto'])): ?>
                        <img src="../assets/uploads/lapangan/<?= $row['foto'] ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                    <?php else: ?>
                        <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">🏟️</div>
                    <?php endif; ?>
                </td>
                <td><strong><?= $row['nama_lapangan'] ?></strong><br><small style="color:#888;"><?= substr($row['deskripsi'], 0, 50) ?>...</small></td>
                <td>Rp <?= number_format($row['harga_per_jam'], 0, ',', '.') ?></td>
                <td>
                    <a href="<?= str_replace('/embed?', '/search?', $row['lokasi']) ?>" target="_blank" style="color:#e63946;">🗺️ Lihat Peta</a>
                </td>
                <td>
                    <?php if ($row['status'] == 'aktif'): ?>
                        <span class="status-active">✓ Aktif</span>
                    <?php else: ?>
                        <span class="status-inactive">✗ Nonaktif</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_lapangan.php?id=<?= $row['id'] ?>" class="btn-edit">✏️ Edit</a>
                    <a href="hapus_lapangan.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Yakin hapus lapangan ini?')">🗑️ Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <a href="?page=1" class="<?= $page <= 1 ? 'disabled' : '' ?>">« First</a>
        <a href="?page=<?= $page-1 ?>" class="<?= $page <= 1 ? 'disabled' : '' ?>">‹ Prev</a>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        
        <a href="?page=<?= $page+1 ?>" class="<?= $page >= $totalPages ? 'disabled' : '' ?>">Next ›</a>
        <a href="?page=<?= $totalPages ?>" class="<?= $page >= $totalPages ? 'disabled' : '' ?>">Last »</a>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-data">
        <p>📭 Belum ada lapangan. Silakan tambah lapangan baru.</p>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer_admin.php'; ?>