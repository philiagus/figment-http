<?php
/*
 * This file is part of philiagus/figment-http
 *
 * (c) Andreas Eicher <philiagus@philiagus.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Philiagus\Figment\Http\DTO;

use Philiagus\Figment\Http\Contract;

readonly class RequestCookies implements Contract\DTO\RequestCookies
{
    private array $cookies;

    public function __construct(array $cookies)
    {
        foreach ($cookies as $name => $value)
            if (!is_string($value))
                throw new \LogicException("Cookies must be an array<string, string>, non-string provided for '$name'");
        $this->cookies = $cookies;
    }

    public static function fromGlobal(array $cookies): self
    {
        return new self($cookies);
    }

    public function has(string $name): bool
    {
        return isset($this->cookies[$name]);
    }

    public function get(string $name): string
    {
        return $this->cookies[$name] ?? throw new \OutOfBoundsException(
            "Cookie '$name' is not defined"
        );
    }
}
