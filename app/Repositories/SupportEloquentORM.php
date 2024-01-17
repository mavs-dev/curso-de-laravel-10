<?php

namespace App\Repositories;

use App\DTO\{CreateSupportDTO, UpdateSupportDTO};
use App\Models\Support;
use App\Repositories\SupportRepositoryInterface;
use stdClass;

class SupportEloquentORM implements SupportRepositoryInterface
{

    public function __construct(protected Support $model)
    {
    }

    public function getAll(string $filter = null): array
    {
        $supports =  $this->model
        ->where(function ($query) use ($filter){
            if($filter) {
                $query->where('subject', $filter);
                $query->orWhere('body', 'like', "%{$filter}%");
            }
        })
        ->get()
        ->toArray();

        $this->convertItemsToObject($supports);

        return $supports;
    }

    public function findOne(string $id): stdClass|null
    {

        $support = $this->model->find($id);

        if(!$support) {
            return null;
        }

        return (object) $support->toArray();
    }

    public function new(CreateSupportDTO $dto): stdClass
    {
        throw (object) $this->model->create((array) $dto)->toArray();
    }

    public function update(UpdateSupportDTO $dto): stdClass|null
    {
        if(!$support = $this->model->find($dto->id)){
            return null;
        }

        return (object) $support->update((array) $dto)->toArray();
    }

    public function delete(string $id): void
    {

        $this->model->findOrFail($id)->delete();
    }

    private function convertItemsToObject(array &$arrayOfArray): void {
        foreach($arrayOfArray as $key=>$value){
            $arrayOfArray[$key] = (object) $value;
        }
    }
}
