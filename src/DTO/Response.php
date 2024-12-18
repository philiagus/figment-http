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

namespace Philiagus\Figment\Http\DTO;

use Philiagus\Figment\Http\Contract;

readonly class Response implements Contract\DTO\Response
{

    private const array STATUS_DESCRIPTIONS = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Content Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Content',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];
    public \Philiagus\Figment\Http\Contract\DTO\Headers $headers;
    private ?string $statusDescription;

    public function __construct(
        public \Philiagus\Figment\Http\Contract\DTO\Request         $request,
        public int                                                  $statusCode = 204,
        ?string                                                     $statusDescription = null,
        public string                                               $body = '',
        array|\Philiagus\Figment\Http\Contract\DTO\Headers          $headers = new Headers(),
        public \Philiagus\Figment\Http\Contract\DTO\ResponseCookies $cookies = new ResponseCookies()
    )
    {
        $this->statusDescription = $statusDescription;
        $this->headers = $headers instanceof \Philiagus\Figment\Http\Contract\DTO\Headers ? $headers : new Headers($headers);
    }

    public function getRequest(): \Philiagus\Figment\Http\Contract\DTO\Request
    {
        return $this->request;
    }

    public function withStatus(int $status, ?string $description): self
    {
        return new self(
            $this->request,
            $status,
            $description,
            $this->body,
            $this->headers,
            $this->cookies
        );
    }

    public function withBody(string $body): self
    {
        return new self(
            $this->request,
            $this->statusCode,
            $this->statusDescription,
            $body,
            $this->headers,
            $this->cookies
        );
    }

    public function withHeaders(array|\Philiagus\Figment\Http\Contract\DTO\Headers $headers): self
    {
        return new self(
            $this->request,
            $this->statusCode,
            $this->statusDescription,
            $this->body,
            $this->headers->merge($headers),
            $this->cookies
        );
    }

    public function withCookies(array|\Philiagus\Figment\Http\Contract\DTO\ResponseCookies $cookies): self
    {
        return new self(
            $this->request,
            $this->statusCode,
            $this->statusDescription,
            $this->body,
            $this->headers,
            $this->cookies->merge($cookies)
        );
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getStatusDescription(): string
    {
        return $this->statusDescription ?? self::STATUS_DESCRIPTIONS[$this->statusCode];
    }

    public function getCookies(): \Philiagus\Figment\Http\Contract\DTO\ResponseCookies
    {
        return $this->cookies;
    }

    public function getHeaders(): \Philiagus\Figment\Http\Contract\DTO\Headers
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function send(): self
    {
        $description = $this->statusDescription ?? self::STATUS_DESCRIPTIONS[$this->statusCode] ?? '';
        header($this->request->getProtocol() . " " . $this->statusCode . " " . $description);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        $this->cookies->send();
        echo $this->body;

        return $this;
    }
}
