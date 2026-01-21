<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentReservations extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Reservation::with(['user', 'screening', 'screening.movie'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('Reservation #')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('screening.movie.title')
                    ->label('Movie')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('USD', 100)
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'pending',
                        'success' => 'confirmed',
                    ]),

                TextColumn::make('created_at')
                    ->label('Booked')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->paginated([5, 10, 25])
            ->striped();
    }
}
