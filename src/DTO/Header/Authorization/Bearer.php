<?php
declare(strict_types=1);

namespace Philiagus\Figment\Http\DTO\Header\Authorization;

use Philiagus\Figment\Http\DTO\Header\Authorization;

class Bearer extends Authorization
{
    public string $tokenRaw {
        get => $this->parameters;
    }
}
