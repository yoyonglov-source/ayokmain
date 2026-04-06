<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">Set Jam Istirahat / Penutupan Lapangan ({{ $field->name }})</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('field-breaks.store') }}" method="POST">
            @csrf
            <input type="hidden" name="field_id" value="{{ $field->id }}">
            
            <div class="row">
                <div class="col-md-3">
                    <label>Tanggal (Kosongkan jika Rutin)</label>
                    <input type="date" name="date" class="form-control">
                </div>
                <div class="col-md-2">
                    <label>Mulai</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label>Selesai</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label>Alasan (Opsional)</label>
                    <input type="text" name="reason" class="form-control" placeholder="Misal: Perbaikan Lampu">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-danger w-100">Blokir Jam</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Jam Terblokir</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Tipe</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Alasan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($field->breaks as $break)
                <tr>
                    <td>
                        <span class="badge {{ $break->date ? 'bg-warning text-dark' : 'bg-info' }}">
                            {{ $break->date ? 'Insidentil' : 'Rutin Harian' }}
                        </span>
                    </td>
                    <td>{{ $break->date ?? 'Setiap Hari' }}</td>
                    <td>{{ substr($break->start_time, 0, 5) }} - {{ substr($break->end_time, 0, 5) }}</td>
                    <td>{{ $break->reason ?? '-' }}</td>
                    <td>
                        <form action="{{ route('field-breaks.destroy', $break->id) }}" method="POST" onsubmit="return confirm('Aktifkan jam ini kembali?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-success">Hapus Blokir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-3 text-muted">Belum ada jam istirahat/blokir.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>