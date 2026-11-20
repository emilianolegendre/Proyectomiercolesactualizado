<?php

header("Content-Type: application/json; charset=UTF-8");


// =====================================================
// AULAVIVA - API SIMULADA DE SENSORES
// =====================================================
//
// ESTA API ES DE PRUEBA.
//
// Simula exactamente la respuesta que posteriormente
// podría entregar la API real de los sensores.
//
// Cada consulta genera valores nuevos.
// =====================================================


// -----------------------------------------------------
// MQ-5
// -----------------------------------------------------

$mq5 = rand(400, 700);


// -----------------------------------------------------
// DHT11
// -----------------------------------------------------

$dhtTemperatura = rand(220, 280) / 10;

$dhtHumedad = rand(500, 750) / 10;


// -----------------------------------------------------
// DS18B20
// -----------------------------------------------------

$ds18b20 = rand(220, 270) / 10;


// -----------------------------------------------------
// HORA
// -----------------------------------------------------

$hora = date("H:i:s");


// -----------------------------------------------------
// RESPUESTA
// -----------------------------------------------------

$respuesta = [

    "ok" => true,

    "estado" => "conectado",

    "mensaje" => "Datos recibidos correctamente",

    "sensores" => [

        "mq5" => [

            "valor" => $mq5,

            "unidad" => "ppm",

            "hora" => $hora,

            "estado" => "Activo"

        ],


        "dht11" => [

            "temperatura" => $dhtTemperatura,

            "humedad" => $dhtHumedad,

            "unidadTemperatura" => "°C",

            "unidadHumedad" => "%",

            "hora" => $hora,

            "estado" => "Activo"

        ],


        "ds18b20" => [

            "temperatura" => $ds18b20,

            "unidad" => "°C",

            "hora" => $hora,

            "estado" => "Activo"

        ]

    ]

];


// -----------------------------------------------------
// DEVOLVER JSON
// -----------------------------------------------------

echo json_encode(
    $respuesta,
    JSON_UNESCAPED_UNICODE
);

?>