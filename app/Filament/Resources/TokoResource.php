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
                Forms\Components\TextInput::make('latitude')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\TextInput::make('longitude')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\Select::make('sales_id')
                    ->relationship('sales', 'name')
                    ->required(),
                // Generate barcode automatically
                Forms\Components\Hidden::make('barcode')
                    ->default(fn() => \App\Models\Toko::generateBarcode()),
                Forms\Components\FileUpload::make('photo')
                    ->image()
                    ->imageEditor()
                    ->imageCropAspectRatio('16:9')
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('1080')
                    ->directory('toko_photos')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->helperText('Upload foto toko (maksimal 2MB, format: JPG, PNG, GIF)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sales_transactions_sum_total_amount', 'desc') // default urut berdasarkan total penjualan tertinggi
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('address'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('wilayah.name')->label('Wilayah'),
                Tables\Columns\TextColumn::make('sales.name')->label('Sales'),
                Tables\Columns\TextColumn::make('sales_transactions_sum_total_amount') // kolom agregat total penjualan
                    ->label('Total Penjualan')
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state === null ? '0' : number_format($state, 0, ',', '.')),
                // Tables\Columns\TextColumn::make('barcode'),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto Toko')
                    ->circular()
                    ->size(40),
                Tables\Columns\ViewColumn::make('barcode_image')->label('Barcode')->view('filament.toko.barcode'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print_barcode')
                    ->label('Cetak Barcode')
                    ->icon('heroicon-o-printer')
                    ->url(fn(Toko $record): string => route('toko.print-barcode', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('print_barcodes')
                        ->label('Cetak Barcode Terpilih')
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $ids = $records->pluck('id')->join(',');
                            // redirect ke halaman bulk print
                            return redirect(route('toko.print-barcodes-bulk', ['ids' => $ids]));
                        })
                        ->requiresConfirmation(false),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withSum('salesTransactions', 'total_amount') // agregasi total penjualan per toko
            ->orderByDesc('sales_transactions_sum_total_amount');
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
