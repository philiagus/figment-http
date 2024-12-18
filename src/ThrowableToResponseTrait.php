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

use Philiagus\Figment\Http\Contract\DTO\Request;
use Philiagus\Figment\Http\Contract\DTO\Response;
use Philiagus\Figment\Http\Contract\HttpResponseBuilder;
use Philiagus\Parser\Exception\ParsingException;

trait ThrowableToResponseTrait {

    protected function throwableToResponse(Request $request, \Throwable $exception, int $fallbackHttpStatusCode): Response
    {
        if($exception instanceof HttpResponseBuilder) {
            return $exception->getHttpResponse($request);
        } elseif($exception instanceof ParsingException) {
            $error = $exception->getError();
            if($error instanceof HttpResponseBuilder) {
                return $error->getHttpResponse($request);
            }
        }

        return $request->response(statusCode: $fallbackHttpStatusCode);
    }

}
