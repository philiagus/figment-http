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

namespace Philiagus\Figment\Http\Worker;

use Philiagus\Figment\Http\Contract;
use Philiagus\Figment\Http\Contract\DTO\Request;
use Philiagus\Figment\Http\DTO\Response;

class ProcessorStack implements Contract\Processor\ProcessorStack
{
    public bool $isLast {
        get => $this->pointer === $this->max;
    }
    public bool $hasNext {
        get => $this->pointer !== $this->max;
    }
    /** @var Contract\Processor[] */
    private array $processors;
    private int $pointer = 0;
    private int $max;

    public function __construct(Contract\Processor ...$processors)
    {
        $this->processors = array_values($processors);
        $this->max = array_key_last($processors);
    }

    public function __invoke(Request $request): Response
    {
        return $this->next($request);
    }

    public function next(Request $request): Response
    {
        $this->pointer++;
        try {
            return $this->processors[$this->pointer]->process($request, $this);
        } finally {
            $this->pointer--;
        }
    }
}
