<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Entity\Panier;
use App\Repository\ContenuPanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/contenu/panier")
 */
class ContenuPanierController extends AbstractController
{
    /**
     * @Route("/", name="contenu_panier_index" , methods="GET")
     */
    public function index(ContenuPanierRepository $contenuPanierRepository): Response
    {
    

    //on recupere le panier correspondant à l'user et à l'etat non acheté
    $panier = $this->getDoctrine()->getRepository(Panier::class)->findOneBy(['utilisateur' => $this->getUser(), 'etat' => false]);
 

        return $this->render('contenu_panier/index.html.twig', [
            //grace au $panier qui recupere notre panier, on peut utiliser notre relation avec contenu panier pour retrouver le contenu lié au panier 
            'contenu_paniers' => $contenuPanierRepository->findBy(['panier' => $panier ]),
            'panier' => $panier
        ]);
    }



    /**
     * @Route("/{id}", name="contenu_panier_delete")
     */
    public function delete( ContenuPanier $contenuPanier, TranslatorInterface $translator): Response
    {
     
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contenuPanier);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('flash.baskettAdd'));
        return $this->redirectToRoute('contenu_panier_index');
    }

    /**
     * @Route("/achat/{panier}", name="achat")
     */
    public function Achat( Panier $panier, ContenuPanier $contenuPanier=null, TranslatorInterface $translator): Response
    {  
        if($contenuPanier != null){
            
            $entityManager= $this->getDoctrine()->getManager();
            $panier->addContenuPanier($contenuPanier);
            $panier->setEtat(true);
            $entityManager->persist($panier);
            $entityManager->flush(); 

            return $this->redirectToRoute('produit_index');
            $this->addFlash('success', $translator->trans('flash.achat'));
            
    }
     else{
        $this->addFlash('error', $translator->trans('flash.nobasket'));
        return $this->redirectToRoute('contenu_panier_index');
     }   

      
    }
}
