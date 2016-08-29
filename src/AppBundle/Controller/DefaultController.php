<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     **
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response

    @Route("/chat/{id}", name="chat")
     */
    public function chatAction(Request $request, $id)
    {


        return $this->render(':default:chat.html.twig', array(
            'roomID'            => $id,
            'websocketServer'   => $this->getParameter('websocket_server')
        ));
    }
}
