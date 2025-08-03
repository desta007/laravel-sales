<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesVisitHistoryResource\Pages;
use App\Filament\Resources\SalesVisitHistoryResource\RelationManagers;
use App\Models\SalesVisitHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesVisitHistoryResource extends Resource
{
    protected static ?string $model = SalesVisitHistory::class;

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
                Forms\Components\DatePicker::make('visit_date')->required(),
                Forms\Components\Textarea::make('notes'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sales.name')->label('Sales'),
                Tables\Columns\TextColumn::make('toko.name')->label('Toko'),
                Tables\Columns\TextColumn::make('visit_date')->date(),
                Tables\Columns\TextColumn::make('latitude')
                    ->label('Latitude')
                    ->url(fn(SalesVisitHistory $record): ?string => ($record->latitude && $record->longitude)
                        ? "https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longitude}"
                        : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('longitude')
                    ->label('Longitude')
                    ->url(fn(SalesVisitHistory $record): ?string => ($record->latitude && $record->longitude)
                        ? "https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longitude}"
                        : null)
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('notes')->limit(30),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            // nonaktifkan click/redirect saat row disorot
            ->recordUrl(fn(?SalesVisitHistory $record): ?string => null)
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalesVisitHistories::route('/'),
            'create' => Pages\CreateSalesVisitHistory::route('/create'),
            'edit' => Pages\EditSalesVisitHistory::route('/{record}/edit'),
        ];
    }
}
