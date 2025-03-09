<?php

namespace App\Infrastructures\Core;

use App\Infrastructures\Core\Interfaces\RepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class Repository implements RepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->begin();
    }

    abstract protected function model(): string;

    protected function begin()
    {
        $class = $this->model();
        $model = new $class();
        $this->model = $model;
    }

    public function all($with = []): Collection
    {
        return $this->model->with($with)->get();
    }

    public function findById($id, $with = []): ?Model
    {
        return $this->model->with($with)->findOrFail($id);
    }

    public function create(array $dataRequest): Model
    {
        return $this->model->create($dataRequest);
    }

    public function updateById($id, array $dataRequest)
    {
        $data = $this->findById($id);
        $data->update($dataRequest);

        return $data;
    }

    public function deleteById($id)
    {
        $data = $this->findById($id);
        $data->delete();

        return $data;
    }

    public function paginator($size = 50, $page = 1): LengthAwarePaginator
    {
        return $this->model->paginate($size, ['*'], 'page', $page);
    }
}
