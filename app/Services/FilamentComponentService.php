<?php

namespace App\Services;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class FilamentComponentService
{
    public static function getMediaComponents($mediaCollection)
    {
        return SpatieMediaLibraryFileUpload::make('image')
            ->collection($mediaCollection)
            ->multiple()
            ->imagePreviewHeight('150')
            ->maxSize(2000)
            ->hiddenLabel()
            ->extraAttributes(['style' => 'max-height:450px; overflow-y:auto;']);
    }
}
