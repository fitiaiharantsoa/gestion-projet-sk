<?php

namespace App\Service;

use App\Entity\UserTrustedDevice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TrustedDeviceManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function addTrustedDevice(UserInterface $user, string $deviceToken, string $userAgent, \DateTimeImmutable $expiresAt): void
    {
        $trustedDevice = new UserTrustedDevice();
        $trustedDevice->setOwner($user);
        $trustedDevice->setDeviceToken($deviceToken);
        $trustedDevice->setUserAgent($userAgent);
        $trustedDevice->setCreatedAt(new \DateTimeImmutable());
        $trustedDevice->setExpiresA($expiresAt);

        $this->em->persist($trustedDevice);
        $this->em->flush();
    }

    public function isTrustedDevice(UserInterface $user, string $deviceToken): bool
    {
        $repo = $this->em->getRepository(UserTrustedDevice::class);
        $trustedDevice = $repo->findOneBy([
            'owner' => $user,
            'deviceToken' => $deviceToken,
        ]);

        if (!$trustedDevice) {
            return false;
        }

        // Check if not expired
        return $trustedDevice->getExpiresA() > new \DateTimeImmutable();
    }
}
