<?php

namespace App\Controller;

use App\Service\GitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GitHook extends AbstractController
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
        LoggerInterface $logger,
        GitService $gitService
    ): Response {

        $request = $requestStack->getCurrentRequest();
        $signature = $request->headers->get('X-Hub-Signature-256');
        $content = $request->getContent();

        if(
            $signature 
            != 
            ( "sha256=" . hash_hmac(
                "sha256", 
                $content,
                $_ENV['GITHUB_WEBHOOK_SECRET'],
                false
                )
            )
        ){
            $logger->info('GitHook: Unauthorized');
            throw new UnauthorizedHttpException('Unauthorized');
        }

        
        $post = \json_decode(
            $content,
            true
        );

        if(
            isset($post['ref'])
            &&
            isset($post['repository'])
        ){

            $logger->info('GitHook: ' . $post['repositoy']['full_name']);
        
            $projects = $gitService->findRespositories($_ENV['REPOSITORIES_PATH']);
        
            $found  = false;
            foreach ($projects as $p) {
                $url = $p->getUrl();
    
                if (
                    ( 
                      $url == $post['repository']['git_url'] ||
                      $url == $post['repository']['ssh_url'] ||
                      $url == $post['repository']['clone_url']
                    )
                    &&
                    $p->getUpstreamRef() == $post['ref'] 
                ) {
                    $found = true;
                    $response = $p->pull();
                    $logger->info('GitHook: Pulled ' . $post['repositoy']['full_name'] . ",\n" . implode("\n", $response));
                }
            }
    
            if (!$found) {
                $logger->info('GitHook: ' . $post['repositoy']['full_name'] . ' not found');
            }
    
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

}
