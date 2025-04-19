<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class AudioRecorderHelper extends Field
{
    protected string $view = 'components.audio-recorder-helper';
    protected string | \Closure | null $uploadComponentId = null;

    public function uploadComponentId(string | \Closure | null $id): static
    {
        $this->uploadComponentId = $id;

        return $this;
    }

    public function getUploadComponentId(): ?string
    {
        return $this->evaluate($this->uploadComponentId);
    }
}
