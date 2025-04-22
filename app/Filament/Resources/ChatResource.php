<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatResource\Pages;
use App\Models\Message;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Filament\Infolists\Components\TextEntry;

class ChatResource extends Resource
{
    protected static ?string $model = Message::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Customer Support';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Chat';
    protected static ?string $pluralModelLabel = 'Chat';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Pesan')
                ->schema([
                    Forms\Components\Select::make('receiver_id')
                        ->label('Kirim ke')
                        ->options(fn() => User::where('role', 'customer')->pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                    Forms\Components\Textarea::make('message')
                        ->label('Pesan')
                        ->required()
                        ->maxLength(1000),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('sender.name')
                        ->label('Pengirim')
                        ->searchable()
                        ->sortable()
                        ->weight('bold'),
                    Tables\Columns\TextColumn::make('message')
                        ->label('Pesan')
                        ->limit(50)
                        ->searchable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->label('Dikirim')
                        ->dateTime()
                        ->sortable(),
                    Tables\Columns\IconColumn::make('read_at')
                        ->label('Status')
                        ->boolean()
                        ->trueIcon('heroicon-o-check-circle')
                        ->falseIcon('heroicon-o-clock')
                        ->trueColor('success')
                        ->falseColor('warning'),
                ])->space(2),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label('Customer')
                    ->options(fn() => User::where('role', 'customer')->pluck('name', 'id'))
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            return $query->where(function ($q) use ($data) {
                                $q->where('sender_id', $data['value'])
                                    ->orWhere('receiver_id', $data['value']);
                            });
                        }
                    }),
            ])
            ->actions([
                Action::make('reply')
                    ->label('Balas')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->form([
                        Forms\Components\Textarea::make('message')
                            ->label('Pesan')
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->action(function (Message $record, array $data): void {
                        Message::create([
                            'sender_id' => Auth::id(),
                            'receiver_id' => $record->sender_id,
                            'message' => $data['message'],
                        ]);
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChats::route('/'),
            'create' => Pages\CreateChat::route('/create'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where(function ($query) {
                $query->where('sender_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            });
    }
}
