<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Alert;

class ProductController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $products = Product::all();
        confirmDelete('Hapus Data!', 'Apakah anda yakin menghapus data ini?');
        return view('pages.admin.product.index', compact('products'));
    }

    public function create()
    {
        return view('pages.admin.product.create');
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'price' => 'numeric',
        'category' => 'required',
        'description' => 'required',
        'image' => 'required|mimes:png,jpeg,jpg',
        'discount' => 'nullable|numeric|min:0|max:100', // Validasi tetap sama
    ]);

    if ($validator->fails()) {
        Alert::error('Gagal!', 'Pastikan semua terisi dengan benar!');
        return redirect()->back();
    }

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move('images/', $imageName);
    }

    $product = Product::create([
        'name' => $request->name,
        'price' => $request->price,
        'category' => $request->category,
        'description' => $request->description,
        'image' => $imageName ?? null,
        'discount' => $request->discount ?? 0, // Tambahkan nilai default 0 jika tidak diisi
    ]);

    if ($product) {
        Alert::success('Berhasil!', 'Produk berhasil ditambahkan!');
        return redirect()->route('admin.product');
    } else {
        Alert::error('Gagal!', 'Produk gagal ditambahkan!');
        return redirect()->back();
    }
    }


    public function detail($id)
    {
        $product = Product::findOrFail($id);
        return view('pages.admin.product.detail', compact('product'));

    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('pages.admin.product.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'numeric',
            'category' => 'required',
            'description' => 'required',
            'image' => 'nullable|mimes:png,jpeg,jpg',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            Alert::error('Gagal!', 'Pastikan semua terisi dengan benar!');
            return redirect()->back();
        }

        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            $oldPath = public_path('images/' . $product->image);
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move('images/', $imageName);
        } else {
            $imageName = $product->image;
        }

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
            'description' => $request->description,
            'image' => $imageName,
            'discount' => $request->discount ?? 0,
        ]);

        if ($product) {
            Alert::success('Berhasil!', 'Produk berhasil diperbarui!');
            return redirect()->route('admin.product');
        } else {
            Alert::error('Gagal!', 'Produk gagal diperbarui!');
            return redirect()->back();
        }
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);

        $oldPath = public_path('images/' . $product->image);
        if (File::exists($oldPath)) {
            File::delete($oldPath);
        }

        $product->delete();

        if ($product) {
            Alert::success('Berhasil!', 'Produk berhasil dihapus!');
            return redirect()->back();
        } else {
            Alert::error('Gagal!', 'Produk gagal dihapus!');
            return redirect()->back();
        }
    }
}