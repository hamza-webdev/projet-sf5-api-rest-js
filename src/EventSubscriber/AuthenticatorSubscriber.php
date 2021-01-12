<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Http\Event\DeauthenticatedEvent;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;

class AuthenticatorSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $securityLogger;
    private RequestStack $requestStack;

    public function __construct(LoggerInterface $securityLogger, RequestStack $requestStack)
    {
        $this->securityLogger = $securityLogger;
        $this->requestStack = $requestStack;
    }

    /**
     * [getSubscribedEvents description]
     *
     * @return  Array  <string>
     */
    public static function getSubscribedEvents()
    {
        return [
            // 'security.authentication.failure' => 'onSecurityAuthenticationFailure',
            AuthenticationEvents::AUTHENTICATION_FAILURE        => 'onSecurityAuthenticationFailure',
            AuthenticationEvents::AUTHENTICATION_SUCCESS        => 'onSecurityAuthenticationSuccess',
            SecurityEvents::INTERACTIVE_LOGIN                   => 'onSecurityInteractiveLogin',
            'Symfony\Component\Security\Http\Evenet\LogoutEvent' => 'OnSecurityLogout',
            'security.logout_on_change'                         =>  'onSecurityLogoutOnChange',
            SecurityEvents::SWITCH_USER                         =>  'onSecuritySwitchUser'

        ];
    }

    public function onSecurityAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        ['userIP' => $userIp] = $this->getRouteNameAndUserIP();

        /** @var TokenInterface $securityToken    */
        $securityToken = $event->getAuthenticationToken();
        ['email' => $emailEntered] = $securityToken->getCredentials();

        $this->securityLogger->info("Unutilisateur ayany adr IP: '{$userIp}' a tenté d authentifier sans succés avec l email {$emailEntered} :  ");
    }

    public function onSecurityAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        [
            'userIP' => $userIp,
            'route_name' => $routeName
        ] = $this->getRouteNameAndUserIP();

        // onverifie $route name si il est null cad anonimous sinon c un user authentifier
        if (empty($event->getAuthenticationToken()->getRoleNames())) {
            $this->securityLogger->info("Oh, l'utilisateur anonyme ayant adr IP: '{$userIp}' est apparu sur la route {$routeName} :-) ");
        }


        /** @var TokenInterface $securityToken    */
        $securityToken = $event->getAuthenticationToken();
        $userEmail = $securityToken->getUsername();
        $this->securityLogger->info("Un user ayant adr IP  {$userIp} a l email {$userEmail}");
        //dd($userEmail);
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        ['userIP' => $userIp] = $this->getRouteNameAndUserIP();

        /** @var TokenInterface $securityToken    */
        $securityToken = $event->getAuthenticationToken();

        $userEmail = $securityToken->getUsername();
        $this->securityLogger->info("Un user ayant adr IP  {$userIp} a l email {$userEmail}.  function Interactive login");
    }

    public function OnSecurityLogout(LogoutEvent $event): void
    {
        /** @var RedirectResponse|null $response */
        $response = $event->getResponse();

        /** @var TokenInterface $securityToken    */
        $securityToken = $event->getToken();

        if (!$response || !$securityToken) {
            return;
        }

        ['userIP' => $userIp] = $this->getRouteNameAndUserIP();

        $userEmail = $securityToken->getUsername();

        $targetUrl = $response->getTargetUrl();
        $this->securityLogger->info("Un user ayant adr IP  {$userIp} a l email {$userEmail}.  se deconnecter est se derige vers  {$targetUrl} ");
    }

    public function onSecurityLogoutOnChange(DeauthenticatedEvent $event): void
    {
        // ...
    }

    public function onSecuritySwitchUser(SwitchUserEvent $event): void
    {
        // ...
    }


    /**
     *  Methode recupere  ip la nom de route de user pointer
     *
     * @return  array{userIP: string|null, route_name: mixed}
     */
    public function getRouteNameAndUserIP(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return [
                'userIP'   => 'Inconnue',
                'route_name' => 'Inconnue'
            ];
        }

        return [
            'userIP'   => $request->getClientIp() ?? 'inconnue',
            'route_name' => $request->attributes->get('_route')
        ];
    }
}
