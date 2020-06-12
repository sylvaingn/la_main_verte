<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class CompanyFixtures extends Fixture implements DependentFixtureInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository, CompanyRepository $companyRepository)
    {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
    }

    public function load(ObjectManager $manager)
    {

        // On configure dans quelles langues nous voulons nos données
        $faker = Faker\Factory::create('fr_FR');

        // on créé 10 adresses
        for ($i = 0; $i < 100; $i++) {
            $company = new Company();

            $company->setName($faker->company);
            $company->setSiret($faker->siret);
            $company->setDescription($faker->realText);
            $company->setValidated("true");
            $company->setUser($this->userRepository->find(rand(1,1000)));

            

            $manager->persist($company);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
