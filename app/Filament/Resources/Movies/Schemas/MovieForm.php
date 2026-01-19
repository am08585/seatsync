<?php

namespace App\Filament\Resources\Movies\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MovieForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('runtime')
                    ->numeric(),
                FileUpload::make('poster_path')
                    ->disk('public')
                    ->directory('movies/posters')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatioOptions([
                        '3:4',
                        '2:3',
                    ]),
            ]);
    }
}
