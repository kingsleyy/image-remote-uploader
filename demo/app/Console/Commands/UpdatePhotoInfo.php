<?php

namespace App\Console\Commands;

use App;
use App\CacheStorage;
use App\Repositories\AlbumRepository;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Kings\ImageRemoteUploader\Uploaders\Picasa;

class UpdatePhotoInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photo:update-info';

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
     * Init before run
     */
    protected function init()
    {
        // reload list albums
        $this->picasa = App::make('PicasaUploader');
        $this->albumRepository = App::make(AlbumRepository::class);
        $this->albumRepository->truncateDatabase();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->init();

        $albums = $this->loadAlbumsInfo();
        $this->output->write('Updating database ... ');
        $this->albumRepository->insertRawData($albums);
        $this->output->writeln("OK.\nFinished.");
    }

    /**
     * @return array
     */
    protected function loadAlbumsInfo()
    {
        $this->output->write('Loading albums info ... ');
        $jsonData = json_decode($this->picasa->listAlbums(), true);
        $data = [];

        foreach ($jsonData['feed']['entry'] as $entry) {
            $data[] = [
                'g_id' => $this->getGDataValue($entry, 'gphoto$id'),
                'name' => $this->getGDataValue($entry, 'gphoto$name'),
                'c_photo' => (int) $this->getGDataValue($entry, 'gphoto$numphotos'),
            ];
        }
        $this->output->write('OK', true);

        return $data;
    }

    protected function getGDataValue($entry, $key)
    {
        return $entry[$key]['$t'];
    }
}
