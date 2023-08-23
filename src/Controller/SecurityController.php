<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class SecurityController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager, UserRepository $user)
    {
        $this->entityManager = $entityManager;
        $this->user=$user;
    }
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route('/register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher , EntityManagerInterface $entityManager ): JsonResponse
    {
        try {
            // Récupérer les données envoyées depuis Angular
            $data = json_decode($request->getContent(), true);

            // Créer un nouvel utilisateur et remplir ses données
            $user = new User();
            $user->setEmail($data['email']);
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']);

            // Enregistrer l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

           //  $token = $JWTManager->create($user);
            return new JsonResponse(['message' => 'User registered successfully'], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            // En cas d'erreur, renvoyer un message d'erreur avec le code 500 (Internal Server Error)
            return new JsonResponse(['message' => 'An error occurred while registering the user'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/login', methods: ['POST'])]
    public function connexion(Request $request,  UserPasswordHasherInterface $passwordEncoder, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Recherchez l'utilisateur par son email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user || !$passwordEncoder->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['message' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Créer un token JWT pour l'utilisateur
        $token = $JWTManager->create($user);

        // Stocker le token dans la réponse
        return new JsonResponse(['token' => $token]);
    }

    #[Route('/user', name: 'app_user_index', methods: ['GET'])]
    public function user (UserRepository $userRepository): Response
    {
        return $this->render('security/user.html.twig', [
            'User' => $userRepository->findAll(),
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index');
    }


}
