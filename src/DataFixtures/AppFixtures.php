<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Product;
use Liior\Faker\Prices;
use Bezhanov\Faker\Provider\Commerce;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }


    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));

        //Creation Fake Category
        for ($c = 0; $c < 3; $c++) {

            $category = new Category();
            $category->setName($faker->department)
                ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            //Creation Fake products de cette categorie
            for ($i = 0; $i < mt_rand(10, 20); $i++) {
                $product = new Product();
                $product->setName($faker->productName)
                    ->setPrice($faker->price(400, 200000))
                    ->setCategory($category)
                    ->setSlug(strtolower($this->slugger->slug($product->getName())));

                $manager->persist($product);
            }
        }


        $manager->flush();
    }
}