<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    //public function index()

    //public function index(AdRepository $repo , SessionInterface $session) {
     //   $repo = $this->getDoctrine()->getRepository(Ad::class) ;
     public function index(AdRepository $repo) {
      //  dump($session) ;

        $ads = $repo->findAll() ;

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * Permet de créer une formulaire d'annoce
     * 
     * @Route("/ads/new", name="ads_create")
     *
     * @return response
     */
    public function create(Request $request, EntityManagerInterface $manager){
        $ad = new Ad() ;

     /*   $form = $this->createFormBuilder($ad)
        ->add('title')
        ->add('introduction')
        ->add('content')
        ->add('rooms')
        ->add('price')
        ->add('coverImage')
        ->add('save', SubmitType::class, [
            'label' => ' créer la nouvelle annonce' , 
            'attr' => [
                'class' =>'btn btn-primary'
            ]
        ])
        ->getForm() ;*/

        // Instancier un form externe
        // la contion createForm permet de créer un formulaire externe
        
        $form = $this->createForm(AdType::class, $ad) ;

        //permet de parcourir la requête et d'xetraire les informations du form 

        $form->handleRequest($request) ; 

                if($form->isSubmitted() && $form->isValid()){
                    foreach($ad->getImages() as $image){
                        $image->setAd($ad) ; 
                        $manager->persist($image) ;
                    }

                 //   $manager = $this->getDoctrine()->getManager() ; 

                    $manager->persist($ad) ; // sauvez
                    $manager->flush() ;  // envoie la requete sql

                    $this->addflash(
                        'success',
                        "L'annonce <strong>{$ad->getTitle()} </strong> a bien enregister"
                    ) ;

                    return $this->redirectToRoute('ads_show',[
                        'slug' => $ad->getSlug()
                    ]) ;

                }

        return $this->render('ad/new.html.twig',[
            'form' => $form->createView()
        ]) ;
    }
    
    /**
     * Permet d'afficher le formulaire d'édition 
     * 
     * @Route("/ads/{slug}/edit", name="ads_edit")
     *
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager){

        $form = $this->createForm(AdType::class, $ad) ;

        $form->handleRequest($request) ;

        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad) ; 
                $manager->persist($image) ;
            }

         //   $manager = $this->getDoctrine()->getManager() ; 

            $manager->persist($ad) ; // sauvez
            $manager->flush() ;  // envoie la requete sql

            $this->addflash(
                'success',
                "Les modifications l'annonce <strong>{$ad->getTitle()} </strong> ont bien enregister"
            ) ;

            return $this->redirectToRoute('ads_show',[
                'slug' => $ad->getSlug()
            ]) ;

        }

        return $this->render('ad/edit.html.twig',[
            'form' => $form->createView(),
            'ad' => $ad
        ]) ;
    
    }

    /**
     * Permet d'afficher une seule annonce 
     * 
     * @Route("/ads/{slug}", name="ads_show")
     *
     * @return Response
     */
   /* public function  show($slug , AdRepository $repo){
        // Je récupére l'annonce qui correspond au slug ! 
        $ad = $repo->findOneBySlug($slug) ;
        
        return $this->render('ad/show.html.twig', [
            'ad' => $ad

        ]) ;
    }*/


    //ParamConverter
    public function  show(Ad $ad){
        return $this->render('ad/show.html.twig', [
            'ad' => $ad

        ]) ;
    }
}
