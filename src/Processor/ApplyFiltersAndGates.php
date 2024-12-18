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

use Philiagus\Figment\Container\Attribute\Inject;
use Philiagus\Figment\Container\Attribute\InjectList;
use Philiagus\Figment\Container\Contract\List\InstanceList;
use Philiagus\Figment\Http\Contract;
use Philiagus\Figment\Http\Contract\Action;
use Philiagus\Figment\Http\Contract\DTO\Request;
use Philiagus\Figment\Http\Contract\DTO\Response;
use Philiagus\Figment\Http\Contract\Filter;
use Philiagus\Figment\Http\Contract\Processor;

class ApplyFiltersAndGates implements Contract\Processor
{


    /** @var InstanceList<Contract\Action> */
    #[InjectList('figment.http.actions', Contract\Action::class)]
    private InstanceList $actions;


    /** @var InstanceList<Contract\Filter> */
    #[InjectList('figment.http.filters', Contract\Filter::class, false)]
    private InstanceList $filters;

    /** @var InstanceList<Contract\Gate> */
    #[InjectList('figment.http.gates', Contract\Gate::class, emptyIfNotExists: true)]
    private InstanceList $gates;

    public function process(Request $request, Processor\ProcessorStack $stack): Response
    {
        $filters = array_values(iterator_to_array($this->filters));
        $highestFilterIndex = -1;
        $highestFilter = null;
        foreach ($this->actions as $action) {
            foreach ($filters as $index => $filter) {
                if ($index > $highestFilterIndex) {
                    $highestFilterIndex = $index;
                    $highestFilter = $filter;
                }
                if (!$filter->evaluate($request, $action)) {
                    continue 2;
                }
            }
            $stack = new ApplyFiltersAndGates\GateStack(...$this->gates);
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
