<?php

namespace App\DataFixtures;

use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Movie;
use Faker;

class AppFixtures extends Fixture
{
    private Faker\Generator $faker;
    private ObjectManager $manager;
    public function __construct() {
        $this->faker = Faker\Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->makeMovies(60);
        $this->makePersons(60);
    }

    private function makeMovies(int $it = 40): void {
        for ($i = 0; $i < $it; $i++) {
            $movie = (new Movie())
                ->setName($this->faker->company())
                ->setDescription($this->faker->realText(300, 2))
                ->setPublicationOn($this->faker->dateTimeBetween());
            $this->manager->persist($movie);
        }
        $this->manager->flush();
    }

    private function makePersons(int $it = 40): void {
        for ($i = 0; $i < $it; $i++) {
            $person = (new Person())
                ->setFirstname($this->faker->firstName())
                ->setLastname($this->faker->lastName())
                ->setBirthday($this->faker->dateTimeBetween());
            $this->manager->persist($person);
        }
        $this->manager->flush();
    }
}
