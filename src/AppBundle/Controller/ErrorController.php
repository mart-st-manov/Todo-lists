<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ErrorController extends Controller
{


    /**
     * @Route("/error/{errorCode}", name="error_page")
     * @param Request $request
     * @param $errorCode
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editProfileAction(Request $request, $errorCode)
    {
        switch ($errorCode) {
            case "usrReg": $message = "Could not register new user!"; break;
            case "usrLog": $message = "Could not login!"; break;
            case "lstGet": $message = "Could not load Todo lists!"; break;
            case "lstCrt": $message = "Could not create a new lists!"; break;
            case "lstArc": $message = "Could not archive the list!"; break;
            case "lstDel": $message = "Could not delete the list!"; break;
            case "tskCrt": $message = "Could not create a new task!"; break;
            case "tskSts": $message = "Could not change the status of the task!"; break;
            case "tskDel": $message = "Could not delete the task!"; break;
            default: $message = "Something went horribly wrong!";
        }
        return $this->render('default/error.html.twig', [
            'message' => $message
        ]);
    }
}
