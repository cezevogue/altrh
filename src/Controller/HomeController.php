<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
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
    public function home(ProductRepository $repository): Response
    {
        $posts=$repository->findBy([], ['createdAt'=>'DESC']);


        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'posts'=>$posts
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
            $product->setUser($this->getUser());
            $product->setCreatedAt(new \DateTime());

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

        return $this->render('home/updateProduct.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    /**
     * @Route("/listProduct", name="listProduct")
     */
    public function listProduct(ProductRepository $repository)
    {

        $products = $repository->findAll();

        return $this->render('home/listProduct.html.twig', [
            'products' => $products
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

    /**
     * @Route("/updateCategory/{id}", name="updateCategory")
     * @Route("/Category", name="category")
     *
     */
    public function category(Request $request, EntityManagerInterface $manager, CategoryRepository $repository, $id = null)
    {
        $categories = $repository->findAll();

        if ($id !== null):
            $category=$repository->find($id);
        else:

          $category=new Category();
        endif;

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        //dd($form->getErrors());
        if ($form->isSubmitted() && $form->isValid()):



            $manager->persist($category);
            $manager->flush();
            if ($id !== null):

            $this->addFlash('success', 'Catégorie modifiée!!!');
            else:

                $this->addFlash('success', 'Catégorie ajoutée!!!');

            endif;
            return $this->redirectToRoute('category');
        endif;

        return $this->render('home/category.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories
        ]);




    }

    /**
     * @Route("/deleteCategory/{id}", name="deleteCategory")
     */
    public function deleteCategory(Category $category, EntityManagerInterface $manager)
    {

        $manager->remove($category);
        $manager->flush();

        $this->addFlash('success', 'catégorie supprimée!!!');
        return $this->redirectToRoute('category');

    }

}
