<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use ZipArchive;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $excelPath = base_path('Ürünleriniz_22.07.2026-14.10.xlsx');
        if (!file_exists($excelPath)) {
            $this->command->error("Excel dosyası bulunamadı: " . $excelPath);
            return;
        }

        $zip = new ZipArchive();
        if ($zip->open($excelPath) !== TRUE) {
            $this->command->error("Excel dosyası açılamadı.");
            return;
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $sheet = simplexml_load_string($sheetXml);

        $rows = [];
        foreach ($sheet->sheetData->row as $r) {
            $rNum = (int)$r['r'];
            $rowCells = [];
            foreach ($r->c as $c) {
                $colLetter = preg_replace('/[0-9]/', '', (string)$c['r']);
                $val = '';
                if (isset($c->is)) {
                    $val = (string)$c->is->t;
                } elseif (isset($c->v)) {
                    $val = (string)$c->v;
                }
                $rowCells[$colLetter] = trim($val);
            }
            $rows[$rNum] = $rowCells;
        }

        unset($rows[1]); // Remove header row

        $importedCount = 0;
        $categoryMap = [];

        foreach ($rows as $row) {
            $name = $row['L'] ?? '';
            if (empty($name)) continue;

            $categoryName = $row['J'] ?? 'Genel';

            if (!isset($categoryMap[$categoryName])) {
                $categoryMap[$categoryName] = Category::firstOrCreate(
                    ['name' => $categoryName],
                    ['slug' => Str::slug($categoryName)]
                );
            }
            $category = $categoryMap[$categoryName];

            $barcode = $row['B'] ?? null;
            $modelCode = $row['D'] ?? null;
            $color = $row['E'] ?? null;
            $size = $row['G'] ?? null;
            $brand = $row['I'] ?? null;
            $description = $row['M'] ?? null;
            
            $originalPrice = floatval($row['N'] ?? 0);
            $price = floatval($row['O'] ?? 0);
            if ($price <= 0) $price = $originalPrice;
            if ($originalPrice <= 0) $originalPrice = $price;

            $stock = intval($row['Q'] ?? 0);
            $mainImage = $row['U'] ?? null;

            $additionalImages = [];
            foreach (['V','W','X','Y','Z','AA','AB'] as $imgCol) {
                if (!empty($row[$imgCol])) {
                    $additionalImages[] = $row[$imgCol];
                }
            }

            $baseSlug = Str::slug($name);
            $uniqueSlug = $baseSlug . '-' . ($barcode ?: Str::random(6));

            Product::create([
                'category_id' => $category->id,
                'name' => $name,
                'slug' => $uniqueSlug,
                'barcode' => $barcode,
                'model_code' => $modelCode,
                'description' => $description,
                'price' => $price,
                'original_price' => $originalPrice,
                'stock' => $stock,
                'image' => $mainImage,
                'features' => [
                    'color' => $color,
                    'size' => $size,
                    'brand' => $brand,
                    'images' => $additionalImages,
                ],
                'is_active' => true,
            ]);

            $importedCount++;
        }

        $this->command->info("Başarıyla {$importedCount} ürün ve " . count($categoryMap) . " kategori içeri aktarıldı.");
    }
}
