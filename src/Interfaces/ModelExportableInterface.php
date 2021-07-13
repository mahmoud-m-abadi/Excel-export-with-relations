<?php

namespace MahmoudMAbadi\ExcelExportWithRelation\Interfaces;

interface ModelExportableInterface
{
    /**
     * @return array
     */
    public static function exportShowData(): array;
}
