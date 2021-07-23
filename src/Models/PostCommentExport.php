<?php

namespace MahmoudMAbadi\ExcelExportWithRelation\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MahmoudMAbadi\ExcelExportWithRelation\Interfaces\ModelExportableInterface;
use MahmoudMAbadi\ExcelExportWithRelation\Traits\ModelExportableTrait;

class PostCommentExport extends Model implements ModelExportableInterface
{
    use ModelExportableTrait;

    protected $table = 'post_comments';

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserExport::class);
    }

    /**
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(PostExport::class);
    }

    /**
     * @return array
     */
    public static function exportShowData(): array
    {
        return [
            'id' => 'ID',
            'user_id' => [
                'name' => 'User',
                'relation' => 'user',
                'field' => 'name',
            ],
            'post' => [
                'name' => 'Post',
                'relation' => 'post',
                'field' => 'title',
            ],
            'message' => 'Message',
            'created_at' => 'Created at',
        ];
    }
}
