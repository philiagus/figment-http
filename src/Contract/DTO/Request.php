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

interface Request {

    public function getProtocol(): string;
    public function getMethod(): string;
    public function getPath(): string;

    public function getPost(): array;
    public function getGet(): array;
    public function getQuery(): string;
    public function getBody(): string;
    public function getHeaders(): Headers;
    public function getFiles(): Files;
    public function getCookies(): RequestCookies;
    public function getTime(): \DateTimeImmutable;

    public function response(
        int $statusCode = 200,
        string $body = '',
        ?string $statusCodeDescription = null
    ): Response;

}
