<?php

namespace App\Services;

use Illuminate\Filesystem\Filesystem;

class DataProvider
{
    const TYPE_IN = '.in';
    const TYPE_OUT = '.out';

    public function getData($id)
    {
        $dataInput = $this->getInputFiles($id);
        $dataOutput = $this->getOutputFiles($id);

        $data = [];
        foreach ($dataInput as $name => $content) {
            if (!array_key_exists($name, $dataOutput)) {
                $message = 'Problem Data is not match!';
                app('log')->error($message, ['pid' => $id]);
                throw new \LogicException($message);
            }
            $outContent = $dataOutput[$name];
            $data[] = [
                'input' => $content,
                'output' => $outContent,
            ];
        }
        return $data;
    }

    /**
     * @param $id
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getInputFiles($id)
    {
        $patten = $this->getDataPath($id). '*'. self::TYPE_IN;

        $fs = new Filesystem();
        $files = $fs->glob($patten);

        $data = [];
        foreach ($files as $file) {
            $data[$fs->name($file)] = $fs->get($file);
        }

        return $data;
    }

    /**
     * @param $id
     *
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getOutputFiles($id)
    {
        $patten = $this->getDataPath($id). '*'. self::TYPE_OUT;

        $fs = new Filesystem();
        $files = $fs->glob($patten);

        $data = [];
        foreach ($files as $file) {
            $data[$fs->name($file)] = $fs->get($file);
        }

        return $data;
    }

    /**
     * @param $id
     *
     * @return string
     */
    public function getDataPath($id)
    {
        return config('app.data_path').'/'.$id.'/';
    }
}
