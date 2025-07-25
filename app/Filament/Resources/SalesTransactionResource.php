<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesTransactionResource\Pages;
use App\Filament\Resources\SalesTransactionResource\RelationManagers;
use App\Models\SalesTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesTransactionResource extends Resource
{
    protected static ?string $model = SalesTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sales_id')
                    ->relationship('sales', 'name')
                    ->required(),
                Forms\Components\Select::make('toko_id')
                    ->relationship('toko', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('transaction_date')->required(),
                Forms\Components\TextInput::make('total_amount')->numeric()->required(),
                Forms\Components\Textarea::make('notes'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sales.name')->label('Sales'),
                Tables\Columns\TextColumn::make('toko.name')->label('Toko'),
                Tables\Columns\TextColumn::make('transaction_date')->date(),
                Tables\Columns\TextColumn::make('total_amount')->money('IDR'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SalesTransactionDetailRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalesTransactions::route('/'),
            'create' => Pages\CreateSalesTransaction::route('/create'),
            'edit' => Pages\EditSalesTransaction::route('/{record}/edit'),
        ];
    }
}
