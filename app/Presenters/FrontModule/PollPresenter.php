<?php
declare(strict_types=1);

namespace Nexendrie\Presenters\FrontModule;

use Nexendrie\Components\PollControlFactory;
use Nexendrie\Components\PollControl;
use Nexendrie\Model\PollNotFoundException;
use Nexendrie\Model\Polls;

/**
 * Presenter Poll
 *
 * @author Jakub Konečný
 */
final class PollPresenter extends BasePresenter
{
    private \Nexendrie\Orm\Poll $poll;

    public function __construct(private readonly Polls $model)
    {
        parent::__construct();
    }

    /**
     * @throws \Nette\Application\BadRequestException
     */
    public function renderView(int $id): void
    {
        try {
            $this->poll = $this->model->view($id);
        } catch (PollNotFoundException) {
            throw new \Nette\Application\BadRequestException();
        }
        $this->template->pollId = $id;
    }

    protected function createComponentPoll(PollControlFactory $factory): \Nette\Application\UI\Multiplier
    {
        return new \Nette\Application\UI\Multiplier(static function ($id) use ($factory): PollControl {
            $poll = $factory->create();
            $poll->id = (int) $id;
            return $poll;
        });
    }

    protected function getDataModifiedTime(): int
    {
        if (!isset($this->poll)) {
            return 0;
        }
        return $this->poll->updated;
    }
}
