<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Product;
use App\Form\ClientType;
use App\Form\ProductType;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;


//#[Route('/product')]
class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/product/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
    #[Route('/product/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'client' => $product,
            'form' => $form,
        ]);
    }
//    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
//    public function new(Request $request, ProductRepository $productRepository): Response
//    {
//        {
//            $product = new Product();
//            $form = $this->createForm(ProductType::class, $product);
//            $form->handleRequest($request);
//
//            if ($form->isSubmitted() && $form->isValid()) {
//                /** @var UploadedFile $brochureFile */
//                $brochureFile = $form->get('brochure')->getData();
//
//                // this condition is needed because the 'brochure' field is not required
//                // so the PDF file must be processed only when a file is uploaded
//                if ($brochureFile) {
//                    $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
//                    // this is needed to safely include the file name as part of the URL
//                    $safeFilename = $slugger->slug($originalFilename);
//                    $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
//
//                    // Move the file to the directory where brochures are stored
//                    try {
//                        $brochureFile->move(
//                            $this->getParameter('brochures_directory'),
//                            $newFilename
//                        );
//                    } catch (FileException $e) {
//                        // ... handle exception if something happens during file upload
//                    }
//
//                    // updates the 'brochureFilename' property to store the PDF file name
//                    // instead of its contents
//                    $product->setImage($newFilename);
//                }
//
//                // ... persist the $product variable or any other work
//
//                return $this->redirectToRoute('app_product_list');
//            }
//
//            return $this->render('product/new.html.twig', [
//                'form' => $form->createView(),
//            ]);
//
//        }
//    }

    #[Route('/product/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/product/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/ApiProduct', name: 'app_API', methods: ['GET', 'POST'])]
    public function convertToJson(UploaderHelper $uploaderHelper)
    {
        $categoryRepository = $this->entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $categoryList = [];

        foreach ($categories as $category) {
            $categoryData = [
                'category' => [
                    'id' => $category->getId(),
                    'name' => $category->getName()
                ],
                'product' => []
            ];

            foreach ($category->getProduct() as $product) {
                $productData = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrix(),
                    'description' => $product->getDescription(),
                  'image' => $uploaderHelper->asset($product, 'imageFile')

                ];

                $categoryData['product'][] = $productData;
            }

            $categoryList[] = $categoryData;
        }

        return $this->json($categoryList);
    }

}
