CREATE TABLE generated_files (
                                 id VARCHAR(36) PRIMARY KEY,
                                 filename VARCHAR(255) NOT NULL,
                                 template VARCHAR(255) NOT NULL,
                                 data JSON NOT NULL,
                                 file_path VARCHAR(255) NOT NULL,
                                 created_at DATETIME DEFAULT CURRENT_TIMESTAMP )