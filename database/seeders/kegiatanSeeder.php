<?php

namespace Database\Seeders;

use App\Models\Kegiatan;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class kegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Membuat 5 data jadwal kegiatan
        for ($i = 0; $i < 5; $i++) {
            Kegiatan::create([
                'nama_kegiatan' => $faker->sentence(3), // Menghasilkan nama kegiatan random dengan 3 kata
                'tanggal_kegiatan' => $faker->date(), // Format: Y-m-d
                'waktu_kegiatan' => Carbon::createFromFormat('H:i', $faker->time('H:i'))->format('Y-m-d H:i:s')
            ]);
        }
    }
}
