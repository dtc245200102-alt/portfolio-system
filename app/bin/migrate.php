<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/database.php';
require_once dirname(__DIR__) . '/helpers.php';

$connection = db();
$newColumns = [
    'avatar_path' => "ALTER TABLE profile ADD COLUMN avatar_path VARCHAR(255) NOT NULL DEFAULT '' AFTER github_url",
    'school_name' => "ALTER TABLE profile ADD COLUMN school_name VARCHAR(180) NOT NULL DEFAULT '' AFTER student_code",
    'education_details' => "ALTER TABLE profile ADD COLUMN education_details TEXT NOT NULL DEFAULT ('') AFTER school_name",
];
foreach ($newColumns as $column => $alterStatement) {
    $exists = $connection->query("SHOW COLUMNS FROM profile LIKE '{$column}'")->fetch();
    if (!$exists) {
        $connection->exec($alterStatement);
    }
}

$existingTables = $connection->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('about_facts', $existingTables, true)) {
    $connection->exec(<<<'SQL'
        CREATE TABLE about_facts (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            content VARCHAR(180) NOT NULL,
            sort_order INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
        SQL);

    $insertFact = $connection->prepare('INSERT INTO about_facts (content, sort_order) VALUES (:content, :sort_order)');
    foreach (['Luôn tò mò', 'Học qua thực hành', 'Chia sẻ điều hữu ích'] as $index => $content) {
        $insertFact->execute(['content' => $content, 'sort_order' => $index + 1]);
    }
}

function repair_seed_text(string $value): string
{
    if (preg_match('/(?:Ã.|Â.|Ä.|Æ.|á»|áº|â€)/u', $value) !== 1) {
        return $value;
    }

    $bytes = mb_convert_encoding($value, 'Windows-1252', 'UTF-8');
    $roundTrip = mb_convert_encoding($bytes, 'UTF-8', 'Windows-1252');
    if ($roundTrip !== $value || !mb_check_encoding($bytes, 'UTF-8')) {
        return $value;
    }

    return preg_match('/(?:Ã.|Â.|Ä.|Æ.|á»|áº|â€)/u', $bytes) === 1 ? $value : $bytes;
}

$connection->beginTransaction();
try {
    $profile = $connection->query('SELECT display_name, role_title, tagline, about_text FROM profile WHERE id = 1')->fetch();
    if ($profile) {
        $fixed = array_map(static fn ($value): string => repair_seed_text((string) $value), $profile);
        if ($fixed !== $profile) {
            $statement = $connection->prepare('UPDATE profile SET display_name = :display_name, role_title = :role_title, tagline = :tagline, about_text = :about_text WHERE id = 1');
            $statement->execute($fixed);
        }
    }

    foreach ([
        ['table' => 'skills', 'columns' => ['name']],
        ['table' => 'projects', 'columns' => ['title', 'description', 'tech_stack']],
    ] as $definition) {
        $table = $definition['table'];
        $columns = $definition['columns'];
        $selectColumns = implode(', ', array_merge(['id'], $columns));
        $rows = $connection->query("SELECT {$selectColumns} FROM {$table}")->fetchAll();
        $setClause = implode(', ', array_map(static fn ($column): string => "{$column} = :{$column}", $columns));
        $statement = $connection->prepare("UPDATE {$table} SET {$setClause} WHERE id = :id");
        foreach ($rows as $row) {
            $fixed = ['id' => (int) $row['id']];
            foreach ($columns as $column) {
                $fixed[$column] = repair_seed_text((string) $row[$column]);
            }
            if (array_diff_assoc($fixed, $row)) {
                $statement->execute($fixed);
            }
        }
    }

    $connection->commit();
} catch (Throwable $error) {
    if ($connection->inTransaction()) {
        $connection->rollBack();
    }
    throw $error;
}

fwrite(STDOUT, "Database migrations complete.\n");
