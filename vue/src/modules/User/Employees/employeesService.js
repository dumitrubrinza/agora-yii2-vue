import httpService from "../../../core/services/httpService";

const employeeService = {
  url: 'v1/users/employee',

  get(params = {
    sort: '-created_at',
    expand: 'userDepartments, userProfile, userDepartments.department, userDepartments.department.country'
  }) {
    return httpService.get(this.url, {params});
  },
  getModalDropdownData(params = {expand: 'departments'}) {
    return httpService.get(this.url + '/get-dropdown', {params})
  }
}

export default employeeService;