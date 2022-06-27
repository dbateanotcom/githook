<?php

namespace App\Model;

use App\Exception\GitConfigNotFoundException;

/**
 * Class GitRepository
 * 
 * Represents a local git Repository
 */
class GitRepository
{
    /**
     * Repository Path
     * @var string
     */
    private $path;

    /**
     * Config data
     * <example>
     * array(3) {
     *  ["core"]=>
     *   array(4) {
     *    ["repositoryformatversion"]=>
     *     string(1) "0"
     *    ["filemode"]=>
     *     string(1) "1"
     *    ["bare"]=>
     *     string(0) ""
     *    ["logallrefupdates"]=>
     *     string(1) "1"
     *  }
     *  ["remote origin"]=>
     *   array(2) {
     *    ["url"]=>
     *     string(39) "git@github.com:dbateanotcom/githook.git"
     *    ["fetch"]=>
     *     string(35) "+refs/heads/*:refs/remotes/origin/*"
     *   }
     *  ["branch main"]=>
     *   array(2) {
     *    ["remote"]=>
     *     string(6) "origin"
     *    ["merge"]=>
     *     string(15) "refs/heads/main"
     *  }
     * }
     * </example>
     * 
     * @var array
     */
    private $config;


    /**
     * 
     * @var string
     */
    private $currentBranch;


    function __construct($path)
    {
        //git rev-parse --is-inside-work-tree
        $this->path = $path;

        try {
            $this->config = parse_ini_file($path . '/.git/config', true);
        } catch (\Exception $e) {
        }

        if (empty($this->config)) {
            throw new GitConfigNotFoundException($path);
        }

        $this->currentBranch = $this->getBranch();
    }

    /**
     * Get repository Path
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Return current branch
     * 
     * @return string
     */
    public function getBranch()
    {

        $output = [];
        exec('git -C ' . $this->path . '  branch --show-current', $output);

        if (empty($output)) {
            return null;
        }
        //first line of the output
        return $output[0];
    }

    /**
     * Upstream ref
     * 
     * @return string 
     */
    function getUpstreamRef()
    {
        $this->config['branch ' . $this->currentBranch]['merge'];
    }

    /**
     * Upstream remote
     * 
     * @return string
     */
    function getUpstreamRemote()
    {
        $this->config['branch ' . $this->currentBranch]['remote'];
    }


    /**
     * Return URL
     * 
     * @return string
     */
    public function getUrl()
    {
        $remote = $this->getUpstreamRemote();
        if (empty($remote)) {
            $remote = 'origin';
        }

        return $this->config['remote ' . $remote]['url'];
    }


    /**
     * Enable www-data shell in /etc/passwd
     * sudo -E su www-data
     * ssh-keygen -C "your_email@example.com"
     * Don't set password
     * eval "$(ssh-agent -s)"
     * ssh-add /var/www/.ssh/id_rsa
     * Disable www-data shell in /etc/passwd
     */
    public function pull()
    {
        $output = [];
        exec('git -C ' . $this->path . ' pull', $output);
        return $output;
    }
}
