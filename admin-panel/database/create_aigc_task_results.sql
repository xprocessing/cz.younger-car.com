-- Create AIGC task results table
CREATE TABLE IF NOT EXISTS aigc_task_results (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    task_id INT UNSIGNED NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    process_status ENUM('success', 'failed') NOT NULL,
    result_url VARCHAR(255) DEFAULT NULL,
    error_message TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (task_id) REFERENCES aigc_tasks(task_id) ON DELETE CASCADE,
    INDEX idx_task_id (task_id) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
