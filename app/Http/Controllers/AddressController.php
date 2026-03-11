<?php

namespace App\Http\Controllers;

use App\Models\Il;
use App\Models\Ilce;
use App\Models\Mahalle;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    public function getProvinces(): JsonResponse
    {
        $iller = Il::orderBy('il_adi')->get(['id', 'il_adi']);
        return response()->json($iller);
    }

    public function getDistricts(int $il): JsonResponse
    {
        $ilceler = Ilce::where('il_id', $il)
            ->orderBy('ilce_adi')
            ->get(['id', 'ilce_adi']);
        return response()->json($ilceler);
    }

    public function getNeighborhoods(int $ilce): JsonResponse
    {
        $mahalleler = Mahalle::where('ilce_id', $ilce)
            ->orderBy('mahalle_adi')
            ->get(['id', 'mahalle_adi', 'posta_kodu']);
        return response()->json($mahalleler);
    }
}
