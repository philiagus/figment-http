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

namespace Philiagus\Figment\Http\Contract\Gate;

use Philiagus\Figment\Http\Contract\Action;
use Philiagus\Figment\Http\Contract\DTO\Request;
use Philiagus\Figment\Http\DTO\Response;

interface GateStack
{
    public function next(Request $request, Action $action): Response;
    public function isLast(): bool;
    public function hasNext(): bool;
    public function __invoke(Request $request, Action $action): Response;
}
