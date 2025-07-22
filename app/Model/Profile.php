<?php
declare(strict_types=1);

namespace Nexendrie\Model;

use Nexendrie\Orm\Comment;
use Nexendrie\Orm\User as UserEntity;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Relationships\OneHasMany;
use Nexendrie\Orm\Article as ArticleEntity;
use Nexendrie\Orm\UserSkill;

/**
 * Profile Model
 *
 * @author Jakub Konečný
 */
final class Profile {
  protected \Nexendrie\Orm\Model $orm;
  protected \Nette\Security\User $user;
  
  use \Nette\SmartObject;
  
  public function __construct(\Nexendrie\Orm\Model $orm, \Nette\Security\User $user) {
    $this->orm = $orm;
    $this->user = $user;
  }
  
  /**
   * Show user's profile
   *
   * @throws UserNotFoundException
   */
  public function view(string $publicname): UserEntity {
    $user = $this->orm->users->getByPublicname($publicname);
    if($user === null) {
      throw new UserNotFoundException("Specified user does not exist.");
    }
    return $user;
  }
  
  /**
   * Get list of potential town owners
   * 
   * @return string[]
   */
  public function getListOfLords(): array {
    return $this->orm->users->findBy(["group->path" => \Nexendrie\Orm\Group::PATH_TOWER])
      ->fetchPairs("id", "publicname");
  }
  
  /**
   * Get specified user's path
   */
  public function getPath(int $id = null): string {
    $user = $this->orm->users->getById($id ?? $this->user->id);
    if($user === null) {
      throw new UserNotFoundException();
    }
    return $user->group->path;
  }

  /**
   * Get specified user's partner
   */
  public function getPartner(int $id): ?UserEntity {
    $marriage = $this->orm->marriages->getActiveMarriage($id);
    if($marriage === null) {
      return null;
    } elseif($marriage->user1->id === $id) {
      return $marriage->user2;
    }
    return $marriage->user1;
  }

  /**
   * Get specified user's fiance(e)
   */
  public function getFiance(int $id): ?UserEntity {
    $marriage = $this->orm->marriages->getAcceptedMarriage($id);
    if($marriage === null) {
      return null;
    } elseif($marriage->user1->id === $id) {
      return $marriage->user2;
    }
    return $marriage->user1;
  }
  
  /**
   * @return OneHasMany|ArticleEntity[]
   * @throws UserNotFoundException
   */
  public function getArticles(string $publicname): OneHasMany {
    $user = $this->orm->users->getByPublicname($publicname);
    if($user === null) {
      throw new UserNotFoundException("Specified user does not exist.");
    }
    return $user->articles;
  }
  
  /**
   * @return OneHasMany|UserSkill[]
   * @throws UserNotFoundException
   */
  public function getSkills(string $publicname): OneHasMany {
    $user = $this->orm->users->getByPublicname($publicname);
    if($user === null) {
      throw new UserNotFoundException("Specified user does not exist.");
    }
    return $user->skills;
  }

  /**
   * @return ICollection|Comment[]
   * @throws UserNotFoundException
   */
  public function getComments(string $publicname): ICollection {
    $user = $this->orm->users->getByPublicname($publicname);
    if($user === null) {
      throw new UserNotFoundException("Specified user does not exist.");
    }
    return $user->comments->toCollection()->findBy(['deleted' => false, ]); // @phpstan-ignore  return.type
  }
}
?>