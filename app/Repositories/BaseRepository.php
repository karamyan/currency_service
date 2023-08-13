<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class BaseRepository
 */
abstract class BaseRepository
{
    /**
     * @var array
     */
    protected array $fillable = [];

    /**
     * @var Builder
     */
    public Builder $query;

    /**
     * @var Model
     */
    public Model $model;

    /**
     * @return Builder
     */
    public function getQuery(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     *
     * This method will fill the given $object by the given $array.
     *  If the $fillable parameter is not available it will use the fillable
     *  array of the class.
     *
     * @param array $data
     * @param Model $object
     * @param array $fillable
     * @return Model
     */
    public function fill(array $data, Model $object, array $fillable = []): Model
    {
        if (empty($fillable)) {
            $fillable = $this->fillable;
        }
        if (!empty($fillable)) {
            // just fill when fillable array is not empty
            $object->fillable($fillable)->fill($data);
        }

        return $object;
    }

    /**
     * wrap object
     *
     * @param object $object
     *
     * @return object
     */
    public function load(object $object): object
    {
        return $object;
    }

    /**
     * Return all rows from table.
     *
     * @param array $filters
     * @param array $relations
     * @param array $columns
     * @return Collection
     */
    public function all(array $filters = [], array $relations = [], array $columns = ['*']): Collection
    {
        return $this->filters($this->getQuery(), $filters)->with($relations)->get($columns);
    }

    /**
     * Return multi rows from table.
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = Setting::PAGE_SIZE, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->getQuery()->paginate($perPage, $columns);
    }

    /**
     * @param Model $entity
     * @return Model
     */
    public function save(Model $entity): Model
    {
        if (get_class($entity) != $this->model) {
            throw new RepositoryException(
                'You can use save method on ' . static::class . ' for class of type ' . $this->model
            );
        }
        $entity->save();

        return $entity;
    }

    /**
     * @param array $data
     * @param array $fillable
     * @return Model
     */
    public function create(array $data, array $fillable = []): Model
    {
        $this->model = new $this->model();
        $object = $this->fill($data, $this->model, $fillable);
        $object->save();

        return $object;
    }

    /**
     * Update values in table.
     *
     * @param array $data
     * @param $target
     * @param array $fillable
     * @return Model
     */
    public function update(array $data, $target, array $fillable = []): Model
    {
        $object = $this->fetch($target);
        $object = $this->fill($data, $object, $fillable);
        $object->save();

        return $object;
    }

    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        $this->model = new $this->model();
        return $this->model->updateOrCreate($attributes, $values);
    }

    /**
     * Delete row from table.
     *
     * @param $target
     * @return bool|null
     */
    public function delete($target): ?bool
    {
        $object = $this->fetch($target);

        return $object->delete();
    }

    /**
     * @param $target
     * @return Model
     */
    public function fetch($target): Model
    {
        if (!($target instanceof Model) && is_numeric($target)) {
            $target = $this->find($target);
        }
        if (is_array($target)) {
            $array_key = array_key_first($target);
            $target = $this->findBy([$array_key => $target[$array_key]]);
        }

        return $target;
    }

    /**
     * @param int $id
     * @param array $columns
     * @param array $relations
     * @param bool $throwException
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function find(
        int $id,
        array $columns = ['*'],
        array $relations = [],
        bool $throwException = true
    ) {
        return $throwException
            ? $this->getQuery()->with($relations)->findOrFail($id, $columns)
            : $this->getQuery()->with($relations)->find($id, $columns);
    }

    /**
     * Find by column and value from table.
     *
     * @param array $credentials
     * @param array $columns
     * @param array $relations
     * @param bool $throwException
     * @return Builder|Model|object|null
     */
    public function findBy(
        array $credentials,
        array $columns = ['*'],
        array $relations = [],
        bool $throwException = true
    ) {
        return $throwException
            ? $this->getQuery()->with($relations)->where($credentials)->firstOrFail($columns)
            : $this->getQuery()->with($relations)->where($credentials)->first($columns);
    }

    /**
     * @param array $credentials
     * @return bool
     */
    public function existBy(array $credentials): bool
    {
        return $this->getQuery()->where($credentials)->exists();
    }

    /**
     * @param Builder $query
     * @param string $scope
     * @param $value
     * @return Builder
     */
    public function attachScope(Builder $query, string $scope, $value): Builder
    {
        $scope = str_replace("_", "", ucwords($scope, " /_"));
        $query = $query->{$scope}($value);

        return $query;
    }

    /**
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    public function filters(Builder $query, array $filters): Builder
    {
        array_walk($filters, function (&$value, $key) use ($query) {
            return $this->attachScope($query, $key, $value);
        });
        return $query;
    }
}
