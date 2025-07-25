<?php

namespace App\Filament\Resources\SalesResource\Pages;

use App\Filament\Resources\SalesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;

class EditSales extends EditRecord
{
    protected static string $resource = SalesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $sales = $this->record;
        $user = User::where('email', $sales->getOriginal('email'))->first();
        if ($user) {
            $user->update([
                'name' => $sales->name,
                'email' => $sales->email,
                'password' => $sales->password, // already hashed
            ]);
        } else {
            // If user not found (email changed or missing), create new
            User::create([
                'name' => $sales->name,
                'email' => $sales->email,
                'password' => $sales->password,
            ]);
        }
    }

    protected function afterDelete(): void
    {
        $sales = $this->record;
        $user = \App\Models\User::where('email', $sales->email)->first();
        if ($user) {
            $user->delete();
        }
    }
}
