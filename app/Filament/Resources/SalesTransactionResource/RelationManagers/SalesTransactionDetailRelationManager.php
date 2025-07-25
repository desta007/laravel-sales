<?php

namespace App\Filament\Resources\SalesTransactionResource\RelationManagers;

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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('barang_id')
                    ->relationship('barang', 'name')
                    ->required(),
                Forms\Components\TextInput::make('quantity')->numeric()->required(),
                Forms\Components\TextInput::make('price')->numeric()->required(),
                Forms\Components\TextInput::make('subtotal')->numeric()->required(),
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
