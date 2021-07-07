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
        $data = [
            'name' => 'Title',
            'email' => 'Body',
            'created_at' => 'Created at',
            'relations' => [
                'posts' => [
                    'name' => 'Posts',
                    'relation' => 'posts',
                    'fields' => [
                        'title' => 'Title',
                        'body' => 'Body',
                        'published_at' => 'Published at',
                    ]
                ]
            ],
        ];

        return (new ExcelExportWithRelations(new UserExport(), $data))->download('users.xlsx');
    }
}
