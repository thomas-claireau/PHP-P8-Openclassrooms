<?php

namespace App\Repository;

use App\Entity\Task;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAllByUser()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
	/**
	 * @var UserRepository
	 */
	private $userRepository;

	public function __construct(ManagerRegistry $registry, UserRepository $userRepository)
	{
		parent::__construct($registry, Task::class);
		$this->userRepository = $userRepository;
	}

	public function resetIndex()
	{
		$connection = $this->getEntityManager()->getConnection();
		$connection->exec("ALTER TABLE task AUTO_INCREMENT = 1;");
	}

	/**
	 * @return Task[]
	 */
	public function findAllByUser($user): array
	{
		return $this->getQueryDateDesc($user)
			->getQuery()
			->getResult();
	}

	private function getQueryDateDesc($user)
	{
		$anonymousUser = $this->userRepository->findOneBy(['username' => 'anonyme']);

		$query = $this->createQueryBuilder('p')
			->where('p.user = :anonymous_user')
			->orWhere('p.user = :user')
			->setParameters(['anonymous_user' => $anonymousUser, 'user' => $user])
			->orderBy('p.updated_at', 'DESC');

		return $query;
	}
}
