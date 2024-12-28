<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Imports\DistributorImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


class DistributorController extends Controller
{

    public function import(Request $request)
    {
        $distributor = Distributor::all();

        try {
            $file = $request->file('file');
            Excel::import(new DistributorImport, $file);

            Alert::success('Berhasil!', 'Data berhasil di import!');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = '';
            foreach ($failures as $failure) {
            $messages .= 'Kesalahan pada baris ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '. ';
            }

            Alert::error('Gagal!', 'Validasi Gagal: ' . $messages);
        } catch (\Exception $e) {
            Alert::error('Gagal!', 'Pastikan format dan isi sudah benar! Error: ' . $e->getMessage());
        } finally {
            return redirect()->back();
        }
    }

    public function export()
    {
        $distributor = Distributor::all();

        $pdf = Pdf::loadView('pages.admin.distributor.export', compact('distributor'))->setPaper('a4', 'landscape');

        return $pdf->download('distributor.pdf');
    }

    public function index()
    {
        $distributor = Distributor::all();

        return view('pages.admin.distributor.index', compact('distributor'));
    }

    public function create()
    {
        $distributor = Distributor::all();

        // Return the view for creating a distributor
        return view('pages.admin.distributor.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_distributor' => 'required',
            'kota' => 'required',
            'provinsi' => 'required',
            'kontak' => 'numeric|required',
            'email' => 'required|email:dns',
        ]);

        if ($validator->fails()) {
            Alert::error('Gagal!', 'Pastikan semua terisi dengan benar!');
            return redirect()->back();
        }

        $distributor = Distributor::create([
            'nama_distributor' => $request->nama_distributor,
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,
            'kontak' => $request->kontak,
            'email' => $request->email,
        ]);

        if ($distributor) {
            Alert::success('Berhasil!', 'Distributor berhasil ditambahkan!');
            return redirect()->route('admin.distributor');
        } else {
            Alert::error('Gagal', 'Distributor gagal ditambahkan');
            return redirect()->back();
        }
    }

    public function edit($id)
    {
        $distributor = Distributor::findOrFail($id);

        return view('pages.admin.distributor.edit', compact('distributor'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_distributor' => 'required',
            'kota' => 'required',
            'provinsi' => 'required',
            'kontak' => 'numeric|required',
            'email' => 'required|email:dns',
        ]);

        if ($validator->fails()) {
            Alert::error('Gagal!', 'Pastikan semua terisi dengan benar!');
            return redirect()->back();
        }

        $distributor = Distributor::findOrFail($id);

        $distributor->update([
            'nama_distributor' => $request->nama_distributor,
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,
            'kontak' => $request->kontak,
            'email' => $request->email,
        ]);

        if ($distributor) {
            Alert::success('Berhasil!', 'distributor berhasil diperbarui!');
            return redirect()->route('admin.distributor');
        } else {
            Alert::error('Gagal!', 'distributor gagal diperbarui!');
            return redirect()->back();
        }
    }

    public function delete($id)
    {

        $distributor = Distributor::findOrFail($id);
        $distributor->delete();

        if ($distributor) {
            Alert::success('Berhasil', 'Produk berhasil dihapus!');
            return redirect()->back();
        } else {
            Alert::error('Gagal!', 'Produk gagal dihapus!');
            return redirect()->back();
        }
    }


}