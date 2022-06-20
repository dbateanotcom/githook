<?php

namespace App\Controller;

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
        LoggerInterface $logger
    ): Response {

        $post = \json_decode(
            $requestStack->getCurrentRequest()->getContent(),
            true
        );

        if (
            $post['hook']['config']['sectret']
            !==
            $_ENV['GITHUB_WEBHOOK_SECRET']
        ) {
        } {
            throw new UnauthorizedHttpException('Unauthorized');
        }

        $projects = $this->findGitProjects($_ENV['REPOSITORIES_PATH']);

        foreach ($projects as $p) {
            $projectConfig = parse_ini_file($p . '/.git/config');

            if (
                $projectConfig['url'] == $post['repository']['git_url'] ||
                $projectConfig['url'] == $post['repository']['ssh_url'] ||
                $projectConfig['url'] == $post['repository']['clone_url']
            ) {
                $response = $this->performGitPull($p);

                $logger->info('GitHook: ' . implode("\n", $response));
            }
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }


    private function findGitProjects($path)
    {
        $iterator = new \DirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        foreach ($iterator as $item) {
            if (
                $item->isDir() &&
                $item->getFilename() === '.git' &&
                file_exists($item->getPathname() . '/config')
            ) {
                return [$path];
            }
        }

        $paths = [];
        foreach ($iterator as $item) {
            if ($item->isDir()) {

                $paths = array_merge(
                    $paths,
                    $this->findGitProjects($item->getPathname())
                );
            }
        }

        return $paths;
    }



    private function performGitPull($path)
    {
        $output = [];
        exec('git -C ' . $path . ' pull', $output);
        return $output;
    }
}
