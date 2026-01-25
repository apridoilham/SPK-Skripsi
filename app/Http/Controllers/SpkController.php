<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelamar;
use App\Models\User;
use App\Models\Kriteria;
use App\Models\ActivityLog; // Load Model Log
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

        if ($user->role == 'admin') {
            $users = User::all();
            $pelamars = Pelamar::all();
            // Ambil 20 Log Aktivitas Terakhir untuk Admin
            $logs = ActivityLog::with('user')->latest()->limit(20)->get();
            
            return view('dashboard_admin', compact('users', 'pelamars', 'logs'));

        } elseif ($user->role == 'hrd') {
            $pelamars = Pelamar::all();
            $kriterias = Kriteria::all();
            $ranking = Pelamar::orderByDesc('skor_akhir')->get();

            // --- HITUNG MATRIKS NORMALISASI (Display) ---
            $matriks = [];
            $dataNilai = [];
            foreach ($pelamars as $p) {
                foreach($kriterias as $k) {
                    $val = isset($p->nilai_kriteria[$k->kode]) ? (float)$p->nilai_kriteria[$k->kode] : 0;
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
            foreach ($pelamars as $p) {
                $baris = ['nama' => $p->nama];
                foreach($kriterias as $k) {
                    $nilai = isset($p->nilai_kriteria[$k->kode]) ? (float)$p->nilai_kriteria[$k->kode] : 0;
                    $norm = ($k->jenis == 'cost') ? ($nilai != 0 ? $minMax[$k->kode] / $nilai : 0) : ($nilai / $minMax[$k->kode]);
                    $baris[$k->kode] = number_format($norm, 3);
                }
                $matriks[] = $baris;
            }

            return view('dashboard_hrd', compact('pelamars', 'ranking', 'kriterias', 'matriks'));

        } else {
            $pelamar = Pelamar::where('user_id', $user->id)->first();
            return view('dashboard_pelamar', compact('pelamar'));
        }
    }

    public function prosesHitungRanking()
    {
        $pelamars = Pelamar::all();
        if ($pelamars->isEmpty()) return back()->with('error', 'Tidak ada data pelamar.');
        $kriterias = Kriteria::all();
        if ($kriterias->isEmpty()) return back()->with('error', 'Kriteria belum diset.');

        $dataNilai = [];
        foreach ($pelamars as $p) {
            $row = ['id' => $p->id];
            foreach($kriterias as $k) {
                $val = isset($p->nilai_kriteria[$k->kode]) ? $p->nilai_kriteria[$k->kode] : 0;
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
            Pelamar::where('id', $d['id'])->update(['skor_akhir' => $skor]);
        }
        
        // LOG AKTIVITAS
        $this->logActivity(Auth::user()->name . ' melakukan perhitungan ulang ranking SAW.', 'info');

        return back()->with('success', 'Perhitungan ranking SAW selesai! Skor telah diperbarui.');
    }

    // --- FUNGSI UPDATE NILAI (DENGAN LOG) ---
    public function updateNilai(Request $request, $id)
    {
        $pelamar = Pelamar::findOrFail($id);
        $inputNilai = $request->except(['_token', '_method']);
        $cleanNilai = array_map(function($val) { return is_numeric($val) ? (float)$val : 0; }, $inputNilai);
        
        $pelamar->update(['nilai_kriteria' => $cleanNilai]);

        // LOG AKTIVITAS
        $this->logActivity(Auth::user()->name . " mengubah nilai penilaian untuk kandidat: {$pelamar->nama}.", 'warning');

        return redirect()->back()->with('success', 'Data nilai diperbarui.');
    }

    // --- FUNGSI UPDATE STATUS (DENGAN LOG) ---
    public function updateStatus(Request $request, $id)
    {
        $pelamar = Pelamar::findOrFail($id);
        $oldStatus = $pelamar->status_lamaran;
        $pelamar->update(['status_lamaran' => $request->status]);

        // LOG AKTIVITAS
        if($oldStatus != $request->status) {
            $this->logActivity(Auth::user()->name . " mengubah status {$pelamar->nama} dari {$oldStatus} menjadi {$request->status}.", 'warning');
        }

        return redirect()->back()->with('success', 'Status pelamar diubah.');
    }

    // --- FUNGSI DELETE USER (DENGAN LOG) ---
    public function deleteUser($id)
    { 
        $user = User::findOrFail($id); 
        $userName = $user->name;
        $userRole = $user->role;

        if($user->role == 'pelamar') {
            $pelamar = $user->pelamar;
            if ($pelamar) {
                if ($pelamar->file_berkas && Storage::disk('public')->exists($pelamar->file_berkas)) {
                    Storage::disk('public')->delete($pelamar->file_berkas);
                }
                $pelamar->delete();
            }
        }
        $user->delete(); 
        
        // LOG AKTIVITAS
        $this->logActivity(Auth::user()->name . " menghapus user: {$userName} (Role: {$userRole}).", 'danger');

        return back()->with('success','User dan berkas lamaran dihapus permanen.'); 
    }

    public function cetakLaporan(Request $request) {
        $ranking = Pelamar::orderByDesc('skor_akhir')->get();
        if ($request->has('filter_type') && $request->filter_type == 'selected') {
            $selectedIds = $request->input('selected_ids', []);
            $ranking = $ranking->whereIn('id', $selectedIds);
        }
        return view('laporan.cetak', compact('ranking'));
    }

    public function downloadLaporan(Request $request) {
        // ... (Kode download sama seperti sebelumnya, disingkat agar muat)
        $ranking = Pelamar::orderByDesc('skor_akhir')->get();
        if ($request->has('filter_type') && $request->filter_type == 'selected') {
            $selectedIds = $request->input('selected_ids', []);
            $ranking = $ranking->whereIn('id', $selectedIds);
        }
        $type = $request->input('format', 'csv');
        $filename = "Laporan_Seleksi_" . date('Y-m-d') . "." . ($type == 'excel' ? 'xls' : 'csv');
        
        $this->logActivity(Auth::user()->name . " mengunduh laporan seleksi ({$type}).", 'info'); // LOG

        if ($type == 'excel') {
            header('Content-Type: application/vnd.ms-excel'); header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo '<?xml version="1.0"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"><Worksheet ss:Name="Sheet1"><Table><Row><Cell><Data ss:Type="String">Rank</Data></Cell><Cell><Data ss:Type="String">Nama</Data></Cell><Cell><Data ss:Type="String">Skor</Data></Cell></Row>';
            $no=1; foreach($ranking as $r){ echo "<Row><Cell><Data ss:Type='Number'>".$no++."</Data></Cell><Cell><Data ss:Type='String'>".$r->nama."</Data></Cell><Cell><Data ss:Type='Number'>".$r->skor_akhir."</Data></Cell></Row>"; }
            echo '</Table></Worksheet></Workbook>'; exit;
        } else {
            header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="' . $filename . '"');
            $fp = fopen('php://output', 'w'); fputcsv($fp, ['Rank', 'Nama', 'Skor', 'Status']);
            $no=1; foreach($ranking as $r){ fputcsv($fp, [$no++, $r->nama, number_format($r->skor_akhir,4), $r->status_lamaran]); } fclose($fp); exit;
        }
    }

    // --- FUNGSI UPDATE KRITERIA (DENGAN LOG) ---
    public function updateKriteria(Request $request) {
        $data = $request->input('kriteria');
        $totalBobot = 0; foreach ($data as $item) $totalBobot += (float) $item['bobot'];
        if (abs($totalBobot - 100) > 0.1) return redirect()->back()->with('error', 'Total bobot harus 100%.');

        Kriteria::truncate();
        foreach ($data as $item) {
            $opsi = $item['opsi'] ?? ['1','2','3','4','5'];
            $opsi = array_filter($opsi, fn($v) => !is_null($v) && $v !== '');
            $opsi = array_values($opsi); if(empty($opsi)) $opsi = ['1','2','3','4','5'];
            Kriteria::create(['kode'=>$item['kode'], 'nama'=>$item['nama'], 'bobot'=>$item['bobot']/100, 'jenis'=>$item['jenis']??'benefit', 'opsi'=>$opsi]);
        }

        // LOG AKTIVITAS
        $this->logActivity(Auth::user()->name . " memperbarui konfigurasi kriteria dan bobot.", 'warning');

        return redirect()->back()->with('success', 'Konfigurasi Kriteria diperbarui.');
    }

    public function storeLamaran(Request $request) {
        $request->validate(['nama' => 'required|string|max:255','file_berkas' => 'required|mimes:pdf|max:5120']);
        $path = $request->file('file_berkas')->store('berkas_lamaran', 'public');
        Pelamar::create(['user_id' => Auth::id(), 'nama' => $request->nama, 'file_berkas' => $path, 'status_lamaran' => 'Pending', 'nilai_kriteria' => [], 'skor_akhir' => 0]);
        
        $this->logActivity("Pelamar " . $request->nama . " mengirim berkas lamaran baru.", 'info'); // LOG

        return redirect()->back()->with('success', 'Lamaran berhasil dikirim.');
    }

    public function storeUser(Request $request){ 
        User::create(['name'=>$request->name,'email'=>$request->email,'password'=>Hash::make($request->password),'role'=>$request->role]); 
        $this->logActivity(Auth::user()->name . " menambahkan user baru: {$request->name} ({$request->role}).", 'info'); // LOG
        return back()->with('success','User ditambahkan.'); 
    }
    
    public function updateUser(Request $request, $id){ 
        $u = User::findOrFail($id);
        $u->update(['name'=>$request->name,'email'=>$request->email,'role'=>$request->role]); 
        $this->logActivity(Auth::user()->name . " mengupdate data user: {$request->name}.", 'info'); // LOG
        return back()->with('success','User diperbarui.'); 
    }

    public function viewPdf($path){ 
        if(!Storage::disk('public')->exists($path)) abort(404); 
        return response()->file(storage_path('app/public/'.$path)); 
    }
}