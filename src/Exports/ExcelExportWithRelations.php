<?php

namespace MahmoudMAbadi\ExcelExportWithRelation\Exports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\View;
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
    private ModelExportableInterface $model;

    /**
     * @var array
     */
    private array $headers;

    /**
     * @var array
     */
    private array $relations = [];

    /**
     * @var int|null
     */
    private ?int $typeId;

    /**
     * @var array
     */
    private array $fields;

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
        $this->fields = count($fields) ? $fields : $this->model->exportShowData();
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
    public function generateRelationHeaders(): array
    {
        return $this->fields['relations'] ?? [];
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
            if (is_array($headerValue) and isset($headerValue['relation'])) {
                if ($headerValue['relation']) {
                    $tdValues->put($headerKey, (optional($item->{$headerValue['relation']})->{$headerValue['field']} ?? null));
                } elseif (isset($headerValue['values'])) {
                    $tdValues->put($headerKey, ($headerValue['values'][$item->{$headerKey}] ?? null));
                }
            } else {
                $tdValues->put($headerKey, $item->$headerKey);
            }
        }

        return $tdValues;
    }

    /**
     * @param Collection $tdValues Td Values.
     * @param Model      $item     Item.
     *
     * @return Collection
     */
    public function generateRelationsColumnValues(Collection $tdValues, Model $item): Collection
    {
        $countMaxRelations = 0;
        foreach ($this->generateRelationHeaders() as $headerValue) {
            if (!is_null($item->{$headerValue['relation']}) and $item->{$headerValue['relation']}->count() > $countMaxRelations) {
                $countMaxRelations = $item->{$headerValue['relation']}->count();
            }
        }

        $items = collect();
        for ($i = 0; $i < $countMaxRelations; $i++) {
            $relationItems = collect();
            foreach ($this->generateRelationHeaders() as $headerValue) {
                $fields = collect();
                foreach ($headerValue['fields'] as $fieldKey => $field) {
                    if (isset($item->{$headerValue['relation']}[$i])) {
                        $fields->put($fieldKey, $item->{$headerValue['relation']}[$i]->$fieldKey);
                    }
                }
                $relationItems->put(
                    $headerValue['relation'],
                    $fields->count() ? $fields : null
                );
            }
            $items->put($i, $relationItems);
        }

        $tdValues->put('relations', $items);
        $tdValues->put('relations_count', $countMaxRelations);

        return $tdValues;
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
