<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "API E-Commerce Sederhana",
    version: "1.0.0",
    description: "Dokumentasi API Laravel"
)]

#[OA\Server(
    url: "http://localhost:8000",
    description: "Local Server"
)]

#[OA\Schema(
    schema: "Item",
    type: "object",
    required: ["id", "name", "price"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Laptop"),
        new OA\Property(property: "price", type: "number", example: 1500000)
    ]
)]

class OpenApi {}