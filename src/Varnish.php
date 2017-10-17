<?php

namespace Spatie\Varnish;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Varnish
{
    /**
     * @param bool $url
     * @return Process
     */
    public function flush($url = false)
    {

        $command = $this->generateBanCommand($url);

        return $this->executeCommand($command);

    }


    /**
     * @param $url
     * @return string
     */
    public function generateBanCommand($url)
    {
        $config = config('laravel-varnish');

        if($url){

            $url = str_replace(url('/'),'',$url);

            # Command to clear cache for request url
            return "sudo varnishadm -S {$config['administrative_secret']} -T {$config['administrative_host']}:{$config['administrative_port']} ban 'req.url ~ {$url}'";
        }
        # Command to clear complete cache for all URLs and all sub-domains
        return "sudo varnishadm -S {$config['administrative_secret']} -T {$config['administrative_host']}:{$config['administrative_port']} 'ban req.http.host ~ .*'";
    }

    /**
     * @param string $command
     * @return Process
     */
    protected function executeCommand(string $command): Process
    {
        $process = new Process($command);

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process;
    }
}
