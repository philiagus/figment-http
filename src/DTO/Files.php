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

readonly class Files implements Contract\DTO\Files
{

    private array $files;

    public function __construct(\Philiagus\Figment\Http\Contract\DTO\File ...$files)
    {
        $this->files = $files;
    }

    public static function fromGlobal(array $files): self
    {
        return new self();
    }

    public function has(string $name): bool
    {
        return isset($this->files[$name]);
    }

    public function get(string $name): File
    {
        return $this->files[$name] ??
            throw new \OutOfBoundsException("File $name does not exist");
    }

    public function list(): array
    {
        return $this->files;
    }
}
