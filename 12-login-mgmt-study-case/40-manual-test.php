<?php
/**
 * 
 * Manual Test for All Features
 * 
 * 1. Start the development server
 * php -S localhost:8000 -t public
 * 2. Open your browser and navigate to http://localhost:8000
 * 3. Test User Registration
 *   - Go to http://localhost:8000/users/register
 *   - Fill out the registration form and submit
 *   - Verify that you are redirected to the login page
 * 4. Test User Login
 *   - Go to http://localhost:8000/users/login
 *   - Fill out the login form with the registered credentials and submit
 *   - Verify that you are redirected to the home page
 * 5. Test Profile Update
 *   - Go to http://localhost:8000/users/profile
 *   - Update the email address and submit the form
 *   - Verify that the changes are saved and reflected in the profile page
 * 6. Test Password Update
 *   - Go to http://localhost:8000/users/password
 *   - Fill out the password update form with the old and new passwords and submit
 *   - Verify that the password is updated successfully
 * 7. Test Logout
 *   - Click the logout link/button
 *   - Verify that you are redirected to the home page and the session is destroyed
 * 8. Test Error Handling
 *   - Try to register with an existing username
 *   - Try to login with incorrect credentials
 *   - Try to update the profile with invalid data
 *   - Try to update the password with incorrect old password
 *   - Verify that appropriate error messages are displayed for each case
 * 9. Document any issues or unexpected behaviors encountered during testing
 * 10. Review the code for any potential improvements or refactoring opportunities
 * 11. Repeat the tests as necessary after making any code changes
 * * Note: This is a manual test case. For automated testing, consider writing unit and integration tests.
 * 12. After completing the manual tests, ensure all features are functioning as expected.
 * Celebrate the successful implementation and testing of the user management features!
 */ 