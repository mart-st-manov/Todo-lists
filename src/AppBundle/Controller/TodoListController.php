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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;

class TodoListController extends Controller
{
    /**
     * @Route("/lists/{userId}", name="todo_lists")
     * @param Request $request
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewListsAction(Request $request, $userId)
    {
        try {
            /** @var TodoListRepository $listRepo */
            $listRepo = $this->getDoctrine()->getRepository(TodoList::class);
            /** @var TodoList $lists */
            $lists = $listRepo->findBy(["user" => $userId]);

            return $this->render('AppBundle::Todos/todo-lists.html.twig', [
                'lists' => $lists,
                'userId' => $userId
            ]);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_page', ['errorCode' => 'lstGet']);
        }

    }

    /**
     * @Route("/new_list/{userId}", name="new_list")
     * @param Request $request
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createListAction(Request $request, $userId)
    {
        /** @var TodoList $list */
        $list = new TodoList();
        /** @var Form $form */
        $form = $this->createForm(TodoListType::class, $list);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $formData = $form->getData();
                $listName = $formData->getName();

                /** @var UserRepository $userRepo */
                $userRepo = $this->getDoctrine()->getRepository(User::class);
                /** @var User $user */
                $user = $userRepo->find($userId);


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
            'form' => $form->createView()
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
                /** @var User $user */
                $user = $list->getUser();

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

                return $this->redirectToRoute('todo_lists', ['userId' => $user->getId()]);

            } catch (\Exception $e) {
                return $this->redirectToRoute('error_page', ['errorCode' => 'tskCrt']);
            }
        }

        return $this->render('AppBundle::Todos/create-task.html.twig', [
            'form' => $form->createView(),
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
        try {
            /** @var TodoTaskRepository $taskRepo */
            $taskRepo = $this->getDoctrine()->getRepository(TodoTask::class);
            /** @var TodoTask $task */
            $task = $taskRepo->find($taskId);
            $userId = $task->getUser()->getId();
            $isCompleted = $task->getIsCompleted();

            $em = $this->getDoctrine()->getManager();

            if (is_null($isCompleted) || !$isCompleted) {
                $task->setIsCompleted(true);
            } else {
                $task->setIsCompleted(false);
            }

            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('todo_lists', ['userId' => $userId]);

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
        try {
            /** @var TodoTaskRepository $taskRepo */
            $taskRepo = $this->getDoctrine()->getRepository(TodoTask::class);
            /** @var TodoTask $task */
            $task = $taskRepo->find($taskId);
            $userId = $task->getUser()->getId();

            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();

            return $this->redirectToRoute('todo_lists', ['userId' => $userId]);

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
        try {
            /** @var TodoListRepository $listRepo */
            $listRepo = $this->getDoctrine()->getRepository(TodoList::class);
            /** @var TodoList $list */
            $list = $listRepo->find($listId);
            $userId = $list->getUser()->getId();

            $em = $this->getDoctrine()->getManager();
            $list->setIsArchived(true);
            $em->persist($list);
            $em->flush();

            return $this->redirectToRoute('todo_lists', ['userId' => $userId]);

        } catch (\Exception $e) {
            return $this->redirectToRoute('error_page', ['errorCode' => 'lstArc']);
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
        try {
            /** @var TodoListRepository $listRepo */
            $listRepo = $this->getDoctrine()->getRepository(TodoList::class);
            /** @var TodoList $list */
            $list = $listRepo->find($listId);
            $userId = $list->getUser()->getId();

            $em = $this->getDoctrine()->getManager();
            $em->remove($list);
            $em->flush();

            return $this->redirectToRoute('todo_lists', ['userId' => $userId]);

        } catch (\Exception $e) {
            return $this->redirectToRoute('error_page', ['errorCode' => 'lstDel']);
        }


    }
}
