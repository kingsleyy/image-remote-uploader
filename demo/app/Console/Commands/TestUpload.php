<?php

namespace App\Console\Commands;

use App;
use App\CacheStorage;
use App\Repositories\AlbumRepository;
use App\Threads\UploadInfo;
use App\Threads\UploadThread;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Kings\ImageRemoteUploader\Uploaders\Picasa;
use Stackable;

class TestUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update photo info to DB.';

    /**
     * @var Picasa
     */
    protected $picasa;

    /**
     * @var AlbumRepository
     */
    protected $albumRepository;

    /**
     * Create a new command instance.
     *
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
        $filenames = [
            "0-0.62953300-1428147290.jpg",
            "1-0.62984200-1428147290.jpg",
            "10-0.38900300-1428147491.jpg",
            "11-0.85242300-1428147548.jpg",
            "12-0.85273100-1428147548.jpg",
            "13-0.85278500-1428147548.jpg",
            "14-0.01950600-1428147593.jpg",
            "15-0.01959500-1428147593.jpg",
            "16-0.01964000-1428147593.jpg",
            "17-0.69339200-1428147631.jpg",
            "18-0.69376000-1428147631.jpg",
            "2-0.64913700-1428147338.jpg",
            "3-0.64930100-1428147338.jpg",
            "4-0.64934600-1428147338.jpg",
            "5-0.64938600-1428147338.jpg",
            "6-0.35476100-1428147452.jpg",
            "7-0.35508200-1428147452.jpg",
            "8-0.38870400-1428147491.jpg",
            "9-0.38891800-1428147491.jpg",
            "Cherry_Boy_ch12_p01-0.37451500-1428145129.jpg",
            "Cherry_Boy_ch12_p01-0.99436600-1428144448.jpg",
            "Cherry_Boy_ch12_p02-0.37468600-1428145129.jpg",
            "Cherry_Boy_ch12_p02-0.99455500-1428144448.jpg",
            "Cherry_Boy_ch12_p03-0.37478200-1428145129.jpg",
        ];
        $basepath = public_path('test-images/');
        $albumRepo = new AlbumRepository();
        $picasa = App::make('PicasaUploader');

        $album = $albumRepo->choseAlbum(count($filenames), 'otk', $picasa);
        $threads = [];
        $input = new Stackable();
        $output = new Stackable();
        $maxThreads = 2;

        foreach ($filenames as $filename) {
            $uploadInfo = new UploadInfo(
                $basepath . $filename,
                $album->g_id
            );

            $input[] = $uploadInfo;
        }

        // Init thread
        foreach (range(1, $maxThreads) as $i) {
            $thread = new UploadThread($input, $output);
            $thread->start();
            $threads[$i] = $thread;
        }

        while ($input->count() > 0) {
            echo "Images left: " . $input->count() . "\n";
            usleep(500000);
        }

        foreach (range(1, $maxThreads) as $i) {
            $threads[$i]->terminate();
        }

        foreach ($output as $url) {
            echo $url . "\n";
        }
    }
}
