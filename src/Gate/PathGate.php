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

namespace Philiagus\Figment\Http\Gate;

use Philiagus\Figment\Container\Attribute\InjectContextOptional;
use Philiagus\Figment\Http\Contract\Action;
use Philiagus\Figment\Http\Contract\DTO\Request;
use Philiagus\Figment\Http\Contract\Gate;
use Philiagus\Figment\Http\DTO\Response;
use Philiagus\Figment\Http\ThrowableToResponseTrait;
use Philiagus\Parser\Base\Subject;

class PathGate implements Gate
{

    #[InjectContextOptional('.statusCode')]
    private int $httpResponseCode = 404;

    use ThrowableToResponseTrait;

    public function apply(Request $request, Action $action, Gate\GateStack $stack): \Philiagus\Figment\Http\Contract\DTO\Response
    {
        if ($action instanceof Action\PathAware) {
            $expected = $action->expectedPath();
            $received = $request->getPath();
            if (is_string($expected)) {
                if ($expected !== $received)
                    return $this->buildErrorResponse($request, null);
            } else {
                try {
                    $expected->parse(Subject::default($received, 'Request Path'));
                } catch (\Throwable $throwable) {
                    return $this->buildErrorResponse($request, $throwable);
                }
            }
        }

        return $stack->next($request, $action);
    }

    public function buildErrorResponse(Request $request, ?\Throwable $throwable): Response
    {
        return $throwable ?
            $this->throwableToResponse($request, $throwable, $this->httpResponseCode) :
            $request->response(statusCode: $this->httpResponseCode);
    }
}
