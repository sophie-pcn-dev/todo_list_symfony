<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Task;
use DateTimeImmutable;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i <=10; $i++){
        $task = new Task();
        $task->setTitle("Tâche".($i + 1));
        $task->setIsDone(false);
        $task->setCreatedAt(new DateTimeImmutable()); //comme current_date
        // $product = new Product();
        // $manager->persist($product);
        $manager->persist($task); //garder en mémoire : file d'attente pour envoyer dans la bdd
        $manager->flush(); // envoie tout, objet pour envoyer vers la bdd
    
        }
    }
}
