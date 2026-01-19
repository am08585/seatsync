<?php

namespace App\Filament\Resources\Screenings\Schemas;

use App\Models\Screening;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ScreeningInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('movie.title')
                    ->label('Movie'),
                TextEntry::make('theater.name')
                    ->label('Theater'),
                TextEntry::make('start_time')
                    ->dateTime()
                    ->label('Start Time'),
                TextEntry::make('end_time')
                    ->dateTime()
                    ->label('End Time'),
                TextEntry::make('base_price')
                    ->money(divideBy: 100)
                    ->label('Base Price'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Screening $record): bool => $record->trashed())
                    ->label('Deleted At'),
            ]);
    }
}
