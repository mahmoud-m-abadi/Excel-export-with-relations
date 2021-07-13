<?php

namespace MahmoudMAbadi\ExcelExportWithRelation\Traits;

trait ModelExportableTrait
{
    /**
     * @return array
     */
    public static function exportShowData(): array
    {
        return [
            'id' => '#',
        ];
    }
}
