<?php
require_once __DIR__ . '/../../config/db.php';

class CompanyController {

    public static function getAllCompanies(): array {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT cp.*, 
                   COUNT(q.id) as published_questions_count
            FROM company_profiles cp
            LEFT JOIN questions q ON q.company_name = cp.name AND q.is_published = 1
            WHERE cp.is_active = 1
            GROUP BY cp.id
            ORDER BY cp.id ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getCompanyBySlug(string $slug): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT cp.*, 
                   COUNT(q.id) as published_questions_count
            FROM company_profiles cp
            LEFT JOIN questions q ON q.company_name = cp.name AND q.is_published = 1
            WHERE cp.slug = ? AND cp.is_active = 1
            GROUP BY cp.id
        ");
        $stmt->execute([$slug]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($company && !empty($company['rounds_json'])) {
            $company['rounds'] = json_decode($company['rounds_json'], true) ?: [];
        } else {
            $company['rounds'] = [];
        }
        return $company ?: null;
    }
}
