<?php
// admin/edit_lapangan.php - Edit Lapangan
$title = 'Edit Lapangan - Tsubasa Arena';
include '../includes/header_admin.php';
include '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = "SELECT * FROM lapangan WHERE id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: lapangan.php");
    exit();
}

$lapangan = mysqli_fetch_assoc($result);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lapangan = mysqli_real_escape_string($conn, $_POST['nama_lapangan']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $harga_per_jam = (int)$_POST['harga_per_jam'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $foto = $lapangan['foto'];
    
    // Upload foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "../assets/uploads/lapangan/";
        $file_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_baru = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $foto_baru;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            // Hapus foto lama
            if ($lapangan['foto'] && file_exists($target_dir . $lapangan['foto'])) {
                unlink($target_dir . $lapangan['foto']);
            }
            $foto = $foto_baru;
        }
    }
    
    // Hapus foto jika dicentang
    if (isset($_POST['hapus_foto']) && $_POST['hapus_foto'] == '1') {
        if ($lapangan['foto'] && file_exists("../assets/uploads/lapangan/" . $lapangan['foto'])) {
            unlink("../assets/uploads/lapangan/" . $lapangan['foto']);
        }
        $foto = '';
    }
    
    $update = "UPDATE lapangan SET 
           nama_lapangan = '$nama_lapangan',
           deskripsi = '$deskripsi',
           lokasi = '$lokasi',
           harga_per_jam = '$harga_per_jam',
           foto = '$foto',
           status = '$status'
           WHERE id = $id";
    
    if (mysqli_query($conn, $update)) {
        $success = "Lapangan berhasil diupdate!";
        $lapangan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM lapangan WHERE id = $id"));
    } else {
        $error = "Gagal mengupdate lapangan!";
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
    }
    
    input:focus, textarea:focus, select:focus {
        outline: none;
        border-color: #e63946;
    }
    
    textarea {
        resize: vertical;
        min-height: 100px;
    }
    
    .current-image {
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .current-image img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 10px;
    }
    
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
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
    }
    
    .alert-error {
        background: #fee;
        color: #e63946;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .page-header h1 {
    text-align: center;
    }
</style>

<div class="page-header" style="margin-bottom: 20px;">
    <h1>✏️ Edit Lapangan</h1>
</div>

<div class="form-container">
    <?php if ($error): ?>
        <div class="alert-error"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert-success"><?= $success ?> <a href="lapangan.php" style="color: #155724;">Kembali ke daftar</a></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>🏟️ Nama Lapangan</label>
            <input type="text" name="nama_lapangan" value="<?= $lapangan['nama_lapangan'] ?>" required>
        </div>
        
        <div class="form-group">
            <label>📝 Deskripsi</label>
            <textarea name="deskripsi"><?= $lapangan['deskripsi'] ?></textarea>
        </div>

        <div class="form-group">
            <label>📍 Link Google Maps (Embed URL)</label>
            <input type="text" name="lokasi" value="<?= htmlspecialchars($lapangan['lokasi']) ?>" placeholder="https://www.google.com/maps/embed?pb=...">
            <small style="color: #888;">Dapatkan dari Google Maps → Share → Embed map → copy src URL</small>
        </div>
                
        <div class="form-group">
            <label>💰 Harga per Jam</label>
            <input type="number" name="harga_per_jam" value="<?= $lapangan['harga_per_jam'] ?>" required>
        </div>
        
        <div class="form-group">
            <label>📷 Foto Lapangan</label>
            <input type="file" name="foto" accept="image/*" onchange="previewImage(this)">
            
            <?php if ($lapangan['foto'] && file_exists("../assets/uploads/lapangan/" . $lapangan['foto'])): ?>
            <div class="current-image">
                <img src="../assets/uploads/lapangan/<?= $lapangan['foto'] ?>" alt="Current">
                <div class="checkbox-group">
                    <input type="checkbox" name="hapus_foto" value="1" id="hapus_foto">
                    <label for="hapus_foto">Hapus foto ini</label>
                </div>
            </div>
            <?php endif; ?>
            <div id="preview" class="current-image"></div>
        </div>
        
        <div class="form-group">
            <label>📌 Status</label>
            <select name="status">
                <option value="aktif" <?= $lapangan['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="nonaktif" <?= $lapangan['status'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>
        
        <button type="submit" class="btn-submit">💾 Update Lapangan</button>
        <a href="lapangan.php" class="btn-back">↺ Kembali</a>
    </form>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px; margin-top: 10px;">';
            }
            reader.readAsDataURL(input.files[0]);
        } else if (!input.files || input.files.length === 0) {
            preview.innerHTML = '';
        }
    }
</script>

<?php include '../includes/footer_admin.php'; ?>