<?php
/**
 * Project by CoreSite APIAuth
 * @author: Dmitriy Shuba <sda@sda.in.ua>
 * @link: http://maxi-soft.net/ Maxi-Soft
 */

namespace CoreSite\APIAuthBundle\Event;

/**
 * Class Event
 *
 * События
 *
 * @package CoreSite\APIAuthBundle\Event
 */
class Event
{
    const AUTHENTICATION_SUCCESS = 'cs_apiauth.authentication.on_authentication_success';

    const AUTHENTICATION_FAILURE = 'cs_apiauth.authentication.on_authentication_failure';

    const LOGOUT_SUCCESS = 'cs_apiauth.authentication.on_logout_success';
}