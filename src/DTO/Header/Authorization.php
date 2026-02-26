<?php
declare(strict_types=1);

namespace Philiagus\Figment\Http\DTO\Header;

use Philiagus\Figment\Http\Contract\DTO\Header;
use SensitiveParameter;

class Authorization implements Header
{
    public function __construct(
        public readonly string $name,
        #[SensitiveParameter] public readonly string $raw,
        public readonly string $scheme,
        #[SensitiveParameter] public readonly string $parameters,
    )
    {
    }

    public static function infer(string $name, #[SensitiveParameter] string $raw): Header
    {
        $parts = preg_split('~\s+~', $raw, 2);
        if (!isset($parts[1])) {
            return new General($name, $raw);
        }
        [$scheme, $parameters] = $parts;

        return match (strtolower($scheme)) {
            'basic' => new Authorization\Basic($name, $raw, $scheme, $parameters),
            'bearer' => new Authorization\Bearer($name, $raw, $scheme, $parameters),
            default => new self($name, $raw, $scheme, $parameters)
        };
    }
}
