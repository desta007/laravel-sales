<?php

namespace App\Filament\Resources\SalesTransactionResource\Pages;

use App\Filament\Resources\SalesTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\SalesTransactionResource\Widgets\SalesSummaryWidget;

class ListSalesTransactions extends ListRecords
{
    protected static string $resource = SalesTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SalesSummaryWidget::class,
        ];
    }
}
