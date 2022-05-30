<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product            = new Product();
        $product->sku       = rand(1,123456789);
        $product->name      = "Computadora";
        $product->quantity  = 2;
        $product->price     = 500;
        $product->description= "Diseñado para un rendimiento duradero, el Lenovo IdeaPad 3i es el portátil perfecto para sus tareas diarias con funciones en las que puede confiar.";
        $product->image    = "public/images/imagen1.png";
        $product->save();

        $product2            = new Product();
        $product2->sku       = rand(1,123456789);
        $product2->name      = "Telefono";
        $product2->quantity  = 7;
        $product2->price     = 200;
        $product2->description= "El Motorola Moto G50 es un smartphone Android con una pantalla HD+ de 6.5 pulgadas. Por dentro, encontramos un procesador Snapdragon 480 de Qualcomm que provee conectividad 5G.";
        $product2->image    = "public/images/imagen2.png";
        $product2->save();

        $product3            = new Product();
        $product3->sku       = rand(1,123456789);
        $product3->name      = "Audifonos";
        $product3->quantity  = 10;
        $product3->price     = 50;
        $product3->description= "Los auriculares JBL TUNE500BT te permiten transmitir un sonido potente sin ataduras para hasta 16 horas de puro placer.";
        $product3->image    = "public/images/imagen3.png";
        $product3->save();
    }
}
