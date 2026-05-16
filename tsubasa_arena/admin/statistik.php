<?php
// admin/statistik.php - Halaman Statistik
$title = 'Statistik - Tsubasa Arena';
include '../includes/header_admin.php';
include '../config/database.php';

// 1. Ringkasan Angka
$total_lapangan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lapangan"))['total'] ?? 0;
$total_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'user'"))['total'] ?? 0;
$total_booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking"))['total'] ?? 0;
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as total FROM booking WHERE status = 'confirmed' OR status = 'completed'"))['total'] ?? 0;

// 2. Booking per status
$status_stats = [];
$status_query = mysqli_query($conn, "SELECT status, COUNT(*) as total FROM booking GROUP BY status");
while ($row = mysqli_fetch_assoc($status_query)) {
    $status_stats[$row['status']] = $row['total'];
}

// 3. Lapangan paling populer (paling banyak dibooking)
$populer_query = "SELECT l.nama_lapangan, COUNT(b.id) as total_booking, SUM(b.total_harga) as pendapatan
                  FROM lapangan l
                  LEFT JOIN booking b ON l.id = b.id_lapangan
                  GROUP BY l.id
                  ORDER BY total_booking DESC
                  LIMIT 5";
$populer_result = mysqli_query($conn, $populer_query);

// 4. Statistik per bulan (6 bulan terakhir)
$bulan_query = "SELECT DATE_FORMAT(tanggal, '%M %Y') as bulan, 
                       DATE_FORMAT(tanggal, '%Y-%m') as bulan_key,
                       COUNT(*) as total_booking,
                       SUM(total_harga) as pendapatan
                FROM booking 
                WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY YEAR(tanggal), MONTH(tanggal)
                ORDER BY tanggal ASC";
$bulan_result = mysqli_query($conn, $bulan_query);

