<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Team;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

        // Création Utilisateur //

        $users = [];

        for ($u = 0; $u < 10; $u++) {

            $user = new User();
            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');

            
            $users[] = $user;

            $manager->persist($user);
        }



        // Articles //
        $articles = [];
        for ($i = 0; $i < 50; $i++) {
            $article = new Article();
            $article->setTitle($this->faker->word()) // Changed $this->faker->words() to implode the words
                ->setContent($this->faker->paragraphs(rand(2, 5), true)) // Adjusted the range of paragraphs
                ->setUser($users[mt_rand(0, count($users)-1)]);// Add a User to the Article 

            $articles[] = $article;
            $manager->persist($article);
        }


        // Team //
        for ($j = 0; $j < 25; $j++) {
            $team = new Team();
            $team->setName($this->faker->word()) // Changed $this->faker->words() to implode the words
                ->setDescription($this->faker->paragraphs(rand(2, 5), true)) // Adjusted the range of paragraphs
                ->setTime(mt_rand(0, 1) == 1 ? mt_rand(1, 1440) : null)
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
                ->setIsPublic(mt_rand(0, 1) == 1 ? true : false)
                ->setUser($users[mt_rand(0, count($users)-1)]);// Add a User to the Team
            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $team->addArticle($articles[mt_rand(0, count($articles) - 1)]);
            }

            $manager->persist($team);
        }



        $manager->flush();
    }
}
