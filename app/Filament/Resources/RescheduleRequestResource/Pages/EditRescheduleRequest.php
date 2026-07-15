<?php

namespace App\Filament\Resources\RescheduleRequestResource\Pages;

use App\Filament\Resources\RescheduleRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRescheduleRequest extends EditRecord
{
    protected static string $resource = RescheduleRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
