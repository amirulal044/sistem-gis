<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
require_once '../config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Wilayah - SIGAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">SIGAR - Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="wilayah.php">Data Wilayah</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="laporan.php">Laporan Warga</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tambah/Edit Wilayah</h5>
                        <form id="wilayahForm">
                            <input type="hidden" id="wilayahId" name="id">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Wilayah</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Area Wilayah</label>
                                <div id="map"></div>
                                <input type="hidden" id="geojson" name="geojson">
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Daftar Wilayah</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="wilayahList">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>
    <script>
        // Initialize map
        var map = L.map('map').setView([-6.5431569, 110.6862573], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Initialize draw controls
        map.pm.addControls({
            position: 'topleft',
            drawCircle: false,
            drawCircleMarker: false,
            drawPolyline: false,
            drawRectangle: true,
            drawPolygon: true,
            editMode: true,
            dragMode: true,
            cutPolygon: true,
            removalMode: true,
        });

        var currentLayer = null;

        // Handle drawing
        map.on('pm:create', e => {
            if (currentLayer) {
                map.removeLayer(currentLayer);
            }
            currentLayer = e.layer;
            document.getElementById('geojson').value = JSON.stringify(currentLayer.toGeoJSON());
        });

        // Load wilayah list
        function loadWilayahList() {
            fetch('get_wilayah.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('wilayahList');
                    tbody.innerHTML = '';
                    data.forEach(wilayah => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${wilayah.nama}</td>
                                <td>${wilayah.deskripsi || '-'}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editWilayah(${wilayah.id})">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteWilayah(${wilayah.id})">Hapus</button>
                                </td>
                            </tr>
                        `;
                    });
                });
        }

        // Handle form submission
        document.getElementById('wilayahForm').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('save_wilayah.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Data wilayah berhasil disimpan');
                    resetForm();
                    loadWilayahList();
                } else {
                    alert('Gagal menyimpan data wilayah');
                }
            });
        };

        function resetForm() {
            document.getElementById('wilayahForm').reset();
            document.getElementById('wilayahId').value = '';
            if (currentLayer) {
                map.removeLayer(currentLayer);
                currentLayer = null;
            }
            document.getElementById('geojson').value = '';
        }

        function editWilayah(id) {
            fetch(`get_wilayah_detail.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('wilayahId').value = data.id;
                    document.getElementById('nama').value = data.nama;
                    document.getElementById('deskripsi').value = data.deskripsi;
                    
                    if (currentLayer) {
                        map.removeLayer(currentLayer);
                    }
                    currentLayer = L.geoJSON(JSON.parse(data.geojson)).addTo(map);
                    document.getElementById('geojson').value = data.geojson;
                    
                    map.fitBounds(currentLayer.getBounds());
                });
        }

        function deleteWilayah(id) {
            if (confirm('Apakah Anda yakin ingin menghapus wilayah ini?')) {
                fetch(`delete_wilayah.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Wilayah berhasil dihapus');
                            loadWilayahList();
                        } else {
                            alert('Gagal menghapus wilayah');
                        }
                    });
            }
        }

        // Load initial data
        loadWilayahList();
    </script>
</body>
</html>
