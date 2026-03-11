<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\AdminActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    /**
     * Slider listesi
     */
    public function index(): View
    {
        $sliders = Slider::orderBy('sira')->paginate(20);

        return view('admin.sliders.index', compact('sliders'));
    }

    /**
     * Yeni slider formu
     */
    public function create(): View
    {
        return view('admin.sliders.create');
    }

    /**
     * Slider kaydet
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'baslik' => 'required|string|max:255',
            'alt_baslik' => 'nullable|string|max:255',
            'resim' => 'nullable|image|max:5120',
            'link' => 'nullable|string|max:500',
            'tip' => 'required|in:ozel,populer,yeni,indirimli',
            'sira' => 'nullable|integer|min:0',
            'aktif' => 'boolean',
        ]);

        // Resim yukle
        if ($request->hasFile('resim')) {
            $resim = $request->file('resim');
            $resimAdi = time() . '_' . $resim->getClientOriginalName();
            $resim->storeAs('public/sliders', $resimAdi);
            $validated['resim'] = $resimAdi;
        }

        $validated['aktif'] = $request->has('aktif');
        $validated['sira'] = $validated['sira'] ?? 0;

        $slider = Slider::create($validated);

        AdminActivityLog::log('slider.created', $slider, null, $validated, 'Slider olusturuldu: ' . $slider->baslik);

        return redirect()->route('admin.sliders.index')
            ->with('basarili', 'Slider basariyla olusturuldu.');
    }

    /**
     * Slider duzenle formu
     */
    public function edit(Slider $slider): View
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    /**
     * Slider guncelle
     */
    public function update(Request $request, Slider $slider): RedirectResponse
    {
        $validated = $request->validate([
            'baslik' => 'required|string|max:255',
            'alt_baslik' => 'nullable|string|max:255',
            'resim' => 'nullable|image|max:5120',
            'link' => 'nullable|string|max:500',
            'tip' => 'required|in:ozel,populer,yeni,indirimli',
            'sira' => 'nullable|integer|min:0',
            'aktif' => 'boolean',
        ]);

        $eskiDegerler = $slider->toArray();

        // Yeni resim yuklendiyse
        if ($request->hasFile('resim')) {
            // Eski resmi sil
            if ($slider->resim) {
                Storage::delete('public/sliders/' . $slider->resim);
            }

            $resim = $request->file('resim');
            $resimAdi = time() . '_' . $resim->getClientOriginalName();
            $resim->storeAs('public/sliders', $resimAdi);
            $validated['resim'] = $resimAdi;
        }

        $validated['aktif'] = $request->has('aktif');
        $validated['sira'] = $validated['sira'] ?? 0;

        $slider->update($validated);

        AdminActivityLog::log('slider.updated', $slider, $eskiDegerler, $validated, 'Slider guncellendi: ' . $slider->baslik);

        return redirect()->route('admin.sliders.index')
            ->with('basarili', 'Slider basariyla guncellendi.');
    }

    /**
     * Slider sil
     */
    public function destroy(Slider $slider): RedirectResponse
    {
        // Resmi sil
        if ($slider->resim) {
            Storage::delete('public/sliders/' . $slider->resim);
        }

        AdminActivityLog::log('slider.deleted', $slider, $slider->toArray(), null, 'Slider silindi: ' . $slider->baslik);

        $slider->delete();

        return redirect()->route('admin.sliders.index')
            ->with('basarili', 'Slider basariyla silindi.');
    }

    /**
     * Slider siralamasini guncelle (AJAX)
     */
    public function updateSira(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'siralar' => 'required|array',
            'siralar.*.id' => 'required|exists:sliders,id',
            'siralar.*.sira' => 'required|integer|min:0',
        ]);

        foreach ($request->siralar as $item) {
            Slider::where('id', $item['id'])->update(['sira' => $item['sira']]);
        }

        return response()->json(['basarili' => true]);
    }
}
