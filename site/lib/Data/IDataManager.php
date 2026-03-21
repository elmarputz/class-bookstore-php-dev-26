<?php 
namespace Data;

interface IDataManager {
    public function getBooksByCategory(int $categoryId): array;
    public function getCategories(): array;
}