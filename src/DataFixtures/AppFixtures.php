<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    private SluggerInterface $slugger;
    private TagAwareCacheInterface $cache;

    public function __construct(UserPasswordHasherInterface $hasher, SluggerInterface $slugger, TagAwareCacheInterface $cache)
    {
        $this->hasher = $hasher;
        $this->slugger = $slugger;
        $this->cache = $cache;
    }

    public function load(ObjectManager $manager): void
    {
        $this->cache->invalidateTags(['productsCache']);

        $faker = Factory::create('fr_FR');

        for ($i=1; $i <= 50; $i++) {
            $product = new Product();
            $product
                ->setName("Produit $i")
                ->setDescription($faker->text(500))
                ->setPrice($faker->numberBetween(1000, 199999))
            ;

            $manager->persist($product);
        }

        $customersList = [];
        for ($i=1; $i <= 5; $i++) {
            $customer = new Customer();
            $customer
                ->setName("Customer $i")
                ->setEmail("customer$i@gmail.com")
                ->setPassword($this->hasher->hashPassword($customer, 'password'))
            ;

            $manager->persist($customer);
            $customersList[] = $customer;
        }

        for ($i=1; $i <= 50; $i++) {
            $user = new User();
            $firstname = $faker->firstName();
            $lastname = $faker->lastName();
            $username = strtolower($this->slugger->slug($firstname.' '.$lastname, '.'));
            $email = "$username@gmail.com";
            $user
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setUsername($username)
                ->setEmail($email)
                ->setCustomer($faker->randomElement($customersList))
            ;

            $manager->persist($user);
        }

        $manager->flush();
    }
}
