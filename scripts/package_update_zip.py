import zipfile
import os

files_to_pack = [
    # Economy & Hint Backend
    "config/economy.php",
    "app/Controllers/PracticeController.php",
    "app/Services/WalletService.php",
    "app/Services/QuestionParser.php",
    
    # Technical & Adaptive Engine
    "app/Controllers/TechnicalController.php",
    "app/Services/AdaptiveEngine.php",
    
    # Views & Modals
    "app/Views/components/quiz_shell.php",
    "app/Views/technical.php",
    "app/Views/practice.php",
    "app/Views/header.php",
    "app/Views/store.php",
    "app/Views/mock.php",
    
    # 1v1 Quiz Arena & Mock Exams
    "app/Services/DuelService.php",
    "app/Controllers/DuelController.php",
    "app/Views/duel.php",
    "app/Controllers/MockTestController.php",
    "app/Services/ProgressService.php",
    "public/index.php",

    # Public JavaScript Assets
    "public/js/app.js",
    "public/js/app.min.js",
    "public/js/quiz_shell.js",
    "public/sw.js",
    
    # Public CSS Assets
    "public/css/style.css",
    "public/css/style.min.css",
    "public/css/theme.css",
    "public/css/theme.min.css",
    
    # Migration & Seeders
    "database/migrations/20261030_add_typescript_llm_ai_questions.sql",
    "database/migrations/20261031_add_10_company_mock_packs_and_pyqs.sql",
    "database/migrations/20261031_add_6_new_companies_and_pyqs.sql",
    "database/migrations/20261031_reach_100_questions_all_companies.sql",
    "scripts/migrate_ai_ts.php",
    "scripts/migrate_company_mock_packs.php",
    "scripts/migrate_6_new_companies.php",
    "scripts/migrate_reach_100_per_company.php",
    "scripts/migrate_all.php",
    "infinityfree_database_setup.sql"
]

zip_path = "apttitudee_update.zip"

with zipfile.ZipFile(zip_path, "w", zipfile.ZIP_DEFLATED) as zipf:
    for file_path in files_to_pack:
        if os.path.exists(file_path):
            zipf.write(file_path, file_path)
            print(f"Packed: {file_path}")
        else:
            print(f"Warning: File not found: {file_path}")

print(f"\nCreated update archive: {zip_path} ({os.path.getsize(zip_path)} bytes)")
