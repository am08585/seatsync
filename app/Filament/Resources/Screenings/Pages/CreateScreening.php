<?php

namespace App\Filament\Resources\Screenings\Pages;

use App\Filament\Resources\Screenings\ScreeningResource;
use Filament\Resources\Pages\CreateRecord;

class CreateScreening extends CreateRecord
{
    protected static string $resource = ScreeningResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['base_price'] = $data['base_price'] * 100;

        return $data;
    }
}
