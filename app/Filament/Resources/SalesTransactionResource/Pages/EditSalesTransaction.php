<?php

namespace App\Filament\Resources\SalesTransactionResource\Pages;

use App\Filament\Resources\SalesTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalesTransaction extends EditRecord
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
