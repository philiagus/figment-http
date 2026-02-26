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
use Philiagus\Figment\Http\DTO\Header\Authorization;

class HeaderFactory
{

    public static function create(string $name, string $raw): Contract\DTO\Header
    {
        return match (strtolower($name)) {
            'authorization' => Authorization::infer($name, $raw),
            'if-unmodified-since', 'if-modified-since' => new Header\DateTime($name, $raw),

            default => new Header\General($name, $raw)
        };
    }
}
