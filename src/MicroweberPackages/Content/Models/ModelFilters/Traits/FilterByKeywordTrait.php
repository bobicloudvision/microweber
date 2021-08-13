<?php
/**
 * Created by PhpStorm.
 * User: Bojidar
 * Date: 10/15/2020
 * Time: 3:42 PM
 */

namespace MicroweberPackages\Content\Models\ModelFilters\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;

trait FilterByKeywordTrait
{
    public function keyword($keyword)
    {
        $model = $this->getModel();
        $table = $model->getTable();
        $searchInFields = $model->getSearchable();
        $guardedFields = $model->getGuarded();
        $tableFields = $model->getConnection()->getSchemaBuilder()->getColumnListing($table);

        if (isset($this->input['searchInFields'])) {
            if (strpos($this->input['searchInFields'], ',') !== false) {
                $searchInFields = explode(',', $this->input['searchInFields']);
            }
        }

        if ($searchInFields and $tableFields) {
            $searchInFields = array_diff($tableFields, $searchInFields);
        }

        if ($searchInFields and $guardedFields) {
            $searchInFields = array_diff($searchInFields, $guardedFields);
        }

        dump($searchInFields);

        $this->query->where(function ($query) use ($table, $searchInFields, $keyword) {
            if ($searchInFields) {
                foreach ($searchInFields as $field) {
                    $query->orWhere($table .'.'. $field, 'LIKE', '%' . $keyword . '%');
                }
            }
        });

        return $this->query;
    }

}
