<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalItemResource\Pages;
use App\Filament\Resources\RentalItemResource\RelationManagers;
use App\Models\RentalItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentalItemResource extends Resource
{
    protected static ?string $model = RentalItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListRentalItems::route('/'),
            'create' => Pages\CreateRentalItem::route('/create'),
            'edit' => Pages\EditRentalItem::route('/{record}/edit'),
        ];
    }
}
