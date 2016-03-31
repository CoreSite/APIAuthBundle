<?php
/**
 * Project by CP.
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 * Date: 31.03.2016 18:19
 */

namespace CoreSite\APIAuthBundle\Event;


class Event
{
    const AUTHENTICATION_SUCCESS = 'cs_apiauth.authentication.on_authentication_success';

    const AUTHENTICATION_FAILURE = 'cs_apiauth.authentication.on_authentication_failure';
}