<?php

namespace App\Console\Commands;

use App\FileResolver;
use App\Upload;
use Illuminate\Console\Command;

class TestAlias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:upload {alias}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test a file alias and get the result.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $upload = Upload::getCached($this->argument("alias"));
        $resolver = FileResolver::getCachedFromUpload($upload);
        dd($upload, $resolver);
    }
}
