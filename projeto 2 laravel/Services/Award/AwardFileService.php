<?php


namespace App\Services\Award;


use App\Repositories\Repository\AwardFile\GetAwardFileRepository;

class AwardFileService
{
    private const DS = DIRECTORY_SEPARATOR;

    private $getAwardFile;

    public function __construct()
    {
        $this->getAwardFile = new GetAwardFileRepository();
    }

    public function getPath(int $fileId): string
    {
        $file = $this->getAwardFile->byId($fileId);

        return storage_path('app' . ds() . 'public' . ds() . 'awards' . ds() . $file->award_id . ds() . 'files' . ds() . $file->file);
    }
}
