<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Repository\ContenuPanierRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SuperAdminController extends AbstractController
{
    /**
     * @Route("/super/admin", name="super_admin", methods={"GET"})
     */
    public function index(UserRepository $userRepository, ContenuPanierRepository $contenuPanierRepository )
    {
        $NonAchete = $this->getDoctrine()->getRepository(Panier::class)->findBy(['etat' => false]);
       
        return $this->render('super_admin/index.html.twig', [
            'NonAchete' => $NonAchete,
            'contenu_paniers' => $contenuPanierRepository->findBy(['panier' => $NonAchete ]),
            'userRepository'  => $userRepository->findAll()
        ]);
    }
}
