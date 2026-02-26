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
use Philiagus\Figment\Http\Contract\DTO\ResponseCookies;

readonly class Request implements Contract\DTO\Request
{

    public function __construct(
        public float $time,
        public string $protocol,
        public bool $https,
        public string $method,
        public string $path,
        public string $query,
        public string $body,
        public ?string $authUser,
        public ?string $authPassword,
        public Contract\DTO\Headers $headers,
        public Contract\DTO\Files $files,
        public Contract\DTO\RequestCookies $cookies,
        public array $post,
        public array $get
    )
    {

    }

    public static function fromGlobals(
        ?array $server = null,
        ?array $post = null,
        ?array $get = null,
        ?array $files = null,
        ?array $cookie = null,
        null|array|Contract\DTO\Headers $headers = null
    ): self
    {
        $server ??= $_SERVER;
        $post ??= $_POST;
        $get ??= $_GET;
        $files ??= $_FILES;
        $cookie ??= $_COOKIE;
        $parts = parse_url($_SERVER['REQUEST_URI']);
        if($headers === null) {
            $headers = Headers::fromGlobal($server);
        } elseif(is_array($headers)) {
            $headers = new Headers($headers);
        }
        return new self(
            $_SERVER['REQUEST_TIME_FLOAT'],
            $_SERVER['SERVER_PROTOCOL'],
            !empty($_SERVER['HTTPS']),
            $_SERVER['REQUEST_METHOD'],
            $parts['path'] ?? '/',
            $parts['query'] ?? '',
            file_get_contents('php://input'),
            $_SERVER['PHP_AUTH_USER'] ?? null,
            $_SERVER['PHP_AUTH_PW'] ?? null,
            $headers,
            Files::fromGlobal($files),
            RequestCookies::fromGlobal($cookie),
            $post,
            $get
        );
    }


    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getHeaders(): \Philiagus\Figment\Http\Contract\DTO\Headers
    {
        return $this->headers;
    }

    public function getFiles(): \Philiagus\Figment\Http\Contract\DTO\Files
    {
        return $this->files;
    }

    public function getCookies(): \Philiagus\Figment\Http\Contract\DTO\RequestCookies
    {
        return $this->cookies;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }

    public function response(
        int $statusCode = 200,
        string $body = '',
        ?string $statusCodeDescription = null,
        array|Contract\DTO\Headers $headers = new Headers(),
        ResponseCookies $cookies = new \Philiagus\Figment\Http\DTO\ResponseCookies()
    ): \Philiagus\Figment\Http\Contract\DTO\Response
    {
        return new Response(
            $this,
            $statusCode,
            $statusCodeDescription,
            $body,
            $headers,
            $cookies
        );
    }

    public function getTime(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromTimestamp($this->time);
    }

    public function getPost(): array
    {
        return $this->post;
    }

    public function getGet(): array
    {
        return $this->get;
    }
}
