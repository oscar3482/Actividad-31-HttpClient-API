<?php

namespace App\Data;

class ProductosData
{
    public static function todos()
    {
        return [
            [
                'id' => 1,
                'nombre' => 'Teclado Mecánico RGB',
                'descripcion' => 'Teclado mecánico con iluminación RGB, switches blue y diseño compacto TKL.',
                'precio' => 899.99,
                'existencia' => 15,
                'imagen1' => 'https://picsum.photos/id/0/500/300',
                'imagen2' => 'https://picsum.photos/id/1/500/300',
                'imagen3' => 'https://picsum.photos/id/2/500/300',
            ],
            [
                'id' => 2,
                'nombre' => 'Mouse Gamer Pro',
                'descripcion' => 'Mouse inalámbrico con sensor óptico de alta precisión y 7 botones programables.',
                'precio' => 649.99,
                'existencia' => 30,
                'imagen1' => 'https://picsum.photos/id/3/500/300',
                'imagen2' => 'https://picsum.photos/id/4/500/300',
                'imagen3' => 'https://picsum.photos/id/5/500/300',
            ],
            [
                'id' => 3,
                'nombre' => 'Monitor 24" Full HD',
                'descripcion' => 'Monitor IPS de 24 pulgadas con resolución 1080p, 144Hz y tiempo de respuesta 1ms.',
                'precio' => 3499.99,
                'existencia' => 8,
                'imagen1' => 'https://picsum.photos/id/6/500/300',
                'imagen2' => 'https://picsum.photos/id/7/500/300',
                'imagen3' => 'https://picsum.photos/id/8/500/300',
            ],
            [
                'id' => 4,
                'nombre' => 'Audífonos Bluetooth',
                'descripcion' => 'Audífonos over-ear con cancelación de ruido activa y 30 horas de batería.',
                'precio' => 1299.99,
                'existencia' => 20,
                'imagen1' => 'https://picsum.photos/id/9/500/300',
                'imagen2' => 'https://picsum.photos/id/10/500/300',
                'imagen3' => 'https://picsum.photos/id/11/500/300',
            ],
            [
                'id' => 5,
                'nombre' => 'Webcam Full HD',
                'descripcion' => 'Cámara web 1080p con micrófono integrado y compatibilidad plug & play.',
                'precio' => 549.99,
                'existencia' => 25,
                'imagen1' => 'https://picsum.photos/id/12/500/300',
                'imagen2' => 'https://picsum.photos/id/13/500/300',
                'imagen3' => 'https://picsum.photos/id/14/500/300',
            ],
            [
                'id' => 6,
                'nombre' => 'SSD 1TB NVMe',
                'descripcion' => 'Unidad de estado sólido NVMe con velocidades de lectura de hasta 3500 MB/s.',
                'precio' => 1199.99,
                'existencia' => 12,
                'imagen1' => 'https://picsum.photos/id/15/500/300',
                'imagen2' => 'https://picsum.photos/id/16/500/300',
                'imagen3' => 'https://picsum.photos/id/17/500/300',
            ],
        ];
    }

    public static function porId($id)
    {
        $productos = self::todos();
        foreach ($productos as $producto) {
            if ($producto['id'] == $id) {
                return $producto;
            }
        }
        return null;
    }
}