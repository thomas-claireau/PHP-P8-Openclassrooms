<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findUsers()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, User::class);
	}

	public function resetIndex()
	{
		$connection = $this->getEntityManager()->getConnection();
		$connection->exec("ALTER TABLE user AUTO_INCREMENT = 1;");
	}

	/**
	 * @return User[]
	 */
	public function findUsers(): array
	{
		return $this->getQueryDesc()
			->getQuery()
			->getResult();
	}

	private function getQueryDesc()
	{
		$query = $this->createQueryBuilder('p')
			->orderBy('p.id', 'DESC');

		return $query;
	}
}
