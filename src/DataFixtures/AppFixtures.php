<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
//use Cocur\Slugify\Slugify;
use App\Entity\Image;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
       $faker = Factory::create('fr-FR') ;
       $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

     //  $slugify = new Slugify() ;
       
       for($i = 1; $i <= 30; $i++){
        $ad = new Ad();

        $title = $faker->sentence();
       // $slug = $slugify->slugify($title) ;
        $coverImage = $faker->imageUrl(1000,350,true) ;
        $introduction = $faker->paragraph(2);
        $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>' ;

       // $ad->setTitle("Titre de l'annonce N°$i") ==> simple
       $ad->setTitle($title)
          // ->setSlug("titre-de-l-annonce-n-$i")
         // ->setSlug($slug)
          // ->setCoverImage("http://placehold.it/1000x300")
          ->setCoverImage($coverImage)
         //  ->setIntroduction("Bonjout a tous c'est une introduction")
         ->setIntroduction($introduction)
         //  ->setContent("<p>Je suis un contenu riche !</p>")
         ->setContent($content)
           ->setPrice(mt_rand(500,2000))
           ->setRooms(mt_rand(1,5)) ;

           for($j=1; $j <= mt_rand(2,5); $j++){
               $image = new Image() ; 
            
               $image->setUrl($faker->imageUrl())
                     ->setCaption($faker->sentence())
                     ->setAd($ad) ; 

                $manager->persist($image) ; 
           }
            
        $manager->persist($ad) ;

        $manager->flush();
       }
    }
}
