<?php

namespace MahmoudMAbadi\ExcelExportWithRelation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MahmoudMAbadi\ExcelExportWithRelation\Exports\ExcelExportWithRelations;
use MahmoudMAbadi\ExcelExportWithRelation\Models\PostExport;
use MahmoudMAbadi\ExcelExportWithRelation\Models\UserExport;

class ExcelExportController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function __invoke(Request $request)
    {
        $data = [
            'name' => 'Name',
            'email' => 'Email',
            'created_at' => 'Created at',
            'relations' => [
                'posts' => [
                    'name' => 'Posts',
                    'relation' => 'posts',
                    'fields' => PostExport::exportShowData()
                ]
            ],
        ];

        return (new ExcelExportWithRelations(new UserExport(), $data))->download('users.xlsx');
    }
}
