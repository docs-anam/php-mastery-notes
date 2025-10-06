<?php
/*
* PHP MVC Model Summary
*
* In the MVC (Model-View-Controller) architecture, the "Model" is responsible for managing the application's data, logic, and rules. It acts as the data layer, interacting with databases or other data sources, and encapsulates the business logic.
*
* Key Responsibilities of the Model:
* 1. Data Management:
*     - Handles CRUD operations (Create, Read, Update, Delete) with the database.
*     - Retrieves and stores data as required by the application.
*
* 2. Business Logic:
*     - Contains the core logic and rules of the application.
*     - Validates data before saving or processing.
*
* 3. Data Representation:
*     - Represents the structure of data (e.g., User, Product).
*     - Can be implemented as classes or objects.
*
* 4. Communication:
*     - Interacts with the Controller to receive instructions.
*     - Provides data to the Controller, which then passes it to the View.
*
* Typical Model Structure in PHP:
* - A model is usually a PHP class.
* - It may include properties for data fields and methods for data operations.
* - Example: A `User` model class with methods like `findUserById($id)`, `save()`, `delete()`.
*
* Example Model Class:
* class User {
*      public $id;
*      public $name;
*      public $email;
*
*      public function find($id) {
*           // Query database to find user by ID
*      }
*
*      public function save() {
*           // Save user data to database
*      }
* }
*
* Best Practices:
* - Keep models focused on data and logic, not presentation.
* - Use models to validate and sanitize data.
* - Separate database access logic from controllers and views.
*
* Summary:
* The Model in PHP MVC is the backbone for data and business logic. It ensures data integrity, encapsulates rules, and provides a clean interface for controllers to interact with the application's data.
*/

/**
 * ########### Implementation the model in PHP MVC continuing from the previous explanation and project.
 *
 * 1. create a new variable as representation of data in the home controller
 *   (app/App/Controller/HomeController.php)
 * 
 * ...
 * 
 *  public function index(): void
 *  {
 *      $model = [
 *          'title' => 'Home Page',
 *          'content' => 'Welcome to the Home Page!'
 *      ];
 *
 *      echo "HomeController.index()";
 *  }
 * 
 */