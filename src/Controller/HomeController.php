<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/createProduct", name="createProduct")
     */
    public function createProduct(Request $request, EntityManagerInterface $manager)
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product, ['add' => true]);

        $form->handleRequest($request);
        //dd($form->getErrors());
        if ($form->isSubmitted() && $form->isValid()):

            //dd('coucou');
            $picture = $form->get('picture')->getData();

            $pictureName = date('YmdHis') . uniqid() . $picture->getClientOriginalName();
            $picture->move($this->getParameter('uploads'), $pictureName);

            $product->setPicture($pictureName);
            $manager->persist($product);
            $manager->flush();
            $this->addFlash('success', 'Produit ajouté!!!');
            return $this->redirectToRoute('listProduct');
        endif;


        return $this->render('home/createProduct.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/updateProduct/{id}", name="updateProduct")
     */
    public function updateProduct(Request $request, EntityManagerInterface $manager, Product $product)
    {

        $form = $this->createForm(ProductType::class, $product, ['update' => true]);

        $form->handleRequest($request);
        //dd($form->getErrors());
        if ($form->isSubmitted() && $form->isValid()):

            //dd('coucou');
            $picture = $form->get('pictureUpdate')->getData();


            if ($picture):
                $pictureName = date('YmdHis') . uniqid() . $picture->getClientOriginalName();
                $picture->move($this->getParameter('uploads'), $pictureName);
                $product->setPicture($pictureName);
            endif;

            $manager->persist($product);
            $manager->flush();
            $this->addFlash('success', 'Produit modifié!!!');
            return $this->redirectToRoute('listProduct');
        endif;

        return $this->render('home/updateProduct.html.twig',[
            'form'=>$form->createView(),
            'product'=>$product
        ]);
    }

    /**
     * @Route("/listProduct", name="listProduct")
     */
    public function listProduct(ProductRepository $repository)
    {

        $products=$repository->findAll();

        return $this->render('home/listProduct.html.twig',[
            'products'=>$products
        ]);
    }


    /**
     * @Route("/deleteProduct/{id}", name="deleteProduct")
     */
    public function deleteProduct(Product $product, EntityManagerInterface $manager)
    {

        $manager->remove($product);
        $manager->flush();

        $this->addFlash('success', 'Produit supprimé!!!');
        return $this->redirectToRoute('listProduct');

    }


}
