ALTER TABLE %PREFIX%fleet_event ADD `lockedAt` int(11) DEFAULT NULL AFTER `lock`;
-- Existing in-flight locks must get a real lockedAt, otherwise clearStaleLocks
-- treats them as orphans (lockedAt IS NULL) and unlocks them mid-run.
UPDATE %PREFIX%fleet_event SET lockedAt = UNIX_TIMESTAMP() WHERE `lock` IS NOT NULL AND lockedAt IS NULL;
