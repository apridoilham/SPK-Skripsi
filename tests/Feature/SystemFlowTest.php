<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pelamar;
use App\Models\Kriteria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SystemFlowTest extends TestCase
{
    use RefreshDatabase; // Reset database setiap test run

    /**
     * Test Authentication & Redirection
     */
    public function test_user_can_login_and_access_dashboard()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['role' => 'hrd']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password', // Default password factory
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    /**
     * Test Pelamar Flow
     */
    public function test_pelamar_can_submit_application()
    {
        Storage::fake('public');
        
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['role' => 'pelamar']);
        
        // Login sebagai pelamar
        $this->actingAs($user);

        // Pelamar belum punya data di tabel pelamar
        $this->assertDatabaseMissing('pelamars', ['user_id' => $user->id]);

        $file = UploadedFile::fake()->create('lamaran.pdf', 100);

        $response = $this->post(route('lamar.store'), [
            'nama' => 'John Doe',
            'file_berkas' => $file,
        ]);

        $response->assertRedirect(); // Biasanya redirect back
        
        // Pastikan data tersimpan
        $this->assertDatabaseHas('pelamars', [
            'user_id' => $user->id,
            'nama' => 'John Doe',
            'status_lamaran' => 'Pending',
        ]);

        // Pastikan file tersimpan (Nama file di hash, jadi kita cek keberadaannya saja di storage logic)
        // Karena di controller biasanya path disimpan, kita bisa cek user->pelamar->file_berkas
        $pelamar = Pelamar::where('user_id', $user->id)->first();
        $this->assertTrue(Storage::disk('public')->exists($pelamar->file_berkas));
    }

    /**
     * Test HRD Flow: Kriteria Management
     */
    public function test_hrd_can_manage_kriteria()
    {
        /** @var \App\Models\User $hrd */
        $hrd = User::factory()->create(['role' => 'hrd']);
        $this->actingAs($hrd);

        // Update/Create Kriteria (Route: PUT /kriteria/update)
        // Route ini menghapus semua kriteria lama dan insert baru (bulk update)
        
        $kriteriaData = [
            ['kode' => 'C1', 'nama' => 'Pengalaman', 'bobot' => 50, 'jenis' => 'benefit', 'opsi' => ['1 Thn', '2 Thn']],
            ['kode' => 'C2', 'nama' => 'Pendidikan', 'bobot' => 50, 'jenis' => 'benefit', 'opsi' => ['D3', 'S1']]
        ];

        $response = $this->put(route('kriteria.update'), [
            'kriteria' => $kriteriaData
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('kriterias', ['kode' => 'C1', 'nama' => 'Pengalaman']);
        $this->assertDatabaseHas('kriterias', ['kode' => 'C2', 'nama' => 'Pendidikan']);
    }

    /**
     * Test HRD Flow: Input Nilai & Hitung SAW
     */
    public function test_hrd_can_calculate_ranking_saw()
    {
        /** @var \App\Models\User $hrd */
        $hrd = User::factory()->create(['role' => 'hrd']);
        $this->actingAs($hrd);

        // Setup Data: 2 Kriteria, 2 Pelamar
        $c1 = Kriteria::factory()->create(['kode' => 'C1', 'bobot' => 0.5, 'jenis' => 'benefit']);
        $c2 = Kriteria::factory()->create(['kode' => 'C2', 'bobot' => 0.5, 'jenis' => 'benefit']);

        $p1 = Pelamar::factory()->create(['nama' => 'Pelamar A']);
        $p2 = Pelamar::factory()->create(['nama' => 'Pelamar B']);

        // 1. Input Nilai Pelamar (PUT /nilai/{id})
        // Misal: P1 dapat nilai 5 di C1, 5 di C2
        $this->put(route('nilai.update', $p1->id), [
            'C1' => 5, 'C2' => 5
        ]);

        // Misal: P2 dapat nilai 1 di C1, 1 di C2
        $this->put(route('nilai.update', $p2->id), [
            'C1' => 1, 'C2' => 1
        ]);

        // Verifikasi nilai masuk JSON
        $this->assertDatabaseHas('pelamars', [
            'id' => $p1->id,
            'nilai_kriteria->C1' => 5
        ]);

        // 2. Hitung Ranking (POST /hitung-ranking)
        $response = $this->post(route('ranking.hitung'));
        $response->assertRedirect();

        // Verifikasi Skor Akhir
        // P1: Max C1=5, Max C2=5. Norm P1: 5/5=1, 5/5=1. Skor: 1*0.5 + 1*0.5 = 1.0
        // P2: Max C1=5, Max C2=5. Norm P2: 1/5=0.2, 1/5=0.2. Skor: 0.2*0.5 + 0.2*0.5 = 0.2
        
        $p1->refresh();
        $p2->refresh();

        $this->assertEquals(1.0, $p1->skor_akhir);
        $this->assertEquals(0.2, $p2->skor_akhir);
    }

    /**
     * Test HRD Flow: Update Status Pelamar
     */
    public function test_hrd_can_update_applicant_status()
    {
        /** @var \App\Models\User $hrd */
        $hrd = User::factory()->create(['role' => 'hrd']);
        $this->actingAs($hrd);

        $pelamar = Pelamar::factory()->create(['status_lamaran' => 'Pending']);

        $response = $this->put(route('status.update', $pelamar->id), [
            'status' => 'Lulus'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pelamars', ['id' => $pelamar->id, 'status_lamaran' => 'Lulus']);
    }

    /**
     * Test Admin Flow: Manage Users
     */
    public function test_admin_can_create_user()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->post(route('user.store'), [
            'name' => 'New HRD',
            'email' => 'newhrd@example.com',
            'password' => 'password123',
            'role' => 'hrd'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'newhrd@example.com', 'role' => 'hrd']);
    }

    /**
     * Test Chatbot AI (Mocking External API)
     */
    public function test_chatbot_can_send_message()
    {
        /** @var \App\Models\User $hrd */
        $hrd = User::factory()->create(['role' => 'hrd']);
        $this->actingAs($hrd);

        // Mock Groq API
        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => ['content' => 'Halo, saya AI assistant.']
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson(route('chat.send'), [
            'message' => 'Halo'
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('reply', 'Halo, saya AI assistant.');
    }
}
