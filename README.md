# Laravel Excel export with multiple relations

This package uses <a href="https://github.com/Maatwebsite/Laravel-Excel" >maatwebsite/excel</a> to make excel report.

## Installation
To install package copy below link. <br />
`composer require mahmoud-m-abadi/excel-export-with-relations`

### Requirement
<ul>
    <li>PHP Version >= 7.4</li>
    <li>Laravel 8</li>
</ul>

## Description
Assume you have one User model and Post model. User has some posts and you want to make an export report with related posts in one Excel file.
<br />
<br />
Now you can make a controller and use of the below Export class from this package:
<br />

`MahmoudMAbadi\ExcelExportWithRelation\Exports\ExcelExportWithRelations`
<br />

Your models must have `ModelExportableInterface` to prepare to export using this package.
Also there is one Trait to define default fields with relations.
<br />

Actually, I've attached some example as Controllers, Models, Route. You can check the files and make sure you proceed correctly.
<br />

Controller Example:
```php
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
```

## License

This package is released under the MIT license.

Copyright (c) 2012-2017 Markus Poerschke

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
