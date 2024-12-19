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

namespace Philiagus\Figment\Http\Filter;

use Philiagus\Figment\Container\Attribute\InjectContextOptional;
use Philiagus\Figment\Http\Contract\Action;
use Philiagus\Figment\Http\Contract\DTO\Request;
use Philiagus\Figment\Http\Contract\Filter;
use Philiagus\Figment\Http\DTO\Response;
use Philiagus\Parser\Base\Subject;
use Philiagus\Parser\Exception\ParsingException;

class MethodFilter implements Filter
{
    public function __construct(
        #[InjectContextOptional('.statusCode')]
        private int $httpStatusCode = 405
    )
    {
    }

    public function explainWhyNoContinue(Request $request): Response
    {
        return $request->response(statusCode: $this->httpStatusCode);
    }

    public function evaluate(Request $request, Action $action): bool
    {
        if (!$action instanceof Action\MethodAware)
            return true;

        try {
            $expected = $action->expectedMethod();
            if (is_string($expected)) {
                return $expected === $request->getMethod();
            } else {
                $expected->parse(
                    Subject::default($request->getMethod(), 'Request Method')
                );
            }
        } catch (ParsingException) {
            return false;
        }

        return true;
    }
}
