<?php
// admin/index.php - Dashboard Admin Tsubasa Arena
$title = 'Dashboard - Tsubasa Arena';
include '../includes/header_admin.php';
include '../config/database.php';

// Statistik
$total_lapangan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lapangan"))['total'] ?? 0;
$total_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'user'"))['total'] ?? 0;
$total_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking"))['total'] ?? 0;
$booking_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE tanggal = CURDATE()"))['total'] ?? 0;
$pending_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE status = 'pending'"))['total'] ?? 0;
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as total FROM booking WHERE status = 'confirmed' OR status = 'completed'"))['total'] ?? 0;

// 5 booking terbaru
$recent_booking = mysqli_query($conn, "SELECT b.*, u.nama as user_nama, l.nama_lapangan 
                                       FROM booking b 
                                       JOIN users u ON b.id_user = u.id 
                                       JOIN lapangan l ON b.id_lapangan = l.id 
                                       ORDER BY b.created_at DESC LIMIT 5");
?>

<style>
    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, #e63946 0%, #c1121f 100%);
        border-radius: 16px;
        padding: 20px 25px;
        margin-bottom: 25px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .welcome-banner h3 {
        font-size: 20px;
        margin-bottom: 5px;
    }
    
    .welcome-banner p {
        opacity: 0.9;
        font-size: 13px;
    }
    
    .date-badge {
        background: rgba(255,255,255,0.2);
        padding: 6px 15px;
        border-radius: 25px;
        font-size: 12px;
    }
    
    /* Stat Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 18px;
        margin-bottom: 25px;
    }
    
    .stat-card {
        background: white;
        border-radius: 14px;
        padding: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: transform 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    
    .stat-info h4 {
        color: #888;
        font-size: 12px;
        margin-bottom: 6px;
    }
    
    .stat-number {
        font-size: 24px;
        font-weight: 700;
        color: #0a2b4e;
    }
    
    .stat-icon {
        font-size: 38px;
        opacity: 0.3;
    }
    
    /* Section Title */
    .section-title {
        font-size: 17px;
        margin-bottom: 15px;
        border-left: 4px solid #e63946;
        padding-left: 12px;
    }
    
    /* Table */
    .table-container {
        background: white;
        border-radius: 14px;
        padding: 18px;
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
        padding: 12px 10px;
        text-align: left;
        font-size: 12px;
    }
    
    td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #eee;
        font-size: 12px;
    }
    
    tr:hover {
        background: #f8f9fa;
    }
    
    .status-badge {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: bold;
        display: inline-block;
    }
    
    .status-pending { background: #fff3cd; color: #856404; }
    .status-confirmed { background: #d4edda; color: #155724; }
    .status-completed { background: #cce5ff; color: #004085; }
    .status-canceled { background: #f8d7da; color: #721c24; }
    
    .empty-data {
        text-align: center;
        padding: 30px;
        color: #888;
    }
    
    @media (max-width: 768px) {
        .welcome-banner {
            flex-direction: column;
            text-align: center;
            gap: 10px;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <div>
        <h3>⚡ Selamat Datang, <?= $_SESSION['admin_nama'] ?>!</h3>
        <p>Kelola lapangan, booking, dan user dengan mudah di dashboard Tsubasa Arena.</p>
    </div>
    <div class="date-badge">
        📅 <?= date('l, d F Y') ?>
    </div>
</div>

<!-- STATISTIK -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h4>🏟️ Total Lapangan</h4>
            <div class="stat-number"><?= $total_lapangan ?></div>
        </div>
        <div class="stat-icon">🏟️</div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h4>👥 Total User</h4>
            <div class="stat-number"><?= $total_user ?></div>
        </div>
        <div class="stat-icon">👥</div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h4>📅 Total Booking</h4>
            <div class="stat-number"><?= $total_booking ?></div>
        </div>
        <div class="stat-icon">📅</div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h4>⏳ Booking Pending</h4>
            <div class="stat-number"><?= $pending_booking ?></div>
        </div>
        <div class="stat-icon">⏳</div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h4>💰 Total Pendapatan</h4>
            <div class="stat-number">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
        </div>
        <div class="stat-icon">💰</div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h4>📆 Booking Hari Ini</h4>
            <div class="stat-number"><?= $booking_hari_ini ?></div>
        </div>
        <div class="stat-icon">📆</div>
    </div>
</div>

<!-- BOOKING TERBARU -->
<h3 class="section-title">📋 Booking Terbaru</h3>
<div class="table-container">
    <?php if (mysqli_num_rows($recent_booking) > 0): ?>
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
            </tr>
        </thead>
        <tbody>
            <?php while ($booking = mysqli_fetch_assoc($recent_booking)): ?>
            <tr>
                <td><?= $booking['kode_booking'] ?></td>
                <td><?= $booking['user_nama'] ?></td>
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