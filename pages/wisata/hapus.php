<?php
include '../../config/koneksi.php';

$id = $_GET['id'];

// Ambil nama file gambar
$query = mysqli_query($koneksi, "SELECT gambar FROM wisata WHERE id = $id");
$data = mysqli_fetch_array($query);
$gambar = $data['gambar'];

// Hapus file gambar
unlink("../../assets/images/wisata/" . $gambar);

// Hapus data dari database
$query = mysqli_query($koneksi, "DELETE FROM wisata WHERE id = $id");

if($query) {
    echo "<script>
            alert('Data berhasil dihapus!');
            window.location='daftar-wisata.php';
          </script>";
} else {
    echo "<script>
            alert('Gagal menghapus data!');
            window.location='daftar-wisata.php';
          </script>";
}
?>
