<?php

namespace App\Services;

use Illuminate\Filesystem\Filesystem;

class DataProvider
{
    const TYPE_IN = '.in';
    const TYPE_OUT = '.out';

    /**
     * @param $id
     * @param $type
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getProblemData($id, $type)
    {
        $path = $this->getDataPath($id, $type);
        $fs = new Filesystem();

        return $fs->get($path);
    }

    public function writeProblemData($id, $type, $content)
    {
        $path = $this->getDataPath($id, $type);
        $fs = new Filesystem();
        $fs->put($path, $content);
    }

    /**
     * @param $id
     * @param $type
     *
     * @return string
     */
    public function getDataPath($id, $type)
    {
        $fName = $id.$type;

        return config('app.data_path').'/'.$id.'/'.$fName;
    }
}
