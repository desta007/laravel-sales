<?php

namespace App\Filament\Resources\SalesTransactionResource\RelationManagers;

use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesTransactionDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'details';
    protected static ?string $recordTitleAttribute = 'barang_id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('barang_id')
                    ->label('Barang')
                    ->relationship('barang', 'name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (?int $state, callable $set, callable $get) {
                        if ($state) {
                            $barang = Barang::find($state);
                            $price = $barang?->price ?? 0;
                            $set('price', $price);

                            $quantity = $get('quantity') ?? 0;
                            $set('subtotal', $price * $quantity);
                        } else {
                            $set('price', 0);
                            $set('subtotal', 0);
                        }
                    }),
                Forms\Components\TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->required()
                    ->disabled()
                    ->default(0),
                Forms\Components\TextInput::make('quantity')
                    ->label('Kuantitas')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $price = $get('price') ?? 0;
                        $set('subtotal', $price * $state);
                    }),
                Forms\Components\TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->required()
                    ->disabled()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barang.name')->label('Barang'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('price')->money('IDR'),
                Tables\Columns\TextColumn::make('subtotal')->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
