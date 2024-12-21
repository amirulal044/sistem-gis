<?php include '../../config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Wisata - Sistem Informasi Wisata Jepara</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.pm@latest/dist/leaflet.pm.css"/>
    
    <style>
        #map { height: 450px; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Tambah Data Wisata</h5>
            </div>
            <div class="card-body">
                <form action="proses.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nama Wisata</label>
                        <input type="text" class="form-control" name="nama_wisata" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="kategori" required>
                            <option value="Pantai">Pantai</option>
                            <option value="Gunung">Gunung</option>
                            <option value="Taman">Taman</option>
                            <option value="Budaya">Budaya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Lokasi pada Peta</label>
                        <div id="map"></div>
                        <input type="hidden" name="geojson" id="geojson">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Latitude</label>
                                <input type="text" class="form-control" name="latitude" id="latitude" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Longitude</label>
                                <input type="text" class="form-control" name="longitude" id="longitude" required readonly>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar</label>
                        <input type="file" class="form-control" name="gambar" accept="image/jpeg, image/png, image/jpg" required>
                    </div>
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                    <a href="daftar-wisata.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.pm@latest/dist/leaflet.pm.min.js"></script>
    <script>
        var map = L.map('map').setView([-6.5888, 110.6684], 11);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var currentLayer = null;

        map.pm.addControls({
            position: 'topleft',
            drawCircle: true,
            drawCircleMarker: true,
            drawPolyline: true,
            drawRectangle: true,
            drawPolygon: true,
            editMode: true,
            dragMode: true,
            cutPolygon: true,
            removalMode: true,
            drawMarker: true
        });

        map.on('pm:create', e => {
            if (currentLayer) {
                map.removeLayer(currentLayer);
            }
            currentLayer = e.layer;
            
            const latlng = currentLayer.getLatLng();
            document.getElementById('latitude').value = latlng.lat;
            document.getElementById('longitude').value = latlng.lng;
            
            document.getElementById('geojson').value = JSON.stringify(currentLayer.toGeoJSON());
        });

        map.on('pm:remove', e => {
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
            document.getElementById('geojson').value = '';
            currentLayer = null;
        });
    </script>
</body>
</html>
