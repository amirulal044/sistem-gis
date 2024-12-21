<?php include '../../config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Wisata - Sistem Informasi Wisata Jepara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../peta/index.php">
                <i class="fas fa-map-marked-alt"></i> Wisata Jepara
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                        <a class="nav-link " href="../peta/index.php">Peta Wisata</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="daftar-wisata.php">Kelola Wisata</a>
                    </li>
                  
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Wisata</h5>
                <a href="tambah.php" class="btn btn-light">
                    <i class="fas fa-plus"></i> Tambah Wisata
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabelWisata" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Wisata</th>
                                <th>Kategori</th>
                                <th>Alamat</th>
                                <th>Koordinat</th>
                                <th>Gambar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = mysqli_query($koneksi, "SELECT * FROM wisata ORDER BY id DESC");
                            $no = 1;
                            while($data = mysqli_fetch_array($query)) {
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $data['nama_wisata'] ?></td>
                                <td><?= $data['kategori'] ?></td>
                                <td><?= $data['alamat'] ?></td>
                                <td>
                                    <small>
                                        Lat: <?= $data['latitude'] ?><br>
                                        Long: <?= $data['longitude'] ?>
                                    </small>
                                </td>
                                <td>
                                    <img src="../../assets/images/wisata/<?= $data['gambar'] ?>" 
                                         alt="<?= $data['nama_wisata'] ?>" 
                                         width="100">
                                </td>
                                <td>
                                    <a href="edit.php?id=<?= $data['id'] ?>" 
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hapus.php?id=<?= $data['id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelWisata').DataTable();
        });
    </script>
</body>
</html>
