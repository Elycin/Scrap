<?php

namespace App\Console\Commands;

use App\FileResolver;
use App\Upload;
use Illuminate\Console\Command;
use Carbon\Carbon;

class FilesList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all uploaded files by alias.';

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
     * @throws \Exception
     */
    public function handle()
    {
        $uploads = Upload::orderBy("created_at", "asc")->get();
        foreach ($uploads as $upload) {
            echo $upload->getAlias() . "\n";
        }

        //Iterate over each resolver and find where 0 uploads are.
        $resolvers = FileResolver::all();
        foreach ($resolvers as $resolver) if (Upload::where('resolver_id', $resolver->id)->get()->count() == 0) $resolver->delete();
    }
}
