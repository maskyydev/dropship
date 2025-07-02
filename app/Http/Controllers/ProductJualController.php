<?php

namespace App\Http\Controllers;

use App\Models\ProductJual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductJualController extends Controller
{
    /**
     * Menampilkan daftar produk dengan filter pencarian dan kategori.
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user_id = Auth::id();

        $query = ProductJual::where('user_id', $user_id)
            ->where('filter', 'jual') // ⬅️ Tambahkan filter khusus di sini
            ->with('product.images');

        // Pencarian berdasarkan nama produk
        if ($request->filled('cari')) {
            $query->where('name', 'like', '%' . $request->cari . '%');
        }

        // Filter berdasarkan kategori (dari kolom category di tabel)
        if ($request->filled('kategori')) {
            $query->where('category', $request->kategori);
        }

        $produk = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Ambil daftar kategori unik dari kolom category, hanya dari produk yang 'jual'
        $kategoriList = ProductJual::where('user_id', $user_id)
            ->where('filter', 'jual') // ⬅️ Tambahkan juga pada kategoriList
            ->select('category')
            ->distinct()
            ->pluck('category');

        // Statistik produk
        $statistik = [
            'total' => ProductJual::where('user_id', $user_id)->where('filter', 'jual')->count(),
            'aktif' => ProductJual::where('user_id', $user_id)->where('filter', 'jual')->where('stock', '>', 0)->count(),
            'habis' => ProductJual::where('user_id', $user_id)->where('filter', 'jual')->where('stock', '<=', 0)->count(),
            'pribadi' => ProductJual::where('user_id', $user_id)->where('filter', 'jual')->where('recommend_percent', 0)->count(),
        ];

        return view('produk.daftar', compact('produk', 'kategoriList', 'statistik'));
    }


    /**
     * Menghapus beberapa produk sekaligus berdasarkan checkbox yang dipilih.
     */
    public function bulkDelete(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user_id = Auth::id();
        $ids = $request->input('selected', []);

        if (!empty($ids)) {
            // Hapus produk hanya milik user ini
            ProductJual::where('user_id', $user_id)->whereIn('id', $ids)->delete();
            return redirect()->route('produk.daftar')->with('success', 'Produk berhasil dihapus.');
        }

        return redirect()->route('produk.daftar')->with('warning', 'Tidak ada produk yang dipilih.');
    }

    /**
     * Menampilkan daftar produk wishlist
     */
    public function wishlist(Request $request)
    {
        $user_id = Auth::id();

        $query = ProductJual::where('user_id', $user_id)
            ->where('filter', 'wishlist')
            ->with('product.images');

        // Pencarian berdasarkan nama produk
        if ($request->filled('cari')) {
            $query->where('name', 'like', '%' . $request->cari . '%');
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('category', $request->kategori);
        }

        // Filter berdasarkan lokasi (kabupaten yang dipilih)
        if ($request->filled('locations')) {
            $lokasiList = $request->input('locations');
            $query->where(function ($q) use ($lokasiList) {
                foreach ($lokasiList as $kabupaten) {
                    $q->orWhere('alamat', 'like', "%$kabupaten%");
                }
            });
        }

        // Filter berdasarkan harga rekomendasi (harga + percent)
        if ($request->filled('price_ranges')) {
            $query->where(function ($q) use ($request) {
                foreach ($request->price_ranges as $range) {
                    [$min, $max] = explode(',', $range);
                    $q->orWhere(function ($sub) use ($min, $max) {
                        if ($min !== '') {
                            $sub->whereRaw('(price + (price * recommend_percent / 100)) >= ?', [$min]);
                        }
                        if ($max !== '') {
                            $sub->whereRaw('(price + (price * recommend_percent / 100)) <= ?', [$max]);
                        }
                    });
                }
            });
        }

        $produk = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Ambil daftar kategori unik
        $kategoriList = ProductJual::where('user_id', $user_id)
            ->where('filter', 'wishlist')
            ->select('category')
            ->distinct()
            ->pluck('category');

        // Ambil daftar kabupaten unik dari alamat
        $lokasiList = ProductJual::where('user_id', $user_id)
            ->where('filter', 'wishlist')
            ->pluck('alamat')
            ->filter()
            ->map(function ($alamat) {
                $parts = explode(',', $alamat);
                return isset($parts[1]) ? trim($parts[1]) : null;
            })
            ->unique()
            ->filter()
            ->values();

        return view('produk.wishlist', compact('produk', 'kategoriList', 'lokasiList'));
    }

    public function hapusWishlist($id)
    {
        $user_id = Auth::id();

        $produk = ProductJual::where('id', $id)
            ->where('user_id', $user_id)
            ->where('filter', 'wishlist')
            ->first();

        if (!$produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan atau bukan milik Anda.');
        }

        $produk->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus dari wishlist.');
    }
}
