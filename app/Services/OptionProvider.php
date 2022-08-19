<?php

namespace App\Services;

use App\Entities\Option;
use Illuminate\Database\Eloquent\Collection;

class OptionProvider
{
    /** @var Collection|Option[] */
    private $options;

    private function loadOptions(): array|Collection
    {
        if (is_null($this->options)) {
            $this->options = Option::all();
        }

        return $this->options;
    }

    public function getOption($name, $default = null)
    {
        $options = $this->loadOptions();
        foreach ($options as $option) {
            if ($option->key === $name && $option->value) {
                return $option->value;
            }
        }

        return $default;
    }
}
