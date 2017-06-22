<?php

namespace Spatie\Varnish;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Varnish
{
    /**
     * @param null $host
     * @param string $url
     * @return Process
     */
    public function flush($host = null,$url = false)
    {
        $host = $this->getHosts($host);

        $command = $this->generateBanCommand($host,$url);

        return $this->executeCommand($command);
    }

    /**
     * @param array|string $host
     *
     * @return array
     */
    protected function getHosts($host = null): array
    {
        $host = $host ?? config('laravel-varnish.host');

        if (! is_array($host)) {
            $host = [$host];
        }

        return $host;
    }

    public function generateBanCommand(array $hosts,$url = false): string
    {
        if($url == false){
            if (! is_array($hosts)) {
                $hosts = [$hosts];
            }

            $hostsRegex = collect($hosts)
                ->map(function (string $host) {
                    return "(^{$host}$)";
                })
                ->implode('|');
        }


        $config = config('laravel-varnish');
        if($url == true){
            foreach($hosts as $host){
                return "sudo varnishadm -S {$config['administrative_secret']} -T {$config['administrative_host']}:{$config['administrative_port']} ban 'req.url == {$host}'";
            }
        }
        return "sudo varnishadm -S {$config['administrative_secret']} -T {$config['administrative_host']}:{$config['administrative_port']} 'ban req.http.host ~ {$hostsRegex}'";
    }

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
