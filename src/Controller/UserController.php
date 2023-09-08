<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{

    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    /**
     * Permet d'editer l'utilisateur
     *
     * @param UserRepository $repository
     * @param integer $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(UserRepository $repository, int $id, Request $request, EntityManagerInterface $manager): Response

    {


        $user = $repository->findOneBy(["id" => $id]);


        // condition autorisation d'édition d'utilisateur

        if ($user !== $this->getUser()) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à modifier ce compte.');
        }

        $form = $this->createForm(UserType::class, $user);



        /**
         * Envoie du formulaire modifié
         */
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été modifié avec succès !'
            );
            return $this->redirectToRoute('team.index');
        }


        return  $this->render('pages/user/edit.html.twig', [

            'form' => $form->createView()

        ]);
    }
    public function __construct(private ManagerRegistry $doctrine)
    {
    }

    public function someAction()
    {
        // access Doctrine
        $this->doctrine;
    }

    #[Route('/utilisateur/edition-mdp/{id}', 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(
        int $id, // Injectez l'ID depuis la route
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $hasher
    ): Response {
        $user = $this->doctrine->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable');
        }

        // Vérifier si l'utilisateur actuel est autorisé à modifier le mot de passe
        if ($user !== $this->getUser()) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à modifier ce mot de passe.');
        }

        
        $user = $this->doctrine->getRepository(User::class)->find($id);
        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()['plainPassword'])) {

                $user->setPassword(
                    $hasher->hashPassword($user, $form->getData()['newPassword'])
                );

                $this->addFlash(
                    'success',
                    'Le mot de passe a été modifié.'
                );

                $manager->persist($user);
                $manager->flush();

                return $this->redirectToRoute('team.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
            }
        }

        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
