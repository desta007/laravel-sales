<?php

namespace App\Filament\Widgets;

use App\Models\Sales;
use App\Models\SalesTransaction;
use App\Models\Toko;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStats extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $threshold = Carbon::now('Asia/Jakarta')->subDays(30)->toDateString();

        return [
            Stat::Make('Total Sales', Sales::query()->count()),
            Stat::make('Total Penjualan Sales Hari Ini', 'IDR ' . number_format(SalesTransaction::query()
                ->where('transaction_date', $today)
                ->sum('total_amount'))),
            Stat::make('Total toko aktif (ada transaksi dalam 30 hari terakhir)', Toko::whereHas('salesTransactions', function ($q) use ($threshold) {
                $q->whereDate('transaction_date', '>=', $threshold);
            })->count())
        ];
    }
}
