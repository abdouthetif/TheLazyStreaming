<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        UserFactory::new()->create([
            'roles' => ['ROLE_ADMIN'],
            'password' => 'admin',
            'email' => 'admin@admin.com',
            'firstname' => 'admin',
            'lastname' => 'admin',
            'pseudo' => 'admin'
        ]);

        UserFactory::new()->createMany(15);






        $manager->flush();
    }
}
