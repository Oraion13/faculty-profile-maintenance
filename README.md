# Faculty Profile Maintenance

## File Structure

- 📄 [README.md](README.md)
- 📂 **api**
  - 📄 [api.php](api/api.php)
  - 📂 **login_register**
    - 📄 [forget_password.php](api/login_register/forget_password.php)
    - 📄 [login.php](api/login_register/login.php)
    - 📄 [logout.php](api/login_register/logout.php)
    - 📄 [register.php](api/login_register/register.php)
    - 📄 [reset_password.php](api/login_register/reset_password.php)
    - 📄 [verify.php](api/login_register/verify.php)
    - 📄 [verify_mail.php](api/login_register/verify_mail.php)
  - 📂 **profile**
    - 📂 **private**
      - 📂 **type_4**
        - 📄 [invigilation_duties.php](api/profile/private/type_4/invigilation_duties.php)
        - 📄 [onduty_orders.php](api/profile/private/type_4/onduty_orders.php)
      - 📂 **type_6**
        - 📄 [incharge_duty_files.php](api/profile/private/type_6/incharge_duty_files.php)
    - 📂 **public**
      - 📂 **type_0**
        - 📄 [positions_prev.php](api/profile/public/type_0/positions_prev.php)
        - 📄 [user_info.php](api/profile/public/type_0/user_info.php)
        - 📄 [users.php](api/profile/public/type_0/users.php)
      - 📂 **type_2**
        - 📄 [departments.php](api/profile/public/type_2/departments.php)
        - 📄 [positions.php](api/profile/public/type_2/positions.php)
      - 📂 **type_3**
        - 📄 [area_of_specialization.php](api/profile/public/type_3/area_of_specialization.php)
        - 📄 [memberships.php](api/profile/public/type_3/memberships.php)
      - 📂 **type_4**
        - 📄 [additional_responsibilities_present.php](api/profile/public/type_4/additional_responsibilities_present.php)
        - 📄 [honors.php](api/profile/public/type_4/honors.php)
        - 📄 [invited_lectures.php](api/profile/public/type_4/invited_lectures.php)
      - 📂 **type_5**
        - 📄 [additional_responsibilities_prev.php](api/profile/public/type_5/additional_responsibilities_prev.php)
        - 📄 [books_published.php](api/profile/public/type_5/books_published.php)
        - 📄 [degree.php](api/profile/public/type_5/degree.php)
        - 📄 [other_employment.php](api/profile/public/type_5/other_employment.php)
        - 📄 [papers_presented.php](api/profile/public/type_5/papers_presented.php)
        - 📄 [papers_published.php](api/profile/public/type_5/papers_published.php)
        - 📄 [patents.php](api/profile/public/type_5/patents.php)
        - 📄 [photo.php](api/profile/public/type_5/photo.php)
        - 📄 [programme_attended.php](api/profile/public/type_5/programme_attended.php)
        - 📄 [programme_chaired.php](api/profile/public/type_5/programme_chaired.php)
        - 📄 [programme_organized.php](api/profile/public/type_5/programme_organized.php)
        - 📄 [special_representations.php](api/profile/public/type_5/special_representations.php)
      - 📂 **type_6**
        - 📄 [exp_abroad.php](api/profile/public/type_6/exp_abroad.php)
        - 📄 [extension_outreach.php](api/profile/public/type_6/extension_outreach.php)
        - 📄 [research_degree.php](api/profile/public/type_6/research_degree.php)
        - 📄 [sponsored_projects_completed.php](api/profile/public/type_6/sponsored_projects_completed.php)
      - 📂 **type_8**
        - 📄 [research_guidance.php](api/profile/public/type_8/research_guidance.php)
- 📂 **config**
  - 📄 [DbConnection.php](config/DbConnection.php)
- 📂 **data**
  - 📄 [data.jsx](data/data.jsx)
  - 📄 [faculty_profile_maintenance.sql.gz](data/faculty_profile_maintenance.sql.gz)
- 📄 [list.md](list.md)
- 📂 **models**
  - 📄 [Positions_prev.php](models/Positions_prev.php)
  - 📄 [Type_2.php](models/Type_2.php)
  - 📄 [Type_3.php](models/Type_3.php)
  - 📄 [Type_4.php](models/Type_4.php)
  - 📄 [Type_5.php](models/Type_5.php)
  - 📄 [Type_6.php](models/Type_6.php)
  - 📄 [Type_8.php](models/Type_8.php)
  - 📄 [User_info.php](models/User_info.php)
  - 📄 [Users.php](models/Users.php)
  - 📄 [model.php](models/model.php)
- 📂 **utils**
  - 📄 [loggedin_verified.php](utils/loggedin_verified.php)
  - 📄 [send.php](utils/send.php)
  - 📄 [verification_mail.php](utils/verification_mail.php)
