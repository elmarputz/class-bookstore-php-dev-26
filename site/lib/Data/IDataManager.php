<?php 
namespace Data;

interface IDataManager {
    public static function getBooksByCategory(int $categoryId): array;
    public static function getCategories(): array;
    public static function getBooksForSearchCriteria(string $term): array;
}