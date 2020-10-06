import BaseModel from "../../../core/components/input-widget/BaseModel";
import i18n from "../../../shared/i18n";
import RoleModel from "@/modules/User/Employees/RoleModel";
import UserDepartmentModel from "@/modules/User/Employees/UserDepartmentModel";

export default class EmployeeModel extends BaseModel {
  id = null;
  first_name = null;
  last_name = null;
  email = null;
  roles = []
  userDepartments = [];

  rules = {
    first_name: 'required',
    last_name: 'required',
    email: [
      {rule: 'required'},
      {rule: 'email'},
    ],
  }

  attributeLabels = {
    email: i18n.t('Email'),
    first_name: i18n.t('First Name'),
    last_name: i18n.t('Last Name'),
  };

  constructor(data = {}) {
    super();

    const userDepartments = [];

    if (data.userDepartments) {
      for (let userDepartment of data.userDepartments) {
        userDepartments.push(new UserDepartmentModel({
          id: userDepartment.id,
          position: userDepartment.position,
          country_id: userDepartment.department.country_id,
          department_id: userDepartment.department.id
        }))
      }
    }
    const roles = [];
    if (data.roles) {
      for (let role in data.roles) {
        if (data.roles.hasOwnProperty(role)) {
          roles.push(new RoleModel({name: role}))
        }
      }
    }
    data.userDepartments = userDepartments;
    data.roles = roles;
    Object.assign(this, {...data});
  }
}