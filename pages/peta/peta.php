<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}
include '../../config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peta Wisata - Sistem Informasi Wisata Jepara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <style>
        #map {
            height: 75vh;
            width: 100%;
            border-radius: 10px;
        }
        .info-box {
            padding: 10px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        .legend {
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        .legend i {
            width: 18px;
            height: 18px;
            float: left;
            margin-right: 8px;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="peta.php">
                <i class="fas fa-map-marked-alt"></i> Wisata Jepara
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="peta.php">Peta Wisata</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../wisata/daftar-wisata.php">Kelola Wisata</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-user"></i> <?= $_SESSION['nama_lengkap'] ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../auth/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Filter Wisata</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Pantai" id="filterPantai" checked>
                                    <label class="form-check-label" for="filterPantai">
                                        <i class="fas fa-umbrella-beach text-info"></i> Pantai
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Gunung" id="filterGunung" checked>
                                    <label class="form-check-label" for="filterGunung">
                                        <i class="fas fa-mountain text-success"></i> Gunung
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Taman" id="filterTaman" checked>
                                    <label class="form-check-label" for="filterTaman">
                                        <i class="fas fa-tree text-warning"></i> Taman
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Budaya" id="filterBudaya" checked>
                                    <label class="form-check-label" for="filterBudaya">
                                        <i class="fas fa-landmark text-danger"></i> Budaya
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([-6.5888, 110.6684], 11);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Menyimpan semua marker dalam grup berdasarkan kategori
        var markers = {
            'Pantai': L.layerGroup(),
            'Gunung': L.layerGroup(),
            'Taman': L.layerGroup(),
            'Budaya': L.layerGroup()
        };

        // Warna ikon untuk setiap kategori
        var iconColors = {
            'Pantai': 'blue',
            'Gunung': 'green',
            'Taman': 'orange',
            'Budaya': 'red'
        };

        <?php
        $query = mysqli_query($koneksi, "SELECT * FROM wisata");
        while($data = mysqli_fetch_array($query)) {
        ?>
            var icon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color: ${iconColors['<?= $data['kategori'] ?>']}" 
                       class="marker-pin"></div><i class="fas fa-map-marker-alt" 
                       style="color: ${iconColors['<?= $data['kategori'] ?>']};"></i>`,
                iconSize: [30, 42],
                iconAnchor: [15, 42]
            });

            var marker = L.marker([<?= $data['latitude'] ?>, <?= $data['longitude'] ?>], {icon: icon})
                .bindPopup(`
                    <div class="info-box">
                        <h6><?= $data['nama_wisata'] ?></h6>
                        <img src="../../assets/images/wisata/<?= $data['gambar'] ?>" 
                             style="width:100%;max-width:200px;height:auto;margin:5px 0;">
                        <p><strong>Kategori:</strong> <?= $data['kategori'] ?></p>
                        <p><strong>Alamat:</strong> <?= $data['alamat'] ?></p>
                        <p><?= substr($data['deskripsi'], 0, 100) ?>...</p>
                    </div>
                `);
            
            markers['<?= $data['kategori'] ?>'].addLayer(marker);
        <?php } ?>

        // Tambahkan semua layer grup ke peta
        Object.values(markers).forEach(layer => map.addLayer(layer));

        // Event listener untuk checkbox filter
        $('.form-check-input').on('change', function() {
            var kategori = $(this).val();
            if(this.checked) {
                map.addLayer(markers[kategori]);
            } else {
                map.removeLayer(markers[kategori]);
            }
        });

        // Tambahkan legenda
        var legend = L.control({position: 'bottomright'});
        legend.onAdd = function(map) {
            var div = L.DomUtil.create('div', 'legend');
            div.innerHTML = '<h6>Kategori Wisata</h6>';
            Object.entries(iconColors).forEach(([kategori, color]) => {
                div.innerHTML += `
                    <div class="mb-1">
                        <i style="background: ${color}"></i>
                        ${kategori}
                    </div>`;
            });
            return div;
        };
        legend.addTo(map);
    </script>

    <style>
        .marker-pin {
            width: 30px;
            height: 30px;
            border-radius: 50% 50% 50% 0;
            position: absolute;
            transform: rotate(-45deg);
            left: 50%;
            top: 50%;
            margin: -15px 0 0 -15px;
        }

        .custom-div-icon i {
            position: absolute;
            width: 22px;
            font-size: 22px;
            left: 0;
            right: 0;
            margin: 10px auto;
            text-align: center;
        }
    </style>
</body>
</html>
