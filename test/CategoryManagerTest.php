<?php

require_once 'vendor/autoload.php'; // Include PHPUnit's autoloader
require_once __DIR__ . '/../CategoryManager.php';

use PHPUnit\Framework\TestCase;

class CategoryManagerTest extends TestCase {
    public function testCreateCategory() {
        $categoryManager = new CategoryManager();

        // Test creating root category
        $categoryManager->create('root1');
        $this->assertTrue(in_array('root1', $categoryManager->getRootCategories()));

        // Test creating subcategory
        $categoryManager->create('cat1', 'root1');
        $this->assertTrue(in_array('cat1', $categoryManager->getSubcategories('root1')));

        // Test creating category with non-existent parent
        $this->expectException(InvalidArgumentException::class);
        $categoryManager->create('cat2', 'nonexistentparent');
    }

    public function testGetSubcategories() {
        $categoryManager = new CategoryManager();

        // Adding categories
        $categoryManager->create('root1');
        $categoryManager->create('cat1', 'root1');
        $categoryManager->create('cat2', 'root1');
        $categoryManager->create('cat3', 'cat2');

        // Test getting subcategories
        $this->assertEquals(['cat1', 'cat2'], $categoryManager->getSubcategories('root1'));
        $this->assertEquals(['cat3'], $categoryManager->getSubcategories('cat2'));

        // Test getting subcategories of non-existent category
        $this->expectException(InvalidArgumentException::class);
        $categoryManager->getSubcategories('nonexistentcategory');
    }

    public function testGetPath() {
        $categoryManager = new CategoryManager();

        // Adding categories
        $categoryManager->create('root1');
        $categoryManager->create('cat1', 'root1');
        $categoryManager->create('cat2', 'root1');
        $categoryManager->create('cat3', 'cat2');

        // Test getting path
        $this->assertEquals(['root1', 'cat2', 'cat3'], $categoryManager->getPath('cat3'));

        // Test getting path of non-existent category
        $this->expectException(InvalidArgumentException::class);
        $categoryManager->getPath('nonexistentcategory');
    }
}
