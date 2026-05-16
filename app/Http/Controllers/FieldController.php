<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ImageManager; // 1. Import Trait-nya
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FieldController extends Controller
{
    use ImageManager; // 2. Gunakan Trait di dalam class

    /**
     * Menampilkan daftar lapangan berdasarkan Gedung (Venue)
     */
    public function index($venueId)
    {
        $venue = Venue::with(['fields'])->withCount('fields')
            ->where('user_id', Auth::id())
            ->findOrFail($venueId);

        return view('admin.fields.index', compact('venue'));
    }

    /**
     * Form tambah lapangan
     */
    public function create($venueId)
    {
        $venue = Venue::where('user_id', Auth::id())->findOrFail($venueId);
        return view('admin.fields.create', compact('venue'));
    }

    /**
     * Menyimpan data lapangan baru ke database
     */
    public function store(Request $request, $venueId)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'field_type' => 'required',
            'price_regular' => 'required|numeric',
            'price_peak' => 'required|numeric',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'description' => 'nullable|string',
        ]);

        // 2. Pastikan Gedung milik owner yang sedang login
        $venue = Venue::where('user_id', Auth::id())->findOrFail($venueId);

        // 3. Siapkan data dasar
        $data = $request->all();
        $data['venue_id'] = $venue->id;
        $data['is_active'] = true;

        // 4. Proses Foto menggunakan Trait (Auto WebP & Compress)
        if ($request->hasFile('image')) {
            // Kita panggil fungsi dari Trait, arahkan ke folder 'fields'
            $data['image'] = $this->uploadAndCompress(
                $request->file('image'), 
                'fields', 
                $request->name
            );
        }

        // 5. Simpan ke Database
        Field::create($data);

        return redirect()->route('admin.venues.fields.index', $venue->id)
                         ->with('success', "Lapangan $request->name berhasil ditambahkan!");
    }

    public function edit(Venue $venue, Field $field)
    {
        // Kita kirimkan $venue dan $field agar Blade bisa menampilkan nama GOR 
        // dan mengisi value di form edit.
        return view('admin.fields.edit', compact('venue', 'field'));
    }

    /**
     * Memproses pembaruan data lapangan
     */
    // Pastikan urutannya: Request, lalu Venue, baru kemudian Field
    public function update(Request $request, Venue $venue, Field $field)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'field_type'    => 'required',
            'price_regular' => 'required|numeric',
            'price_peak'    => 'required|numeric',
            'description'   => 'nullable|string',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Opsional: Hapus foto lama agar storage tidak penuh
            if ($field->image && \Storage::disk('public')->exists($field->image)) {
                \Storage::disk('public')->delete($field->image);
            }
            $data['image'] = $request->file('image')->store('fields', 'public');
        }

        $field->update($data);

        // Gunakan $venue->id untuk redirect balik ke halaman yang benar
        return redirect()->route('admin.venues.fields.index', $venue->id)
                        ->with('success', 'Data lapangan berhasil diperbarui!');
    }

    public function getSchedules(Request $request, $fieldId)
    {
        $date = $request->query('date'); // format Y-m-d
        
        // Pastikan konversi tanggal aman dan dipaksa menjadi Integer (0-6)
        $dayOfWeek = intval(date('w', strtotime($date)));
        
        $field = Field::with('venue.operatingHours')->findOrFail($fieldId);

        // 1. Ambil jam operasional sesuai hari (Gunakan tipe data integer yang konsisten)
        $operatingHour = $field->venue->operatingHours->first(function($value) use ($dayOfWeek) {
            return intval($value->day) === $dayOfWeek;
        });
        
        // JIKA HARI TERSEBUT TIDAK DISET ATAU STATUS `is_closed` ADALAH TRUE (LIBUR)
        if (!$operatingHour || $operatingHour->is_closed == true || $operatingHour->is_closed == 1) {
            return response()->json([]); // LANGSUNG KEMBALIKAN ARRAY KOSONG (Jadwal otomatis tidak muncul)
        }

        // 2. Ambil data break & booking di tanggal tersebut
        $breaks = DB::table('field_breaks')->where('field_id', $fieldId)->where('date', $date)->get();
        $bookings = DB::table('bookings')->where('field_id', $fieldId)->where('booking_date', $date)->where('status', 'paid')->get();

        $slots = [];
        $start = Carbon::parse($operatingHour->open_time);
        $end = Carbon::parse($operatingHour->close_time);

        // 3. Generate jam per jam
        while ($start < $end) {
            $slotStart = $start->format('H:i');
            $slotEnd = $start->copy()->addHour()->format('H:i');

            // Cek apakah jam ini masuk waktu break/tutup manual oleh admin
            $isBlocked = $breaks->contains(function($b) use ($slotStart) {
                // Paksa format waktu dari database menjadi H:i (menghilangkan detik agar cocok dengan $slotStart)
                $breakStart = date('H:i', strtotime($b->start_time));
                $breakEnd = date('H:i', strtotime($b->end_time));
                
                return $slotStart >= $breakStart && $slotStart < $breakEnd;
            });

            // Cek apakah sudah di-book orang
            $isBooked = $bookings->contains('start_time', $slotStart);

            // Tentukan harga peak atau regular
            $price = $field->price_regular;
            if ($operatingHour->peak_start && $operatingHour->peak_end) {
                if ($slotStart >= $operatingHour->peak_start && $slotStart < $operatingHour->peak_end) {
                    $price = $field->price_peak;
                }
            }

            $slots[] = [
                'id' => $slotStart, 
                'start_time' => $slotStart,
                'end_time' => $slotEnd,
                'price' => $price,
                'is_booked' => $isBooked,
                'is_blocked' => $isBlocked
            ];
            
            $start->addHour();
        }

        return response()->json($slots);
    }
}