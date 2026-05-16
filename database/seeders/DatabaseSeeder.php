<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed demo user
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // 2. Seed electronic products based on the exact DB structure
        $products = [
            [
                'name' => 'iPhone 15 Pro Max',
                'brand' => 'Apple',
                'category' => 'phones',
                'price' => 5499.00,
                'rating' => 4.9,
                'image' => 'iphone15_pro_max.jpg',
                'stock' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'MacBook Pro M3',
                'brand' => 'Apple',
                'category' => 'laptops',
                'price' => 7499.00,
                'rating' => 4.8,
                'image' => 'macbook_pro_m3.jpg',
                'stock' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lenovo Legion Pro 5i',
                'brand' => 'Lenovo',
                'category' => 'laptops',
                'price' => 6299.00,
                'rating' => 4.7,
                'image' => 'lenovo_legion_5.jpg',
                'stock' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lenovo ThinkPad X1 Carbon',
                'brand' => 'Lenovo',
                'category' => 'laptops',
                'price' => 8200.00,
                'rating' => 4.9,
                'image' => 'thinkpad_x1.jpg',
                'stock' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Honor Magic 6 Pro',
                'brand' => 'Honor',
                'category' => 'phones',
                'price' => 3999.00,
                'rating' => 4.6,
                'image' => 'honor_magic_6.jpg',
                'stock' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        DB::table('products')->insert($products);
        
        // 3. Seed an external sample order & payments to test relationship displays
        $product1 = Product::where('brand', 'Apple')->first();
        
        if ($product1) {
             $orderId = DB::table('orders')->insertGetId([
                'session_id' => 'sample_session_xyz987',
                'user_id' => $user->id,
                'subtotal' => $product1->price,
                'shipping' => 0.00,
                'total' => $product1->price,
                'status' => 'completed',
                'delivery_name' => 'Demo User',
                'delivery_phone' => '0123456789',
                'delivery_address_line1' => '12 Electronic Avenue',
                'delivery_city' => 'Kuala Lumpur',
                'delivery_state' => 'KL',
                'delivery_postcode' => '50000',
                'delivery_country' => 'Malaysia',
                'delivery_status' => 'delivered',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $product1->id,
                'product_name' => $product1->name,
                'price' => $product1->price,
                'quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::table('payments')->insert([
                'order_id' => $orderId,
                'payment_method' => 'maybank',
                'amount' => $product1->price,
                'status' => 'completed',
                'reference' => 'TXN-9876543210',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
