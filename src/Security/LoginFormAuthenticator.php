<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthenticator extends AbstractAuthenticator
{
    protected $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }
    /**
     * If the route is security_login and the request method is POST, then return true.
     * 
     * @param Request request The current request object.
     * 
     * @return ?bool The return value is a boolean.
     */
    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'security_login' && $request->isMethod('POST');
    }

    /**
     * It takes a request, extracts the email and password from it, and returns a Passport object
     * 
     * @param Request request The request object.
     * 
     * @return Passport A Passport object
     */
    public function authenticate(Request $request): Passport
    {
        try {
            $credentials = $request->request->get('login');
            return new Passport(new UserBadge($credentials['email']), new PasswordCredentials($credentials['password']));
        } catch (\Exception $e) {
            throw new AuthenticationException('L\'adresse mail ou le mot de passe est incorrect');
        }
    }

    /**
     * If the user is successfully authenticated, redirect them to the homepage
     * 
     * @param Request request The request that resulted in an AuthenticationException
     * @param TokenInterface token The token that was used to authenticate the user.
     * @param string firewallName The name of the firewall that was used to authenticate the user.
     * 
     * @return ?Response A RedirectResponse object.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse("/");
    }

    /**
     * If the user fails to login, redirect them to the login page
     * 
     * @param Request request The request that resulted in an AuthenticationException
     * @param AuthenticationException exception The exception that was thrown to cause this authentication
     * exception
     * 
     * @return ?Response A RedirectResponse object.
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        return new RedirectResponse($this->generator->generate('security_login'));
    }
}
