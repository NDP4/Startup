<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;  // Add this import
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Reviews';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Review')->schema([
                Forms\Components\Select::make('booking_id')
                    ->relationship('booking', 'id')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('bus_id')
                    ->relationship('bus', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('bus_rating')
                    ->label('Bus Rating')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->suffix('⭐'),

                Forms\Components\Textarea::make('bus_comment')
                    ->label('Bus Review')
                    ->required(),

                Forms\Components\Select::make('crew_id')
                    ->relationship('crew', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('crew_rating')
                    ->label('Crew Rating')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->suffix('⭐')
                    ->required(fn(Get $get): bool => filled($get('crew_id'))),

                Forms\Components\Textarea::make('crew_comment')
                    ->label('Crew Review')
                    ->required(fn(Get $get): bool => filled($get('crew_id'))),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();

        return $table
            ->columns(match ($user->role) {
                'admin' => self::getAdminColumns(),
                'crew' => self::getCrewColumns(),
                'customer' => self::getCustomerColumns(),
                default => []
            })
            // ->filters([
            //     Tables\Filters\SelectFilter::make('rating')
            //         ->options([
            //             '5' => '★★★★★ (5)',
            //             '4' => '★★★★ (4)',
            //             '3' => '★★★ (3)',
            //             '2' => '★★ (2)',
            //             '1' => '★ (1)',
            //         ])
            //         ->label(fn() => Auth::user()->role === 'crew' ? 'Rating Crew' : 'Rating Bus'),
            // ])
            ->actions(self::getTableActions())
            ->bulkActions([]);
    }

    protected static function getAdminColumns(): array
    {
        return [
            Split::make([
                Stack::make([
                    Tables\Columns\TextColumn::make('booking.customer.name')
                        ->label('Customer')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('booking.booking_date')
                        ->label('Tanggal Booking')
                        ->date()
                        ->sortable(),
                ]),
                Stack::make([
                    Tables\Columns\TextColumn::make('bus.name')
                        ->label('Bus')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('bus_rating')
                        ->label('Rating Bus')
                        ->formatStateUsing(fn($state) => str_repeat('★', $state))
                        ->color('warning'),
                    Tables\Columns\TextColumn::make('bus_comment')
                        ->label('Ulasan Bus')
                        ->limit(50),
                ]),
                Stack::make([
                    Tables\Columns\TextColumn::make('crew.name')
                        ->label('Crew')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('crew_rating')
                        ->label('Rating Crew')
                        ->formatStateUsing(fn($state) => $state ? str_repeat('★', $state) : '-')
                        ->color('warning'),
                    Tables\Columns\TextColumn::make('crew_comment')
                        ->label('Ulasan Crew')
                        ->limit(50),
                ]),
            ]),
        ];
    }

    protected static function getCrewColumns(): array
    {
        return [
            Split::make([
                Stack::make([
                    Tables\Columns\TextColumn::make('booking.customer.name')
                        ->label('Customer')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('booking.booking_date')
                        ->label('Tanggal Booking')
                        ->date(),
                ]),
                Stack::make([
                    Tables\Columns\TextColumn::make('crew_rating')
                        ->label('Rating Anda')
                        ->formatStateUsing(fn($state) => str_repeat('★', $state))
                        ->color('warning')
                        ->size('lg'),
                    Tables\Columns\TextColumn::make('crew_comment')
                        ->label('Ulasan untuk Anda')
                        ->words(30),
                ]),
                Stack::make([
                    Tables\Columns\TextColumn::make('booking.pickup_location')
                        ->label('Dari'),
                    Tables\Columns\TextColumn::make('booking.destination')
                        ->label('Ke'),
                ]),
            ]),
        ];
    }

    protected static function getCustomerColumns(): array
    {
        return [
            Split::make([
                Stack::make([
                    Tables\Columns\TextColumn::make('bus.name')
                        ->label('Bus')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('booking.booking_date')
                        ->label('Tanggal Booking')
                        ->date(),
                ]),
                Stack::make([
                    Tables\Columns\TextColumn::make('bus_rating')
                        ->label('Rating Bus')
                        ->formatStateUsing(fn($state) => str_repeat('★', $state))
                        ->color('warning')
                        ->size('lg'),
                    Tables\Columns\TextColumn::make('bus_comment')
                        ->label('Ulasan Bus')
                        ->words(30),
                ]),
                Stack::make([
                    Tables\Columns\TextColumn::make('crew.name')
                        ->label('Crew')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('crew_rating')
                        ->label('Rating Crew')
                        ->formatStateUsing(fn($state) => $state ? str_repeat('★', $state) : '-')
                        ->color('warning'),
                ]),
            ]),
        ];
    }

    protected static function getTableActions(): array
    {
        $user = Auth::user();

        $actions = [
            Tables\Actions\ViewAction::make()
                ->form([
                    Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\Placeholder::make('booking')
                                ->content(
                                    fn(Review $record): string =>
                                    "Booking #{$record->booking->id} - {$record->booking->pickup_location} → {$record->booking->destination}"
                                ),
                            Forms\Components\Placeholder::make('customer')
                                ->content(fn(Review $record): string => $record->booking->customer->name),
                            Forms\Components\Placeholder::make('bus_rating')
                                ->label('Rating Bus')
                                ->content(fn(Review $record): string => str_repeat('★', $record->bus_rating)),
                            Forms\Components\Placeholder::make('bus_comment')
                                ->label('Ulasan Bus')
                                ->content(fn(Review $record): string => $record->bus_comment),
                            Forms\Components\Placeholder::make('crew_rating')
                                ->label('Rating Crew')
                                ->content(
                                    fn(Review $record): string =>
                                    $record->crew_rating ? str_repeat('★', $record->crew_rating) : '-'
                                )
                                ->visible(fn(Review $record): bool => (bool)$record->crew_id),
                            Forms\Components\Placeholder::make('crew_comment')
                                ->label('Ulasan Crew')
                                ->content(fn(Review $record): string => $record->crew_comment ?? '-')
                                ->visible(fn(Review $record): bool => (bool)$record->crew_id),
                            Forms\Components\Placeholder::make('created_at')
                                ->label('Dibuat pada')
                                ->content(
                                    fn(Review $record): string =>
                                    $record->created_at->format('d F Y H:i')
                                ),
                        ])
                        ->columns(2),
                ])
                ->modalWidth('xl'),
        ];

        if ($user->role === 'admin') {
            $actions[] = Tables\Actions\DeleteAction::make();
        }

        return $actions;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
