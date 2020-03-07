<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAllByUser()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Task::class);
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
		$query = $this->createQueryBuilder('p')
			->where('p.user = :user')
			->setParameter('user', $user)
			->orderBy('p.updated_at', 'DESC');

		return $query;
	}
}
