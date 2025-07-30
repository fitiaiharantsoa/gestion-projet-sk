<?php
namespace App\EventListener;

use App\Repository\UserTrustedDeviceRepository;
use Scheb\TwoFactorBundle\Security\TwoFactor\Condition\TwoFactorConditionInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\AuthenticationContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
// TrustedDeviceBypassListener
class TrustedDeviceBypassListener implements TwoFactorConditionInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private UserTrustedDeviceRepository $trustedDeviceRepo,
    ) {}

    public function shouldPerformTwoFactorAuthentication(AuthenticationContextInterface $context): bool
    {
        $user = $context->getToken()->getUser();
        $request = $this->requestStack->getCurrentRequest();
        $cookie = $request->cookies->get('trusted_device_token');

        if ($cookie && $user) {
            $device = $this->trustedDeviceRepo->findOneBy([
                'owner' => $user,
                'deviceToken' => $cookie,
            ]);
            if ($device && $device->getExpiresA() > new \DateTimeImmutable()) {
                return false; // 2FA bypassé
            }
        }

        return true; // Sinon, 2FA requis
    }
}
