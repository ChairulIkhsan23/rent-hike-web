<?php

namespace App\Filament\Resources\RentalResource\Pages;

use App\Filament\Resources\RentalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentals extends ListRecords
{
    protected static string $resource = RentalResource::class;

    protected static ?string $title = 'Daftar Rental';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Rental Baru')
            ->icon('heroicon-o-folder-plus'),
        ];
    }
}
