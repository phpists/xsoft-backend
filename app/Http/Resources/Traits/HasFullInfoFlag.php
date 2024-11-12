<?php

namespace App\Http\Resources\Traits;

trait HasFullInfoFlag
{
    /** @var false|mixed Полная информация */
    protected $fullInfo;
    protected $fullChildrenInfo;

    public function __construct($resource, $fullInfo = true)
    {
        $this->fullInfo = $fullInfo;
        parent::__construct($resource);
    }
}
