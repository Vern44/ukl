<?php
/**
 * includes/form_barang.php
 * Komponen form input bersama untuk tambah dan edit barang
 */
?>
<div class="form-row">
    <div class="form-group">
        <label for="kode_barang">Kode Barang <span class="required">*</span></label>
        <input type="text" id="kode_barang" name="kode_barang" class="form-control" 
               placeholder="Contoh: BRG-007" value="<?= htmlspecialchars($d['kode_barang']) ?>" required maxlength="20" autofocus>
    </div>
    <div class="form-group">
        <label for="nama_barang">Nama Barang <span class="required">*</span></label>
        <input type="text" id="nama_barang" name="nama_barang" class="form-control" 
               placeholder="Contoh: Monitor LG 27 Inch" value="<?= htmlspecialchars($d['nama_barang']) ?>" required maxlength="100">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="id_kategori">Kategori Barang <span class="required">*</span></label>
        <select id="id_kategori" name="id_kategori" class="form-control" required>
            <option value="" disabled <?= empty($d['id_kategori']) ? 'selected' : '' ?>>-- Pilih Kategori --</option>
            <?php foreach ($kategoriList as $kat): ?>
                <option value="<?= $kat['id_kategori'] ?>" <?= ($d['id_kategori'] == $kat['id_kategori']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="id_lokasi">Lokasi Penempatan <span class="required">*</span></label>
        <select id="id_lokasi" name="id_lokasi" class="form-control" required>
            <option value="" disabled <?= empty($d['id_lokasi']) ? 'selected' : '' ?>>-- Pilih Lokasi --</option>
            <?php foreach ($lokasiList as $lok): ?>
                <option value="<?= $lok['id_lokasi'] ?>" <?= ($d['id_lokasi'] == $lok['id_lokasi']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($lok['nama_lokasi']) ?> (PJ: <?= htmlspecialchars($lok['penanggung_jawab']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="stok">Jumlah Stok (Unit) <span class="required">*</span></label>
        <input type="number" id="stok" name="stok" class="form-control" placeholder="0" min="0" value="<?= htmlspecialchars((string)$d['stok']) ?>" required>
    </div>
    <div class="form-group">
        <label for="harga">Harga Satuan (Rp) <span class="required">*</span></label>
        <input type="number" id="harga" name="harga" class="form-control" placeholder="1500000" min="0" step="500" value="<?= htmlspecialchars((string)$d['harga']) ?>" required>
    </div>
</div>

<div class="form-group">
    <label for="tanggal_masuk">Tanggal Masuk <span class="required">*</span></label>
    <input type="date" id="tanggal_masuk" name="tanggal_masuk" class="form-control" value="<?= htmlspecialchars($d['tanggal_masuk']) ?>" required>
</div>

<div class="form-actions">
    <a href="index.php" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-primary"><?= $btn_label ?? '💾 Simpan' ?></button>
</div>
