<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Sales;
use App\Models\Toko;
use App\Models\Wilayah;
use Illuminate\Support\Facades\Storage;
use App\Models\SalesVisitHistory;
use App\Models\SalesTransaction;
use App\Models\SalesTransactionDetail;
use App\Models\Barang;
use Illuminate\Support\Facades\URL;

// Tempatkan endpoint API di bawah ini

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $sales = Sales::where('email', $request->email)->first();

    if (! $sales || ! Hash::check($request->password, $sales->password)) {
        return response()->json(['message' => 'Email atau password salah'], 401);
    }

    $token = $sales->createToken('mobile')->plainTextToken;

    return response()->json([
        'token' => $token,
        'sales' => $sales,
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    // Get all wilayah (untuk form input toko - selectbox)
    Route::get('/wilayah', function () {
        $wilayah = Wilayah::select('id', 'name', 'description')->get();
        return response()->json($wilayah);
    });

    // Get all barang (untuk form input transaksi penjualan)
    Route::get('/barang', function () {
        $barang = Barang::select('id', 'name', 'code', 'price', 'stock')->get();
        return response()->json($barang);
    });

    // Get all toko (untuk form laporan transaksi penjualan - checkbox)
    Route::get('/toko', function () {
        $toko = Toko::with('wilayah:id,name')
            ->select('id', 'name', 'address', 'phone', 'wilayah_id')
            ->get();
        return response()->json($toko);
    });

    // List semua toko
    Route::get('/tokos', function () {
        $tokos = Toko::all();
        
        // Add photo URL with server prefix
        $tokos->transform(function ($toko) {
            if ($toko->photo) {
                $toko->photo = URL::to('storage/' . $toko->photo);
            }
            return $toko;
        });
        
        return $tokos;
    });

    // Detail toko
    Route::get('/tokos/{id}', function ($id) {
        $toko = Toko::findOrFail($id);
        
        // Add photo URL with server prefix
        if ($toko->photo) {
            $toko->photo = URL::to('storage/' . $toko->photo);
        }
        
        return $toko;
    });

    // Create toko
    Route::post('/tokos', function (Request $request) {
        $data = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'nullable',
            'barcode' => 'required|unique:tokos',
            'wilayah_id' => 'required|exists:wilayahs,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('toko_photos', 'public');
        }
        $toko = Toko::create($data);
        return response()->json($toko, 201);
    });

    // Update toko
    Route::post('/tokos/{id}', function (Request $request, $id) {
        $toko = Toko::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|required',
            'address' => 'sometimes|required',
            'phone' => 'nullable',
            'barcode' => 'sometimes|required|unique:tokos,barcode,' . $id,
            'wilayah_id' => 'sometimes|required|exists:wilayahs,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($toko->photo) {
                Storage::disk('public')->delete($toko->photo);
            }
            $data['photo'] = $request->file('photo')->store('toko_photos', 'public');
        }
        $toko->update($data);
        return response()->json($toko);
    });

    // Delete toko
    Route::delete('/tokos/{id}', function ($id) {
        $toko = Toko::findOrFail($id);
        if ($toko->photo) {
            Storage::disk('public')->delete($toko->photo);
        }
        $toko->delete();
        return response()->json(['message' => 'Toko deleted']);
    });

    // Catat kehadiran sales (scan barcode di toko)
    Route::post('/kehadiran', function (Request $request) {
        $data = $request->validate([
            'toko_id' => 'required|exists:tokos,id',
            'barcode' => 'required',
            'visit_date' => 'required|date',
            'notes' => 'nullable',
        ]);
        $toko = Toko::findOrFail($data['toko_id']);
        if ($toko->barcode !== $data['barcode']) {
            return response()->json(['message' => 'Barcode tidak sesuai dengan toko'], 422);
        }
        $sales = $request->user();
        $history = SalesVisitHistory::create([
            'sales_id' => $sales->id,
            'toko_id' => $toko->id,
            'visit_date' => $data['visit_date'],
            'notes' => $data['notes'] ?? null,
        ]);
        return response()->json($history, 201);
    });

    // Transaksi penjualan barang
    Route::post('/penjualan', function (Request $request) {
        $data = $request->validate([
            'toko_id' => 'required|exists:tokos,id',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'notes' => 'nullable',
        ]);
        $sales = $request->user();
        $total = 0;
        foreach ($data['items'] as $item) {
            $total += $item['quantity'] * $item['price'];
        }
        $transaction = SalesTransaction::create([
            'sales_id' => $sales->id,
            'toko_id' => $data['toko_id'],
            'transaction_date' => now(),
            'total_amount' => $total,
            'notes' => $data['notes'] ?? null,
        ]);
        foreach ($data['items'] as $item) {
            SalesTransactionDetail::create([
                'sales_transaction_id' => $transaction->id,
                'barang_id' => $item['barang_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['quantity'] * $item['price'],
            ]);
        }
        return response()->json($transaction->load('details'), 201);
    });

    // Laporan penjualan per sales yang login (POST, filter di body)
    Route::post('/laporan-penjualan', function (Request $request) {
        $sales = $request->user();
        $query = SalesTransaction::with(['toko', 'details.barang'])
            ->where('sales_id', $sales->id);
        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', $request->input('to'));
        }
        if ($request->filled('toko_ids')) {
            $query->whereIn('toko_id', $request->input('toko_ids'));
        }
        $data = $query->orderBy('transaction_date', 'desc')->get();
        return response()->json($data);
    });
});
