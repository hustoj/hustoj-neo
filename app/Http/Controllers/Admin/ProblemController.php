<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Criteria\Like;
use App\Repositories\Criteria\Where;
use App\Repositories\ProblemRepository;
use Czim\Repository\Criteria\Common\WithRelations;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;

class ProblemController extends DataController
{
    public function index()
    {
        if (request('id')) {
            $this->repository->pushCriteria(new Where('id', request('id')));
        }

        if (request('title')) {
            $this->repository->pushCriteria(new Like('title', request('title')));
        }

        if (request('source')) {
            $this->repository->pushCriteria(new Like('source', request('source')));
        }

        $this->repository->pushCriteria(new WithRelations(['author']));

        return parent::index();
    }

    public function getFile($id, $name)
    {
        $name = htmlspecialchars($name);
        $problem = $this->repository->find($id);

        if ($problem) {
            $fs = new Filesystem();
            $path = config('app.data_path').'/'.$id.'/'.$name;
            if ($fs->exists($path)) {
                return response()->download($path, $name);
            }
        }

        return '';
    }

    public function removeFile($id, $name)
    {
        $name = htmlspecialchars($name);

        try {
            $this->repository->findOrFail($id);
            $fs = new Filesystem();
            $path = config('app.data_path').'/'.$id.'/'.$name;
            if ($fs->exists($path) && $fs->delete($path)) {
                return ['code' => 0];
            }

            return [
                'code'    => -1,
                'message' => 'file not exist or you have no permission delete it!',
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'code'    => -1,
                'message' => 'File not found!',
            ];
        }
    }

    public function dataFiles($id)
    {
        $problem = $this->repository->find($id);

        if ($problem) {
            $fs = new Filesystem();
            $path = config('app.data_path').'/'.$id;
            if (!$fs->exists($path)) {
                $fs->makeDirectory($path);
            }
            $files = $fs->files($path);

            $result = [];
            foreach ($files as $file) {
                $result[] = $fs->basename($file);
            }

            return $result;
        }

        return [];
    }

    public function upload($id)
    {
        $problem = $this->repository->find($id);

        if ($problem && request()->hasFile('files')) {
            $fs = new Filesystem();
            $path = config('app.data_path').'/'.$id;
            if (!$fs->exists($path)) {
                $fs->makeDirectory($path);
            }
            /** @var UploadedFile[] $files */
            $files = request()->allFiles();
            foreach ($files as $file) {
                $file->move($path, $file->getClientOriginalName());
            }
        }
    }

    protected function getRepository()
    {
        return ProblemRepository::class;
    }
}
