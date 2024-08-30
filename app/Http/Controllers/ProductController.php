<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'min:4'
            ],
            'description' => [
                'sometimes',
                'min:12'
            ],
            'product_image' => [
                'sometimes',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:1024',
                'dimensions:min_width=100,min_height=100,max_width=2048,max_height=2048',
            ],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return $this->sendError("Error in product data", $errors);
        }

        $productImage = $request->file('product_image');
        $originalName = null;
        $storedName = null;
        $mimeType = null;

        if ($productImage != null) {
            $originalExtension = $productImage->extension();
            $originalName = $productImage->getClientOriginalName();
            $storedName = Str::uuid() . '.' . $originalExtension;
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
        $product->save();

        return $this->sendResponse($product, "Created product");

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::whereId($id)->get();

        if ($product->isEmpty()) {
            return $this->sendError("Product Not Found", $product);
        }

        return $this->sendResponse($product, "Product retrieved successfully");

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
    public function destroy(int $id)
    {

        $products = Product::whereId($id)->limit(1)->get();

        if ($products->isEmpty()) {
            return $this->sendError("Product Not Found", $products);
        }

        foreach ($products as $product) {
            $storedName = $product->stored_filename;

            if(Storage::exists('images/' . $storedName)) {
                Storage::delete('images/' . $storedName);
            }

            $product->delete();
        }

        return $this->sendResponse($product, "Product deleted");
    }
}
