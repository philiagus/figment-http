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

use Philiagus\Figment\Http\Contract\DTO\Headers;
use Philiagus\Parser\Base\Parser;
use Philiagus\Parser\Base\Parser\ResultBuilder;
use Philiagus\Parser\Base\Subject;
use Philiagus\Parser\Error;
use Philiagus\Parser\Exception\ParsingException;
use Philiagus\Parser\Result;
use Philiagus\Parser\Subject\PropertyValue;
use Philiagus\Parser\Util\Stringify;

class AssertHeaders extends Parser
{

    private array $headers = [];

    public function giveRawHeader(string $name, int $statusCodeOnMissing, int $statusCodeOnError, Parser $parser): self
    {
        $this->headers[] = [$name, $statusCodeOnMissing, null, $statusCodeOnError, $parser];

        return $this;
    }

    public function giveRawHeaderDefaulted(string $name, string $default, int $statusCodeOnError, Parser $parser): self
    {
        $this->headers[] = [$name, null, $default, $statusCodeOnError, $parser];

        return $this;
    }

    protected function execute(ResultBuilder $builder): Result
    {
        $subject = $builder->getSubject();
        $value = $subject->getValue();
        if (!$value instanceof Headers) {
            $builder->logErrorStringify("Parser expected object of type Headers, but got {value.type} instead");
            return $builder->createResultUnchanged();
        }

        /**
         * @var string $name
         * @var int|null $statusCodeOnMissing
         * @var mixed $default
         * @var int $statusCodeOnError
         * @var \Philiagus\Parser\Contract\Parser $parser
         */
        foreach ($this->headers as [$name, $statusCodeOnMissing, $default, $statusCodeOnError, $parser]) {
            $headerValue = $value->has($name) ? $value->getRaw($name) : $default;
            if ($headerValue === null && $statusCodeOnMissing !== null) {
                $builder->logError(
                    new HttpResponseError(
                        $subject,
                        "Expected header {$name} is missing",
                        $statusCodeOnMissing
                    )
                );
                continue;
            }
            try {
                $result = $parser->parse(
                    new PropertyValue($builder->getSubject(), $name, $headerValue)
                );
                if($result->hasErrors()) {
                    $builder->logError(
                        new HttpResponseError(
                            $subject,
                            "Expected header {$name} is malformed",
                            $statusCodeOnError,
                            sourceErrors: $result->getErrors()
                        )
                    );
                }
            } catch (ParsingException $e) {
                $builder->logError(
                    new HttpResponseError(
                        $subject,
                        "Expected header {$name} is malformed",
                        $statusCodeOnError,
                        sourceThrowable: $e,
                        sourceErrors: [$e->getError()]
                    )
                );
            }
        }

        return $builder->createResultUnchanged();
    }

    protected function getDefaultParserDescription(Subject $subject): string
    {
        return 'parse headers';
    }
}
