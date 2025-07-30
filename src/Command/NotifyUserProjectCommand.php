<?php

namespace App\Command;

use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

#[
    AsCommand(
    name: "NotifyUserProject",
    description: "Add a short description for your command",
),
]
class NotifyUserProjectCommand extends Command
{
    public function __construct(
        private HubInterface $hub,
        private ProjectRepository $projectRepository,
        private UserRepository $userRepository,
        private TaskRepository $taskRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            "arg1",
            InputArgument::OPTIONAL,
            "Argument description",
        )->addOption(
                "option1",
                null,
                InputOption::VALUE_NONE,
                "Option description",
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument("arg1");

        if ($arg1) {
            $io->note(sprintf("You passed an argument: %s", $arg1));
        }

        if ($input->getOption("option1")) {
            // ...
        }
        // $update = new Update("http://gestionprojet.com/notif", "[]");
        // $this->hub->publish($update);
        $project = $this->projectRepository->findAll();
        foreach ($project as $key => $value) {
            $now = new DateTime('now');
            if ($value->getStatut() != "terminé") {
                if ($value->getDateFin() <= $now) {
                    $responsable = $value->getResponsable();
                    $update = new Update("http://gestionprojet.com/notif", json_encode([
                        "titre_projet" => $value->getTitre(),
                        "email_responsable" => $responsable->getEmail(),
                        "id_responsable" => $responsable->getId(),
                        "date_debut" => $value->getDateDebut()->format('d/m/y'),
                        "deadline" => $value->getDateFin()->format("d/m/Y"),
                        "status" => $value->getStatut(),
                        "Message" => "Date limite du projet atteint ! "
                    ]));
                    $this->hub->publish($update);
                }
                $task = $this->taskRepository->findBy(['project'=>$value->getId()]);
                foreach ($task as $key => $valueTask) {
                   if ($valueTask->getStatut() != "terminé") {
                    $assigne = $valueTask->getAssigne();
                    $update = new Update("http://gestionprojet.com/notif/tache", json_encode([
                        "titre_tache" => $valueTask->getTitre(),
                        "email_responsable" => $assigne->getEmail(),
                        "id_responsable" => $assigne->getId(),
                        "deadline" => $valueTask->getDateEcheance()->format("d/m/Y"),
                        "status" => $valueTask->getStatut(),
                        "Message" => "Date limite de la tâche atteint ! "
                    ]));
                    $this->hub->publish($update);
                   }
                }
            }
        }
        $io->success(
            "You have a new command! Now make it your own! Pass --help to see your options.",
        );

        return Command::SUCCESS;
    }
}
