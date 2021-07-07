<?php

namespace MahmoudMAbadi\ExcelExportWithRelation\Interfaces;

interface ModelExportableInterface
{
    /**
     * @return array
     */
    public function exportShowData(): array;
}
