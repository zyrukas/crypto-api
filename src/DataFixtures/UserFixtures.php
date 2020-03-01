<?php

namespace App\DataFixtures;

use App\Entity\Asset;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     *
     * @return void
     * @throws \Exception
     */
    public function load($manager): void
    {
        $user = (new User())
            ->setEmail('demo+' . \microtime(true) . '@example.com')
            ->setRoles([User::ROLE_USER])
            ->setApiToken('6c4d3cbb569fa002d525b2302d4cf546');
        $manager->persist($user);

        $asset = (new Asset())
            ->setUser($user)
            ->setLabel('binance')
            ->setCurrency(Asset::AVAILABLE_CURRENCIES[\array_rand(Asset::AVAILABLE_CURRENCIES)])
            ->setValue(\random_int(1, 100))
            ->setUid(\md5(\uniqid()));
        $manager->persist($asset);

        $manager->flush();
    }
}
