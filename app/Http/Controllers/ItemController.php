<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;


class ItemController extends Controller
{

    #[OA\Get(
    path: "/api/items",
    summary: "Ambil semua item",
    responses: [
        new OA\Response(
            response: 200,
            description: "Berhasil",
            content: new OA\JsonContent(
                type: "array",
                items: new OA\Items(ref: "#/components/schemas/Item")
            )
        )
    ]
    )]

    public function index()
    {
        $path = storage_path('app/items.json');

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        return response()->json($data);
    }


    #[OA\Get(
    path: "/api/items/{id}",
    summary: "Ambil item berdasarkan ID",
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: "Berhasil",
            content: new OA\JsonContent(ref: "#/components/schemas/Item")
        ),
        new OA\Response(response: 404, description: "Tidak ditemukan")
    ]
)]

    public function show($id)
    {
        $path = storage_path('app/items.json');
        $json = file_get_contents($path);
        $data = json_decode($json, true);

        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return response()->json($item);
            }
        }

        return response()->json([
            'message' => "Item dengan ID $id tidak ditemukan"
        ], 404);
    }


    #[OA\Post(
    path: "/api/items",
    summary: "Tambah item",
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "price"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Mouse"),
                new OA\Property(property: "price", type: "number", example: 50000)
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: "Berhasil ditambahkan",
            content: new OA\JsonContent(ref: "#/components/schemas/Item")
        )
    ]
)]

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0'
        ]);

        $path = storage_path('app/items.json');
        $json = file_get_contents($path);
        $data = json_decode($json, true);

        $newId = count($data) > 0 ? max(array_column($data, 'id')) + 1 : 1;

        $newItem = [
            'id' => $newId,
            'name' => $request->name,
            'price' => $request->price
        ];

        $data[] = $newItem;

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

        return response()->json([
            'message' => 'Item berhasil ditambahkan',
            'data' => $newItem
        ], 201);
    }


    #[OA\Put(
    path: "/api/items/{id}",
    summary: "Update seluruh data item",
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
        )
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "price"],
            properties: [
                new OA\Property(property: "name", type: "string"),
                new OA\Property(property: "price", type: "number")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Berhasil diupdate"),
        new OA\Response(response: 404, description: "Tidak ditemukan")
    ]
)]

    public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0'
    ]);

    $path = storage_path('app/items.json');
    $json = file_get_contents($path);
    $data = json_decode($json, true);

    foreach ($data as &$item) {
        if ($item['id'] == $id) {
            $item['name'] = $request->name;
            $item['price'] = $request->price;

            file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

            return response()->json([
                'message' => 'Item berhasil diupdate (PUT)',
                'data' => $item
            ]);
        }
    }

    return response()->json([
        'message' => "Item dengan ID $id tidak ditemukan"
    ], 404);
}

#[OA\Patch(
    path: "/api/items/{id}",
    summary: "Update sebagian data item",
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
        )
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "name", type: "string"),
                new OA\Property(property: "price", type: "number")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Berhasil diupdate"),
        new OA\Response(response: 404, description: "Tidak ditemukan")
    ]
)]

public function patch(Request $request, $id)
{
    $path = storage_path('app/items.json');
    $json = file_get_contents($path);
    $data = json_decode($json, true);

    foreach ($data as &$item) {
        if ($item['id'] == $id) {

            if ($request->has('name')) {
                $item['name'] = $request->name;
            }

            if ($request->has('price')) {
                $item['price'] = $request->price;
            }

            file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

            return response()->json([
                'message' => 'Item berhasil diupdate (PATCH)',
                'data' => $item
            ]);
        }
    }

    return response()->json([
        'message' => "Item dengan ID $id tidak ditemukan"
    ], 404);
}

#[OA\Delete(
    path: "/api/items/{id}",
    summary: "Hapus item",
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Berhasil dihapus"),
        new OA\Response(response: 404, description: "Tidak ditemukan")
    ]
)]

public function destroy($id)
{
    $path = storage_path('app/items.json');
    $json = file_get_contents($path);
    $data = json_decode($json, true);

    foreach ($data as $index => $item) {
        if ($item['id'] == $id) {

            array_splice($data, $index, 1);

            file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

            return response()->json([
                'message' => "Item dengan ID $id berhasil dihapus"
            ]);
        }
    }

    return response()->json([
        'message' => "Item dengan ID $id tidak ditemukan"
    ], 404);
}
}