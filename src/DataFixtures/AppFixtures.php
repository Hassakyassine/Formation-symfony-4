<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
//use Cocur\Slugify\Slugify;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder ;

    public function __construct(UserPasswordEncoderInterface $encoder){
       $this->encoder = $encoder; 
    }


    public function load(ObjectManager $manager)
    {
       $faker = Factory::create('fr-FR') ;

       $adminRole = new Role() ; 
       $adminRole->setTitle('ROLE_ADMIN') ; 
       $manager->persist($adminRole) ; 

       $adminUser = new User() ;
       $adminUser->setFirstName('Hassak')
                 ->setLastName('Yacine') 
                 ->setEmail('Hassakyacine@domaine.fr')
                 ->setHash($this->encoder->encodePassword($adminUser,'password'))
                 ->setPicture('https://randomuser.me/api/portraits/lego/3.jpg')
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>' .join('</p><p>', $faker->paragraphs(3)) . '</p>')
                 ->addUserRole($adminRole) ; 
      $manager->persist($adminUser) ;
       
       $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

      // Nous gérer les users
      $users = []  ; 
      $genres = ['male' ,'female']  ;

      for( $i =1 ; $i<=10 ; $i++){
         $user = new User() ;

         $genre = $faker->randomElement($genres) ; 

         $picture = 'https://randomuser.me/api/portraits/' ;
         $pictureId = $faker->numberBetween(1,99) . '.jpg' ;

         $picture .=($genre == 'male' ? 'men/' : 'women/') . $pictureId ;

         $hash = $this->encoder->encodePassword($user, 'password') ;

         $user->setFirstName($faker->firstname)
              ->setLastName($faker->lastname)
              ->setEmail($faker->email)
              ->setIntroduction($faker->sentence())
              ->setDescription('<p>' .join('</p><p>', $faker->paragraphs(3)) . '</p>')
              ->setHash($hash) 
              ->setPicture($picture) ;
         
         $manager->persist($user) ;
         $users[] = $user ;     
      }


     //  $slugify = new Slugify() ;
       //Nous gérons les anonces

       for($i = 1; $i <= 30; $i++){
        $ad = new Ad();

        $title = $faker->sentence();
       // $slug = $slugify->slugify($title) ;
        $coverImage = $faker->imageUrl(1000,350,true) ;
        $introduction = $faker->paragraph(2);
        $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>' ;

        $user = $users[mt_rand(0, count($users) - 1)] ;

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
           ->setRooms(mt_rand(1,5)) 
           ->setAuthor($user) ;

           for($j=1; $j <= mt_rand(2,5); $j++){
               $image = new Image() ; 
            
               $image->setUrl($faker->imageUrl())
                     ->setCaption($faker->sentence())
                     ->setAd($ad) ; 

                $manager->persist($image) ; 
           }

           // Gestion des réservation 
            for($j =1 ; $j <= mt_rand(0, 10); $j++){
               $booking = new Booking() ;

               $createdAt = $faker->dateTimeBetween('-6 months') ;
               $startDate = $faker->dateTimeBetween('-3 months') ; 

               // Gestion de la date de fin 

               $duration = mt_rand(3,10) ; 

               $endDate = (clone $startDate)->modify("+$duration days") ;
               $amount = $ad->getPrice() * $duration ;

               $booker  = $users[mt_rand(0, count($users) -1)] ;

               $comment = $faker->paragraph() ;

               $booking->setBooker($booker)
                       ->setAd($ad)
                       ->setStartDate($startDate)
                       ->setEndDate($endDate)
                       ->setCreatedAt($createdAt)
                       ->setAmount($amount) 
                       ->setComment($comment) ;
               
               $manager->persist($booking) ;

               // Gestion des commentaires 
               if(mt_rand(0,1)){
                  $comment = new Comment() ; 
                  $comment->setContent($faker->paragraph())
                          ->setRating(mt_rand(1, 5))
                          ->setAuthor($booker)
                          ->setAd($ad) ;
                          
                  $manager->persist($comment) ;        
               }
            }
            
        $manager->persist($ad) ;

        $manager->flush();
       }
    }
}
