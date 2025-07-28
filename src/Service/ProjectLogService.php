<?php
namespace App\Service;

use App\Entity\Project;
use App\Entity\ProjectLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProjectLogService
{
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function log(Project $project, string $message): void
    {
        $log = new ProjectLog();
        $log->setProject($project);
        $log->setMessage($message);
        $log->setCreatedAt(new \DateTimeImmutable());

        $token = $this->tokenStorage->getToken();
        if ($token) {
            $user = $token->getUser();
            if ($user !== null && $user !== 'anon.') {
                $log->setUser($user);
            }
        }

        $this->em->persist($log);
        $this->em->flush();
    }

    public function getLogsForProject(Project $project): array
{
    return $this->em->getRepository(ProjectLog::class)->findBy(
        ['project' => $project],
        ['createdAt' => 'DESC']
    );
}

}
