<?php

namespace App\Filament\Resources\Screenings;

use App\Filament\Resources\Screenings\Pages\CreateScreening;
use App\Filament\Resources\Screenings\Pages\EditScreening;
use App\Filament\Resources\Screenings\Pages\ListScreenings;
use App\Filament\Resources\Screenings\Pages\ViewScreening;
use App\Filament\Resources\Screenings\Schemas\ScreeningForm;
use App\Filament\Resources\Screenings\Schemas\ScreeningInfolist;
use App\Filament\Resources\Screenings\Tables\ScreeningsTable;
use App\Models\Screening;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScreeningResource extends Resource
{
    protected static ?string $model = Screening::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::VideoCamera;

    public static function form(Schema $schema): Schema
    {
        return ScreeningForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScreeningInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScreeningsTable::configure($table);
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
            'index' => ListScreenings::route('/'),
            'create' => CreateScreening::route('/create'),
            'view' => ViewScreening::route('/{record}'),
            'edit' => EditScreening::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
