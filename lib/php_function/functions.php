<?php 

function compt_entries($pdo, $table, $column, $value) {
 
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM " . $table . " WHERE " . $column . " = :value");
    
    $stmt->execute([':value' => $value]);
    
    
    return (int)$stmt->fetchColumn();
}

