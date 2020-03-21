<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends Voter
{
	/**
	 *
	 */
	const VIEW = 'view';

	/**
	 *
	 */
	const CREATE = 'create';

	/**
	 *
	 */
	const UPDATE = 'update';

	/**
	 * @param  string $attribute
	 * @param  mixed  $subject
	 * @return bool
	 */
	protected function supports($attribute, $subject): bool
	{
		if (!in_array($attribute, [self::VIEW, self::CREATE, self::UPDATE])) {
			return false;
		}


		if (!$subject instanceof User) {
			return false;
		}

		return true;
	}

	/**
	 * @param  string         $attribute
	 * @param  mixed          $subject
	 * @param  TokenInterface $token
	 * @return bool
	 */
	protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
	{
		$user = $token->getUser();

		if (!$user instanceof User) {
			return false;
		}

		switch ($attribute) {
			case self::VIEW:
				return $this->canView($user);
			case self::CREATE:
				return $this->canCreate($user);
			case self::UPDATE:
				return $this->canUpdate($user);
		}

		throw new \LogicException('This code should not be reached!');
	}


	/**
	 * @param  Task $task
	 * @param  User $user
	 * @return bool
	 */
	private function canView(User $user): bool
	{
		return $user->getRole() === '["ROLE_ADMIN"]';
	}

	/**
	 * @param  Task $task
	 * @param  User $user
	 * @return bool
	 */
	private function canCreate(User $user)
	{
		return $user->getRole() === '["ROLE_ADMIN"]';
	}

	/**
	 * @param  Task $task
	 * @param  User $user
	 * @return bool
	 */
	private function canUpdate(User $user)
	{
		return $user->getRole() === '["ROLE_ADMIN"]';
	}
}
