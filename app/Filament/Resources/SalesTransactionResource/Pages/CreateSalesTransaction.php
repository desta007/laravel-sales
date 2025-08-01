<?php

namespace App\Filament\Resources\SalesTransactionResource\Pages;

use App\Filament\Resources\SalesTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSalesTransaction extends CreateRecord
{
    protected static string $resource = SalesTransactionResource::class;

    // tambahkan listener
    protected $listeners = [
        'salesTransactionDetailChanged' => 'refreshTotal',
    ];

    public function refreshTotal(): void
    {
        // reload record dari DB sehingga total_amount terbaru terambil
        $this->record->refresh();

        // isi ulang form (placeholder atau field yang memakai accessor akan ambil nilai baru)
        $this->form->fill($this->record->toArray());
    }
}
