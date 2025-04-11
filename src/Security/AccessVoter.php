<?php

namespace App\Security;

use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\CacheableVoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessVoter extends Voter implements CacheableVoterInterface
{
    const string ACCESS_PROJECT = 'access_project';
    const string ACCESS_TASK    = 'access_task';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
        private readonly ProjectRepository $projectRepository,
        private readonly TaskRepository $taskRepository,
    ) {
    }

    protected function supports(string $attribute, mixed $subject) : bool
    {
        return in_array($attribute, [self::ACCESS_TASK, self::ACCESS_PROJECT]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token) : bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        return match ($attribute) {
            self::ACCESS_TASK => $this->taskVoter($subject, $user),
            self::ACCESS_PROJECT => $this->projectVoter($subject, $user),
            default => false
        };
    }

    private function taskVoter(int $id, UserInterface $user) : bool
    {
        $task = $this->taskRepository->find($id);

        if(!$task || !$task->getProject()->isActive() || !$task->getProject()->getUser()->contains($user)) {
            return false;
        }

        return true;
    }

    private function projectVoter(int $id, UserInterface $user) : bool
    {
        $project = $this->projectRepository->find($id);

        if(!$project || !$project->isActive() || !$project->getUser()->contains($user)) {
            return false;
        }

        return true;
    }
}
