<?php

namespace App\Db\Core;

use App\DB\Core\DateTimeField;
use App\DB\Core\ImageField;
use App\DB\Core\StringField;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class Crud
{
    public function __construct(
        private object $model,
        private ?array $data,
        private ?int $id,
        private $editMode,
        private $deleteMode,
    ) {
        $this->model = $model;
        $this->data = $data;
        $this->id = $id;
        $this->editMode = $editMode;
        $this->deleteMode = $deleteMode;
        self::$tableName = $model->getTable();
    }

    public static string $imageDirectory = '';
    public static string $tableName = '';
    public object $parentModelInstance;
    public string $relationship = '';
    private object $record;

    public function setImageDirectory(string $directoryPath, string $tablename)
    {
        self::$imageDirectory = $directoryPath;
        self::$tableName = $tablename;
    }

    // relationship saving with crud
    // public function relationshipSaving(object $parentModelInstance, string $relationship)
    // {
    //     $this->parentModelInstance = $parentModelInstance;
    //     $this->relationship = $relationship;
    // }

    public function getData(string $model, string $id)
    {
        $modelInstance = new $model;
        return $modelInstance->findOrFail($id);
    }

    public function execute(): mixed
    {
        try {

            if ($this->editMode) {
                return $this->handleEditMode();
            } elseif ($this->deleteMode) {
                return $this->handleDeleteMode();
            } else {
                return $this->handleStoreMode();
            }
        } catch (QueryException $e) {
            return response($e->getMessage());
        }
    }

    protected function iterateData(array $data, ?object $record = null): object
    {
        $target = $record ?? $this->model;
        foreach ($data as $column => $value) {
            $target->{$column} = $this->savableField($column)->setValue($value)->execute();
        }
        return $target;
    }

    protected function handleStoreMode(): object
    {
        if ($this->data) {
            $model = $this->iterateData($this->data, null);
            return $model->save() ? $this->model : response(status: 500);
        }
    }

    // relationship saving with crud
    // protected function handleStoreMode(): object
    // {
    //     if ($this->data) {
    //         if ($this->relationship !== '') {
    //             $methodName = $this->relationship;
    //             $modelInstance = new $this->model;
    //             $modelInstance->fill($this->iterateData($this->data, null)->toArray());
    //             $modelInstance->$methodName()->associate($this->parentModelInstance);
    //             return $modelInstance->save() ? $modelInstance : response(status: 500);
    //         }
    //         $model = $this->iterateData($this->data, null);
    //         return $model->save() ? $this->model : response(status: 500);
    //     }
    // }

    protected function handleEditMode(): object
    {
        $this->record = $this->model->findOrFail($this->id);
        if ($this->model->getTable() === Config::get('variables.IMAGE')) {
            $this->deleteImage();
        }
        if ($this->data) {
            return $this->iterateData($this->data, $this->record)->save() ? $this->record : response(status: 500);
        }
    }

    protected function handleDeleteMode(): bool
    {
        $this->record = $this->model->findOrFail($this->id);
        //return $this->record->delete() ? $this->record : response(status: 500);
        return $this->record->delete() ? true : false;
    }

    // protected function handleEditORDeleteMode()
    // {
    //     $this->record = $this->model->findOrFail($this->id);
    //     if (!$this->deleteMode) {
    //         if ($this->data) {
    //             $result = $this->iterateData($this->data, $this->record);
    //             return $result->save() ? $this->record : response()->failed();
    //         }
    //     }
    //     return $this->record->delete() ? $this->record : response()->failed();
    // }

    public function savableField($column)
    {
        return $this->model->saveableFields()[$column];//new instance fil
    }

    public function deleteImage()
    {
        $old_image = $this->record->upload_url;
        Storage::delete($old_image);
    }

    public static function storeImage($value, $imageDirectory, $imageName)
    {
        $value->storeAs($imageDirectory, $imageName);
    }
}
