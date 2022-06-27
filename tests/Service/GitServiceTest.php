<?php

namespace App\Tests\Service;

use App\Service\GitService;

class GitServiceTest extends \PHPUnit\Framework\TestCase{

    private $service;

    function __construct(GitService $gitService)
    {
        $this->service = $gitService;    
    }

    public function getPathRepository(){
        return './../../';
    }
    
    /**
     * @depends getPathRepository
     */
    public function testFindRespositories($path){
        $this->service->findRespositories($path);
    }
}