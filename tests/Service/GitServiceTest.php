<?php

namespace App\Tests\Service;

use App\Service\GitService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GitServiceTest extends KernelTestCase{

    public function getPathRepository(){
        return './../../';
    }

    /**
     * @depends getPathRepository
     */
    public function testFindRespositories($path){

        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        // (3) run some service & test the result
        $gitService = $container->get(GitService::class);

        $repos = $gitService->findRespositories($path);

        $this->assertEquals(1, count($repos));
    }
}