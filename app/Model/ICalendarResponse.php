<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nette;
use Nette\Application\Response;

final readonly class ICalendarResponse implements Response
{
    public function __construct(public string $source)
    {
    }

    public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse): void
    {
        $httpResponse->setContentType("text/calendar");
        echo $this->source;
    }
}
