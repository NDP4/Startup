<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class Rating extends Field
{
    protected string $view = 'forms.components.rating';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(0);

        $this->rules([
            'required',
            'integer',
            'min:1',
            'max:5'
        ]);

        $this->dehydrateStateUsing(function (?string $state): ?int {
            return $state ? (int) $state : null;
        });
    }
}
