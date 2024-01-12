<?php
class Animal
{
    // Properties
    public $name;
    public $sound;

    // Constructor
    public function __construct($name, $sound)
    {
        $this->name = $name;
        $this->sound = $sound;
    }

    // Method
    public function makeSound()
    {
        echo "{$this->name} says {$this->sound}\n";
    }
}

// Create an object (instance of the class)
$cat = new Animal("Cat", "Meow");

// Call a method
$cat->makeSound();

// Inheritance example
class Dog extends Animal
{
    // Additional method
    public function fetch()
    {
        echo "{$this->name} is fetching!\n";
    }
}

// Create an instance of the Dog class
$dog = new Dog("Dog", "Woof");

// Call methods from the parent class
$dog->makeSound();

// Call method specific to the Dog class
$dog->fetch();
