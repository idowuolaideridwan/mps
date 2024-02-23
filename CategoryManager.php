<?php

class Category {
    public $name;
    public $parent = null;
    public $children = [];

    public function __construct($name, $parent = null) {
        $this->name = $name;
        $this->parent = $parent;
    }
}

class CategoryManager {
    protected $categories = [];

    public function create(string $categoryName, string $parentName = null): void {
        if (array_key_exists($categoryName, $this->categories)) {
            throw new InvalidArgumentException("Category already exists.");
        }

        $parent = null;
        if ($parentName !== null) {
            if (!array_key_exists($parentName, $this->categories)) {
                throw new InvalidArgumentException("Parent does not exist.");
            }
            $parent = $this->categories[$parentName];
        }

        $category = new Category($categoryName, $parent);
        $this->categories[$categoryName] = $category;

        if ($parent !== null) {
            $parent->children[$categoryName] = $category;
        }
    }

    public function getRootCategories() {
        $rootCategories = [];
        foreach ($this->categories as $category) {
            if ($category->parent === null) {
                $rootCategories[] = $category->name;
            }
        }
        return $rootCategories;
    }

    public function getSubcategories(string $parentName): array {
        if (!array_key_exists($parentName, $this->categories)) {
            throw new InvalidArgumentException("Category does not exist.");
        }
        return array_keys($this->categories[$parentName]->children);
    }

    public function getPath(string $categoryName): array {
        if (!array_key_exists($categoryName, $this->categories)) {
            throw new InvalidArgumentException("Category does not exist.");
        }
        $path = [];
        $current = $this->categories[$categoryName];
        while ($current !== null) {
            array_unshift($path, $current->name);
            $current = $current->parent;
        }
        return $path;
    }
}

// Example usage
$c = new CategoryManager();   
$c->create('root1', null);  // ('root category','')
$c->create('cat1', 'root1'); // ('child category/subcategory','category')
$c->create('cat2', 'root1');
$c->create('cat3', 'cat2');
$c->create('cat4', 'cat2');
$c->create('cat5', 'cat2');
$c->create('cat6', 'cat2');
$c->create('cat7', 'cat6');
$c->create('root2', null);
$c->create('cat8', 'root2');
$c->create('cat9', 'cat8');
$c->create('cat10', 'cat8');

//$c->create('cat10', 'root1'); // InvalidArgumentException : Category already exists
//$c->create('cat11', 'cat12'); // InvalidArgumentException : Parent does not exist.

// Demonstrating the usage of getSubcategories and getPath methods
echo implode(',', $c->getSubcategories('root1')) . "<br>"; // Outputs: cat1,cat2
echo implode(',', $c->getSubcategories('cat2')) . "<br>";  // Outputs: cat3,cat4,cat5,cat6
echo implode(',', $c->getSubcategories('cat8')) . "<br>";  // Outputs: cat9,cat10
echo implode(',', $c->getPath('cat7')) . "<br>";  // Outputs: root1,cat2,cat6,cat7
