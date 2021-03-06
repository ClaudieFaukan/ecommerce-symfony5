<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $passwordHasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher)
    {
        $this->slugger = $slugger;
        $this->passwordHasher = $passwordHasher;
    }

    //RAPPEL: lancer la commande de fixture avec php bin/console doctrine:fixtures:load

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        //creation admin
        $admin = new User;
        $hash = $this->passwordHasher->hashPassword($admin, "admin");

        $admin->setEmail('admin@admin.fr')
            ->setFullName("admin")
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        //Creation fake user
        $users = [];
        for ($u = 0; $u < 5; $u++) {

            $user = new User;
            $hash = $this->passwordHasher->hashPassword($user, 'password');
            $user->setEmail("user$u@gmail.com");
            $user->setPassword($hash);
            $user->setFullName($faker->name);

            //sert pour les fake purchases
            $users[] = $user;

            $manager->persist($user);
        }

        //Creation Fake Category
        for ($c = 0; $c < 3; $c++) {

            $category = new Category();
            $category->setName($faker->department);

            $manager->persist($category);

            //Creation Fake products de cette categorie
            $products = [];
            for ($i = 0; $i < mt_rand(10, 20); $i++) {
                $product = new Product();
                $product->setName($faker->productName)
                    ->setPrice($faker->price(400, 200000))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph)
                    ->setMainPicture($faker->imageUrl(400, 400, true));

                $manager->persist($product);
                $products[] = $product;
            }
        }
        //creation fake purchase
        for ($p = 0; $p < mt_rand(20, 40); $p++) {

            $purchase = new Purchase;

            $purchase->setFullName($faker->name)
                ->setAddress($faker->streetAddress)
                ->setCity($faker->city)
                ->setPostalCode($faker->postcode)
                ->setUser($faker->randomElement($users))
                ->setTotal(mt_rand(2000, 30000))
                ->setPurchasedAt($faker->dateTimeBetween('-6 month'));

            $selectedProducts =  $faker->randomElements($products, mt_rand(3, 5));

            foreach ($selectedProducts as $product) {

                $purchaseItem = new PurchaseItem;

                $purchaseItem->setProduct($product)
                    ->setProductName($product->getName())
                    ->setQuantity(mt_rand(1, 3))
                    ->setProductPrice($product->getPrice())
                    ->setTotal($purchaseItem->getProductPrice() * $purchaseItem->getQuantity())
                    ->setPurchase($purchase);

                $manager->persist($purchaseItem);
            }

            if ($faker->boolean(90)) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            }

            $manager->persist($purchase);
        }

        $manager->flush();
    }
}
