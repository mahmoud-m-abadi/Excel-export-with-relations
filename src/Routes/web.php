<?php

use MahmoudMAbadi\ExcelExportWithRelation\Http\Controllers\ExcelExportController;

Route::get('excel-export-user', ExcelExportController::class)->name('mahmoudMAbadi.excelExportWithRelation.excel');
