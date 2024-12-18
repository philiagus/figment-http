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

readonly class Headers implements Contract\DTO\Headers
{
    private array $normalizedHeaders;

    public function __construct(private array $headers = [])
    {
        $uppercaseHeaders = [];
        foreach ($headers as $name => $value)
            $uppercaseHeaders[strtolower($name)] = $value;
        $this->normalizedHeaders = $uppercaseHeaders;
    }

    public static function fromGlobal(array $server): self
    {
        $headers = [];
        foreach ($server as $key => $value)
            if (str_starts_with($key, 'HTTP_'))
                $headers[self::humanize(substr($key, 5))] = $value;
        return new self($headers);
    }

    private static function humanize(string $name): string
    {
        $name = strtolower($name);
        $uc = true;
        $len = strlen($name);
        for ($i = 0; $i < $len; $i++) {
            if ($name[$i] === '_') {
                $name[$i] = '-';
                $uc = true;
            } elseif ($uc) {
                $name[$i] = strtoupper($name[$i]);
                $uc = false;
            }
        }
        return $name;
    }

    public function has(string $name): bool
    {
        return isset($this->normalizedHeaders[strtolower($name)]);
    }

    public function getRaw(string $name): string
    {
        return $this->normalizedHeaders[strtolower($name)] ??
            throw new \OutOfBoundsException("Header {$this->humanize($name)} does not exist");
    }

    public function get(string $name): Contract\DTO\Header
    {
        return HeaderFactory::create($name, $this->getRaw($name));
    }

    public function empty(): bool
    {
        return empty($this->headers);
    }

    public function merge(array|Contract\DTO\Headers $headers): Contract\DTO\Headers
    {
        if (empty($this->headers)) {
            if ($headers instanceof self) {
                return $headers;
            }
            return new self($headers);
        }
        $headers = $headers instanceof Contract\DTO\Headers ? $headers->all() : $headers;
        $newHeaders = $this->headers;
        foreach ($newHeaders as $newName => $newValue) {
            foreach ($headers as $oldName => $oldValue) {
                if (strcasecmp($newName, $oldName) === 0) {
                    $newHeaders[$oldName] = $newValue;
                    continue 2;
                }
            }
            $newHeaders[$newName] = $newValue;
        }
        return new self($newHeaders);
    }

    public function all(): array
    {
        return $this->headers;
    }
}
