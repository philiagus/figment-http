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

namespace Philiagus\Figment\Http\Parser;

use Philiagus\Figment\Http\Contract;
use Philiagus\Figment\Http\Contract\DTO\Request;
use Philiagus\Figment\Http\Contract\DTO\Response;
use Philiagus\Parser\Base\Subject;
use Philiagus\Parser\Error;

readonly class HttpResponseError extends Error implements Contract\HttpResponseBuilder
{

    public function __construct(
        Subject $subject,
        string $message,
        private int $httpStatusCode,
        private ?string $httpStatusDescription = null,
        private string $responseBody = '',
        ?\Throwable $sourceThrowable = null,
        array $sourceErrors = []
    )
    {
        parent::__construct($subject, $message, $sourceThrowable, $sourceErrors);
    }

    public function getHttpResponse(Request $request): Response
    {
        return $request->response($this->httpStatusCode, $this->httpStatusDescription, $this->responseBody);
    }
}
