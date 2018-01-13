Welcome, and thank you for wanting to contribute to this project.

The guidelines are simple, and we ask the following:
1. Your code is well commented, and PHPDOCS are properly used
2. Your indentation style is 4 spaces and is in K&R Format
3. Your functions are in camelCase() and your variables are in $snake_case for clarity.
4. Committing code should be done in the form of a pull request.
5. Please be descriptive on what you wish to append/improve
6. Please use Scalar Type Hinting!

Example format:
```php
<?php

class MyClass {
    
    public $example_class_variable;

    public function __construct()
    {
        // Example Constructor
    }
    
    /**
     * Example Function
     *
     * A brief description of what this function does.
     * 
     * @param $condition_given
     * @param $expected_result
     * @return boolean
     */
    public function exampleFunctionWithVariables(string $condition_given, string $expected_result) : boolean
    {
        // Example If Statement
        if ($condition_given == $expected_result) {
            $this->myFunction();
        } else {
            // Example Else Statement  
            $this->myFunctionElse();
        }
        
        // Ternary Operator
        return ($condition_given == $expected_result) ? true : false
    }
}
?>
```


Happy Committing, and thank you!
