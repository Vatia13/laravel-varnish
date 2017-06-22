<?php

namespace Spatie\Varnish\Commands;

<<<<<<< HEAD
=======
use Spatie\Varnish\Varnish;
>>>>>>> 56dcb551d7e9a3b10dd86c19b3d5e2c75160241f
use Illuminate\Console\Command;

class FlushVarnishCache extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'varnish:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush the varnish cache.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
<<<<<<< HEAD
        varnish()->flush();
=======
        (new Varnish())->flush();
>>>>>>> 56dcb551d7e9a3b10dd86c19b3d5e2c75160241f

        $this->comment('The varnish cache has been flushed!');
    }
}
