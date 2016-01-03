<?php

namespace AG\VaultBundle\Repository;

use AG\UserBundle\Entity\User;
use AG\VaultBundle\Entity\File;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * FileRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FileRepository extends EntityRepository
{
    /**
     * @param $search
     * @param $user
     * @return array
     */
    public function findSearch($search, $user)
    {
        $qb = $this->createQueryBuilder('f')
            ->orderBy('f.name', 'ASC')
            ->where('f.owner = :user')
            ->setParameter('user', $user)
        ;

        $search = trim($search);
        $words = explode(' ', $search);

        foreach ($words as $key => $word)
        {
            $qb
                ->andWhere(
                    $qb->expr()->like('f.name', ":word$key")
                )
                ->setParameter("word$key", "%$word%")
            ;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param User $user
     * @return array
     */
    public function findByAuthorizedUsers(User $user)
    {
        $qb = $this->createQueryBuilder('f')
            ->join('f.authorizedUsers', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
            ->getQuery()
            ->getResult()
        ;

        return $qb;
    }

    /**
     * @param $token
     * @return File|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByToken($token)
    {
        $qb = $this->createQueryBuilder('f')
            ->join('f.shareLinks', 's')
            ->where('s.token = :token')
            ->setParameter('token', $token)
            ->getQuery();

        try {
            return $qb->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}