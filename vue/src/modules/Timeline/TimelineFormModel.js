import BaseModel from "@/core/components/input-widget/BaseModel";
import i18n from "@/shared/i18n";

export default class TimelineFormModel extends BaseModel {
  workspace_id = null;
  file = null;
  description = '';

  rules = {
    workspace: [
      {rule: 'required'}
    ]
  }

  attributeLabels = {
    workspace: i18n.t('Workspace'),
    file: i18n.t('Image or Video'),
    description: i18n.t('Description'),
  }

  constructor(data = {}) {
    super();
    if (data.workspaceTimelinePosts) {
      data.workspace_id = data.workspaceTimelinePosts.map(w => w.workspace_id).toString();
    }
    data.created_at = data.created_at / 1000;
    data.updated_at = data.updated_at / 1000;
    Object.assign(this, {...data});
  }
}