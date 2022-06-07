<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Cocur\Slugify\Slugify;
use Cocur\Slugify\SlugifyInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $faker;

    private $slug;

    public function __construct(SlugifyInterface $slugify)
    {
        $this->faker = Factory::create();
         $this->slug = $slugify;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        //$manager->flush();
        $this->loadArticles($manager);
    }

    public function loadArticles(ObjectManager $manager)
    {
        for ($i = 1; $i < 20; $i++) {
            $post = new Article();
            $post->setTitle($this->faker->text(100));
            $post->setSlug($this->slug->slugify($post->getTitle()));
            $post->setBody($this->faker->text(1000));
            $post->setCreatedAt($this->faker->dateTime);

            $manager->persist($post);
        }

        $manager->flush();
    }
}
