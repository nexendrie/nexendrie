<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Model as ORM;
use Nexendrie\Orm\Skill as SkillEntity;
use Nexendrie\Orm\UserSkill as UserSkillEntity;
use Nextras\Orm\Collection\ICollection;

/**
 * Skills Model
 *
 * @author Jakub Konečný
 */
final readonly class Skills
{
    public function __construct(private ORM $orm, private \Nette\Security\User $user)
    {
    }

    /**
     * Get list of all skills
     *
     * @return SkillEntity[]|ICollection
     */
    public function listOfSkills(?string $type = null): ICollection
    {
        if ($type === null) {
            return $this->orm->skills->findAll();
        }
        return $this->orm->skills->findByType($type);
    }

    /**
     * Add new skill
     */
    public function add(array $data): void
    {
        $skill = new SkillEntity();
        foreach ($data as $key => $value) {
            $skill->$key = $value;
        }
        $this->orm->skills->persistAndFlush($skill);
    }

    /**
     * Edit specified skill
     *
     * @throws SkillNotFoundException
     */
    public function edit(int $id, array $data): void
    {
        $skill = $this->get($id);
        foreach ($data as $key => $value) {
            $skill->$key = $value;
        }
        $this->orm->skills->persistAndFlush($skill);
    }

    /**
     * Get details of specified skill
     *
     * @throws SkillNotFoundException
     */
    public function get(int $id): SkillEntity
    {
        $skill = $this->orm->skills->getById($id);
        return $skill ?? throw new SkillNotFoundException();
    }

    /**
     * @throws AuthenticationNeededException
     */
    public function getUserSkill(int $skill): UserSkillEntity
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        }
        $userSkill = $this->orm->userSkills->getByUserAndSkill($this->user->id, $skill);
        if ($userSkill === null) {
            $userSkill = new UserSkillEntity();
            $this->orm->userSkills->attach($userSkill);
            $userSkill->skill = $skill;
            $userSkill->user = $this->user->id;
            $userSkill->level = 0;
        }
        return $userSkill;
    }

    /**
     * Learn new/improve existing skill
     *
     * @throws AuthenticationNeededException
     * @throws SkillNotFoundException
     * @throws SkillMaxLevelReachedException
     * @throws InsufficientFundsException
     */
    public function learn(int $id): void
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationNeededException();
        }
        $skill = $this->get($id);
        $userSkill = $this->getUserSkill($id);
        if ($userSkill->level === $skill->maxLevel) {
            throw new SkillMaxLevelReachedException();
        }
        $price = $userSkill->learningPrice;
        if ($userSkill->user->money < $price) {
            throw new InsufficientFundsException();
        }
        $userSkill->level++;
        $userSkill->user->money -= $price;
        $userSkill->user->lastActive = time();
        $this->orm->userSkills->persistAndFlush($userSkill);
    }

    /**
     * Get level of user's specified skill
     *
     * @throws AuthenticationNeededException
     */
    public function getLevelOfSkill(int $skillId): int
    {
        $skill = $this->getUserSkill($skillId);
        $level = $skill->level;
        if ($level === 0) {
            $this->orm->userSkills->detach($skill);
        }
        return $level;
    }
}
