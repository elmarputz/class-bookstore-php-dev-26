<?php 
namespace Data;
use Bookshop\User;
interface IDataManager {
    public static function getBooksByCategory(int $categoryId): array;
    public static function getCategories(): array;
    public static function getBooksForSearchCriteria(string $term): array;
    public static function getUserByUserName(string $userName): ?User;
    public static function getUserById(int $userId): ?User;
}