<?php
// index.php - Halaman Depan User
session_start();
include 'config/database.php';

$title = 'Beranda - Tsubasa Arena';
include 'includes/header.php';
?>

<style>
    .hero {
        background: linear-gradient(135deg, #0a2b4e 0%, #1e4d7c 100%);
        border-radius: 24px;
        padding: 50px;
        text-align: center;
        color: white;
        margin: 30px 0;
    }
    
    .hero h1 {
        font-size: 42px;
        margin-bottom: 15px;
    }
    
    .hero p {
        font-size: 16px;
        margin-bottom: 25px;
        opacity: 0.9;
    }
    
    .btn-lapangan {
        background: #e63946;
        color: white;
        padding: 12px 30px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        transition: all 0.3s;
    }
    
    .btn-lapangan:hover {
        background: #c1121f;
        transform: translateY(-2px);
    }
    
    .section-title {
        font-size: 24px;
        margin: 40px 0 20px;
        border-left: 4px solid #e63946;
        padding-left: 15px;
    }
    
    .lapangan-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }
    
    .lapangan-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    
    .lapangan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .lapangan-img {
        height: 180px;
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: flex-end;
        justify-content: flex-start;
        padding: 10px;
    }
    
    .lapangan-status {
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
    }
    
    .lapangan-info {
        padding: 20px;
    }
    
    .lapangan-info h3 {
        color: #0a2b4e;
        margin-bottom: 8px;
    }
    
    .lapangan-info p {
        color: #666;
        font-size: 13px;
        margin-bottom: 12px;
    }
    
    .harga {
        font-size: 20px;
        font-weight: bold;
        color: #e63946;
        margin-bottom: 15px;
    }
    
    .btn-booking {
        background: #0a2b4e;
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-block;
        transition: all 0.3s;
    }
    
    .btn-booking:hover {
        background: #1e4d7c;
    }
</style>

<div class="container">
    <!-- Hero Section -->
    <div class="hero">
        <h1>⚡ Tsubasa Arena ⚡</h1>
        <p>Booking lapangan futsal dengan mudah dan cepat. Terbang tinggi seperti Tsubasa!</p>
        <a href="lapangan.php" class="btn-lapangan">Lihat Lapangan →</a>
    </div>
    
    <!-- Daftar Lapangan -->
    <h2 class="section-title">🏟️ Lapangan Futsal</h2>
    
    <div class="lapangan-grid">
        <?php
        $query = "SELECT * FROM lapangan WHERE status = 'aktif' ORDER BY id DESC LIMIT 3";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0):
            while ($lapangan = mysqli_fetch_assoc($result)):
        ?>
        <div class="lapangan-card">
            <div class="lapangan-img" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('assets/uploads/lapangan/<?= $lapangan['foto'] ?? 'default.jpg' ?>');">
                <span class="lapangan-status">✅ Tersedia</span>
            </div>
            <div class="lapangan-info">
                <h3><?= $lapangan['nama_lapangan'] ?></h3>
                <p><?= substr($lapangan['deskripsi'], 0, 80) ?>...</p>
                <div class="harga">Rp <?= number_format($lapangan['harga_per_jam'], 0, ',', '.') ?> <span style="font-size: 14px;">/ jam</span></div>
                <a href="detail_lapangan.php?id=<?= $lapangan['id'] ?>" class="btn-booking">Booking Sekarang →</a>
            </div>
        </div>
        <?php 
            endwhile;
        else:
        ?>
        <p style="color:#888;">Belum ada lapangan tersedia.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>