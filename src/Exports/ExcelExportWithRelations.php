<?php

namespace MahmoudMAbadi\ExcelExportWithRelation\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use MahmoudMAbadi\ExcelExportWithRelation\Interfaces\ModelExportableInterface;

class ExcelExportWithRelations implements FromView, WithEvents
{
    use Exportable;
    use RegistersEventListeners;

    /**
     * @var ModelExportableInterface
     */
    private $model;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    private $relations = [];

    /**
     * @var Collection
     */
    private $headerRelations = [];

    /**
     * @var int|null
     */
    private $typeId;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var int
     */
    private $countMaxRelations = 0;

    /**
     * @var Collection
     */
    private $generatedRelations = [];

    /**
     * ExcelExport constructor.
     * @param ModelExportableInterface $model
     * @param array                    $fields
     * @param integer|null             $typeId
     */
    public function __construct(
        ModelExportableInterface $model,
        array $fields = [],
        ?int $typeId = null
    ) {
        $this->model = $model;
        $this->typeId = $typeId;
        $this->fields = count($fields) ? $fields : $this->model::exportShowData();
        $this->relations = collect();
        $this->headerRelations = collect();
        $this->generatedRelations = collect();
    }

    /**
     * @return View
     */
    public function view(): View
    {
        $relations = $this->fields['relations'] ?? [];
        $model = $this->model::when(count($relations), function ($joinRelations) use ($relations) {
            return $joinRelations->with(collect($relations)->pluck('relation')->toArray());
        })
            ->select('*')
            ->when($this->typeId, function ($addId) {
                return $addId->whereId($this->typeId);
            })
            ->get();

        return view('ExcelExportWithRelation::export.general', [
            'model' => $model,
            'generateHeaders' => $this->generateHeaders(),
            'items' => $this->generateTdValues($model),
            'relationHeaders' => $this->generateRelationHeaders(),
        ]);
    }

    /**
     * @return Collection
     */
    public function generateHeaders(): Collection
    {
        $selectHeaders = collect();

        foreach ($this->fields as $column => $header) {
            if ($column == 'relations') continue;

            $selectHeaders->put($column, $header);
        }

        if (!$selectHeaders->contains('id')) {
            $selectHeaders->put('id', 'id');
        }

        return $selectHeaders;
    }

    /**
     * @return array
     */
    public function generateRelationHeaders(): Collection
    {
        $this->headerRelationRecursive($this->fields['relations'] ?? []);
        return $this->headerRelations;
    }

    /**
     * @param $relations
     */
    public function headerRelationRecursive($relations)
    {
        foreach ($relations as $relation) {
            $justMainHeader = $relation['fields'];

            $this->headerRelations->put($relation['name'],
                collect($justMainHeader)->reject(function ($item, $index) {
                    return $index == 'relations';
                })
            );

            if(isset($relation['fields']['relations']) and count($relation['fields']['relations'] )) {
                $this->headerRelationRecursive($relation['fields']['relations']);
            }
        }
    }

    /**
     * @param Collection $items Items.
     *
     * @return Collection
     */
    public function generateTdValues(Collection $items): Collection
    {
        return $items->map(function ($item) {
            $item->tdValues = collect();
            $item->tdValues = $this->generateMainColumnValues($item->tdValues, $item);
            $item->tdValues = $this->generateRelationsColumnValues($item->tdValues, $item);

            return $item;
        });
    }

    /**
     * @param Collection $tdValues Td Values.
     * @param Model      $item     Item.
     *
     * @return Collection
     */
    public function generateMainColumnValues(Collection $tdValues, Model $item): Collection
    {
        foreach ($this->generateHeaders() as $headerKey => $headerValue) {
            $tdValues->put($headerKey, $this->generateColumnValues($item, $headerKey, $headerValue));
        }

        return $tdValues;
    }

    /**
     * @param Model $item
     * @param string $fieldKey
     * @param mixed $field
     * @return mixed|null
     */
    public function generateColumnValues(Model $item, string $fieldKey, $field)
    {
        if (is_array($field) and isset($field['relation'])) {
            return optional($item->{$field['relation']})->{$field['field']} ?? null;
        }
        if (is_array($field) and isset($field['values'])) {
            return $field['values'][$item->{$fieldKey}] ?? null;
        }

        return $item->$fieldKey;
    }

    /**
     * @param Collection $tdValues Td Values.
     * @param Model      $item     Item.
     *
     * @return Collection
     */
    public function generateRelationsColumnValues(Collection $tdValues, Model $item): Collection
    {
        $this->columnRelationCountRecursive($item, $this->fields['relations'] ?? []);
        $this->columnRelationRecursive($item, $this->fields['relations'] ?? []);

        $tdValues->put('relations', $this->generatedRelations);
        $tdValues->put('relations_count', $this->countMaxRelations);

        return $tdValues;
    }

    /**
     * @param $model
     * @param $relations
     */
    public function columnRelationCountRecursive($model, $relations)
    {
        foreach ($relations as $relation) {
            if (!is_null($model->{$relation['relation']}) and $model->{$relation['relation']}->count() > $this->countMaxRelations) {
                $this->countMaxRelations = $model->{$relation['relation']}->count();
            }

            if(isset($relation['fields']['relations']) and count($relation['fields']['relations'] )) {
                foreach ($model->{$relation['relation']} as $modelRelation) {
                    $this->columnRelationCountRecursive($modelRelation, $relation['fields']['relations']);
                }
            }
        }
    }

    /**
     * @param ModelExportableInterface $model
     * @param array $relations
     */
    public function columnRelationRecursive($model, array $relations)
    {
        $this->generatedRelations = collect();

        for ($i = 0; $i < $this->countMaxRelations; $i++) {
            $relationItems = $this->columnRelationValuesRecursive($i, $model, $relations, collect());
            $this->generatedRelations->put($i, $relationItems);
        }
    }

    /**
     * @param int $i
     * @param Model $model
     * @param array $relation
     * @param Collection $relationValueItems
     * @return Collection
     */
    public function columnRelationValuesRecursive($i, $model, $relation, $relationValueItems)
    {
        foreach ($relation as $allFields) {
            $fieldsValues = collect();
            foreach ($allFields['fields'] as $fieldKey => $field) {
                if($fieldKey != 'relations') {
                    if (isset($model->{$allFields['relation']}[$i])) {
                        $fieldsValues->put($fieldKey, $this->generateColumnValues($model->{$allFields['relation']}[$i], $fieldKey, $field));
                    } else {
                        $fieldsValues->put($fieldKey, null);
                    }
                }
            }

            $relationValueItems->put(
                $allFields['relation'].$i,
                $fieldsValues->count() ? $fieldsValues : null
            );

            if(isset($allFields['fields']['relations'])) {
                foreach ($model->{$allFields['relation']} as $modelRelation) {
                    $relationValueItems = $this->columnRelationValuesRecursive($i, $modelRelation, $allFields['fields']['relations'], $relationValueItems);
                }
            }
        }

        return $relationValueItems;
    }

    /**
     * Run event.
     *
     * @param AfterSheet $event
     */
    public static function afterSheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        $sheet->getRowDimension(1)->setRowHeight(30);

        $header = $sheet->getStyle('A1:' . $sheet->getHighestDataColumn() . '1');
        $header->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $header->getFont()->setBold(true);
        $header->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('00000000');
        $header->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

        $other = $sheet->getStyle('A2:' . $sheet->getHighestDataColumn() . $sheet->getHighestRow());
        $other->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        foreach ([$header, $other] as $item) {
            $item->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $item->getAlignment()->setWrapText(true);
        }
    }
}
