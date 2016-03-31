<?php

namespace CoreSite\APIAuthBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * TokenRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TokenRepository extends EntityRepository
{
    public function getToken(string $token)
    {
        $currentDateTime = new \DateTime('now');

        $qb = $this->createQueryBuilder('t')
            ->select(array('t'))
        ;

        $qb
            ->where($qb->expr()->eq('t.token', ':token'))
            ->andWhere($qb->expr()->gt('t.expiresAt', ':expiresAt'))
            ->setParameter('token', $token)
            ->setParameter('expiresAt', $currentDateTime->format('Y-m-d H:i:s'))
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}
