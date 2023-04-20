<?php

namespace App\Http\Services\Case;

class BaseCaseService
{
    // Update or Create Model
    protected function handleModel($data, $class): bool
    {
        if (!$this->hasID($data)) {
            $class::create($data);
            return true;
        }

        $model = $class::find($data['id']);

        if (is_null($model)) {
            return false;
        }

        $model->update($data);
        return true;
    }

    // Update or Create Models
    protected function handleModels($data, $caseID, $class): void
    {
        if (count($data) <= 0) return;

        foreach ($data as $key => $item) {
            if (is_null($item)) continue;
            if ($this->hasID($item)) {
                $class::find($item['id'])->update($item);
                continue;
            }
            unset($item['case_id']);
            $class::create(['case_id' => $caseID, ...$item]);
        }
    }

    protected function hasID($data): bool
    {
        return isset($data['id']) && !is_null($data['id']);
    }
}
