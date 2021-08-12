<?php


namespace App\Repositories\Repository\AwardFile;


use App\AwardFile;

class GetAwardFileRepository
{

    public function byId(int $fileId): AwardFile
    {
        return AwardFile::find($fileId);
    }
}
