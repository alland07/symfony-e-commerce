<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Form\ContenuPanierType;
use App\Form\ProduitType;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/produit")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
     
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="produit_new", methods={"GET","POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $fichier = $form->get('photo')->getData();

            if($fichier){
                $nomFile = uniqid().'.'.$fichier->guessExtension();

                try{
                    $fichier->move(
                        $this->getParameter('upload'),
                        $nomFile
                    );
                }
                catch(FileException $e){
                    $this->addFlash('danger', $translator->trans('flash.photoValide'));
                    return $this->redirectToRoute('produit_index');
                }
                $produit->setPhoto($nomFile);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produit_index');
            $this->addFlash('success', $translator->trans('flash.produitsuccess'));
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="produit_show", methods={"GET","POST"})
     */
    public function show( Produit $produit=null, Request $request, PanierRepository $panierRepository, TranslatorInterface $translator): Response
    {
        
        if($produit != null){

            $entityManager= $this->getDoctrine()->getManager();

            if($panierRepository->findOneBy(['utilisateur' => $this->getUser(), 'etat' => false ]) == false){

                $panier = new Panier();
                $panier->setUtilisateur($this->getUser());
                $panier->setAchat(new \DateTime());
               
                $entityManager->persist($panier);
                $entityManager->flush();

            }
            else{
                $panier = $panierRepository->findOneBy(['utilisateur' => $this->getUser(), 'etat' => false]);
            }

            $contenuPanier = new ContenuPanier();
            $form = $this->createForm(ContenuPanierType::class, $contenuPanier);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $contenuPanier->setProduit($produit);
                $contenuPanier->setPanier($panier);
                $contenuPanier->setDate(new \DateTime());
                $entityManager->persist($contenuPanier);
                $entityManager->flush(); 
                $this->addFlash('success', $translator->trans('flash.ajoutpanier'));
                return $this->redirectToRoute('contenu_panier_index');
            }
       
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'ajout_article' => $form->createView(),
        ]);
        }
        
        else{       
            $this->addFlash('eror', $translator->trans('flash.errorajout'));
            return $this->redirectToRoute('produit_index');
    
        }
}

    /**
     * @Route("edit/{id}", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $translator->trans('flash.produitEdit'));
            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="produit_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Produit $produit, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }
        $this->addFlash('success', $translator->trans('flash.produitDelete'));
        return $this->redirectToRoute('produit_index');
    }

 

    // voir si le produit existe
     // voir si l'user a un panier qui a l"etat false
     // si c false ca créé un new panier et ca integrer l'idée de luser
     //  si true on recup le panier
     
}

