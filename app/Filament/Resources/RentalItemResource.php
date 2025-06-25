<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalItemResource\Pages;
use App\Models\RentalItem;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class RentalItemResource extends Resource
{
    protected static ?string $model = RentalItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Item Rental Aktif';

    protected static ?string $navigationGroup = 'Rental';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.nama')
                    ->label('Produk')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->sortable(),

                TextColumn::make('rental.user.name')
                    ->label('Penyewa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rental.tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable(),

                TextColumn::make('rental.tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->date()
                    ->sortable(),

                TextColumn::make('harga_total')
                    ->label('Harga Total')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Nanti bisa ditambahkan filter aktif/sudah selesai
            ])
            ->actions([]) // readonly
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentalItems::route('/'),
        ];
    }
}
