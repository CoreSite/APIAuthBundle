<?php
/**
 * Project sf3.loc.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 12.01.2017 15:46
 */

namespace CoreSite\APIAuthBundle\Entity;


interface ApiAuthUser
{
    public function getToken();
}