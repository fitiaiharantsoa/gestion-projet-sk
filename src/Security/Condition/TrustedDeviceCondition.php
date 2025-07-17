<?php

namespace App\Security\Condition;

use App\Service\TrustedDeviceManager;
use Scheb\TwoFactorBundle\Security\TwoFactor\Condition\TwoFactorConditionInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\AuthenticationContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TrustedDeviceCondition implements TwoFactorConditionInterface
{
    private TrustedDeviceManager $trustedDeviceManager;
    private RequestStack $requestStack;

    public function __construct(TrustedDeviceManager $trustedDeviceManager, RequestStack $requestStack)
    {
        $this->trustedDeviceManager = $trustedDeviceManager;
        $this->requestStack = $requestStack;
    }

    public function shouldPerformTwoFactorAuthentication(AuthenticationContextInterface $context): bool
    {
        $user = $context->getUser();
        $request = $this->requestStack->getCurrentRequest();
        
        if (!$request) {
            return true;
        }

        // Vérifier si un trusted_device_token existe dans la requête
        $trustedDeviceToken = $request->request->get('trusted_device_token');
        
        if (!$trustedDeviceToken) {
            // Pas de token, on vérifie s'il y a un cookie ou un token stocké
            $trustedDeviceToken = $request->cookies->get('trusted_device_token');
        }

        if (!$trustedDeviceToken) {
            // Générer un token basé sur l'appareil (IP + User Agent)
            $trustedDeviceToken = hash('sha256', $request->getClientIp() . $request->headers->get('User-Agent'));
        }

        // Vérifier si cet appareil est de confiance
        return !$this->trustedDeviceManager->isTrustedDevice($user, $trustedDeviceToken);
    }
}