<?php
declare(strict_types=1);

namespace Philiagus\Figment\Http\DTO\Header\Authorization;

use Philiagus\Figment\Http\Contract\DTO\Header;
use Philiagus\Figment\Http\DTO\Header\Authorization;

class Basic extends Authorization
{
    public string $username {
        get {
            if(!isset($this->username)) $this->populate();
            return $this->username;
        }
    }

    public string $password {
        get {
            if(!isset($this->password)) $this->populate();
            return $this->password;
        }
    }

    private function populate(): void
    {
        $decoded = base64_decode($this->raw);
        if($decoded !== false) {
            $parts = explode(':', $decoded, 2);
            if(isset($parts[1])) {
                $this->username = $parts[0];
                $this->password = $parts[1];
            }
        }
        throw new \RuntimeException("Basic Authentication header is malformed");
    }
}
