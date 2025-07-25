<?php

namespace App\Filament\Resources\SalesResource\Pages;

use App\Filament\Resources\SalesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;

class CreateSales extends CreateRecord
{
    protected static string $resource = SalesResource::class;

    protected function afterCreate(): void
    {
        $sales = $this->record;
        User::create([
            'name' => $sales->name,
            'email' => $sales->email,
            'password' => $sales->password, // already hashed
        ]);
    }
}
