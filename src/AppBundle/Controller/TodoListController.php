<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TodoList;
use AppBundle\Entity\TodoTask;
use AppBundle\Entity\User;
use AppBundle\Form\TodoListType;
use AppBundle\Form\TodoTaskType;
use AppBundle\Repository\TodoListRepository;
use AppBundle\Repository\TodoTaskRepository;
use AppBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;

class TodoListController extends Controller
{
    /**
     * @Route("/lists", name="todo_lists")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewListsAction(Request $request)
    {
        $userId = $this->get('session')->get('loginUserId');
        if (empty($userId)) {
            return $this->redirectToRoute('login');
        }

        try {
            /** @var TodoListRepository $listRepo */
            $listRepo = $this->getDoctrine()->getRepository(TodoList::class);
            /** @var UserRepository $userRepo */
            $userRepo = $this->getDoctrine()->getRepository(User::class);
            /** @var TodoList $lists */
            $lists = $listRepo->findBy(["user" => $userId]);
            /** @var User $user */
            $user = $userRepo->find($userId);

            return $this->render('AppBundle::Todos/todo-lists.html.twig', [
                'pageTitle' => "My Todo Lists",
                'lists' => $lists,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_page', ['errorCode' => 'lstGet']);
        }

    }

    /**
     * @Route("/new_list", name="new_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createListAction(Request $request)
    {
        $userId = $this->get('session')->get('loginUserId');
        if (empty($userId)) {
            return $this->redirectToRoute('login');
        }

        /** @var UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        /** @var User $user */
        $user = $userRepo->find($userId);

        /** @var TodoList $list */
        $list = new TodoList();
        /** @var Form $form */
        $form = $this->createForm(TodoListType::class, $list);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $formData = $form->getData();
                $listName = $formData->getName();

                $list->setName($listName);
                $list->setUser($user);
                $list->setIsArchived(false);

                $user->addList($list);

