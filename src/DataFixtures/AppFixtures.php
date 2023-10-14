<?php

namespace App\DataFixtures;

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
        $this->makeMovies(30);
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
}
