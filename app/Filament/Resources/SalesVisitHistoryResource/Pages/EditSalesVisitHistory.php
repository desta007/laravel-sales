<?php

namespace App\Filament\Resources\SalesVisitHistoryResource\Pages;

use App\Filament\Resources\SalesVisitHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalesVisitHistory extends EditRecord
{
    protected static string $resource = SalesVisitHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
