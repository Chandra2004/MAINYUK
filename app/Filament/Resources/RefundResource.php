<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RefundResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RefundResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?string $navigationLabel = 'Refunds';
    protected static ?string $modelLabel = 'Refund';
    protected static ?string $pluralModelLabel = 'Refunds';
    protected static ?string $slug = 'refunds';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('refund_status', '!=', 'none');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('booking_code')
                    ->disabled(),
                Forms\Components\Select::make('refund_status')
                    ->options([
                        'requested' => 'Requested',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('refund_account')
                    ->label('Bank Account Info')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Refund Amount')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('refund_account')
                    ->label('Account Info'),
                Tables\Columns\SelectColumn::make('refund_status')
                    ->options([
                        'requested' => 'Requested',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListRefunds::route('/'),
            'edit' => Pages\EditRefund::route('/{record}/edit'),
        ];
    }
}
