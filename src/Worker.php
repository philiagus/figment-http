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

namespace Philiagus\Figment\Http;

use Philiagus\Figment\Container\Attribute\InjectList;
use Philiagus\Figment\Container\Contract\List\InstanceList;
use Philiagus\Figment\Http\Contract\DTO\Request;
use Philiagus\Figment\Http\Contract\DTO\Response;
use Philiagus\Figment\Http\Contract\Processor;

class Worker
{

    /**
     * @param InstanceList<Processor> $processors
     */
    public function __construct(
        #[InjectList('figment.http.processors')]
        private InstanceList $processors
    )
    {
    }

    public function execute(Request $request): Response
    {
        $stack = new Worker\ProcessorStack(...$this->processors->traverseInstances(Processor::class));
        return $stack->next($request);
    }

}
