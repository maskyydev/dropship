<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductFile;
use App\Models\ProductPreview;
use App\Models\ProductJual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $products = Product::when($search, function ($query) use ($search) {
            return $query->search($search);
        })->latest()->paginate(10);

        return view('admin.products.index', compact('products', 'search'));
    }

    public function create()
    {
        $categories = Product::$categories;
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'marketing_files.*' => 'nullable|file|max:5120',
            'preview_urls' => 'nullable|string',
            'barcode' => 'nullable|string|unique:products',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'alamat' => 'nullable|string',
            'recommend_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $product = Product::create($validated);

        // Simpan gambar
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = uniqid() . '-' . $image->getClientOriginalName();
                $image->storeAs('products', $filename, 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'filename' => $filename,
                ]);
            }
        }

        // Simpan file marketing
        if ($request->hasFile('marketing_files')) {
            foreach ($request->file('marketing_files') as $file) {
                $filename = uniqid() . '-' . $file->getClientOriginalName();
                $file->storeAs('product/kit', $filename, 'public');

                ProductFile::create([
                    'product_id' => $product->id,
                    'filename' => $filename,
                ]);
            }
        }

        // Simpan preview URL
        if ($request->filled('preview_urls')) {
            $urls = explode("\n", trim($request->preview_urls));
            foreach ($urls as $url) {
                ProductPreview::create([
                    'product_id' => $product->id,
                    'url' => trim($url),
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        $categories = Product::$categories;
        $images = $product->images;
        return view('admin.products.edit', compact('product', 'categories', 'images'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'stock'             => 'required|integer|min:0',
            'category'          => 'required|string',
            'images.*'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'marketing_files.*' => 'nullable|file|max:5120',
            'preview_urls'      => 'nullable|string',
            'barcode'           => 'nullable|string|unique:products,barcode,' . $product->id,
            'weight'            => 'nullable|numeric|min:0',
            'dimensions'        => 'nullable|string',
            'alamat'            => 'nullable|string',
            'recommend_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        // Update data utama produk
        $product->update($validated);

        // === HAPUS GAMBAR YANG DITANDAI ===
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $imgId) {
                $img = ProductImage::find($imgId);
                if ($img) {
                    Storage::disk('public')->delete('products/' . $img->filename);
                    $img->delete();
                }
            }
        }

        // === UPLOAD GAMBAR BARU ===
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = uniqid() . '-' . $image->getClientOriginalName();
                $image->storeAs('products', $filename, 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'filename'   => $filename,
                ]);
            }
        }

        // === HAPUS FILE MARKETING YANG DITANDAI ===
        if ($request->has('remove_files')) {
            foreach ($request->remove_files as $fileId) {
                $file = ProductFile::find($fileId);
                if ($file) {
                    Storage::disk('public')->delete('product/kit/' . $file->filename);
                    $file->delete();
                }
            }
        }

        // === UPLOAD FILE MARKETING BARU ===
        if ($request->hasFile('marketing_files')) {
            foreach ($request->file('marketing_files') as $file) {
                $filename = uniqid() . '-' . $file->getClientOriginalName();
                $file->storeAs('product/kit', $filename, 'public');

                ProductFile::create([
                    'product_id' => $product->id,
                    'filename'   => $filename,
                ]);
            }
        }

        // === UPDATE PREVIEW URL ===
        $product->previews()->delete(); // hapus semua sebelumnya
        if ($request->filled('preview_urls')) {
            $urls = preg_split('/\r\n|\r|\n/', trim($request->preview_urls));
            foreach ($urls as $url) {
                if (!empty($url)) {
                    ProductPreview::create([
                        'product_id' => $product->id,
                        'url'        => trim($url),
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }


    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            $img->delete();
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus sementara');
    }

    public function trash()
    {
        $products = Product::onlyTrashed()->latest()->paginate(10);
        return view('admin.products.trash', compact('products'));
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        \App\Models\ProductImage::onlyTrashed()
            ->where('product_id', $product->id)
            ->restore();

        return redirect()->route('admin.products.trash')->with('success', 'Produk dan gambar berhasil dikembalikan');
    }

    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        foreach ($product->images as $img) {
            Storage::disk('public')->delete('products/' . $img->filename);
            $img->delete();
        }

        $product->forceDelete();

        return redirect()->route('admin.products.trash')->with('success', 'Produk berhasil dihapus permanen');
    }

    public function jual(Product $product)
    {
        $userId = Auth::id();

        // Cek apakah sudah ada di product_jual sebagai wishlist
        $exists = ProductJual::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->where('filter', 'jual')
            ->exists();

        if ($exists) {
            return back()->with('info', 'Produk sudah ada di wishlist Anda.');
        }

        ProductJual::create([
            'user_id' => $userId,
            'product_id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'image' => $product->image,
            'category' => $product->category,
            'barcode' => $product->barcode,
            'weight' => $product->weight,
            'dimensions' => $product->dimensions,
            'alamat' => $product->alamat,
            'recommend_percent' => $product->recommend_percent,
            'filter' => 'jual', // ✅ Penting: tandai sebagai wishlist
        ]);

        return back()->with('success', 'Produk berhasil dijual.');
    }

    public function wishlist(Product $product)
    {
        $userId = Auth::id();

        // Cek apakah sudah ada di product_jual sebagai wishlist
        $exists = ProductJual::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->where('filter', 'withlist')
            ->exists();

        if ($exists) {
            return back()->with('info', 'Produk sudah ada di wishlist Anda.');
        }

        ProductJual::create([
            'user_id' => $userId,
            'product_id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'image' => $product->image,
            'category' => $product->category,
            'barcode' => $product->barcode,
            'weight' => $product->weight,
            'dimensions' => $product->dimensions,
            'alamat' => $product->alamat,
            'recommend_percent' => $product->recommend_percent,
            'filter' => 'wishlist', // ✅ Penting: tandai sebagai wishlist
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan ke wishlist.');
    }
}
