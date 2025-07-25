<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TokoResource\Pages;
use App\Filament\Resources\TokoResource\RelationManagers;
use App\Models\Toko;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TokoResource extends Resource
{
    protected static ?string $model = Toko::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('address')->required(),
                Forms\Components\TextInput::make('phone'),
                Forms\Components\Select::make('wilayah_id')
                    ->relationship('wilayah', 'name')
                    ->required(),
                Forms\Components\TextInput::make('barcode')
                    ->readOnly()
                    ->default(fn() => uniqid('TKO')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('address'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('wilayah.name')->label('Wilayah'),
                Tables\Columns\TextColumn::make('barcode'),
                Tables\Columns\ViewColumn::make('barcode_image')->label('Barcode')->view('filament.toko.barcode'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTokos::route('/'),
            'create' => Pages\CreateToko::route('/create'),
            'edit' => Pages\EditToko::route('/{record}/edit'),
        ];
    }
}
