<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints\IsTrue;

class ProductController extends Controller
{

    /**
     * @Route("/product" , name="product_index")
     */
    public function index()
    {
        $repository = $this
        ->getDoctrine()
        ->getRepository(product::class); //App\Entity\product

    $product = $repository->findAll(); //Tous les produits

    // afficher la liste des utilisateurs avec un template twig
    return $this->render('Product/product.html.twig', [
        'prod' => $product,
    ]);
    }


    /**
     * @Route("/product/{id}" ,
     * name="product_show",
     * requirements={"id": "\d+"} )
     */
    public function show($id)
    {
        $product = $this->findProductById($id);

        return $this->render('Product/detail-product.html.twig', [
            'prod' => $product,
        ]);
    }

    /**
     * @Route("/product/create" , name="product_create")
     */
    public function create(Request $request)
    {
        $product = new product();

        $form = $this->createProductForm($product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'id' => $product->getId(),
            ]);

        }

        return $this->render('Product/product-create.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }

    /**
     * @Route("/product/{id}/update" , name="product_update")
     */
    public function update(Request $request)
    {

        $product = $this->findProductById(
            $request->attributes->get('id')
        );

        $form = $this->createProductForm($product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'id' => $product->getId(),
            ]);

        }

        return $this->render('Product/edit-product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/{id}/delete" , name="product_delete")
     */
    public function delete(Request $request)
    {

        $id = $request->attributes->get('id');
        $product = $this->findProductById($id);

        $form = $this
            ->createFormBuilder()
            ->add('confirm', Type\CheckboxType::class, [
                'required' => false,
                'constraints' => [
                    new isTrue(),
                ],
            ])
            ->add('submit', Type\SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($product);
            $em->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('Product/delete-product.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    private function findProductById($id)
    {
        $repository = $this
            ->getDoctrine()
            ->getRepository(product::class); //App\Entity\Product

        $product = $repository->find($id); //Tous les groupes
        if (null === $product) {
            throw $this->createNotFoundException("Le produit est introuvable");
        }

        return $product;

    }

    private function createProductForm(Product $product)
    {
        return $this
            ->createFormBuilder($product)
            ->add('designation', Type\ChoiceType::class, [
                'choices' => [
                    'Apple' => [
                        'Iphone 4' => 'Iphone 4',
                        'Iphone 6s' => 'Iphone 6s',
                        'Iphone 10' => 'Iphone 10',
                    ],
                    'Asus' => [
                        'Zenfone 2' => 'Zenfone 2',
                        'Zenfone 3' => 'Zenfone 3',
                    ],
                    'Blackberry' => [
                        'Key²' => 'Key²',
                        'Motion' => 'Motion',
                    ],
                    'Huawei' => [
                        'P20' => 'P20',
                        'P20 Pro' => 'P20 Pro',
                        'P8' => 'P8',
                    ],
                    'LG' => [
                        'G3' => 'G3',
                        'G4' => 'G4',
                        'G5' => 'G5',
                    ],
                    'Samsung' => [
                        'Galaxy S4' => 'Galaxy S4',
                        'Galaxy S7' => 'Galaxy S7',
                        'Galaxy S9' => 'Galaxy S9',
                    ],
                    'Wiko' => [
                        'View2' => 'View2',
                        'View plus' => 'View plus',
                    ],
                ],
            ])
            ->add('reference', Type\TextType::class)
            ->add('brand', Type\ChoiceType::class ,[
                'choices'  => [
                    'Apple' => "Apple",
                    'Asus' => "Asus",
                    'Blackberry' => "Blackberry",
                    'Huawei' => "Huawei",
                    'LG' => "LG",
                    'Samsung' => "Samsung",
                    'Wiko' => "Wiko",
                ],
            ])
            ->add('price', Type\NumberType::class)
            ->add('stock', Type\IntegerType::class)
            ->add('active', Type\CheckboxType::class , [
                'required' => false
            ])
            ->add('description', Type\TextareaType::class)
            ->add('submit', Type\SubmitType::class)
            ->getForm();
    }

}
