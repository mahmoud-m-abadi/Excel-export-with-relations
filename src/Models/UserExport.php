<?php

namespace MahmoudMAbadi\ExcelExportWithRelation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MahmoudMAbadi\ExcelExportWithRelation\Interfaces\ModelExportableInterface;
use MahmoudMAbadi\ExcelExportWithRelation\Traits\ModelExportableTrait;

class UserExport extends Model implements ModelExportableInterface
{
    use ModelExportableTrait;

    protected $table = 'users';

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(PostExport::class, 'user_id');
    }
}
