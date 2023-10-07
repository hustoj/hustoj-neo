<?php

namespace App\Services;

use Illuminate\Filesystem\Filesystem;
use LogicException;

class DataProvider
{
    public const TYPE_IN = '.in';
    public const TYPE_OUT = '.out';

    public function getData($id)
    {
        $dataInput = $this->getInputFiles($id);
        $dataOutput = $this->getOutputFiles($id);

        $data = [];
        foreach ($dataInput as $name => $content) {
            if (! array_key_exists($name, $dataOutput)) {
                $message = 'Problem Data is not match!';
                app('log')->error($message, ['pid' => $id]);

                throw new LogicException($message);
            }
            $outContent = $dataOutput[$name];
            $data[] = [
                'input'  => $content,
                'output' => $outContent,
            ];
        }

        return $data;
    }

    /**
     * @param  $id
     * @return array
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getInputFiles($id)
    {
        $pattern = $this->getDataPath($id).'*'.self::TYPE_IN;

        return $this->getDataFiles($pattern);
    }

    /**
     * @param  $id
     * @return string
     */
    public function getDataPath($id)
    {
        return config('hustoj.data_path').'/'.$id.'/';
    }

    /**
     * @param  $id
     * @return array
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getOutputFiles($id)
    {
        $pattern = $this->getDataPath($id).'*'.self::TYPE_OUT;

        return $this->getDataFiles($pattern);
    }

    public function getDataFiles($pattern)
    {
        $fs = new Filesystem();
        $files = $fs->glob($pattern);

        $data = [];
        foreach ($files as $file) {
            $data[$fs->name($file)] = $fs->get($file);
        }

        return $data;
    }
}
