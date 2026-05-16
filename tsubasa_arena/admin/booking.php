<?php
// admin/booking.php - Kelola Booking
$title = 'Kelola Booking - Tsubasa Arena';
include '../includes/header_admin.php';
include '../config/database.php';

// Filter status
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$query = "SELECT b.*, u.nama as user_nama, u.no_hp, l.nama_lapangan 
          FROM booking b 
          JOIN users u ON b.id_user = u.id 
          JOIN lapangan l ON b.id_lapangan = l.id 
          ORDER BY b.created_at DESC";

if ($filter && $filter != 'all') {
    $query = "SELECT b.*, u.nama as user_nama, u.no_hp, l.nama_lapangan 
              FROM booking b 
              JOIN users u ON b.id_user = u.id 
              JOIN lapangan l ON b.id_lapangan = l.id 
              WHERE b.status = '$filter'
              ORDER BY b.created_at DESC";
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
    
    .filter-group {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .filter-btn {
        background: #f0f2f5;
        color: #333;
        padding: 8px 16px;
        border-radius: 25px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .filter-btn:hover, .filter-btn.active {
        background: #e63946;
        color: white;
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
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
        display: inline-block;
    }
    
    .status-pending { background: #fff3cd; color: #856404; }
    .status-confirmed { background: #d4edda; color: #155724; }
    .status-completed { background: #cce5ff; color: #004085; }
    .status-canceled { background: #f8d7da; color: #721c24; }
    
    .btn-action {
        background: #0a2b4e;
        color: white;
        padding: 5px 12px;
        border-radius: 15px;
        text-decoration: none;
        font-size: 11px;
        margin-right: 5px;
        display: inline-block;
        transition: all 0.3s;
    }
    
    .btn-action:hover {
        background: #1e4d7c;
    }
    
    .btn-confirm {
        background: #28a745;
    }
    
    .btn-confirm:hover {
        background: #218838;
    }
    
    .btn-cancel {
        background: #dc3545;
    }
    
    .btn-cancel:hover {
        background: #c82333;
    }
    
    .empty-data {
        text-align: center;
        padding: 40px;
        color: #888;
    }
    
    .kode-booking {
        font-family: monospace;
        font-weight: bold;
        color: #e63946;
    }
</style>

<div class="page-header">
    <h1>📅 Kelola Booking</h1>
    <div class="filter-group">
        <a href="?filter=all" class="filter-btn <?= $filter == 'all' || $filter == '' ? 'active' : '' ?>">Semua</a>
        <a href="?filter=pending" class="filter-btn <?= $filter == 'pending' ? 'active' : '' ?>">Pending</a>
        <a href="?filter=confirmed" class="filter-btn <?= $filter == 'confirmed' ? 'active' : '' ?>">Confirmed</a>
        <a href="?filter=completed" class="filter-btn <?= $filter == 'completed' ? 'active' : '' ?>">Completed</a>
        <a href="?filter=canceled" class="filter-btn <?= $filter == 'canceled' ? 'active' : '' ?>">Canceled</a>
    </div>
</div>

<div class="table-container">
    <?php if (mysqli_num_rows($result) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>User</th>
                <th>Lapangan</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Durasi</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($booking = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td class="kode-booking"><?= $booking['kode_booking'] ?></td>
                <td><?= $booking['user_nama'] ?><br><small><?= $booking['no_hp'] ?></small></td>
                <td><?= $booking['nama_lapangan'] ?></td>
                <td><?= date('d/m/Y', strtotime($booking['tanggal'])) ?></td>
                <td><?= date('H:i', strtotime($booking['jam_mulai'])) ?> WIB</td>
                <td><?= $booking['durasi'] ?> jam</td>
                <td>Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></td>
                <td>
                    <?php
                    $status_class = '';
                    switch($booking['status']) {
                        case 'pending': $status_class = 'status-pending'; break;
                        case 'confirmed': $status_class = 'status-confirmed'; break;
                        case 'completed': $status_class = 'status-completed'; break;
                        case 'canceled': $status_class = 'status-canceled'; break;
                    }
                    ?>
                    <span class="status-badge <?= $status_class ?>"><?= ucfirst($booking['status']) ?></span>
                </td>
                <td>
                    <a href="detail_booking.php?id=<?= $booking['id'] ?>" class="btn-action">📋 Detail</a>
                    <?php if ($booking['status'] == 'pending'): ?>
                        <a href="konfirmasi_booking.php?id=<?= $booking['id'] ?>&action=confirm" class="btn-action btn-confirm" onclick="return confirm('Konfirmasi booking ini?')">✅ Confirm</a>
                        <a href="konfirmasi_booking.php?id=<?= $booking['id'] ?>&action=cancel" class="btn-action btn-cancel" onclick="return confirm('Batalkan booking ini?')">❌ Cancel</a>
                    <?php endif; ?>
                    <?php if ($booking['status'] == 'confirmed'): ?>
                        <a href="konfirmasi_booking.php?id=<?= $booking['id'] ?>&action=complete" class="btn-action btn-confirm" onclick="return confirm('Tandai selesai?')">✅ Complete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="empty-data">
        <p>📭 Belum ada booking.</p>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer_admin.php'; ?>