<?php
/*
Summary: Handling Form POST in PHP

1. HTML Form Setup:
    - Use the <form> tag with method="post" to send data securely.
    - Example:
      <form action="12-form-post.php" method="post">
            <input type="text" name="username">
            <input type="submit" value="Submit">
      </form>

2. Receiving POST Data in PHP:
    - Use the $_POST superglobal array to access submitted data.
    - Example:
      $username = $_POST['username'];

3. Validating and Sanitizing Input:
    - Always validate and sanitize user input to prevent security risks.
    - Example:
      $username = htmlspecialchars(trim($_POST['username']));

4. Checking if Form is Submitted:
    - Use isset() or !empty() to check if the form was submitted.
    - Example:
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // process form
      }

5. Security Considerations:
    - Use htmlspecialchars() to prevent XSS attacks.
    - Validate data types and required fields.
    - Consider CSRF protection for sensitive forms.

6. Example Code:
*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $username = htmlspecialchars(trim($_POST['username'] ?? ''));
     if (!empty($username)) {
          echo "Hello, " . $username . "!";
     } else {
          echo "Please enter your username.";
     }
}
?>

<!-- Example HTML Form -->
<form action="12-form-post.php" method="post">
     <label for="username">Username:</label>
     <input type="text" name="username" id="username">
     <input type="submit" value="Submit">
</form>