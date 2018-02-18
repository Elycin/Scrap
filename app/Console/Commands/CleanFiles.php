<?php

namespace App\Console\Commands;

use App\FileResolver;
use App\Upload;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old files that are no longer used.';

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
        // Delete files older than the defined number of days
        $num_of_days = config('app.days_to_store', 30);
        $expiration_date = Carbon::now()->subDays($num_of_days);
        Upload::where('created_at', '>=', $expiration_date->toDateTimeString())->delete();

        //Iterate over each resolver and find where 0 uploads are.
        $resolvers = FileResolver::all();
        foreach ($resolvers as $resolver) if (Upload::where('resolver_id', $resolver->id)->get()->count() == 0) $resolver->delete();
    }
}
