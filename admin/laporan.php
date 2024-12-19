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
    <title>Manajemen Laporan - SIGAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
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
                        <a class="nav-link" href="wilayah.php">Data Wilayah</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="laporan.php">Laporan Warga</a>
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
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Daftar Laporan</h5>
                        <div class="mb-3">
                            <select class="form-select" id="filterStatus" onchange="loadLaporan()">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="proses">Proses</option>
                                <option value="selesai">Selesai</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Pelapor</th>
                                        <th>Judul</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="laporanList">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Peta Lokasi Laporan</h5>
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Laporan -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Pelapor:</strong> <span id="modalPelapor"></span></p>
                            <p><strong>Tanggal:</strong> <span id="modalTanggal"></span></p>
                            <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                            <p><strong>Judul:</strong> <span id="modalJudul"></span></p>
                            <p><strong>Deskripsi:</strong> <span id="modalDeskripsi"></span></p>
                        </div>
                        <div class="col-md-6">
                            <img id="modalFoto" class="img-fluid mb-3" alt="Foto Laporan">
                            <div id="modalMap" style="height: 200px;"></div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6>Update Status</h6>
                        <form id="updateForm">
                            <input type="hidden" id="laporanId">
                            <div class="mb-3">
                                <select class="form-select" id="newStatus" required>
                                    <option value="pending">Pending</option>
                                    <option value="proses">Proses</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" id="tindakLanjut" placeholder="Tindak lanjut..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Initialize map
        var map = L.map('map').setView([-6.5431569, 110.6862573], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var markers = L.layerGroup().addTo(map);
        var modalMap = null;

        function loadLaporan() {
            const status = document.getElementById('filterStatus').value;
            fetch(`get_laporan.php${status ? '?status=' + status : ''}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('laporanList');
                    tbody.innerHTML = '';
                    
                    // Clear existing markers
                    markers.clearLayers();

                    data.forEach(laporan => {
                        // Add marker to map
                        const marker = L.marker([laporan.latitude, laporan.longitude])
                            .bindPopup(laporan.judul)
                            .addTo(markers);

                        // Add table row
                        tbody.innerHTML += `
                            <tr>
                                <td>${laporan.created_at}</td>
                                <td>${laporan.nama}</td>
                                <td>${laporan.judul}</td>
                                <td>
                                    <span class="badge bg-${getStatusBadge(laporan.status)}">${laporan.status}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="showDetail(${laporan.id})">Detail</button>
                                </td>
                            </tr>
                        `;
                    });
                });
        }

        function getStatusBadge(status) {
            switch(status) {
                case 'pending': return 'warning';
                case 'proses': return 'info';
                case 'selesai': return 'success';
                case 'ditolak': return 'danger';
                default: return 'secondary';
            }
        }

        function showDetail(id) {
            fetch(`get_laporan_detail.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalPelapor').textContent = data.nama;
                    document.getElementById('modalTanggal').textContent = data.created_at;
                    document.getElementById('modalStatus').textContent = data.status;
                    document.getElementById('modalJudul').textContent = data.judul;
                    document.getElementById('modalDeskripsi').textContent = data.deskripsi;
                    document.getElementById('modalFoto').src = '../uploads/' + data.foto;
                    document.getElementById('laporanId').value = data.id;
                    document.getElementById('newStatus').value = data.status;
                    document.getElementById('tindakLanjut').value = data.tindak_lanjut || '';

                    // Initialize modal map
                    if (modalMap) {
                        modalMap.remove();
                    }
                    modalMap = L.map('modalMap').setView([data.latitude, data.longitude], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(modalMap);
                    L.marker([data.latitude, data.longitude]).addTo(modalMap);

                    new bootstrap.Modal(document.getElementById('detailModal')).show();
                });
        }

        document.getElementById('updateForm').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('id', document.getElementById('laporanId').value);
            formData.append('status', document.getElementById('newStatus').value);
            formData.append('tindak_lanjut', document.getElementById('tindakLanjut').value);

            fetch('update_laporan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Status laporan berhasil diupdate');
                    bootstrap.Modal.getInstance(document.getElementById('detailModal')).hide();
                    loadLaporan();
                } else {
                    alert('Gagal mengupdate status laporan');
                }
            });
        };

        // Load initial data
        loadLaporan();
    </script>
</body>
</html>
