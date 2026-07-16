<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RescheduleRequestResource\Pages;
use App\Filament\Resources\RescheduleRequestResource\RelationManagers;
use App\Models\RescheduleRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RescheduleRequestResource extends Resource
{
    protected static ?string $model = RescheduleRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('booking_id')
                    ->relationship('booking', 'booking_code')
                    ->required(),
                Forms\Components\DatePicker::make('requested_date')
                    ->required(),
                Forms\Components\Textarea::make('reason')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking.booking_code')
                    ->label('Booking Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('requested_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (RescheduleRequest $record) => $record->status === 'pending')
                    ->action(function (RescheduleRequest $record) {
                        $record->update(['status' => 'approved']);
                        // Update booking date
                        $record->booking->update(['date' => $record->requested_date]);
                    }),
                Tables\Actions\Action::make('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (RescheduleRequest $record) => $record->status === 'pending')
                    ->action(fn (RescheduleRequest $record) => $record->update(['status' => 'rejected'])),
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
            'index' => Pages\ListRescheduleRequests::route('/'),
            'create' => Pages\CreateRescheduleRequest::route('/create'),
            'edit' => Pages\EditRescheduleRequest::route('/{record}/edit'),
        ];
    }
}
