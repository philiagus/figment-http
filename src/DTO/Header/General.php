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

namespace Philiagus\Figment\Http\DTO\Header;

use Philiagus\Figment\Http\Contract\DTO\Header;

readonly class General implements Header
{

    /**
     * @param string $name
     * @param string $raw
     */
    public function __construct(public string $name, public string $raw)
    {
    }
}
