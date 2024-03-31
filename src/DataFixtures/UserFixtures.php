<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setName('Admin');
        $admin->setFirstname('admin');
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $passwordAdmin = $this->hasher->hashPassword($admin, 'adminadmin');
        $admin->setPassword($passwordAdmin);
        $admin->setRegistrationDate(new \DateTime('now', new DateTimeZone('Europe/Paris')));
        $manager->persist($admin);

        $superAdmin = new User();
        $superAdmin->setName('SUPER');
        $superAdmin->setFirstname('Admin');
        $superAdmin->setEmail('superadmin@superadmin.com');
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $passwordSuperAdmin = $this->hasher->hashPassword($superAdmin, 'superadmin');
        $superAdmin->setPassword($passwordSuperAdmin);
        $superAdmin->setRegistrationDate(new \DateTime('now', new DateTimeZone('Europe/Paris')));
        $manager->persist($superAdmin);

        $manager->flush();
    }
}