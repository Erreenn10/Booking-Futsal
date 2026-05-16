<?php
// admin/detail_booking.php - Detail Booking
$title = 'Detail Booking - Tsubasa Arena';
include '../includes/header_admin.php';
include '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT b.*, u.nama as user_nama, u.email, u.no_hp, l.nama_lapangan, l.harga_per_jam
          FROM booking b 
          JOIN users u ON b.id_user = u.id 
          JOIN lapangan l ON b.id_lapangan = l.id 
          WHERE b.id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: booking.php");
    exit();
}

$booking = mysqli_fetch_assoc($result);
?>

<style>
    .detail-container {
        background: white;
        border-radius: 16px;
        padding: 30px;
        max-width: 600px;
        margin: 0 auto;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .detail-header {
        border-bottom: 2px solid #e63946;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }
    
    .detail-header h1 {
        color: #0a2b4e;
    }
    
    .detail-row {
        display: flex;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    
    .detail-label {
        width: 140px;
        font-weight: 600;
        color: #555;
    }
    
    .detail-value {
        flex: 1;
        color: #333;
    }
    
    .kode-booking {
        font-family: monospace;
        font-size: 18px;
        font-weight: bold;
        color: #e63946;
    }
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        display: inline-block;
    }
    
    .status-pending { background: #fff3cd; color: #856404; }
    .status-confirmed { background: #d4edda; color: #155724; }
    .status-completed { background: #cce5ff; color: #004085; }
    .status-canceled { background: #f8d7da; color: #721c24; }
    
    .btn-back {
        background: #888;
        color: white;
        padding: 10px 24px;
        border: none;
        border-radius: 30px;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
        transition: all 0.3s;
    }
    
    .btn-back:hover {
        background: #666;
    }
    
    .action-buttons {
        margin-top: 20px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .btn-confirm {
        background: #28a745;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
    }
    
    .btn-cancel {
        background: #dc3545;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
    }
</style>

<div class="detail-container">
    <div class="detail-header">
        <h1>📋 Detail Booking</h1>
        <p class="kode-booking">Kode: <?= $booking['kode_booking'] ?></p>
    </div>
    
    <div class="detail-row">
        <div class="detail-label">User</div>
        <div class="detail-value"><?= $booking['user_nama'] ?></div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Email / No. HP</div>
        <div class="detail-value"><?= $booking['email'] ?> / <?= $booking['no_hp'] ?></div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Lapangan</div>
        <div class="detail-value"><?= $booking['nama_lapangan'] ?></div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Tanggal</div>
        <div class="detail-value"><?= date('l, d F Y', strtotime($booking['tanggal'])) ?></div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Jam Mulai</div>
        <div class="detail-value"><?= date('H:i', strtotime($booking['jam_mulai'])) ?> WIB</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Durasi</div>
        <div class="detail-value"><?= $booking['durasi'] ?> jam</div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Total Harga</div>
        <div class="detail-value">Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></div>
    </div>
    <div class="detail-row">
        <div class="detail-label">Status</div>
        <div class="detail-value">
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
        </div>
    </div>
    <?php if ($booking['catatan']): ?>
    <div class="detail-row">
        <div class="detail-label">Catatan</div>
        <div class="detail-value"><?= nl2br($booking['catatan']) ?></div>
    </div>
    <?php endif; ?>

    <?php if ($booking['bukti_pembayaran'] && file_exists('../assets/uploads/bukti/' . $booking['bukti_pembayaran'])): ?>
    <div class="detail-row">
        <div class="detail-label">📎 Bukti Transfer</div>
        <div class="detail-value">
            <a href="../assets/uploads/bukti/<?= $booking['bukti_pembayaran'] ?>" target="_blank" class="btn-action">Lihat Bukti</a>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($booking['status'] == 'pending'): ?>
    <div class="action-buttons">
        <a href="konfirmasi_booking.php?id=<?= $booking['id'] ?>&action=confirm" class="btn-confirm" onclick="return confirm('Konfirmasi booking ini?')">✅ Konfirmasi Booking</a>
        <a href="konfirmasi_booking.php?id=<?= $booking['id'] ?>&action=cancel" class="btn-cancel" onclick="return confirm('Batalkan booking ini?')">❌ Batalkan Booking</a>
    </div>
    <?php endif; ?>
    
    <?php if ($booking['status'] == 'confirmed'): ?>
    <div class="action-buttons">
        <a href="konfirmasi_booking.php?id=<?= $booking['id'] ?>&action=complete" class="btn-confirm" onclick="return confirm('Tandai booking ini selesai?')">✅ Tandai Selesai</a>
    </div>
    <?php endif; ?>
    
    <a href="booking.php" class="btn-back">↺ Kembali ke Daftar Booking</a>
</div>

<?php include '../includes/footer_admin.php'; ?>