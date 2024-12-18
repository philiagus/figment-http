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

namespace Philiagus\Figment\Http\Contract\DTO;

/**
 * @implements \Traversable<string, ResponseCookie>
 */
interface ResponseCookies extends \Traversable
{

    public function send(): void;

    public function merge(array|ResponseCookies $cookies): ResponseCookies;

    /**
     * @return ResponseCookie[]
     */
    public function all(): array;
}
