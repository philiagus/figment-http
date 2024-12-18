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

use Philiagus\Figment\Http\Contract\DTO\RequestCookies;
use Philiagus\Parser\Base\Parser;
use Philiagus\Parser\Base\Parser\ResultBuilder;
use Philiagus\Parser\Base\Subject;
use Philiagus\Parser\Contract\Parser as ParserContract;
use Philiagus\Parser\Parser\Parse\ParseArray;
use Philiagus\Parser\Result;

class AssertRequestCookies extends Parser {

    /** @var array<array{string, bool, ?string, ParserContract}> */
    private array $cookies = [];

    public function giveCookie(string $name, ParserContract $parser): self
    {
        $this->cookies[] = [$name, false, null, $parser];

        return $this;
    }

    public function giveOptionalCookie(string $name, ParserContract $parser): self
    {
        $this->cookies[] = [$name, true, null, $parser];

        return $this;
    }

    public function giveDefaultedCookie(string $name, string $default, ParserContract $parser): self
    {
        $this->cookies[] = [$name, true, $default, $parser];

        return $this;
    }

    protected function execute(ResultBuilder $builder): Result
    {
        $cookies = $builder->getValue();

        if(!$cookies instanceof RequestCookies) {
            $builder->logErrorStringify("Parser expected object of type Cookies, but got {value.type} instead");
            return $builder->createResultUnchanged();
        }

        foreach($this->cookies as [$name, $optional, $default, $parser]) {
            if($cookies->has($name)) {
                $cookieValue = $cookies->get($name);
            } elseif($optional) {
                if($default === null)
                    continue;
                $cookieValue = $default;
            } else {
                $builder->logErrorStringify(
                    "No cookie {name} received",
                    ['name' => $name]
                );
                continue;
            }
            $builder->unwrapResult($parser->parse($cookieValue));
        }

        return $builder->createResultUnchanged();
    }

    protected function getDefaultParserDescription(Subject $subject): string
    {
        return 'assert cookies';
    }
}
