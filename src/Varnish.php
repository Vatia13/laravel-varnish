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
        $hosts = $config['host'];
        if (! is_array($hosts)) {
            $hosts = [$hosts];
        }

        if($url == false){
            $hostsRegex = collect($hosts)
                ->map(function (string $host) {
                    return "(^{$host}$)";
                })
                ->implode('|');
        }


        if($url){
            $url = str_replace(url('/'),'',$url);
            return "sudo varnishadm -S {$config['administrative_secret']} -T {$config['administrative_host']}:{$config['administrative_port']} ban 'req.url == {$url}'";
        }
        return "sudo varnishadm -S {$config['administrative_secret']} -T {$config['administrative_host']}:{$config['administrative_port']} 'ban req.http.host ~ {$hostsRegex}'";
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
