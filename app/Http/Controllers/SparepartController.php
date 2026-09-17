<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
   public function index()
    {
        $spareparts = Sparepart::latest()->paginate(10);
        return view('spareparts.index', compact('spareparts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'      => 'required|unique:spareparts,code',
            'name'      => 'required|string|max:255',
            'stock'     => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'unit'      => 'required|string',
            'price'     => 'required|numeric|min:0',
        ]);

        Sparepart::create($request->all());
        return redirect()->back()->with('success', 'Sparepart berhasil ditambahkan!');
    }

    public function destroy(Sparepart $sparepart)
    {
        $sparepart->delete();
        return redirect()->back()->with('success', 'Sparepart berhasil dihapus!');
    } 
}
