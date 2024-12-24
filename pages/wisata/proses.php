<?php
include '../../config/koneksi.php';

// Proses Tambah Data
if(isset($_POST['tambah'])) {
    $nama_wisata = $_POST['nama_wisata'];
    $deskripsi = $_POST['deskripsi'];
    $alamat = $_POST['alamat'];
    $kategori = $_POST['kategori'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    
    // Upload gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $tipe = $_FILES['gambar']['type'];
    $ukuran = $_FILES['gambar']['size'];
    
    // Tentukan ekstensi yang diizinkan
    $allowed = array('image/jpeg', 'image/png', 'image/jpg');
    
    if(!in_array($tipe, $allowed)) {
        echo "<script>
                alert('Format file tidak didukung!');
                window.location='tambah.php';
              </script>";
        exit();
    }
    
    // Generate nama unik untuk file
    $nama_file = date('YmdHis') . '_' . $gambar;
    
    // Upload file
    move_uploaded_file($tmp, "../../assets/images/wisata/" . $nama_file);
    
    // Simpan ke database
    $query = mysqli_query($koneksi, "INSERT INTO wisata (nama_wisata, deskripsi, alamat, kategori, 
                                    latitude, longitude, gambar) 
                                    VALUES ('$nama_wisata', '$deskripsi', '$alamat', '$kategori', 
                                    '$latitude', '$longitude', '$nama_file')");
    
    if($query) {
        echo "<script>
                alert('Data berhasil ditambahkan!');
                window.location='daftar-wisata.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan data!');
                window.location='tambah.php';
              </script>";
    }
}

// Proses Edit Data
if(isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama_wisata = $_POST['nama_wisata'];
    $deskripsi = $_POST['deskripsi'];
    $alamat = $_POST['alamat'];
    $kategori = $_POST['kategori'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $gambar_lama = $_POST['gambar_lama'];
    
    // Cek apakah ada file gambar baru
    if($_FILES['gambar']['name'] != "") {
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $tipe = $_FILES['gambar']['type'];
        
        $allowed = array('image/jpeg', 'image/png', 'image/jpg');
        
        if(!in_array($tipe, $allowed)) {
            echo "<script>
                    alert('Format file tidak didukung!');
                    window.location='edit.php?id=$id';
                  </script>";
            exit();
        }
        
        // Hapus gambar lama
        unlink("../../assets/images/wisata/" . $gambar_lama);
        
        // Generate nama unik untuk file baru
        $nama_file = date('YmdHis') . '_' . $gambar;
        
        // Upload file baru
        move_uploaded_file($tmp, "../../assets/images/wisata/" . $nama_file);
    } else {
        $nama_file = $gambar_lama;
    }
    
    // Update database
    $query = mysqli_query($koneksi, "UPDATE wisata SET 
                                    nama_wisata = '$nama_wisata',
                                    deskripsi = '$deskripsi',
                                    alamat = '$alamat',
                                    kategori = '$kategori',
                                    latitude = '$latitude',
                                    longitude = '$longitude',
                                    gambar = '$nama_file'
                                    WHERE id = $id");
    
    if($query) {
        echo "<script>
                alert('Data berhasil diupdate!');
                window.location='daftar-wisata.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal mengupdate data!');
                window.location='edit.php?id=$id';
              </script>";
    }
}
?>
