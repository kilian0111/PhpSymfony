<?php

namespace App\DataFixtures;

use App\Entity\Song;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Faker\Factory;
class AppFixtures extends Fixture
{

    private Generator $faker;

    public function __construct() {
        $this->faker = Factory::create("fr_FR");
    }

    public function load(ObjectManager $manager): void
    {
        $today = new \DateTime();
        
        for ($i=0; $i < 100; $i++) { 
            $created = $this->faker->dateTime();
            $updated = $this->faker->dateTimeBetween($created, 'now');
            $song = new Song();
            $song->setName($this->faker->word())->setStatus("on")->setCreatedAt($created)->setUpdatedAt($updated);
            $manager->persist($song);
        }

        $manager->flush();
    }
}
