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

readonly class ResponseCookie implements Contract\DTO\ResponseCookie
{

    public function __construct(
        private string $name,
        private string $value,
        private ?int    $expiresAt = null,
        private string $path = '',
        private string $domain = '',
        private bool   $secure = false,
        private bool   $httpOnly = true,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getExpiresAt(): ?int
    {
        return $this->expiresAt;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function isSecure(): bool
    {
        return $this->secure;
    }

    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    public function send(): void
    {
        setcookie(
            $this->name, $this->value, $this->expiresAt,
            $this->path, $this->domain,
            $this->secure, $this->httpOnly
        );
    }
}
