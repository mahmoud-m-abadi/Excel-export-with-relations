# Laravel Excel export with multiple relations

This package uses <a href="https://github.com/Maatwebsite/Laravel-Excel" >maatwebsite/excel</a> to make excel report.

## Installation
To install package copy below link. <br />
`composer require mahmoud-m-abadi/excel-export-with-relations`

### Requirement
<ul>
    <li>PHP Version >= 7.3</li>
    <li>Laravel 8</li>
</ul>

## Description
It's assumed that you have one User model, Post model and Post Comment Model. 
User has many posts and each post has many comments and you want to make an export report for User model along with all relations in one Excel file.
<br />
<br />
Now you can make a controller and use the below Export class from this package:
<br />

`MahmoudMAbadi\ExcelExportWithRelation\Exports\ExcelExportWithRelations`
<br />

Your target model to export must have `ModelExportableInterface` to prepare to export using this package.
For example: If you want to make export from User model, you need to add `ModelExportableInterface` class to the UserModel.
You can also use `ModelExportableTrait` to make relevant function or replace it to your own.
<br />

Actually, I've attached some example as Controllers, Models, Route. You can check the files and make sure you are doing process correctly.
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
        return (new ExcelExportWithRelations(new UserExport()))->download('users.xlsx');
    }
}
```
You can check the UserExport model in the package to find out what's been done there.

## License

This package is released under the MIT license.

Copyright (c) 2012-2017 Markus Poerschke

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
