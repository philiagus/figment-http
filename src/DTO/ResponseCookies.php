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
use Traversable;

readonly class ResponseCookies implements Contract\DTO\ResponseCookies, \IteratorAggregate
{

    private array $cookies;

    public function __construct(
        \Philiagus\Figment\Http\Contract\DTO\ResponseCookie ...$cookies
    )
    {
        $cleanCookies = [];
        foreach($cookies as $cookie) {
            $cleanCookies[$cookie->getName()] = $cookie;
        }
        $this->cookies = $cleanCookies;
    }

    public function send(): void
    {
        foreach ($this->cookies as $cookie)
            $cookie->send();
    }

    public function merge(array|Contract\DTO\ResponseCookies $cookies): Contract\DTO\ResponseCookies
    {
        $newCookies = $cookies instanceof Contract\DTO\ResponseCookies ? $cookies->all() : $cookies;
        $newCookies = $newCookies + $this->cookies;
        return new self(...$this->cookies, ...$newCookies);
    }

    public function all(): array
    {
        return $this->cookies;
    }

    public function getIterator(): Traversable
    {
        yield from $this->cookies;
    }
}
