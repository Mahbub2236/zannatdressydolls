<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // <--- Eta thakte hobe
use Illuminate\Support\Str;

class ProductImport implements ToModel, WithHeadingRow // <--- Eta thakte hobe
{
    public function model(array $row)
    {
        // Jodi CSV te header thake 'name', tahole ekhane $row['name'] hobe
        // Jodi header thake 'Product Name', tahole ekhane $row['product_name'] hobe
        
        $last_id = Product::orderBy('id', 'desc')->value('id') ?? 0;
        $next_id = $last_id + 1;

        return new Product([
            'name'           => $row['name'], // CSV column name match thakte hobe
            'category_id'    => $row['category_id'] ?? 1,
            'new_price'      => $row['new_price'] ?? 0,
            'purchase_price' => $row['purchase_price'] ?? 0,
            'stock'          => $row['stock'] ?? 0,
            'description'    => $row['description'] ?? '',
            'status'         => $row['status'] ?? 1,
            'slug'           => Str::slug($row['name'] . '-' . $next_id),
            'product_code'   => 'P' . str_pad($next_id, 4, '0', STR_PAD_LEFT),
        ]);
    }
}
