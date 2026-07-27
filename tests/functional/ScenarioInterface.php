<?php

/**
 * Risistar - Functional Test & Scenario Interface
 */

interface ScenarioInterface
{
    /**
     * Identifiant court du scénario (ex: 'incoming_attacks')
     */
    public function getKey(): string;

    /**
     * Nom lisible du scénario
     */
    public function getName(): string;

    /**
     * Description détaillée du scénario et de ce qu'il teste
     */
    public function getDescription(): string;

    /**
     * Exécute le scénario avec les options fournies
     * 
     * @param array $options Options CLI/Web (count, eta, target_user, target_type, etc.)
     * @param bool $isCli Mode d'affichage (CLI ou Web)
     */
    public function execute(array $options, bool $isCli): void;
}
