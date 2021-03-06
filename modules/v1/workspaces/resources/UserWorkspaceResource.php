<?php


namespace app\modules\v1\workspaces\resources;

use app\modules\v1\setup\resources\UserResource;
use app\modules\v1\workspaces\models\UserWorkspace;

/**
 * Class UserWorkspaceResource
 *
 * @package app\modules\v1\workspaces\resources
 */
class UserWorkspaceResource extends UserWorkspace
{
    public function fields()
    {
        return [
            'id',
            'workspace_id',
            'user_id',
            'role'
        ];
    }

    public function extraFields()
    {
        return ['workspace'];
    }

    public function getUser()
    {
        return $this->hasOne(UserResource::class, ['id' => 'user_id']);
    }
}