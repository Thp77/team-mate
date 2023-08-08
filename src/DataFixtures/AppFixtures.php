<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Generator;

class AppFixtures extends Fixture
{

    /**
     * @var Generator
     */
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 50; $i++) {
            $article = new Article();
            $article->setTitle($this->faker->word()) // Changed $this->faker->words() to implode the words
                ->setContent($this->faker->paragraphs(rand(2, 5), true)); // Adjusted the range of paragraphs

            $manager->persist($article);
        }

        $manager->flush();
    }
}
