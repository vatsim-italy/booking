<?php

namespace App\View\FormComponents\Components;

class FormLabel extends Component
{
    public string $label;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $label = '')
    {
        $this->label = $label;
    }
}
