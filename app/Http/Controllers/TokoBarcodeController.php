<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;
use Milon\Barcode\DNS1D;

class TokoBarcodeController extends Controller
{
    public function print(Toko $toko)
    {
        $d = new DNS1D();
        $d->setStorPath(public_path('barcode/')); // opsional, untuk cache
        $barcodeSvg = $d->getBarcodeSVG($toko->barcode, 'C128', 2, 60); // sesuaikan ukuran

        return view('filament.toko.print-barcode', compact('toko', 'barcodeSvg'));
    }

    public function printBulk(Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));
        $tokos = Toko::whereIn('id', $ids)->get();

        $d = new DNS1D();
        $d->setStorPath(public_path('barcode/'));

        // Siapkan SVG untuk tiap toko
        $barcodes = $tokos->mapWithKeys(function ($toko) use ($d) {
            $svg = $d->getBarcodeSVG($toko->barcode, 'C128', 2, 60);
            return [$toko->id => ['toko' => $toko, 'svg' => $svg]];
        });

        return view('filament.toko.print-barcodes-bulk', compact('barcodes'));
    }
}
