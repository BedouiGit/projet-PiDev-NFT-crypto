<?php

namespace App\DataFixtures;

use App\Entity\Commande;
use App\Entity\NFT;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\DateTime;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10000; $i++) {


            $nFT = new NFT();
            $nFT = $manager->getRepository(NFT::class)->find(mt_rand(17, 28));
            $user = new User();
            $user = $manager->getRepository(NFT::class)->find(mt_rand(3, 6));
            $commande = new Commande();
            $commande->setUser($user);
           
            $min = strtotime('2024-03-02 17:18:28');
            $max = strtotime('2024-05-22 17:18:28');
            $val = rand($min, $max);
            $date = new \DateTime("@$val");
            $commande->setDate($date);

            $commande->setEmail("this is for the ai part");
            $commande->setWallet("this is for the ai trainning :D");
            $commande->setTotal($nFT->getPrice() * mt_rand(0.4, 1.8));
            $manager->persist($commande);
        
          //  $nFT = new NFT();
          //  $nFT->setUser(NULL);
          //  $nFT->setName('product '.$i);
          //  $nFT->setPrice(mt_rand(10, 100));
          //  $nFT->setStatus('sell');
          //  $nFT->setCreationDate(new \DateTime());
          //  $nFT->setImage('FrontOffice/assets/images/collection/collection-lg-01.jpg');
          //  $nFT->setCommande(NULL);
          //  $manager->persist($nFT);

        }


        $manager->flush();
    }
}
