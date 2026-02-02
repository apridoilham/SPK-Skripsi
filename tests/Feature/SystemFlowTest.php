<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Kriteria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SystemFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Authentication & Redirection
     */
    public function test_user_can_login_and_access_dashboard()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['role' => 'hrd']); // HRD = Manager

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    /**
     * Test Staff (Supplier) Flow
     */
    public function test_staff_can_submit_supplier()
    {
        Storage::fake('public');
        
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['role' => 'staff']);
        
        $this->actingAs($user);

        $this->assertDatabaseMissing('suppliers', ['user_id' => $user->id]);

        $file = UploadedFile::fake()->create('offer.pdf', 100);

        $response = $this->post(route('supplier.store'), [
            'nama' => 'PT Test Supplier',
            'file_berkas' => $file,
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('suppliers', [
            'user_id' => $user->id,
            'nama' => 'PT Test Supplier',
            'status_supplier' => 'Pending',
        ]);

        $supplier = Supplier::where('user_id', $user->id)->first();
        $this->assertTrue(Storage::disk('public')->exists($supplier->file_berkas));
    }

    /**
     * Test Manager (HRD) Flow: Kriteria Management
     */
    public function test_manager_can_manage_kriteria()
    {
        /** @var \App\Models\User $manager */
        $manager = User::factory()->create(['role' => 'hrd']);
        $this->actingAs($manager);

        $kriteriaData = [
            ['kode' => 'C1', 'nama' => 'Harga', 'bobot' => 50, 'jenis' => 'cost', 'opsi' => ['Murah', 'Mahal']],
            ['kode' => 'C2', 'nama' => 'Kualitas', 'bobot' => 50, 'jenis' => 'benefit', 'opsi' => ['Bagus', 'Jelek']]
        ];

        $response = $this->put(route('kriteria.update'), [
            'kriteria' => $kriteriaData
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('kriterias', ['kode' => 'C1', 'nama' => 'Harga']);
        $this->assertDatabaseHas('kriterias', ['kode' => 'C2', 'nama' => 'Kualitas']);
    }

    /**
     * Test Manager Flow: Input Nilai & Hitung SAW
     */
    public function test_manager_can_calculate_ranking_saw()
    {
        /** @var \App\Models\User $manager */
        $manager = User::factory()->create(['role' => 'hrd']);
        $this->actingAs($manager);

        // Setup Data
        $c1 = Kriteria::factory()->create(['kode' => 'C1', 'bobot' => 0.5, 'jenis' => 'benefit']);
        $c2 = Kriteria::factory()->create(['kode' => 'C2', 'bobot' => 0.5, 'jenis' => 'benefit']);

        $s1 = Supplier::factory()->create(['nama' => 'Supplier A']);
        $s2 = Supplier::factory()->create(['nama' => 'Supplier B']);

        // 1. Input Nilai
        $this->put(route('nilai.update', $s1->id), [
            'C1' => 5, 'C2' => 5
        ]);

        $this->put(route('nilai.update', $s2->id), [
            'C1' => 1, 'C2' => 1
        ]);

        $this->assertDatabaseHas('suppliers', [
            'id' => $s1->id,
            'nilai_kriteria->C1' => 5
        ]);

        // 2. Hitung Ranking
        $response = $this->post(route('ranking.hitung'));
        $response->assertRedirect();

        $s1->refresh();
        $s2->refresh();

        $this->assertEquals(1.0, $s1->skor_akhir);
        $this->assertEquals(0.2, $s2->skor_akhir);
    }

    /**
     * Test Manager Flow: Update Status Supplier
     */
    public function test_manager_can_update_supplier_status()
    {
        /** @var \App\Models\User $manager */
        $manager = User::factory()->create(['role' => 'hrd']);
        $this->actingAs($manager);

        $supplier = Supplier::factory()->create(['status_supplier' => 'Pending']);

        $response = $this->put(route('status.update', $supplier->id), [
            'status' => 'Lulus'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'status_supplier' => 'Lulus']);
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
            'name' => 'New Manager',
            'email' => 'manager@example.com',
            'password' => 'password123',
            'role' => 'hrd'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'manager@example.com', 'role' => 'hrd']);
    }

    /**
     * Test Chatbot AI
     */
    public function test_chatbot_can_send_message()
    {
        /** @var \App\Models\User $manager */
        $manager = User::factory()->create(['role' => 'hrd']);
        $this->actingAs($manager);

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
