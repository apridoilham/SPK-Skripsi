<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier; // Renamed from Pelamar
use App\Models\User;
use App\Models\Kriteria;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class SpkController extends Controller
{
    // === HELPER PENCATAT LOG ===
    private function logActivity($message, $type = 'info')
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'description' => $message,
            'type' => $type
        ]);
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Admin: Full Access
        if ($user->role == 'admin') {
            $users = User::all();
            $suppliers = Supplier::all();
            $logs = ActivityLog::with('user')->latest()->paginate(20);
            
            return view('dashboard_admin', compact('users', 'suppliers', 'logs'));

        // Manager (formerly HRD): View Rankings & Analytics
        } elseif ($user->role == 'hrd') {
            $suppliers = Supplier::all();
            $kriterias = Kriteria::all();
            $ranking = Supplier::orderByDesc('skor_akhir')->get();

            // --- HITUNG MATRIKS NORMALISASI (Display) ---
            $matriks = [];
            $dataNilai = [];
            foreach ($suppliers as $s) {
                foreach($kriterias as $k) {
                    $val = isset($s->nilai_kriteria[$k->kode]) ? (float)$s->nilai_kriteria[$k->kode] : 0;
                    $dataNilai[$k->kode][] = $val;
                }
            }
            $minMax = [];
            foreach($kriterias as $k) {
                if(!empty($dataNilai[$k->kode])) {
                    if($k->jenis == 'cost') {
                        $min = min($dataNilai[$k->kode]);
                        $minMax[$k->kode] = $min == 0 ? 1 : $min;
                    } else {
                        $max = max($dataNilai[$k->kode]);
                        $minMax[$k->kode] = $max == 0 ? 1 : $max;
                    }
                } else {
                    $minMax[$k->kode] = 1;
                }
            }
            foreach ($suppliers as $s) {
                $baris = ['nama' => $s->nama];
                foreach($kriterias as $k) {
                    $nilai = isset($s->nilai_kriteria[$k->kode]) ? (float)$s->nilai_kriteria[$k->kode] : 0;
                    $norm = ($k->jenis == 'cost') ? ($nilai != 0 ? $minMax[$k->kode] / $nilai : 0) : ($nilai / $minMax[$k->kode]);
                    $baris[$k->kode] = number_format($norm, 3);
                }
                $matriks[] = $baris;
            }

            return view('dashboard_manager', compact('suppliers', 'ranking', 'kriterias', 'matriks'));

        // Staff (formerly Pelamar): Input Data & Negotiation
        } else {
            // Staff sees ALL suppliers to manage them
            $suppliers = Supplier::all(); 
            return view('dashboard_staff', compact('suppliers'));
        }
    }

    // --- DETAIL PERHITUNGAN SAW ---
    public function detailPerhitungan()
    {
        $suppliers = Supplier::all();
        $kriterias = Kriteria::all();

        // 1. Matriks Keputusan (X)
        $matriksX = [];
        foreach ($suppliers as $s) {
            $row = ['nama' => $s->nama];
            foreach($kriterias as $k) {
                $val = isset($s->nilai_kriteria[$k->kode]) ? (float)$s->nilai_kriteria[$k->kode] : 0;
                $row[$k->kode] = $val;
            }
            $matriksX[] = $row;
        }

        // 2. Normalisasi Matriks (R)
        $matriksR = [];
        $dataNilai = [];
        foreach ($suppliers as $s) {
            foreach($kriterias as $k) {
                $val = isset($s->nilai_kriteria[$k->kode]) ? (float)$s->nilai_kriteria[$k->kode] : 0;
                $dataNilai[$k->kode][] = $val;
            }
        }
        $minMax = [];
        foreach($kriterias as $k) {
            if(!empty($dataNilai[$k->kode])) {
                if($k->jenis == 'cost') {
                    $min = min($dataNilai[$k->kode]);
                    $minMax[$k->kode] = $min == 0 ? 1 : $min;
                } else {
                    $max = max($dataNilai[$k->kode]);
                    $minMax[$k->kode] = $max == 0 ? 1 : $max;
                }
            } else {
                $minMax[$k->kode] = 1;
            }
        }
        foreach ($suppliers as $s) {
            $row = ['nama' => $s->nama];
            foreach($kriterias as $k) {
                $nilai = isset($s->nilai_kriteria[$k->kode]) ? (float)$s->nilai_kriteria[$k->kode] : 0;
                $norm = ($k->jenis == 'cost') ? ($nilai != 0 ? $minMax[$k->kode] / $nilai : 0) : ($nilai / $minMax[$k->kode]);
                $row[$k->kode] = number_format($norm, 3);
            }
            $matriksR[] = $row;
        }

        // 3. Matriks Terbobot (V) & Skor Akhir
        $matriksV = [];
        $ranking = [];
        foreach ($suppliers as $s) {
            $row = ['nama' => $s->nama];
            $skor = 0;
            foreach($kriterias as $k) {
                $nilai = isset($s->nilai_kriteria[$k->kode]) ? (float)$s->nilai_kriteria[$k->kode] : 0;
                $norm = ($k->jenis == 'cost') ? ($nilai != 0 ? $minMax[$k->kode] / $nilai : 0) : ($nilai / $minMax[$k->kode]);
                $weighted = $norm * $k->bobot;
                $row[$k->kode] = number_format($weighted, 3);
                $skor += $weighted;
            }
            $row['skor_akhir'] = number_format($skor, 3);
            $matriksV[] = $row;
            
            $s->skor_kalkulasi = $skor; 
            $ranking[] = $s;
        }

        usort($ranking, function($a, $b) {
            return $b->skor_kalkulasi <=> $a->skor_kalkulasi;
        });

        $rankingArray = [];
        foreach($ranking as $r) {
            $rankingArray[] = [
                'nama' => $r->nama,
                'skor_kalkulasi' => number_format($r->skor_kalkulasi, 3)
            ];
        }

        return view('detail_perhitungan', [
            'kriterias' => $kriterias,
            'matriksX' => $matriksX,
            'matriksR' => $matriksR,
            'matriksV' => $matriksV,
            'ranking' => $rankingArray
        ]);
    }

    public function prosesHitungRanking()
    {
        $suppliers = Supplier::all();
        if ($suppliers->isEmpty()) return back()->with('error', __('No supplier data found.'));
        $kriterias = Kriteria::all();
        if ($kriterias->isEmpty()) return back()->with('error', __('Criteria not set.'));

        $dataNilai = [];
        foreach ($suppliers as $s) {
            $row = ['id' => $s->id];
            foreach($kriterias as $k) {
                $val = isset($s->nilai_kriteria[$k->kode]) ? $s->nilai_kriteria[$k->kode] : 0;
                $row[$k->kode] = (float) $val;
            }
            $dataNilai[] = $row;
        }

        $minMax = [];
        foreach($kriterias as $k) {
            $col = array_column($dataNilai, $k->kode);
            $maxData = !empty($col) ? max($col) : 1;
            $minData = !empty($col) ? min($col) : 1;
            $minMax[$k->kode] = ($k->jenis == 'cost') ? ($minData == 0 ? 1 : $minData) : ($maxData == 0 ? 1 : $maxData);
        }

        foreach ($dataNilai as $d) {
            $skor = 0;
            foreach($kriterias as $k) {
                $nilaiReal = $d[$k->kode];
                $norm = ($k->jenis == 'cost') ? ($nilaiReal != 0 ? $minMax[$k->kode] / $nilaiReal : 0) : ($nilaiReal / $minMax[$k->kode]);
                $skor += $norm * ($k->bobot); 
            }
            Supplier::where('id', $d['id'])->update(['skor_akhir' => $skor]);
        }
        
        $this->logActivity(Auth::user()->name . ' recalculated SAW ranking.', 'info');

        return back()->with('success', __('SAW ranking calculation completed! Scores updated.'));
    }

    public function updateNilai(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $inputNilai = $request->except(['_token', '_method']);
        $cleanNilai = array_map(function($val) { return is_numeric($val) ? (float)$val : 0; }, $inputNilai);
        
        $supplier->update(['nilai_kriteria' => $cleanNilai]);

        $this->logActivity(Auth::user()->name . " updated score for supplier: {$supplier->nama}.", 'warning');

        return redirect()->back()->with('success', __('Score data updated.'));
    }

    public function updateStatus(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $oldStatus = $supplier->status_supplier;
        $supplier->update(['status_supplier' => $request->status]);

        if($oldStatus != $request->status) {
            $this->logActivity(Auth::user()->name . " changed status of {$supplier->nama} from {$oldStatus} to {$request->status}.", 'warning');
        }

        return redirect()->back()->with('success', __('Supplier status updated.'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,hrd,staff',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $this->logActivity(Auth::user()->name . " created new user: {$request->name} ({$request->role}).", 'warning');

        return redirect()->back()->with('success', __('User created successfully.'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,hrd,staff',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $this->logActivity(Auth::user()->name . " updated user: {$request->name}.", 'warning');

        return redirect()->back()->with('success', __('User updated successfully.'));
    }

    public function deleteUser($id)
    { 
        $user = User::findOrFail($id); 
        // Logic change: Deleting a User (Staff) should NOT delete Suppliers automatically, 
        // unless they are strictly bound. For now, we keep suppliers safe.
        $user->delete(); 
        
        $this->logActivity(Auth::user()->name . " deleted user: {$user->name}.", 'danger');

        return back()->with('success', __('User deleted.')); 
    }

    public function cetakLaporan(Request $request) {
        $ranking = Supplier::orderByDesc('skor_akhir')->get();
        if ($request->has('filter_type') && $request->filter_type == 'selected') {
            $selectedIds = $request->input('selected_ids', []);
            $ranking = $ranking->whereIn('id', $selectedIds);
        }
        return view('laporan.cetak', compact('ranking'));
    }

    public function downloadLaporan(Request $request) {
        $ranking = Supplier::orderByDesc('skor_akhir')->get();
        if ($request->has('filter_type') && $request->filter_type == 'selected') {
            $selectedIds = $request->input('selected_ids', []);
            $ranking = $ranking->whereIn('id', $selectedIds);
        }
        $type = $request->input('format', 'csv');
        $filename = "Supplier_Report_" . date('Y-m-d') . "." . ($type == 'excel' ? 'xls' : 'csv');
        
        $this->logActivity(Auth::user()->name . " downloaded report ({$type}).", 'info');

        if ($type == 'excel') {
            header('Content-Type: application/vnd.ms-excel'); header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo '<?xml version="1.0"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"><Worksheet ss:Name="Sheet1"><Table><Row><Cell><Data ss:Type="String">Rank</Data></Cell><Cell><Data ss:Type="String">Supplier Name</Data></Cell><Cell><Data ss:Type="String">Score</Data></Cell></Row>';
            $no=1; foreach($ranking as $r){ echo "<Row><Cell><Data ss:Type='Number'>".$no++."</Data></Cell><Cell><Data ss:Type='String'>".$r->nama."</Data></Cell><Cell><Data ss:Type='Number'>".$r->skor_akhir."</Data></Cell></Row>"; }
            echo '</Table></Worksheet></Workbook>'; exit;
        } else {
            header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="' . $filename . '"');
            $fp = fopen('php://output', 'w'); fputcsv($fp, ['Rank', 'Supplier Name', 'Score', 'Status']);
            $no=1; foreach($ranking as $r){ fputcsv($fp, [$no++, $r->nama, number_format($r->skor_akhir,4), $r->status_supplier]); } fclose($fp); exit;
        }
    }

    public function updateKriteria(Request $request) {
        $data = $request->input('kriteria');
        $totalBobot = 0; foreach ($data as $item) $totalBobot += (float) $item['bobot'];
        if (abs($totalBobot - 100) > 0.1) return redirect()->back()->with('error', __('Total weight must be 100%.'));

        Kriteria::truncate();
        foreach ($data as $item) {
            $opsi = $item['opsi'] ?? ['1','2','3','4','5'];
            $opsi = array_filter($opsi, fn($v) => !is_null($v) && $v !== '');
            $opsi = array_values($opsi); if(empty($opsi)) $opsi = ['1','2','3','4','5'];
            Kriteria::create(['kode'=>$item['kode'], 'nama'=>$item['nama'], 'bobot'=>$item['bobot']/100, 'jenis'=>$item['jenis']??'benefit', 'opsi'=>$opsi]);
        }

        $this->logActivity(Auth::user()->name . " updated criteria configuration.", 'warning');

        return redirect()->back()->with('success', __('Criteria configuration updated.'));
    }

    // --- NEW: STAFF STORE SUPPLIER (Replaces storeLamaran) ---
    public function storeSupplier(Request $request) {
        try {
            $request->validate([
                'nama' => 'required|string|max:255',
                'file_berkas' => 'nullable|mimes:pdf|max:5120',
                'email' => 'nullable|email',
                'telepon' => 'nullable|string',
                'nama_barang' => 'nullable|string',
                'harga' => 'nullable|numeric',
                'tempo_pembayaran' => 'nullable|string',
                'estimasi_pengiriman' => 'nullable|string',
                'catatan_negosiasi' => 'nullable|string'
            ]);
            
            $path = null;
            if ($request->hasFile('file_berkas')) {
                $path = $request->file('file_berkas')->store('berkas_supplier', 'public');
            }

            Supplier::create([
                'user_id' => Auth::id(), // Recorded as 'Created By'
                'nama' => $request->nama,
                'email' => $request->email,
                'telepon' => $request->telepon,
                'nama_barang' => $request->nama_barang,
                'harga' => $request->harga ?? 0,
                'tempo_pembayaran' => $request->tempo_pembayaran,
                'estimasi_pengiriman' => $request->estimasi_pengiriman,
                'catatan_negosiasi' => $request->catatan_negosiasi,
                'file_berkas' => $path,
                'status_supplier' => 'Pending',
                'nilai_kriteria' => [],
                'skor_akhir' => 0
            ]);
            
            $this->logActivity(Auth::user()->name . " added new supplier: " . $request->nama, 'info');
    
            return redirect()->back()->with('success', __('Supplier added successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to add supplier: ') . $e->getMessage());
        }
    }

    // --- NEW: STAFF UPDATE SUPPLIER (Replaces updateLamaran) ---
    public function updateSupplier(Request $request, $id) {
        $supplier = Supplier::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'file_berkas' => 'nullable|mimes:pdf|max:5120',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string',
            'nama_barang' => 'nullable|string',
            'harga' => 'nullable|numeric',
            'tempo_pembayaran' => 'nullable|string',
            'estimasi_pengiriman' => 'nullable|string',
            'catatan_negosiasi' => 'nullable|string'
        ]);

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'nama_barang' => $request->nama_barang,
            'harga' => $request->harga,
            'tempo_pembayaran' => $request->tempo_pembayaran,
            'estimasi_pengiriman' => $request->estimasi_pengiriman,
            'catatan_negosiasi' => $request->catatan_negosiasi
        ];

        if ($request->hasFile('file_berkas')) {
            if ($supplier->file_berkas && Storage::disk('public')->exists($supplier->file_berkas)) {
                Storage::disk('public')->delete($supplier->file_berkas);
            }
            $path = $request->file('file_berkas')->store('berkas_supplier', 'public');
            $data['file_berkas'] = $path;
        }

        $supplier->update($data);
        
        $this->logActivity(Auth::user()->name . " updated supplier: " . $request->nama, 'info');

        return redirect()->back()->with('success', __('Supplier updated successfully.'));
    }

    public function viewPdf($path){ 
        if(!Storage::disk('public')->exists($path)) abort(404); 
        return response()->file(storage_path('app/public/'.$path)); 
    }
}
