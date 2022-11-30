<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminCommand extends Command
{
    protected $entityManagerInterface;
    protected $passwordHasher;

    public function __construct(EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->passwordHasher = $passwordHasher;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('app:create-admin')
             ->setDescription('Create an admin user')
             ->setHelp('Commande permettant de créer un utilisateur admin')
             ->setHidden(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion('Voulez-vous créer un compte Administrateur ?', ['Oui', 'Non'], 'Oui');
        $question->setErrorMessage('Réponse incorrecte');

        if ($helper->ask($input, $output, $question) === 'Non') {
            $io->info('Création annulée');
            return Command::FAILURE;
        }

        $io->title("Création de l'utilisateur admin");

        $question = new Question("Saisissez le prénom souhaité : ");
        $firstName = $helper->ask($input, $output, $question);

        $question = new Question("Saisissez le nom souhaité : ");
        $lastName = $helper->ask($input, $output, $question);

        $question = new Question("Saisissez le mot de passe souhaité : ");
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $password = $helper->ask($input, $output, $question);

        $question = new Question("Saisissez l'email souhaité : ");

        $question->setValidator(function ($answer) {
            if (!is_string($answer) || !filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException(sprintf('L\'adresse email %s n\'est pas valide', $answer));
            }

            return $answer;
        });

        $email = $helper->ask($input, $output, $question);

        $question = new ChoiceQuestion('Genre ?', [User::FEMMME, User::HOMME, User::AUTRE], null);
        $question->setErrorMessage('Réponse incorrecte');
        $gender = $helper->ask($input, $output, $question);

        $user = new User();
        $user->setFirstName($firstName)
             ->setLastName($lastName)
             ->setPassword($this->passwordHasher->hashPassword($user, $password))
             ->setEmail($email)
             ->setGender($gender)
             ->setRoles([User::ROLE_ADMIN]);

        $this->entityManagerInterface->persist($user);
        $this->entityManagerInterface->flush();

        $io->success("L'utilisateur admin a bien été créé");

        return Command::SUCCESS;
    }
}