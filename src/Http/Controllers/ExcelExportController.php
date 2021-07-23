<?php

namespace MahmoudMAbadi\ExcelExportWithRelation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MahmoudMAbadi\ExcelExportWithRelation\Exports\ExcelExportWithRelations;
use MahmoudMAbadi\ExcelExportWithRelation\Models\UserExport;

class ExcelExportController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Request $request)
    {
        return (new ExcelExportWithRelations(new UserExport()))->download('users.xlsx');
    }
}
