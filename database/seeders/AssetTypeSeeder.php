<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetType;

class AssetTypeSeeder extends Seeder {
  public function run(): void {
    foreach (['Teléfono','Teclado','Monitor','Laptop','Audífono','Tablet','Mouse','Mochila','Cargador'] as $n) {
      AssetType::firstOrCreate(['name'=>$n]);
    }
  }
}
