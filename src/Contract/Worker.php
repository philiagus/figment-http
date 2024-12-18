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

namespace Philiagus\Figment\Http\Contract;

use Philiagus\Figment\Http\Contract\DTO\Request;
use Philiagus\Figment\Http\Contract\DTO\Response;

interface Worker {

    public function work(Request $request): Response;

}
