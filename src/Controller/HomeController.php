<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

  /**
   * @Route("/hello/{name}/{age}", name="hello")
   * @Route("/salut/", name="hello_base")
   * @Route("/bonjour/{name}", name="hello_name")
   * Montre la page qui dit Bonjour
   * @return void
   */
  public function hello($name = "..." , $age = 0) {
  //  return new Response ("Bonjour ".$name . " vous avez ". $age . " ans" ) ;
    return $this->render(
      'hello.html.twig',
      [
        'name' => $name,
        'age'  => $age , 
      ]
    ) ;
  }

    /**
     * @Route("/", name="homepage")
     */
    
    public function home(AdRepository $adRepo , UserRepository $userRepo){
       /* return new Response("
        <html>
          <head>
            <title>Mon application </title>
          </head>
          <body>
            <h1>Bonjour</h1>
            <p> c'est ma premiére page symfony
          </body>
        </html>    
        ");*/
        $names = ["Yassine" =>26, "Ahmed" =>15 , "Reda" =>45] ;
        return $this->render(
            'home.html.twig', 
            [
              'title' => "Aurevoir tout le monde " , 
              'age' => 26 , 
              'tableau' => $names ,
              'ads'  => $adRepo->findBestAds(3) ,
              'users' =>$userRepo->findBestUsers(2)
            ]
        ) ;
    }
}

?>