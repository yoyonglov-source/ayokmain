<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3">Tambah Jam Istirahat / Blokir</h6>
        <form action="{{ route('admin.field-breaks.store') }}" method="POST">
            @csrf
            <input type="hidden" name="field_id" value="{{ $field->id }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Tanggal (Opsional)</label>
                    <input type="date" name="date" class="form-control rounded-pill">
                    <div class="form-text" style="font-size: 0.65rem;">Kosongkan jika ingin blokir rutin setiap hari</div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Jam Mulai</label>
                    <input type="time" name="start_time" class="form-control rounded-pill" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Jam Selesai</label>
                    <input type="time" name="end_time" class="form-control rounded-pill" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Alasan/Keterangan</label>
                    <input type="text" name="reason" class="form-control rounded-pill" placeholder="Contoh: Maintenance atau Istirahat">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>