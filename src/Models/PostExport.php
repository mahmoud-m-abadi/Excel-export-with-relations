<?php

namespace MahmoudMAbadi\ExcelExportWithRelation\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MahmoudMAbadi\ExcelExportWithRelation\Interfaces\ModelExportableInterface;
use MahmoudMAbadi\ExcelExportWithRelation\Traits\ModelExportableTrait;

class PostExport extends Model implements ModelExportableInterface
{
    use ModelExportableTrait;

    protected $table = 'posts';

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array
     */
    public static function exportShowData(): array
    {
        return [
            'id' => 'ID',
            'title' => 'title',
            'body' => 'Body',
            'published_at' => 'Published at',
        ];
    }
}
