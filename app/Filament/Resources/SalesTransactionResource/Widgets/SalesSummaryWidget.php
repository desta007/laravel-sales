<?php

namespace App\Filament\Resources\SalesTransactionResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\SalesTransaction;
use Illuminate\Support\Carbon;

class SalesSummaryWidget extends ChartWidget
{
    protected static ?string $heading = 'Sales Summary (Daily, Weekly, Monthly)';

    protected function getData(): array
    {
        $today = now()->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        $daily = SalesTransaction::whereDate('transaction_date', $today)->sum('total_amount');
        $weekly = SalesTransaction::whereBetween('transaction_date', [$weekStart, $today])->sum('total_amount');
        $monthly = SalesTransaction::whereBetween('transaction_date', [$monthStart, $today])->sum('total_amount');

        return [
            'datasets' => [
                [
                    'label' => 'Total Sales',
                    'data' => [
                        $daily,
                        $weekly,
                        $monthly,
                    ],
                    'backgroundColor' => [
                        '#60a5fa', // blue
                        '#34d399', // green
                        '#fbbf24', // yellow
                    ],
                ],
            ],
            'labels' => [
                'Today',
                'This Week',
                'This Month',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
