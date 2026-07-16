<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourtResource\Pages;
use App\Filament\Resources\CourtResource\RelationManagers;
use App\Models\Court;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourtResource extends Resource
{
    protected static ?string $model = Court::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\Select::make('sport_type')
                            ->options([
                                'futsal' => 'Futsal',
                                'mini_soccer' => 'Mini Soccer',
                                'badminton' => 'Badminton',
                                'tenis' => 'Tenis',
                                'padel' => 'Padel',
                                'basket' => 'Basket',
                                'billiard' => 'Billiard',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('city')
                            ->required(),
                        Forms\Components\TextInput::make('location')
                            ->required(),
                        Forms\Components\Textarea::make('address')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Harga & Jadwal')
                    ->schema([
                        Forms\Components\TextInput::make('price_per_hour')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TimePicker::make('open_time')
                            ->required(),
                        Forms\Components\TimePicker::make('close_time')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Fasilitas & Detail Lapangan')
                    ->schema([
                        Forms\Components\TagsInput::make('facilities')
                            ->placeholder('Tambah fasilitas...')
                            ->columnSpanFull(),
                        Forms\Components\TagsInput::make('courts_detail')
                            ->placeholder('Nama lapangan kecil (contoh: Lapangan A)')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('images')
                            ->multiple()
                            ->image()
                            ->directory('courts')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->getStateUsing(function (Court $record) {
                        $images = $record->getAttribute('images');
                        return is_array($images) && count($images) > 0 ? $images[0] : null;
                    })
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sport_type')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_per_hour')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListCourts::route('/'),
            'create' => Pages\CreateCourt::route('/create'),
            'edit' => Pages\EditCourt::route('/{record}/edit'),
        ];
    }
}
