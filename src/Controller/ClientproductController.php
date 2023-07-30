<?php

namespace App\Controller;

use App\Entity\Clientproduct;
use App\Entity\Product;
use App\Form\ClientproductType;
use App\Repository\ClientproductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/clientproduct')]
class ClientproductController extends AbstractController
{
    #[Route('/', name: 'app_clientproduct_index', methods: ['GET'])]
    public function index(ClientproductRepository $clientproductRepository): Response
    {
        return $this->render('clientproduct/index.html.twig', [
            'clientproducts' => $clientproductRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_clientproduct_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ClientproductRepository $clientproductRepository): Response
    {
        $clientproduct = new Clientproduct();
        $form = $this->createForm(ClientproductType::class, $clientproduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clientproductRepository->save($clientproduct, true);

            return $this->redirectToRoute('app_clientproduct_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('clientproduct/new.html.twig', [
            'clientproduct' => $clientproduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_clientproduct_show', methods: ['GET'])]
    public function show(Clientproduct $clientproduct): Response
    {
        return $this->render('clientproduct/show.html.twig', [
            'clientproduct' => $clientproduct,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_clientproduct_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Clientproduct $clientproduct, ClientproductRepository $clientproductRepository): Response
    {
        $form = $this->createForm(ClientproductType::class, $clientproduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clientproductRepository->save($clientproduct, true);

            return $this->redirectToRoute('app_clientproduct_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('clientproduct/edit.html.twig', [
            'clientproduct' => $clientproduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_clientproduct_delete', methods: ['POST'])]
    public function delete(Request $request, Clientproduct $clientproduct, ClientproductRepository $clientproductRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$clientproduct->getId(), $request->request->get('_token'))) {
            $clientproductRepository->remove($clientproduct, true);
        }

        return $this->redirectToRoute('app_clientproduct_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/api/orders', name: 'app_apiorder', methods: ['POST'])]
    public function createOrder(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $order = new Clientproduct();

        if (isset($data['customerName']) && !empty($data['customerName'])) {
            $order->setCustomerName($data['customerName']);
        }
        if (isset($data['customerAddress']) && !empty($data['customerAddress'])) {
            $order->setCustomerAddress($data['customerAddress']);
        }
        if (isset($data['customerEmail']) && !empty($data['customerEmail'])) {
            $order->setCustomerEmail($data['customerEmail']);
        }
        if (isset($data['NumeroTelephone']) && !empty($data['NumeroTelephone'])) {
            $order->setNumeroTelephone($data['NumeroTelephone']);
        }

        // Assuming the selected products are sent as an array of product names
        if (isset($data['products']) && is_array($data['products'])) {
            $productRepository = $entityManager->getRepository(Product::class);
            foreach ($data['products'] as $productName) {
                $product = $productRepository->findOneBy(['name' => $productName]);
                if ($product) {
                    $order->addProduct($product);
                }
            }
        }

        // Enregistrez l'entité dans la base de données
        $entityManager->persist($order);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Commande créée avec succès'], 201);
    }

}
