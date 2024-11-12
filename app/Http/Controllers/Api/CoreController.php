<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HasJsonInputValidation;
use App\Http\Controllers\Traits\HasJsonResponses;
use App\Models\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

abstract class CoreController extends Controller
{
    use HasJsonResponses;
    use HasJsonInputValidation;

    protected $debugList = [];

    public function __construct()
    {
        if (config('app.add_sql_debug')) {
            /** @var self $self */
            $self = $this;
            DB::listen(function (QueryExecuted $sql) use ($self) {
                $bindings = array_map(function($bind) {
                    if ($bind instanceof \DateTime) {
                        return $bind->format('Y-m-d H:i:s');
                    } else {
                        return $bind;
                    }
                }, $sql->bindings);

                $query = $sql->sql;

                if ($sql->bindings) {
                    // insert bindings into sql
                    $query = vsprintf(str_replace(array('?'), array('\'%s\''), $sql->sql), $bindings);
                }

                $query = str_replace("\r\n"," ", $query);
                $self->debugList['sql'][] = "[{$sql->time}] $sql->connectionName: {$query}";
            });
        }
    }

    /**
     * Настройки роутинга для контроллера
     * + добавить вызов в routers/api.php
     */
    abstract static public function routers();

    /**
     * Залогиненный пользователь
     */
    protected function user()
    {
        return auth()->user();
    }

    /**
     * Возвращает пользователя по id
     * @param int $userId
     * @return User|null
     */
    protected function userHasById(int $userId)
    {
        return $this->account()->getUserById($userId);
    }

    /**
     * Возвращает кол-во записей на странице по умолчанию
     * @param int $defaultPerPage
     * @return int
     */
    protected function getPerPage(int $defaultPerPage)
    {
        return (int) request()->get('perPage', $defaultPerPage);
    }

    /**
     * Set sorting in query builder
     * @param $builder
     * @param array $fieldsMapping
     */
    protected function setSorting($builder, $fieldsMapping = [])
    {
        $sortBy = request()->get('sortBy');

        if ($sortBy) {
            if (array_key_exists($sortBy, $fieldsMapping)) {
                if (is_callable($fieldsMapping[$sortBy])) {
                    $fieldsMapping[$sortBy]($builder);
                } else {
                    $builder->orderBy($fieldsMapping[$sortBy], $this->getSortingDirection());
                }
            } else {
                $builder->orderBy($sortBy, $this->getSortingDirection());
            }
        }
    }

    protected function getSortingDirection()
    {
        $sortDesc = request()->get('sortDesc', false);
        if ($sortDesc === 'false') {
            $sortDesc = false;
        }

        return ($sortDesc ? 'DESC' : 'ASC');
    }
}