                $em = $this->getDoctrine()->getManager();
                $em->persist($list);
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('todo_lists', ['userId' => $user->getId()]);

            } catch (\Exception $e) {
                return $this->redirectToRoute('error_page', ['errorCode' => 'lstCrt']);
            }
        }

        return $this->render('AppBundle::Todos/create-list.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'pageTitle' => "Create New List",
        ]);
    }

    /**
     * @Route("/new_task/{listId}", name="new_task")
     * @param Request $request
     * @param $listId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createTaskAction(Request $request, $listId)
    {
        $userId = $this->get('session')->get('loginUserId');
        if (empty($userId)) {
            return $this->redirectToRoute('login');
        }

        /** @var UserRepository $userRepo */
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        /** @var User $user */
        $user = $userRepo->find($userId);

        /** @var TodoTask $task */
        $task = new TodoTask();
        /** @var Form $form */
        $form = $this->createForm(TodoTaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $formData = $form->getData();
                $taskDesc = $formData->getDescription();

                /** @var TodoListRepository $listRepo */
                $listRepo = $this->getDoctrine()->getRepository(TodoList::class);
                /** @var TodoList $list */
                $list = $listRepo->find($listId);

                $task->setDescription($taskDesc);
                $task->setList($list);
                $task->setUser($user);
                $task->setCreatedOn(new \DateTime("now"));
                $task->setIsCompleted(false);

                $list->addTask($task);

                $em = $this->getDoctrine()->getManager();
                $em->persist($task);
                $em->persist($list);
                $em->flush();

                return $this->redirectToRoute('todo_lists');

            } catch (\Exception $e) {
                return $this->redirectToRoute('error_page', ['errorCode' => 'tskCrt']);
            }
        }

        return $this->render('AppBundle::Todos/create-task.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'pageTitle' => "Create New Task",
        ]);
    }

    /**
     * @Route("/change_task_status/{taskId}", name="change_task_status")
     * @param Request $request
     * @param $taskId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeTaskStatus(Request $request, $taskId)
    {
        $userId = $this->get('session')->get('loginUserId');
        if (empty($userId)) {
            return $this->redirectToRoute('login');
        }

        try {
            /** @var TodoTaskRepository $taskRepo */
            $taskRepo = $this->getDoctrine()->getRepository(TodoTask::class);
            /** @var TodoTask $task */
            $task = $taskRepo->find($taskId);
            $isCompleted = $task->getIsCompleted();

            $em = $this->getDoctrine()->getManager();

            if ($isCompleted == true) {
                $task->setIsCompleted(false);
            } else {
                $task->setIsCompleted(true);
            }

            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('todo_lists');

        } catch (\Exception $e) {
            return $this->redirectToRoute('error_page', ['errorCode' => 'tskSts']);
        }
    }

    /**
     * @Route("/delete_task/{taskId}", name="delete_task")
     * @param Request $request
     * @param $taskId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTask(Request $request, $taskId)
    {
        $userId = $this->get('session')->get('loginUserId');
        if (empty($userId)) {
            return $this->redirectToRoute('login');
        }

        try {
            /** @var TodoTaskRepository $taskRepo */
            $taskRepo = $this->getDoctrine()->getRepository(TodoTask::class);
            /** @var TodoTask $task */
            $task = $taskRepo->find($taskId);

            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();

            return $this->redirectToRoute('todo_lists');

        } catch (\Exception $e) {
            return $this->redirectToRoute('error_page', ['errorCode' => 'tskDel']);
        }
    }

    /**
     * @Route("/archive_list/{listId}", name="archive_list")
     * @param Request $request
     * @param $listId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function archiveList(Request $request, $listId)
    {
        $userId = $this->get('session')->get('loginUserId');
        if (empty($userId)) {
            return $this->redirectToRoute('login');
        }

        try {
            /** @var TodoListRepository $listRepo */
            $listRepo = $this->getDoctrine()->getRepository(TodoList::class);
            /** @var TodoList $list */
            $list = $listRepo->find($listId);

            $em = $this->getDoctrine()->getManager();
            $list->setIsArchived(true);
            $em->persist($list);
            $em->flush();

            return $this->redirectToRoute('todo_lists');

        } catch (\Exception $e) {
            return $this->redirectToRoute('error_page', ['errorCode' => 'lstArc']);
        }
    }

    /**
     * @Route("/request_deletion/{listId}", name="request_deletion")
     * @param Request $request
     * @param $listId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function requestListDeletion(Request $request, $listId)
    {
        $userId = $this->get('session')->get('loginUserId');
        if (empty($userId)) {
            return $this->redirectToRoute('login');
        }

        try {
            /** @var TodoListRepository $listRepo */
            $listRepo = $this->getDoctrine()->getRepository(TodoList::class);
            /** @var TodoList $list */
            $list = $listRepo->find($listId);

            $em = $this->getDoctrine()->getManager();
            $list->setIsDeletionPending(true);
            $em->persist($list);
            $em->flush();

            return $this->redirectToRoute('todo_lists');

        } catch (\Exception $e) {
            return $this->redirectToRoute('error_page', ['errorCode' => 'lstDel']);
        }
    }

    /**
     * @Route("/user_lists/{listsUserId}", name="user_lists")
     * @param Request $request
     * @param $listsUserId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewUserLists(Request $request, $listsUserId)
    {
        $userId = $this->get('session')->get('loginUserId');
        if (empty($userId)) {
            return $this->redirectToRoute('login');
        }

        try {
            /** @var UserRepository $userRepo */
            $userRepo = $this->getDoctrine()->getRepository(User::class);
            /** @var TodoListRepository $listRepo */
            $listRepo = $this->getDoctrine()->getRepository(TodoList::class);

            /** @var User $user */
            $user = $userRepo->find($userId);
            /** @var User $listsUser */
            $listsUser = $userRepo->find($listsUserId);
            $listsUserEmail = $listsUser->getEmail();
            /** @var TodoList $lists */
            $lists = $listRepo->findBy(["user" => $listsUserId]);


            return $this->render('AppBundle::Todos/todo-lists.html.twig', [
                'pageTitle' => "Lists of user $listsUserEmail",
                'lists' => $lists,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return $this->redirectToRoute('error_page', ['errorCode' => 'lstGet']);
        }
    }

    /**
     * @Route("/delete_list/{listId}", name="delete_list")
     * @param Request $request
     * @param $listId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteList(Request $request, $listId)
    {
        $userId = $this->get('session')->get('loginUserId');
        if (empty($userId)) {
            return $this->redirectToRoute('login');
        }

        try {
            /** @var TodoListRepository $listRepo */
            $listRepo = $this->getDoctrine()->getRepository(TodoList::class);
            /** @var TodoList $list */
            $list = $listRepo->find($listId);
            /** @var User $listOwner */
            $listOwner = $list->getUser();

            $em = $this->getDoctrine()->getManager();
            $em->remove($list);
            $em->flush();

            return $this->redirectToRoute('user_lists', ['listsUserId' => $listOwner->getId()]);

        } catch (\Exception $e) {
            return $this->redirectToRoute('error_page', ['errorCode' => 'lstDel']);
        }
    }

    /**
     * @Route("/export_lists/{_filename}.{_format}", defaults={"_format"="xls","_filename"="example"}, requirements={"_format"="csv|xls|xlsx"}, name="export_lists")
     * @Template("AppBundle:excel.xlsx.twig")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportListAction(Request $request)
    {
        $userId = $this->get('session')->get('loginUserId');
        if (empty($userId)) {
            return $this->redirectToRoute('login');
        }

        try {
            /** @var TodoListRepository $listRepo */
            $listRepo = $this->getDoctrine()->getRepository(TodoList::class);
            /** @var TodoList $lists */
            $lists = $listRepo->findBy(["user" => $userId]);

            return $this->render('AppBundle::excel.xlsx.twig', [
                'lists' => $lists
            ]);

        } catch (\Exception $e) {
            return $this->redirectToRoute('error_page', ['errorCode' => 'xlsExp']);
        }
    }
}