// 5. Booking per hari dalam seminggu
$hari_query = "SELECT DAYNAME(tanggal) as hari, COUNT(*) as total 
               FROM booking 
               GROUP BY DAYNAME(tanggal)
               ORDER BY FIELD(DAYNAME(tanggal), 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
$hari_result = mysqli_query($conn, $hari_query);
?>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
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
        font-size: 13px;
        margin-bottom: 6px;
    }
    
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #0a2b4e;
    }
    
    .stat-icon {
        font-size: 40px;
        opacity: 0.3;
    }
    
    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .chart-title {
        font-size: 18px;
        margin-bottom: 20px;
        border-left: 4px solid #e63946;
        padding-left: 12px;
    }
    
    .two-columns {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        margin-bottom: 25px;
    }
    
    /* Bar Chart */
    .bar-item {
        margin-bottom: 15px;
    }
    
    .bar-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 13px;
    }
    
    .bar-bg {
        background: #f0f0f0;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .bar-fill {
        background: linear-gradient(135deg, #e63946 0%, #c1121f 100%);
        height: 30px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 10px;
        color: white;
        font-size: 12px;
        font-weight: bold;
    }
    
    /* Status Cards */
    .status-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .status-item {
        background: white;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .status-count {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .status-label {
        font-size: 12px;
        color: #888;
    }
    
    .status-pending { border-top: 4px solid #ffc107; }
    .status-confirmed { border-top: 4px solid #28a745; }
    .status-completed { border-top: 4px solid #17a2b8; }
    .status-canceled { border-top: 4px solid #dc3545; }
    
    /* Table */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th {
        background: linear-gradient(135deg, #0a2b4e 0%, #1e4d7c 100%);
        color: white;
        padding: 12px;
        text-align: left;
        font-size: 13px;
    }
    
    .data-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #eee;
        font-size: 13px;
    }
    
    .data-table tr:hover {
        background: #f8f9fa;
    }
    
    @media (max-width: 768px) {
        .two-columns {
            grid-template-columns: 1fr;
        }
        .status-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Ringkasan Angka -->
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
            <h4>💰 Total Pendapatan</h4>
            <div class="stat-number">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
        </div>
        <div class="stat-icon">💰</div>
    </div>
</div>

<!-- Status Booking -->
<div class="status-grid">
    <div class="status-item status-pending">
        <div class="status-count"><?= $status_stats['pending'] ?? 0 ?></div>
        <div class="status-label">⏳ Pending</div>
    </div>
    <div class="status-item status-confirmed">
        <div class="status-count"><?= $status_stats['confirmed'] ?? 0 ?></div>
        <div class="status-label">✅ Confirmed</div>
    </div>
    <div class="status-item status-completed">
        <div class="status-count"><?= $status_stats['completed'] ?? 0 ?></div>
        <div class="status-label">🏆 Completed</div>
    </div>
    <div class="status-item status-canceled">
        <div class="status-count"><?= $status_stats['canceled'] ?? 0 ?></div>
        <div class="status-label">❌ Canceled</div>
    </div>
</div>

<div class="two-columns">
    <!-- Lapangan Terpopuler -->
    <div class="chart-card">
        <h3 class="chart-title">🏆 Lapangan Terpopuler</h3>
        <?php if (mysqli_num_rows($populer_result) > 0): ?>
            <?php 
            $max_booking = 1;
            $populer_data = [];
            while ($row = mysqli_fetch_assoc($populer_result)) {
                $populer_data[] = $row;
                if ($row['total_booking'] > $max_booking) $max_booking = $row['total_booking'];
            }
            foreach ($populer_data as $row):
                $persen = $max_booking > 0 ? ($row['total_booking'] / $max_booking) * 100 : 0;
            ?>
            <div class="bar-item">
                <div class="bar-label">
                    <span><?= $row['nama_lapangan'] ?></span>
                    <span><?= $row['total_booking'] ?> booking | Rp <?= number_format($row['pendapatan'], 0, ',', '.') ?></span>
                </div>
                <div class="bar-bg">
                    <div class="bar-fill" style="width: <?= $persen ?>%;">
                        <?= $persen > 15 ? number_format($row['total_booking']) : '' ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:#888;">Belum ada data booking.</p>
        <?php endif; ?>
    </div>
    
    <!-- Booking per Hari (dalam seminggu) -->
    <div class="chart-card">
        <h3 class="chart-title">📊 Booking per Hari</h3>
        <?php 
        $hari_array = [];
        while ($row = mysqli_fetch_assoc($hari_result)) {
            $hari_array[] = $row;
        }
        $max_hari = !empty($hari_array) ? max(array_column($hari_array, 'total')) : 1;
        foreach ($hari_array as $row):
            $persen = ($row['total'] / $max_hari) * 100;
        ?>
        <div class="bar-item">
            <div class="bar-label">
                <span><?= $row['hari'] ?></span>
                <span><?= $row['total'] ?> booking</span>
            </div>
            <div class="bar-bg">
                <div class="bar-fill" style="width: <?= $persen ?>%; background: linear-gradient(135deg, #0a2b4e 0%, #1e4d7c 100%);">
                    <?= $persen > 15 ? $row['total'] : '' ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($hari_array)): ?>
            <p style="color:#888;">Belum ada data booking.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Statistik per Bulan -->
<div class="chart-card">
    <h3 class="chart-title">📈 Statistik per Bulan (6 Bulan Terakhir)</h3>
    <?php 
    $bulan_data = [];
    while ($row = mysqli_fetch_assoc($bulan_result)) {
        $bulan_data[] = $row;
    }
    $max_pendapatan = !empty($bulan_data) ? max(array_column($bulan_data, 'pendapatan')) : 1;
    ?>
    
    <?php if (!empty($bulan_data)): ?>
        <?php foreach ($bulan_data as $row): 
            $persen = ($row['pendapatan'] / $max_pendapatan) * 100;
        ?>
        <div class="bar-item">
            <div class="bar-label">
                <span><?= $row['bulan'] ?></span>
                <span><?= $row['total_booking'] ?> booking | Rp <?= number_format($row['pendapatan'], 0, ',', '.') ?></span>
            </div>
            <div class="bar-bg">
                <div class="bar-fill" style="width: <?= $persen ?>%;">
                    <?= $persen > 15 ? 'Rp ' . number_format($row['pendapatan'], 0, ',', '.') : '' ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color:#888;">Belum ada data booking.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer_admin.php'; ?>