<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Kategorileri listele
     */
    public function index()
    {
        $kategoriler = Kategori::withCount('urunler')->orderBy('kategori_adi')->get();

        return view('admin.categories.index', compact('kategoriler'));
    }

    /**
     * Kategori duzenle formu
     */
    public function edit(Kategori $kategori)
    {
        return view('admin.categories.edit', compact('kategori'));
    }

    /**
     * Kategori guncelle
     */
    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'kategori_adi' => 'required|string|max:100',
            'komisyon_orani' => 'required|numeric|min:0|max:100',
            'aktif' => 'boolean',
        ]);

        $kategori->update([
            'kategori_adi' => $validated['kategori_adi'],
            'komisyon_orani' => $validated['komisyon_orani'],
            'aktif' => $request->has('aktif'),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori guncellendi.');
    }

    /**
     * Yeni kategori formu
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Yeni kategori kaydet
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_adi' => 'required|string|max:100|unique:kategoriler,kategori_adi',
            'komisyon_orani' => 'required|numeric|min:0|max:100',
            'aktif' => 'boolean',
        ]);

        Kategori::create([
            'kategori_adi' => $validated['kategori_adi'],
            'slug' => \Str::slug($validated['kategori_adi']),
            'komisyon_orani' => $validated['komisyon_orani'],
            'aktif' => $request->has('aktif'),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori eklendi.');
    }

    /**
     * Kategori sil
     */
    public function destroy(Kategori $kategori)
    {
        if ($kategori->urunler()->count() > 0) {
            return back()->with('error', 'Bu kategoride urunler var, silinemez.');
        }

        $kategori->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori silindi.');
    }
}
