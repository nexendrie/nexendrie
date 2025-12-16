<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Model\EventNotFoundException;
use Nexendrie\Model\Events;

/**
 * Presenter Event
 *
 * @author Jakub Konečný
 */
final class EventPresenter extends BasePresenter
{
    public function __construct(private readonly Events $model)
    {
        parent::__construct();
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function renderView(int $id): void
    {
        try {
            $this->template->event = $event = $this->model->getEvent($id);
            $time = time();
            $status = "past";
            if ($event->start <= $time && $event->end >= $time) {
                $status = "active";
            } elseif ($event->start > $time) {
                $status = "future";
            }
            $this->template->status = $status;
        } catch (EventNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
    }

    protected function getDataModifiedTime(): int
    {
        if (isset($this->template->event)) {
            return $this->template->event->updated;
        }
        return 0;
    }
}
