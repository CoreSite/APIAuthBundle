# APIAuthBundle

Авторизация по токену, для REST приложений.

Установка и подключение
=======================

Установка:
----------

    $ composer require coresite/apiauthbundle
    
Подключение:
------------

    // app/AppKernel.php
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...
                new CoreSite\APIAuthBundle\CoreSiteAPIAuthBundle(),
                // ...
            );

            // ...
        }
    }   
     
Настройка:
----------

Пример настройки, вам скорее всего придется производить настройку под свою конфигурацию.

    // app/security.yml
    firewalls:
    
        # ...
        
        api:
            pattern: ^/api
            stateless: true
            simple_preauth:
                authenticator: cs_apiauth_authenticator
                provider: cs_apiauth_user_provider

        api_login:
            provider: fos_userbundle
            stateless: true
            anonymous: ~
            cs_apiauth_login:
                check_path:               api_login_check
                username_parameter:       _username
                password_parameter:       _password
                success_handler:          cs_apiauth_user_handler_authentication_success
                failure_handler:          cs_apiauth_user_handler_authentication_failure
                require_previous_session: false
            logout:
                success_handler:          cs_apiauth_user_handler_logout
        
        # ...
                        
        providers:
            fos_userbundle:
                id: fos_user.user_provider.username
            cs_apiauth_user_provider:
                id: cs_apiauth_user_provider
        
            access_control:
                - { path: ^/api, role: IS_AUTHENTICATED_FULLY }
                - { path: ^/login_check, roles: IS_AUTHENTICATED_ANONYMOUSLY }     
        
        # ...                   
                
Создание таблицы для хранение токенов:
--------------------------------------  
        # php bin/console doctrine:schema:update --force 
        
Отказ от отвестовенности
------------------------
        
Обратите внимание, что данный бандел разработан для личных нужд и не является до конца доработанным проектом, его использование не рекомендуется промышленных целей. Автор не несет ни какой ответственности за проблемы которые могут возникнут при использования данного кода.
        
        