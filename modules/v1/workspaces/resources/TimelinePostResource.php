<?php
/**
 * Created By Nika Gelashvili
 * Date: 30.09.20
 * Time: 13:26
 */

namespace app\modules\v1\workspaces\resources;


use app\modules\v1\users\models\query\UserQuery;
use app\modules\v1\users\resources\UserResource;
use app\modules\v1\workspaces\models\TimelinePost;
use app\rest\ValidationException;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class TimelinePostResource extends TimelinePost
{
    const IS_FILE = 1;

    /**
     * @return array
     * @author Saiat Kalbiev <kalbievich11@gmail.com>
     */
    public function fields()
    {
        return [
            'id',
            'action',
            'workspace_id',
            'description',
            'file_url' => function () {
                return $this->getFileUrl();
            },
            'is_file' => function () {
                return TimelinePostResource::IS_FILE;
            },
            'name',
            'size',
            'created_at' => function () {
                return $this->created_at * 1000;
            },
            'updated_at' => function () {
                return $this->updated_at * 1000;
            },
        ];
    }

    /**
     * @return array|string[]
     * @author Saiat Kalbiev <kalbievich11@gmail.com>
     */
    public function extraFields()
    {
        return [
            'createdBy',
            'updatedBy',
            'article',
            'timelineComments',
            'userLikes',
            'myLikes',
            'workspace',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getWorkspace()
    {
        return $this->hasOne(WorkspaceResource::class, ['workspace_id' => 'id']);
    }

    /**
     * Gets query for [[TimelineComments]].
     *
     * @return ActiveQuery
     */
    public function getTimelineComments()
    {
        return $this->hasMany(UserCommentResource::class, ['timeline_post_id' => 'id'])->orderBy('created_at DESC');
    }

    /**
     * Gets query for [[UserLikes]].
     *
     * @return ActiveQuery
     */
    public function getUserLikes()
    {
        return $this->hasMany(UserLikeResource::class, ['timeline_post_id' => 'id']);
    }

    public function load($data, $formName = null)
    {
        $this->file = UploadedFile::getInstanceByName('file');
        return parent::load($data, $formName);
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     * @throws Exception
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if (!$this->file) {
            return parent::save($runValidation, $attributeNames);
        }
        $dirPath = '/timelinePosts/' . $this->workspace_id;
        $this->name = $this->file->name;
        $this->size = $this->file->size;
        $this->file_path = $dirPath . '/' . Yii::$app->security->generateRandomString() . '.' . $this->file->extension;

        $fullPath = Yii::getAlias('@storage' . $this->file_path);
        if (!is_dir(dirname($fullPath))) FileHelper::createDirectory(dirname($fullPath));
        if (!$this->file->saveAs($fullPath, false)) {
            throw new ValidationException(Yii::t('app', 'File not uploaded'));
        }

        return parent::save($runValidation, $attributeNames);
    }

    /**
     * @return UserQuery|ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(UserResource::class, ['id' => 'created_by']);
    }

    /**
     * @return UserQuery|ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(UserResource::class, ['id' => 'updated_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(ArticleResource::class, ['id' => 'article_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMyLikes()
    {
        return $this->hasMany(UserLikeResource::class, ['timeline_post_id' => 'id'])
            ->andWhere(['created_by' => Yii::$app->user->id]);
    }
}