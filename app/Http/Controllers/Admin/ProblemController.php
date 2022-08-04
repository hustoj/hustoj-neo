<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Problem;
use App\Services\DataProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;

class ProblemController extends DataController
{
    public function index()
    {
        $query = Problem::query();

        if (request()->filled('id')) {
            $query->where('id', request('id'));
        }

        if (request()->filled('title')) {
            $query->where('title', 'like', where_like(request('title')));
        }

        if (request()->filled('source')) {
            $query->where('source', 'like', where_like(request('like')));
        }
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        $query->with(['author']);

        return parent::paginate($query);
    }

    public function getFile($id, $name)
    {
        $name = htmlspecialchars($name);
        $problem = Problem::query()->find($id);

        if ($problem) {
            $dp = app(DataProvider::class);

            $path = $dp->getDataPath($id).$name;

            return response()->download($path, $name);
        }

        return '';
    }

    public function removeFile($id, $name)
    {
        $name = htmlspecialchars($name);

        try {
            $problem = Problem::query()->findOrFail($id);
            $fs = new Filesystem();
            $path = config('hustoj.data_path').'/'.$id.'/'.$name;
            if ($fs->exists($path) && $fs->delete($path)) {
                return ['code' => 0];
            }

            return [
                'code' => -1,
                'message' => 'file not exist or you have no permission delete it!',
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'code' => -1,
                'message' => 'File not found!',
            ];
        }
    }

    public function dataFiles($id)
    {
        $problem = Problem::query()->find($id);

        if ($problem) {
            $fs = new Filesystem();
            $path = config('hustoj.data_path').'/'.$id;
            if (! $fs->exists($path)) {
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
        $problem = Problem::query()->find($id);

        if ($problem && request()->hasFile('files')) {
            $fs = new Filesystem();
            $path = config('hustoj.data_path').'/'.$id;
            if (! $fs->exists($path)) {
                $fs->makeDirectory($path);
            }
            /** @var UploadedFile[] $files */
            $files = request()->allFiles();
            foreach ($files as $file) {
                $file->move($path, $file->getClientOriginalName());
            }
        }
    }

    protected function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Problem::query();
    }
}
