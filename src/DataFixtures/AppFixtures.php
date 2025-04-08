<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Status;
use App\Entity\Task;
use App\Entity\User;
use DateInterval;
use DateInvalidOperationException;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {}

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws DateInvalidOperationException
     */
    public function load(ObjectManager $manager): void
    {
        // Status
        $todo = new Status()->setName('To Do');
        $manager->persist($todo);

        $doing = new Status()->setName('Doing');
        $manager->persist($doing);

        $done = new Status()->setName('Done');
        $manager->persist($done);

        // User
        $user1 = new User()
            ->setEmail('natalie@driblet.com')
            ->setLastName('Dillon')
            ->setFirstName('Natalie')
            ->setContrat('CDI')
            ->setContratStartDate(new DateTime('2019-06-14'));
        $password = $this->hasher->hashPassword($user1, 'pass_1234');
        $user1->setPassword($password);
        $manager->persist($user1);

        $user2 = new User()
            ->setEmail('demi@driblet.com')
            ->setLastName('Baker')
            ->setFirstName('Demi')
            ->setContrat('CDD')
            ->setContratStartDate(new DateTime('2022-09-01'));
        $password = $this->hasher->hashPassword($user2, 'pass_1234');
        $user2->setPassword($password);
        $manager->persist($user2);

        $user3 = new User()
            ->setEmail('marie@driblet.com')
            ->setLastName('Dupont')
            ->setFirstName('Marie')
            ->setContrat('Freelance')
            ->setContratStartDate(new DateTime('2021-12-20'));
        $password = $this->hasher->hashPassword($user3, 'pass_1234');
        $user3->setPassword($password);
        $manager->persist($user3);

        // Project
        $project1 = new Project()
            ->setName('TaskLinker')
            ->addUser($user1)
            ->addUser($user2);
        $manager->persist($project1);

        $project2 = new Project()
            ->setName('Application mobile Grand Nancy')
            ->addUser($user2)
            ->addUser($user3);
        $manager->persist($project2);

        $project3 = new Project()
            ->setName('Site vitrine Les Soeurs Marchand')
            ->addUser($user1)
            ->addUser($user3);
        $manager->persist($project3);

        // Task
        $task1 = new Task()
            ->setName('Développement de la structure globale')
            ->setDescription('Intégrer les maquettes')
            ->setStatus($done)
            ->setUser($user2)
            ->setProject($project1)
            ->setDeadline(new DateTime()->sub(new DateInterval('P7D')));
        $manager->persist($task1);

        $task2 = new Task()
            ->setName('Développement de la page projet')
            ->setDescription('Page projet avec liste des tâches, édition, modification, suppression et création des tâches')
            ->setStatus($done)
            ->setUser($user1)
            ->setProject($project1);
        $manager->persist($task2);

        $task3 = new Task()
            ->setName('Développement de la page employé')
            ->setDescription('Page employé avec liste des employés, édition, modification, suppression et création des employés')
            ->setStatus($doing)
            ->setUser($user2)
            ->setProject($project1)
            ->setDeadline(new DateTime()->add(new DateInterval('P4D')));
        $manager->persist($task3);

        $task4 = new Task()
            ->setName('Gestion des droits d\'accès')
            ->setDescription('Un employé ne peut accéder qu\'à ses projets')
            ->setStatus($todo)
            ->setProject($project1)
            ->setDeadline(new DateTime()->add(new DateInterval('P12D')));
        $manager->persist($task4);

        $task5 = new Task()
            ->setName('Déploiement sur l\'App Store')
            ->setDescription('Vérifier avant que tout fonctionne bien !')
            ->setStatus($todo)
            ->setProject($project2);
        $manager->persist($task5);

        $task6 = new Task()
            ->setName('Réalisation des maquettes')
            ->setDescription('À faire sur Figma')
            ->setStatus($doing)
            ->setUser($user3)
            ->setProject($project3)
            ->setDeadline(new DateTime()->sub(new DateInterval('P18D')));
        $manager->persist($task6);

        $task7 = new Task()
            ->setName('Intégration des maquettes')
            ->setDescription('Bien faire attention au responsive')
            ->setStatus($todo)
            ->setProject($project3)
            ->setUser($user1);
        $manager->persist($task7);

        $task8 = new Task()
            ->setName('Optimisation du référencement')
            ->setStatus($todo)
            ->setDeadline(new DateTime()->sub(new DateInterval('P35D')))
            ->setProject($project3);
        $manager->persist($task8);

        $manager->flush();
    }
}
