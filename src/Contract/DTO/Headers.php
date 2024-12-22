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

interface Headers
{

    public function has(string $name): bool;

    public function getRaw(string $name): string;

    public function empty(): bool;

    public function merge(array|Headers $headers): Headers;

    /**
     * @return array<string, string>
     */
    public function all(): array;

}
