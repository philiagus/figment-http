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

namespace Philiagus\Figment\Http\Processor;

use Philiagus\Figment\Container\Attribute\Instance;
use Philiagus\Figment\Container\Contract\InstanceList;
use Philiagus\Figment\Container\EmptyInstanceList;
use Philiagus\Figment\Http\Contract;
use Philiagus\Figment\Http\Contract\DTO\Request;
use Philiagus\Figment\Http\Contract\DTO\Response;
use Philiagus\Figment\Http\Contract\Processor;

readonly class ApplyFiltersAndGates implements Contract\Processor
{
    /**
     * @param InstanceList<Contract\Action> $actions
     * @param InstanceList<Contract\Filter> $filters
     * @param InstanceList<Contract\Gate> $gates
     */
    public function __construct(
        #[Instance('figment.http.actions')] private InstanceList $actions,
        #[Instance('figment.http.filters')] private InstanceList $filters = new EmptyInstanceList(),
        #[Instance('figment.http.gates')] private InstanceList   $gates = new EmptyInstanceList()
    )
    {
    }

    public function process(Request $request, Processor\ProcessorStack $stack): Response
    {
        $filters = array_values(
            iterator_to_array(
                $this->filters->traverseInstances(Contract\Filter::class)
            )
        );
        $highestFilterIndex = -1;
        $highestFilter = null;
        foreach ($this->actions->traverseInstances(Contract\Action::class) as $action) {
            foreach ($filters as $index => $filter) {
                if ($index > $highestFilterIndex) {
                    $highestFilterIndex = $index;
                    $highestFilter = $filter;
                }
                if (!$filter->evaluate($request, $action)) {
                    continue 2;
                }
            }
            $stack = new ApplyFiltersAndGates\GateStack(
                ...$this->gates->traverseInstances(Contract\Gate::class)
            );
            return $stack->next($request, $action);
        }

        if ($highestFilter === null) {
            return $this->buildNoActionRegisteredResponse($request);
        }

        return $highestFilter->explainWhyNoContinue($request);
    }

    protected function buildNoActionRegisteredResponse(Request $request): Response
    {
        return $request->response(statusCode: 404);
    }
}
