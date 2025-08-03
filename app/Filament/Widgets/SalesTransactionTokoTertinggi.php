<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\TokoResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class SalesTransactionTokoTertinggi extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(TokoResource::getEloquentQuery()
                ->withSum('salesTransactions', 'total_amount') // agregasi total penjualan per toko
                ->orderByDesc('sales_transactions_sum_total_amount'))
            ->defaultPaginationPageOption(5)

            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Toko')->searchable(),
                Tables\Columns\TextColumn::make('wilayah.name')->label('Wilayah'),
                Tables\Columns\TextColumn::make('sales.name')->label('Sales'),
                Tables\Columns\TextColumn::make('sales_transactions_sum_total_amount') // kolom agregat total penjualan
                    ->label('Total Penjualan')
                    ->formatStateUsing(fn($state) => $state === null ? '0' : number_format($state, 0, ',', '.')),
            ]);
    }
}
