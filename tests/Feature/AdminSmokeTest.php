<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use App\Models\Pengembalian;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create([
            'nama'     => 'User ' . $role,
            'email'    => strtolower($role) . '@example.com',
            'password' => 'password123',
            'no_hp'    => '081234567890',
            'role'     => $role,
            'status'   => 'Aktif',
        ]);
    }

    private function dataSeed(): array
    {
        $admin = $this->user('Admin');
        $petugas = $this->user('Petugas');
        $pengguna = $this->user('Pengguna');

        $kategori = Kategori::create(['nama_kategori' => 'Kamera', 'kode' => 'KAM']);

        $barang = Barang::create([
            'kategori_id'    => $kategori->id,
            'kode_barang'    => 'KAM-001',
            'nama_barang'    => 'Canon EOS R5',
            'merk'           => 'Canon',
            'serial_number'  => 'SN-001',
            'stok'           => 5,
            'stok_tersedia'  => 5,
            'kondisi'        => 'Baik',
            'denda_per_hari' => 50000,
            'harga_sewa_per_hari' => 100000,
            'status'         => 'Aktif',
        ]);

        $transaksi = Transaksi::create([
            'kode_transaksi'  => 'TRX-20260805-001',
            'user_id'         => $pengguna->id,
            'tgl_pinjam'      => now()->toDateString(),
            'tgl_jatuh_tempo' => now()->addDays(3)->toDateString(),
            'status'          => 'Menunggu',
            'disetujui_oleh'  => null,
            'catatan'         => null,
        ]);

        \App\Models\DetailTransaksi::create([
            'transaksi_id' => $transaksi->id,
            'barang_id'    => $barang->id,
            'jumlah'       => 1,
        ]);

        return compact('admin', 'petugas', 'pengguna', 'kategori', 'barang', 'transaksi');
    }

    public function test_admin_pages_render(): void
    {
        ['admin' => $admin, 'transaksi' => $transaksi] = $this->dataSeed();

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
        $this->actingAs($admin)->get('/admin/user')->assertOk();
        $this->actingAs($admin)->get('/admin/barang')->assertOk();
        $this->actingAs($admin)->get('/admin/kategori')->assertOk();
        $this->actingAs($admin)->get('/admin/transaksi')->assertOk();
        $this->actingAs($admin)->get('/admin/transaksi/' . $transaksi->id)->assertOk();
        $this->actingAs($admin)->get('/admin/pengembalian')->assertOk();
    }

    public function test_petugas_pages_render(): void
    {
        ['petugas' => $petugas, 'transaksi' => $transaksi] = $this->dataSeed();

        $this->actingAs($petugas)->get('/petugas/dashboard')->assertOk();
        $this->actingAs($petugas)->get('/petugas/transaksi')->assertOk();
        $this->actingAs($petugas)->get('/petugas/transaksi/' . $transaksi->id)->assertOk();
        $this->actingAs($petugas)->get('/petugas/pengembalian')->assertOk();
    }

    public function test_pengguna_pages_render(): void
    {
        ['pengguna' => $pengguna, 'transaksi' => $transaksi] = $this->dataSeed();

        $this->actingAs($pengguna)->get('/user/dashboard')->assertOk();
        $this->actingAs($pengguna)->get('/user/transaksi')->assertOk();
        $this->actingAs($pengguna)->get('/user/transaksi/create')->assertOk();
        $this->actingAs($pengguna)->get('/user/transaksi/' . $transaksi->id)->assertOk();
    }

    public function test_petugas_can_process_return(): void
    {
        ['petugas' => $petugas, 'transaksi' => $transaksi] = $this->dataSeed();

        $this->actingAs($petugas)
            ->post('/petugas/transaksi/' . $transaksi->id . '/approve')
            ->assertRedirect();

        $this->actingAs($petugas)
            ->post('/petugas/transaksi/' . $transaksi->id . '/serah')
            ->assertRedirect();

        $this->assertEquals('Dipinjam', $transaksi->fresh()->status);
        $this->assertEquals(4, Barang::first()->fresh()->stok_tersedia);
        $this->assertDatabaseHas('pembayaran', [
            'transaksi_id' => $transaksi->id,
            'jumlah'       => 300000,
            'metode'       => 'Tunai',
        ]);

        $this->actingAs($petugas)
            ->post('/petugas/pengembalian', [
                'transaksi_id'   => $transaksi->id,
                'tgl_kembali'    => now()->addDays(5)->toDateString(),
                'kondisi_barang' => 'Baik',
                'catatan'        => null,
            ])
            ->assertRedirect('/petugas/pengembalian');

        $this->assertDatabaseHas('pengembalian', [
            'transaksi_id'   => $transaksi->id,
            'terlambat_hari' => 2,
            'total_denda'    => 100000,
        ]);

        $this->assertEquals('Selesai', $transaksi->fresh()->status);
        $this->assertEquals(5, Barang::first()->fresh()->stok_tersedia);
    }

    public function test_login_page_and_process(): void
    {
        $this->get('/login')->assertOk();

        $user = $this->user('Pengguna');

        $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
            ->assertRedirect('/user/dashboard');

        $this->post('/login', ['email' => $user->email, 'password' => 'salah'])
            ->assertSessionHas('error', 'Email atau password salah');
    }

    public function test_biaya_sewa_dihitung_dari_durasi(): void
    {
        ['transaksi' => $transaksi] = $this->dataSeed();

        $this->assertEquals(3, $transaksi->durasiHari());
        $this->assertEquals(300000, $transaksi->totalBiayaSewa());
    }

    public function test_petugas_can_print_payment_receipt_and_report(): void
    {
        ['petugas' => $petugas, 'transaksi' => $transaksi] = $this->dataSeed();

        $this->actingAs($petugas)
            ->post('/petugas/transaksi/' . $transaksi->id . '/approve')
            ->assertRedirect();

        $this->actingAs($petugas)
            ->post('/petugas/transaksi/' . $transaksi->id . '/serah')
            ->assertRedirect();

        $this->actingAs($petugas)
            ->get('/petugas/transaksi/' . $transaksi->id . '/bukti')
            ->assertOk();

        $this->actingAs($petugas)
            ->get('/petugas/laporan')
            ->assertOk();

        $this->actingAs($petugas)
            ->get('/petugas/laporan/cetak')
            ->assertOk();

        $this->actingAs($petugas)
            ->get('/petugas/laporan/cetak?dari=' . now()->toDateString() . '&sampai=' . now()->toDateString())
            ->assertOk();
    }

    public function test_receipt_not_available_before_payment(): void
    {
        ['petugas' => $petugas, 'transaksi' => $transaksi] = $this->dataSeed();

        $this->actingAs($petugas)
            ->get('/petugas/transaksi/' . $transaksi->id . '/bukti')
            ->assertNotFound();
    }

    public function test_admin_can_view_log_aktivitas(): void
    {
        ['admin' => $admin] = $this->dataSeed();

        LogAktivitas::record('login', 'Auth', 'Test login', $admin);

        $this->actingAs($admin)->get('/admin/log-aktivitas')->assertOk();
        $this->assertDatabaseHas('log_aktivitas', [
            'modul' => 'Auth',
            'aksi'  => 'login',
        ]);
    }

    public function test_transaksi_flow_creates_activity_logs(): void
    {
        ['petugas' => $petugas, 'transaksi' => $transaksi] = $this->dataSeed();

        $this->actingAs($petugas)
            ->post('/petugas/transaksi/' . $transaksi->id . '/approve')
            ->assertRedirect();

        $this->actingAs($petugas)
            ->post('/petugas/transaksi/' . $transaksi->id . '/serah')
            ->assertRedirect();

        $this->assertDatabaseHas('log_aktivitas', ['modul' => 'Transaksi', 'aksi' => 'approve']);
        $this->assertDatabaseHas('log_aktivitas', ['modul' => 'Transaksi', 'aksi' => 'serah']);
    }

    public function test_role_middleware_blocks_wrong_role(): void
    {
        ['pengguna' => $pengguna] = $this->dataSeed();

        $this->actingAs($pengguna)->get('/admin/transaksi')->assertForbidden();
    }
}
