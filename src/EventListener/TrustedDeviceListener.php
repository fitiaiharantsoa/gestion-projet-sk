<?php

namespace App\EventListener;

use App\Entity\UserTrustedDevice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class TrustedDeviceListener
{
    private $em;
    private $requestStack;

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $trustedDeviceToken = $request->request->get('trusted_device_token');

        if (!$trustedDeviceToken) {
            // Pas de token, on ne fait rien
            return;
        }

        $user = $event->getAuthenticationToken()->getUser();

        // Vérifier si ce deviceToken existe déjà pour cet utilisateur
        $repo = $this->em->getRepository(UserTrustedDevice::class);
        $existing = $repo->findOneBy([
            'owner' => $user,
            'deviceToken' => $trustedDeviceToken,
        ]);

        if (!$existing) {
            // Création d’un nouveau UserTrustedDevice
            $trustedDevice = new UserTrustedDevice();
            $trustedDevice->setOwner($user);
            $trustedDevice->setDeviceToken($trustedDeviceToken);
            $trustedDevice->setCreatedAt(new \DateTimeImmutable());
            // On peut définir une expiration, par exemple 30 jours plus tard
            $trustedDevice->setExpiresA((new \DateTimeImmutable())->modify('+30 days'));
            // Optionnel : userAgent depuis l’entête HTTP
            $userAgent = $request->headers->get('User-Agent');
            $trustedDevice->setUserAgent($userAgent);

            $this->em->persist($trustedDevice);
            $this->em->flush();
        }

        // Sinon on ne fait rien (device déjà connu)
    }
}
