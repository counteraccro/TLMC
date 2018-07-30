<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class BooleanExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('bool', array($this, 'getOuiNon')),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [$this, 'doSomething']),
        ];
    }

    public function getOuiNon($value)
    {
        return ($value == 1 ? 'Oui' : 'Non');
    }
}
