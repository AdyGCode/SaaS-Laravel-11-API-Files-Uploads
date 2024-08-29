<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Product::all();
        return $this->sendResponse($data, "Products retrieved successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'description' => 'sometimes',
            'product_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        $productImage = $request->file('product_image');
        $originalName = null;
        $storedName = null;
        $mimeType = null;

        if ($productImage != null) {
            $originalExtension = $productImage->extension();
            $originalName = $productImage->getClientOriginalName();
            $storedName = Str::uuid().'.'.$originalExtension;
            $request->product_image->storeAs('images', $storedName);
            $mimeType = $productImage->getClientMimeType();
        }

        $product = new Product([
            "name" => $request->name,
            "detail" => $request->detail,
            "mime_type" => $mimeType ?? null,
            "original_filename" => $originalName ?? null,
            "stored_filename" => $storedName ?? null,
        ]);

        return $this->sendResponse($product, "Created product");

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Product::whereId($id)->get();

        if ($data->isEmpty()) {
            return $this->sendError("Product Not Found", $data);
        }

        return $this->sendResponse($data, "Product retrieved successfully");

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $product = Product::whereId($id)->first()->get();

        $storedName = $product->stored_filename;
        dd($storedName);

        Storage::delete('images/'. $storedName);

        $product->delete();


        return $this->sendResponse($product, "Product deleted");
    }
}
