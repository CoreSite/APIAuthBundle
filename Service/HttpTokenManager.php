<?php
/**
 * Project by CoreSite APIAuth
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 */

namespace CoreSite\APIAuthBundle\Service;


use CoreSite\APIAuthBundle\Entity\HttpToken;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Model\UserInterface;
use Doctrine\DBAL\Exception\ConstraintViolationException;

class HttpTokenManager
{
    const LIFE_TIME_MAX         = 86400;
    const LIFE_TIME_REFRESH     = 3600;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(UserManager $userManager, EntityManager $entityManager)
    {
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $hash
     * @return HttpToken
     */
    public function getToken($hash)
    {
        return $this->entityManager->getRepository('CoreSiteAPIAuthBundle:HttpToken')->getToken($hash);
    }
    
    /**
     * @param UserInterface $user
     * @return HttpToken
     */
    public function createToken(UserInterface $user) : HttpToken
    {
        $currentDateTime = new \DateTime('now');
        
        $expiresAt = clone $currentDateTime;
        $expiresAt->add(new \DateInterval('PT' . self::LIFE_TIME_MAX . 'S'));

        $refreshTo = clone $currentDateTime;
        $refreshTo->add(new \DateInterval('PT' . self::LIFE_TIME_REFRESH . 'S'));

        $token = new HttpToken();

        $token
            ->setUserId($user->getId())
            ->setRefreshTo($refreshTo)
            ->setExpiresAt($expiresAt)
        ;

        $this->entityManager->persist($token);

        try
        {
            $this->entityManager->flush();
        }
        catch(ConstraintViolationException $e)
        {
            error_log($e->getMessage());
            return false;
        }

        return $token;
    }

    public function deleteToken(HttpToken $token) : bool
    {
        $this->entityManager->remove($token);

        try
        {
            $this->entityManager->flush($token);
        }
        catch(ConstraintViolationException $e)
        {
            error_log($e->getMessage());
            return false;
        }

        return true;
    }

    public function refreshToken(HttpToken $token) : bool
    {
        $refreshTo = new \DateTime('now');
        $refreshTo->add(new \DateInterval('PT' . self::LIFE_TIME_REFRESH . 'S'));

        $token->setRefreshTo($refreshTo);

        try
        {
            $this->entityManager->flush($token);
        }
        catch(ConstraintViolationException $e)
        {
            error_log($e->getMessage());
            return false;
        }

        return true;
    }

    public function getUserByToken(string $token)
    {
        $token = $this->entityManager->getRepository('CoreSiteAPIAuthBundle:HttpToken')->getToken($token);
        if(!$token instanceof HttpToken)
        {
            return false;
        }

        $user = $this->userManager->findUserBy(array('id' => $token->getUserId()));
        if(!$user instanceof UserInterface)
        {
            return false;
        }

        return $user;
    }

}