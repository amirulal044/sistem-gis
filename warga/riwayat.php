<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'warga') {
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
    <title>Riwayat Laporan - SIGAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #modalMap {
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">SIGAR - Warga</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="laporan.php">Buat Laporan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="riwayat.php">Riwayat Laporan</a>
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
                        <h5 class="card-title">Riwayat Laporan Anda</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
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
                            <p><strong>Tanggal:</strong> <span id="modalTanggal"></span></p>
                            <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                            <p><strong>Judul:</strong> <span id="modalJudul"></span></p>
                            <p><strong>Deskripsi:</strong> <span id="modalDeskripsi"></span></p>
                            <p><strong>Tindak Lanjut:</strong> <span id="modalTindakLanjut"></span></p>
                        </div>
                        <div class="col-md-6">
                            <img id="modalFoto" class="img-fluid mb-3" alt="Foto Laporan">
                            <div id="modalMap"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        var modalMap = null;

        function loadLaporan() {
            fetch('get_riwayat.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('laporanList');
                    tbody.innerHTML = '';
                    
                    data.forEach(laporan => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${laporan.created_at}</td>
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
            fetch(`get_detail.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTanggal').textContent = data.created_at;
                    document.getElementById('modalStatus').textContent = data.status;
                    document.getElementById('modalJudul').textContent = data.judul;
                    document.getElementById('modalDeskripsi').textContent = data.deskripsi;
                    document.getElementById('modalTindakLanjut').textContent = data.tindak_lanjut || '-';
                    document.getElementById('modalFoto').src = '../uploads/' + data.foto;

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

        // Load initial data
        loadLaporan();
    </script>
</body>
</html>
