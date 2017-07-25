<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\LoginType;
use AppBundle\Form\UserType;
use AppBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
    /**
     * @Route("/", name="login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        $userId = $this->get('session')->get('loginUserId');
        if (!empty($userId)) {
            return $this->redirectToRoute('todo_lists');
        }

        /** @var Form $form */
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $formData = $form->getData();
                $email = $formData->getEmail();
                $password = $formData->getPassword();

                /** @var UserRepository $userRepo */
                $userRepo = $this->getDoctrine()->getRepository(User::class);
                /** @var User $user */
                $user = $userRepo->getUser($email, $password);

                $this->get('session')->set('loginUserId', $user->getId());

                return $this->redirectToRoute('todo_lists');
            } catch (\Exception $e) {
                return $this->redirectToRoute('error_page', ['errorCode' => 'usrLog']);
            }
        }

        return $this->render('AppBundle::User/login.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logoutAction(Request $request)
    {
        try {
            $this->get('session')->set('loginUserId', null);
            return $this->redirectToRoute('login');
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_page', ['errorCode' => 'usrLgt']);
        }
    }

    /**
     * @Route("/register/", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        /** @var User $user */
        $user = new User();
        /** @var Form $form */
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('login');
            } catch (\Exception $e) {
                return $this->redirectToRoute('error_page', ['errorCode' => 'usrReg']);
            }
        }

        return $this->render('AppBundle::User/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/", name="users")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewUsers(Request $request)
    {
        $userId = $this->get('session')->get('loginUserId');
        if (empty($userId)) {
            return $this->redirectToRoute('login');
        }

        try {
            /** @var UserRepository $userRepo */
            $userRepo = $this->getDoctrine()->getRepository(User::class);
            /** @var User $user */
            $user = $userRepo->find($userId);

            if (!$user->getIsAdmin()) {
                return $this->redirectToRoute('error_page', ['errorCode' => 'notAdm']);
            }

            $users = $userRepo->findAll();

            return $this->render('AppBundle::User/users.html.twig', [
                'pageTitle' => "Users",
                'user' => $user,
                'users' => $users
            ]);

        } catch (\Exception $e) {
            return $this->redirectToRoute('error_page', ['errorCode' => 'getUsr']);
        }
    }
}
