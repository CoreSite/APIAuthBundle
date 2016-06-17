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
    public function getToken(string $token)
    {
        $currentDateTime = new \DateTime('now');

        $qb = $this->createQueryBuilder('t')
            ->select(array('t'))
        ;

        $qb
            ->where($qb->expr()->eq('t.id', ':hash'))
            ->andWhere($qb->expr()->gt('t.expiresAt', ':expiresAt'))
            ->andWhere($qb->expr()->gt('t.refreshTo', ':expiresAt'))
            ->setParameter('hash', $token)
            ->setParameter('expiresAt', $currentDateTime->format('Y-m-d H:i:s'))
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

//    public function getTokenByHash($hash)
//    {
//        $currentDateTime = new \DateTime('now');
//
//        $qb = $this->createQueryBuilder('t')
//            ->select(array('t'))
//        ;
//
//        $qb
//            ->where($qb->expr()->eq('t.id', ':hash'))
//            ->andWhere($qb->expr()->gt('t.expiresAt', ':expiresAt'))
//            ->andWhere($qb->expr()->gt('t.refreshTo', ':expiresAt'))
//            ->setParameter('hash', $hash)
//            ->setParameter('expiresAt', $currentDateTime->format('Y-m-d H:i:s'))
//        ;
//
//        return $qb->getQuery()->getOneOrNullResult();
//    }

}
