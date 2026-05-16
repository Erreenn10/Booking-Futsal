<?php
// admin/tambah_lapangan.php - Tambah Lapangan
$title = 'Tambah Lapangan - Tsubasa Arena';
include '../includes/header_admin.php';
include '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lapangan = mysqli_real_escape_string($conn, $_POST['nama_lapangan']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $harga_per_jam = (int)$_POST['harga_per_jam'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Upload foto
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "../assets/uploads/lapangan/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $foto;
        
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $error = "Gagal upload foto!";
        }
    }
    
    if (empty($error)) {
        $query = "INSERT INTO lapangan (nama_lapangan, deskripsi, lokasi, harga_per_jam, foto, status) 
          VALUES ('$nama_lapangan', '$deskripsi', '$lokasi', '$harga_per_jam', '$foto', '$status')";
        
        if (mysqli_query($conn, $query)) {
            $success = "Lapangan berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan lapangan: " . mysqli_error($conn);
        }
    }
}
?>

<style>
    .form-container {
        background: white;
        border-radius: 16px;
        padding: 30px;
        max-width: 700px;
        margin: 0 auto;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }
    
    input, textarea, select {
        width: 100%;
        padding: 12px;
        border: 2px solid #e1e1e1;
        border-radius: 10px;
        font-size: 14px;
        font-family: 'Inter', sans-serif;
    }
    
    input:focus, textarea:focus, select:focus {
        outline: none;
        border-color: #e63946;
    }
    
    textarea {
        resize: vertical;
        min-height: 100px;
    }
    
    .btn-submit {
        background: #e63946;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 30px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-submit:hover {
        background: #c1121f;
        transform: translateY(-2px);
    }
    
    .btn-back {
        background: #888;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 30px;
        text-decoration: none;
        display: inline-block;
        margin-left: 10px;
        transition: all 0.3s;
    }
    
    .btn-back:hover {
        background: #666;
    }
    
    .alert-error {
        background: #fee;
        color: #e63946;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-left: 3px solid #e63946;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 20px;
        border-left: 3px solid #28a745;
    }
    
    .image-preview {
        margin-top: 10px;
    }
    
    .image-preview img {
        max-width: 150px;
        border-radius: 10px;
    }
    .page-header h1 {
    text-align: center;
    }
</style>

<div class="page-header" style="margin-bottom: 20px;">
    <h1>➕ Tambah Lapangan</h1>
</div>

<div class="form-container">
    <?php if ($error): ?>
        <div class="alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert-success"><?= $success ?> <a href="lapangan.php" style="color: #155724;">Lihat daftar</a></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>🏟️ Nama Lapangan</label>
            <input type="text" name="nama_lapangan" required placeholder="Contoh: Lapangan A">
        </div>
        
        <div class="form-group">
            <label>📝 Deskripsi</label>
            <textarea name="deskripsi" placeholder="Deskripsi lapangan..."></textarea>
        </div>

        <div class="form-group">
            <label>📍 Link Google Maps (Embed URL)</label>
            <input type="text" name="lokasi" placeholder="https://www.google.com/maps/embed?pb=..." value="<?= isset($_POST['lokasi']) ? $_POST['lokasi'] : '' ?>">
            <small style="color: #888;">Dapatkan dari Google Maps → Share → Embed map → copy src URL</small>
        </div>
                
        <div class="form-group">
            <label>💰 Harga per Jam</label>
            <input type="number" name="harga_per_jam" required placeholder="Contoh: 120000">
        </div>
        
        <div class="form-group">
            <label>📷 Foto Lapangan</label>
            <input type="file" name="foto" accept="image/*" onchange="previewImage(this)">
            <div class="image-preview" id="preview"></div>
        </div>
        
        <div class="form-group">
            <label>📌 Status</label>
            <select name="status">
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </div>
        
        <button type="submit" class="btn-submit">💾 Simpan Lapangan</button>
        <a href="lapangan.php" class="btn-back">↺ Kembali</a>
    </form>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 150px; border-radius: 10px; margin-top: 10px;">';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.innerHTML = '';
        }
    }
</script>

<?php include '../includes/footer_admin.php'; ?>