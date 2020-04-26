<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Repository\ContenuPanierRepository;
use App\Repository\PanierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CompteController extends AbstractController
{
    /**
     * @Route("/compte", name="compte")
     */
    public function index()
    {
        $historiques = $this->getDoctrine()->getRepository(Panier::class)->findBy(['utilisateur' => $this->getUser(), 'etat' => true]);

        return $this->render('compte/index.html.twig', [
            
        'historiques' => $historiques
        ]);
    }
       /**
     * @Route("/compte/{id}", name="SelectPanier")
     */
    public function SelectPanier(PanierRepository $panierRepository)
    {
        return $this->render('compte/show.html.twig', [
            'panierRepository' => $panierRepository
        ]);
    }
}
