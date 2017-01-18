<?php
/**
 * Project by CoreSite APIAuth
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 */

namespace CoreSite\APIAuthBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * TokenRepository
 */
class TokenHttpRepository extends EntityRepository
{
    public function getToken(string $token, bool $recovery = false)
    {
        $currentDateTime = new \DateTime('now');

        $qb = $this->createQueryBuilder('t')
            ->select(array('t'))
        ;

        $qb
            ->where($qb->expr()->eq('t.id', ':hash'))
            ->andWhere($qb->expr()->gt('t.expiresAt', ':expiresAt'))
            ->andWhere($qb->expr()->gt('t.refreshTo', ':expiresAt'))
            ->andWhere($qb->expr()->eq('t.recovery', ':recovery'))
            ->setParameter('hash', $token)
            ->setParameter('expiresAt', $currentDateTime->format('Y-m-d H:i:s'))
            ->setParameter('recovery', $recovery)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

}
