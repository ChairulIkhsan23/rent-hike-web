<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalResource\Pages;
use App\Models\Rental;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

// Forms Component
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Card;

// Tables Component
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ViewColumn;


class RentalResource extends Resource
{
    protected static ?string $model = Rental::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Rental';
    protected static ?string $modelLabel = 'Rental';
        protected static ?string $navigationGroup = 'Rental';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                Select::make('user_id')
                    ->label('Penyewa')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai Sewa')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (callable $set, Forms\Get $get) {
                        static::updateAllPrices($set, $get);
                    }),

                DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai Sewa')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (callable $set, Forms\Get $get) {
                        static::updateAllPrices($set, $get);
                    })
                    ->rule('after_or_equal:tanggal_mulai'),

                Repeater::make('rentalItems')
                    ->label('Barang yang Disewa')
                    ->relationship()
                    ->live()
                    ->schema([
                        Select::make('product_id')
                            ->label('Produk')
                            ->relationship('product', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, Forms\Get $get) {
                                static::updateItemPrice($set, $get);
                            }),

                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->live()
                            ->afterStateUpdated(function (callable $set, Forms\Get $get) {
                                static::updateItemPrice($set, $get);
                            }),

                        TextInput::make('harga_total')
                            ->label('Harga Total (Rp)')
                            ->disabled()
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3)
                    ->itemLabel(fn (array $state): ?string => isset($state['product_id']) ? \App\Models\Product::find($state['product_id'])?->nama : null)
                    ->afterStateUpdated(function (callable $set, Forms\Get $get) {
                        $set('total_harga', static::calculateTotalPrice($get('rentalItems') ?? []));
                    })
                    ->addActionLabel('Tambah Item')
                    ->collapsible()
                    ->cloneable()
                    ->defaultItems(1),

                TextInput::make('total_harga')
                    ->label('Total Harga (Rp)')
                    ->disabled()
                    ->numeric()
                    ->default(0),

                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'dibayar' => 'Dibayar',
                        'dikirim' => 'Dikirim',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->default('pending')
                    ->required(),
            ])
        ]);
    }

    public static function updateItemPrice(callable $set, Forms\Get $get): void
    {
        $productId = $get('product_id');
        $jumlah = (int) $get('jumlah') ?: 1;
        $product = \App\Models\Product::find($productId);

        if (!$product) {
            $set('harga_total', 0);
            return;
        }

        $mulai = $get('../../tanggal_mulai');
        $selesai = $get('../../tanggal_selesai');

        $durasi = 1;
        if ($mulai && $selesai) {
            try {
                $mulaiDate = \Illuminate\Support\Carbon::parse($mulai);
                $selesaiDate = \Illuminate\Support\Carbon::parse($selesai);
                $durasi = max($mulaiDate->diffInDays($selesaiDate) + 1, 1); // Tambah 1 agar hari pertama dihitung
            } catch (\Exception $e) {
                $durasi = 1;
            }
        }

        $harga = $product->harga_sewa_per_hari * $jumlah * $durasi;
        $set('harga_total', $harga);
        $set('../../total_harga', static::calculateTotalPrice($get('../../rentalItems') ?? []));
    }

    public static function updateAllPrices(callable $set, Forms\Get $get): void
    {
        $items = $get('rentalItems') ?? [];

        foreach ($items as $key => $item) {
            $productId = $item['product_id'] ?? null;
            $jumlah = (int) ($item['jumlah'] ?? 1);

            if ($productId) {
                $product = \App\Models\Product::find($productId);
                $mulai = $get('tanggal_mulai');
                $selesai = $get('tanggal_selesai');

                $durasi = 1;
                if ($mulai && $selesai) {
                    try {
                        $mulaiDate = \Illuminate\Support\Carbon::parse($mulai);
                        $selesaiDate = \Illuminate\Support\Carbon::parse($selesai);
                        $durasi = max($mulaiDate->diffInDays($selesaiDate) + 1, 1); // Tambah 1 agar hari pertama dihitung
                    } catch (\Exception $e) {
                        $durasi = 1;
                    }
                }

                if ($product) {
                    $harga = $product->harga_sewa_per_hari * $jumlah * $durasi;
                    $set("rentalItems.{$key}.harga_total", $harga);
                }
            }
        }

        $set('total_harga', static::calculateTotalPrice($get('rentalItems') ?? []));
    }

    public static function calculateTotalPrice(array $items): int
    {
        return collect($items)->sum(fn ($item) => (int) ($item['harga_total'] ?? 0));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Penyewa')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),
                
                TextColumn::make('tanggal_selesai')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->sortable(),
                
                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->money('IDR')
                    ->sortable(),
                
               TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'dibayar' => 'success',
                        'dikirim' => 'info',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'dibayar' => 'Dibayar',
                        'dikirim' => 'Dikirim',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        default => ucfirst($state),
                    })
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'dibayar' => 'Dibayar',
                        'dikirim' => 'Dikirim',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
                
                Tables\Filters\Filter::make('tanggal_mulai')
                    ->form([
                        DatePicker::make('dari_tanggal'),
                        DatePicker::make('sampai_tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari_tanggal'],
                                fn ($query) => $query->whereDate('tanggal_mulai', '>=', $data['dari_tanggal'])
                            )
                            ->when($data['sampai_tanggal'],
                                fn ($query) => $query->whereDate('tanggal_mulai', '<=', $data['sampai_tanggal'])
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markAsPaid')
                        ->label('Tandai sebagai Dibayar')
                        ->action(function ($records) {
                            $records->each->update(['status' => 'dibayar']);
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relations if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentals::route('/'),
            'create' => Pages\CreateRental::route('/create'),
            'edit' => Pages\EditRental::route('/{record}/edit'),
            'view' => Pages\ViewRental::route('/{record}'),
        ];
    }
}