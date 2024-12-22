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

namespace Philiagus\Figment\Http\Processor\ApplyFiltersAndGates;

use Philiagus\Figment\Http\Contract;
use Philiagus\Figment\Http\Contract\Action;
use Philiagus\Figment\Http\Contract\DTO\Request;
use Philiagus\Figment\Http\DTO\Response;

class GateStack implements Contract\Gate\GateStack
{
    public bool $isLast {
        get => $this->pointer === $this->max;
    }

    public bool $hasNext {
        get => $this->pointer !== $this->max;
    }
    /** @var Contract\Gate[] */
    private array $gates;
    private int $pointer = 0;
    private int $max;

    public function __construct(
        Contract\Gate ...$gates
    )
    {
        $this->gates = array_values($gates);
        $this->max = array_key_last($this->gates);
    }

    public function __invoke(Request $request, Action $action): Response
    {
        return $this->next($request, $action);
    }

    public function next(Request $request, Action $action): Response
    {
        if ($this->isLast) {
            return $action->execute($request);
        }
        $this->pointer++;
        try {
            return $this->gates[$this->pointer]->apply($request, $action, $this);
        } finally {
            $this->pointer--;
        }
    }
}
