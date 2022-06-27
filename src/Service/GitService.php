<?php

namespace App\Service;

use App\Model\GitRepository;

/**
 * Git Service
 */
class GitService {

    /**
     * Return an array of GitRepository
     * @param $path main path where search repositories
     * 
     * @return array
     */
    public function findRespositories($path): array
    {
        $iterator = new \DirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        foreach ($iterator as $item) {
            if (
                $item->isDir() &&
                $item->getFilename() === '.git' &&
                file_exists($item->getPathname() . '/config')
            ) {
                return [new GitRepository($path)];
            }
        }

        $paths = [];
        foreach ($iterator as $item) {
            if ($item->isDir()) {

                $paths = array_merge(
                    $paths,
                    $this->findRespositories($item->getPathname())
                );
            }
        }

        return $paths;
    }

}