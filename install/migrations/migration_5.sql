ALTER TABLE %PREFIX%cronjobs ADD `lockedAt` int(11) DEFAULT NULL AFTER `lock`;
-- Existing in-flight locks must get a real lockedAt, otherwise clearStaleLocks
-- treats them as orphans (lockedAt IS NULL) and unlocks them mid-run.
UPDATE %PREFIX%cronjobs SET lockedAt = UNIX_TIMESTAMP() WHERE `lock` IS NOT NULL AND lockedAt IS NULL;
