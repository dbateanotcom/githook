<?php

namespace App\Controller\Follow;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

class ClientUploadProfileImage extends AbstractController
{

    /**
     * @Route(
     *     "/webhook",
     *     name="webhook",
     *     methods={"POST"}
     * )
     */
    public function webhook(
        RequestStack $requestStack,
        LoggerInterface $logger
    ): Response {

        $content = \json_decode($requestStack->getCurrentRequest()->getContent(), true);
        ob_start();
        var_dump($content);
        $data = ob_get_clean();
        $logger->info($data);
        return new JsonResponse([]);
   }
}
