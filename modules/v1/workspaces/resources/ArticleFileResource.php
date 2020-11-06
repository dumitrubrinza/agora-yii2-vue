<?php


namespace app\modules\v1\workspaces\resources;


use app\modules\v1\users\resources\UserResource;
use app\modules\v1\workspaces\models\ArticleFile;
use app\modules\v1\workspaces\models\TimelinePost;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class ArticleFileResource
 *
 * @package app\modules\v1\workspaces\resources
 */
class ArticleFileResource extends ArticleFile
{
    const VIDEO = 'video';

    public $share_count;

    public function fields()
    {
        return [
            'id',
            'name',
            'label',
            'size',
            'to_process',
            'processing',
            'share_count' => function () {
                return $this->getShareCount();
            },
            'mime' => function () {
                $mime = explode('/', $this->mime)[0];
                return $mime == self::VIDEO ? $mime : $this->mime;
            },
            'path' => function () {
                return $this->getPublicUrl();
            },
            'created_at' => function () {
                return $this->updated_at * 1000;
            },
            'updated_at' => function () {
                return $this->updated_at * 1000;
            },
            'content'
        ];
    }

    public function extraFields()
    {
        return ['updatedBy'];
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(UserResource::class, ['id' => 'updated_by']);
    }

    /**
     * Get public url
     *
     * @return bool|string
     */
    public function getPublicUrl()
    {
        if (explode('/', $this->mime)[0] == self::VIDEO) {
            return $this->path ? Yii::getAlias('@storageUrl' . explode('.', $this->path)[0] . '.mp4') : '';
        } else {
            return $this->path ? Yii::getAlias('@storageUrl' . $this->path) : '';
        }
    }

    /**
     * Get share article file count
     *
     * @return bool|int|string|null
     */
    public function getShareCount()
    {
        $timelinePostTb = TimelinePostResource::tableName();
        $tb = $this::tableName();

        return $this::find()
            ->byId($this->id)
            ->innerJoin("$timelinePostTb t", [
                "AND",
                ["t.action" => TimelinePost::ACTION_SHARE_FILE],
                "t.article_id = $tb.article_id",
                "FIND_IN_SET($tb.id,SUBSTR(t.attachment_ids,2,LENGTH(t.attachment_ids)-2)) > 0"
            ])
            ->count();
    }
}