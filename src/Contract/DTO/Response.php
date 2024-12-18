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

interface Response
{
    public function getRequest(): Request;

    public function getStatusCode(): int;

    public function getStatusDescription(): string;

    public function getCookies(): ResponseCookies;

    public function getHeaders(): Headers;

    public function getBody(): string;

    public function send(): self;
}
