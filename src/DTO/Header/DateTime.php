<?php
declare(strict_types=1);

namespace Philiagus\Figment\Http\DTO\Header;

use Philiagus\Figment\Http\Contract\DTO\Header;

class DateTime implements Header
{

    private readonly \DateTimeImmutable $datetime;

    public function __construct(
        public readonly string $name,
        public readonly string $raw
    )
    {
    }

    public function asDateTime(): \DateTimeImmutable
    {
        if (!isset($this->datetime)) {
            $dateTime = \DateTimeImmutable::createFromFormat('!D, d M Y H:i:s \G\M\T', $this->raw) ?:
                new \DateTimeImmutable($this->raw);
            if ($dateTime === false)
                throw new \RuntimeException("Header {$this->name} does not contain a valide DateTime");
            $this->datetime = $dateTime;
        }

        return $this->datetime;
    }
}
