<?php
// riwayat.php - Riwayat Booking User
session_start();
include 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['user_id'];
$title = 'Riwayat Booking - Tsubasa Arena';
include 'includes/header.php';

$query = "SELECT b.*, l.nama_lapangan 
          FROM booking b 
          JOIN lapangan l ON b.id_lapangan = l.id 
          WHERE b.id_user = $id_user 
          ORDER BY b.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<style>
    .page-header {
        margin: 30px 0;
        text-align: center;
    }
    
    .page-header h1 {
        font-size: 32px;
        color: #0a2b4e;
    }
    
    .table-container {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
    
    .btn-cancel {
        background: #dc3545;
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        text-decoration: none;
        font-size: 11px;
        display: inline-block;
    }
    
    .btn-cancel:hover {
        background: #c82333;
    }
    
    .empty-data {
        text-align: center;
        padding: 50px;
        color: #888;
    }
    
    .kode-booking {
        font-family: monospace;
        font-weight: bold;
        color: #e63946;
    }
    
    .btn-upload {
        background: #28a745;
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        text-decoration: none;
        font-size: 11px;
        display: inline-block;
    }
    
    .btn-upload:hover {
        background: #218838;
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .container {
        flex: 1;
    }
</style>

<div class="container">
    <div class="page-header">
        <h1>📋 Riwayat Booking</h1>
        <p>Daftar booking lapangan futsal Anda</p>
    </div>
    
    <div class="table-container">
        <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Durasi</th>
                    <th>Total</th>
                    <th>Bukti</th>
                    <th>Status Bayar</th>
                    <th>Status Booking</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td class="kode-booking"><?= $booking['kode_booking'] ?></td>
                    <td><?= $booking['nama_lapangan'] ?></td>
                    <td><?= date('d/m/Y', strtotime($booking['tanggal'])) ?></td>
                    <td><?= date('H:i', strtotime($booking['jam_mulai'])) ?> WIB</td>
                    <td><?= $booking['durasi'] ?> jam</td>
                    <td>Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></td>
                    
                    <!-- Kolom Bukti -->
                    <td>
                        <?php if ($booking['bukti_pembayaran'] && file_exists('assets/uploads/bukti/' . $booking['bukti_pembayaran'])): ?>
                            <a href="assets/uploads/bukti/<?= $booking['bukti_pembayaran'] ?>" target="_blank" class="btn-upload">📎 Lihat</a>
                        <?php else: ?>
                            <?php if ($booking['status'] == 'pending'): ?>
                                <a href="upload_bukti.php?id=<?= $booking['id'] ?>" class="btn-upload">📤 Upload</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    
                    <!-- Kolom Status Pembayaran -->
                    <td>
                        <?php if ($booking['status'] == 'confirmed'): ?>
                            <span class="status-badge status-confirmed">✅ Lunas</span>
                        <?php elseif ($booking['status'] == 'pending' && $booking['bukti_pembayaran']): ?>
                            <span class="status-badge status-pending">🔄 Verifikasi</span>
                        <?php elseif ($booking['status'] == 'pending'): ?>
                            <span class="status-badge status-pending">⏳ Belum Bayar</span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    
                    <!-- Kolom Status Booking -->
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
                    
                    <!-- Kolom Aksi -->
                    <td>
                        <?php if ($booking['status'] == 'pending'): ?>
                            <a href="batalkan_booking.php?id=<?= $booking['id'] ?>" class="btn-cancel" onclick="return confirm('Yakin batalkan booking?')">Batalkan</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-data">
            <p>📭 Belum ada booking. Yuk booking lapangan sekarang!</p>
            <a href="lapangan.php" class="btn-lapangan" style="margin-top: 15px; display: inline-block;">Lihat Lapangan</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>