import httpService from "../../../core/services/httpService";

const employeeService = {
  url: 'v1/users/employee',

  get(params = {
    sort: '-created_at',
    expand: 'userDepartments, userProfile, userDepartments.department, userDepartments.country'
  }) {
    return httpService.get(this.url, {params});
  },
  getModalDropdownData() {
    return httpService.get(this.url)
  }
}

export default employeeService;