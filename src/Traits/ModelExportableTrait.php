<?php

namespace MahmoudMAbadi\ExcelExportWithRelation\Traits;

trait ModelExportableTrait
{
    /**
     * @return array
     */
    public function exportShowData(): array
    {
        return [
            self::ID => '#',
        ];
    }
}
