<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected static ?string $title = 'Daftar Kategori';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Kategori Baru')
            ->icon('heroicon-o-folder-plus'),
        ];
    }

}
