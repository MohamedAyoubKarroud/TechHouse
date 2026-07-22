-- Migration: add OAuth columns to existing `users` table.
-- Run once via phpMyAdmin or:  mysql -u root techhouse < migration_oauth.sql

USE techhouse;

ALTER TABLE users
    MODIFY password_hash VARCHAR(255) DEFAULT NULL,
    ADD COLUMN provider    ENUM('local','google','facebook') NOT NULL DEFAULT 'local' AFTER country,
    ADD COLUMN provider_id VARCHAR(80)  DEFAULT NULL                                   AFTER provider,
    ADD COLUMN avatar_url  VARCHAR(255) DEFAULT NULL                                   AFTER provider_id,
    ADD UNIQUE KEY uniq_provider (provider, provider_id);
