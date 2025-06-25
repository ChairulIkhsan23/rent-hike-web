<?php

namespace App\Filament\Resources\RentalResource\Pages;

use App\Filament\Resources\RentalResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;

class EditRental extends EditRecord
{
    protected static string $resource = RentalResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Calculate total price from all items
        $data['total_harga'] = collect($data['rentalItems'] ?? [])->sum('harga_total');
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Update rental items with calculated prices
        foreach ($this->record->rentalItems as $item) {
            try {
                $mulaiDate = Carbon::parse($this->record->tanggal_mulai);
                $selesaiDate = Carbon::parse($this->record->tanggal_selesai);
                $durasi = max($mulaiDate->diffInDays($selesaiDate), 1);
            } catch (\Exception $e) {
                $durasi = 1;
            }
            
            $hargaTotal = $item->product->harga_sewa_per_hari * $item->jumlah * $durasi;
            
            $item->update([
                'harga_total' => $hargaTotal,
            ]);
        }

        // Update the total price in rental record
        $this->record->update([
            'total_harga' => $this->record->rentalItems()->sum('harga_total'),
        ]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Rental berhasil diperbarui';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}