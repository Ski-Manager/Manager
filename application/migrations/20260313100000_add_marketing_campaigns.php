<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add more Marketing Campaigns
 *
 * Ensures the game_marketing_campaigns catalogue table exists and seeds six
 * additional diverse campaigns:
 *
 *   1. Social Media Blitz          – cheap, tourist/affluence focus
 *   2. Television Commercial       – expensive, cash + reputation
 *   3. Travel Magazine Feature     – medium cost, reputation + affluence
 *   4. Local Radio Campaign        – cheap, cash focus
 *   5. International Ski Fair      – expensive, balanced rewards
 *   6. Loyalty Rewards Program     – medium cost, genepis + cash
 */
class Migration_Add_marketing_campaigns extends CI_Migration {

    private $campaigns = [
        [
            'name_english'        => 'Social Media Blitz',
            'name_french'         => 'Offensive Réseaux Sociaux',
            'description_english' => 'Launch a targeted social media campaign across popular platforms to attract new visitors and boost online visibility for your resort.',
            'description_french'  => 'Lancez une campagne ciblée sur les réseaux sociaux pour attirer de nouveaux visiteurs et augmenter la visibilité en ligne de votre station.',
            'cost'                => 500,
            'reward_cash'         => 0,
            'reward_genepis'      => 0,
            'reward_affluence'    => 1.03,
            'reward_reputation'   => 5,
            'active'              => 1,
        ],
        [
            'name_english'        => 'Television Commercial',
            'name_french'         => 'Publicité Télévisée',
            'description_english' => 'Air a high-quality TV commercial during peak viewing hours to boost your resort\'s brand recognition and drive ticket sales.',
            'description_french'  => 'Diffusez une publicité télévisée de qualité aux heures de grande écoute pour accroître la notoriété de votre station et stimuler les ventes de billets.',
            'cost'                => 8000,
            'reward_cash'         => 12000,
            'reward_genepis'      => 0,
            'reward_affluence'    => 1.00,
            'reward_reputation'   => 30,
            'active'              => 1,
        ],
        [
            'name_english'        => 'Travel Magazine Feature',
            'name_french'         => 'Article dans un Magazine de Voyage',
            'description_english' => 'Secure a featured article in a top travel magazine to showcase your resort\'s unique attractions and draw affluent visitors.',
            'description_french'  => 'Obtenez un article de fond dans un grand magazine de voyage pour mettre en valeur les atouts de votre station et attirer des visiteurs aisés.',
            'cost'                => 3000,
            'reward_cash'         => 1000,
            'reward_genepis'      => 0,
            'reward_affluence'    => 1.05,
            'reward_reputation'   => 20,
            'active'              => 1,
        ],
        [
            'name_english'        => 'Local Radio Campaign',
            'name_french'         => 'Campagne Radio Locale',
            'description_english' => 'Run daily radio spots on local stations to remind nearby residents of your resort\'s offerings and drive weekend visits.',
            'description_french'  => 'Diffusez des spots radio quotidiens sur les stations locales pour rappeler aux habitants de la région les offres de votre station et encourager les visites du week-end.',
            'cost'                => 1000,
            'reward_cash'         => 2500,
            'reward_genepis'      => 0,
            'reward_affluence'    => 1.01,
            'reward_reputation'   => 5,
            'active'              => 1,
        ],
        [
            'name_english'        => 'International Ski Fair',
            'name_french'         => 'Salon International du Ski',
            'description_english' => 'Exhibit your resort at an international ski and winter sports fair to attract global visitors, tour operators, and media coverage.',
            'description_french'  => 'Exposez votre station lors d\'un salon international du ski et des sports d\'hiver pour attirer des visiteurs du monde entier, des voyagistes et une couverture médiatique.',
            'cost'                => 10000,
            'reward_cash'         => 8000,
            'reward_genepis'      => 3,
            'reward_affluence'    => 1.04,
            'reward_reputation'   => 25,
            'active'              => 1,
        ],
        [
            'name_english'        => 'Loyalty Rewards Program',
            'name_french'         => 'Programme de Fidélité',
            'description_english' => 'Launch a loyalty rewards program offering returning guests exclusive perks, discounts, and points redeemable at your resort.',
            'description_french'  => 'Lancez un programme de fidélité offrant aux clients fidèles des avantages exclusifs, des réductions et des points échangeables dans votre station.',
            'cost'                => 5000,
            'reward_cash'         => 3000,
            'reward_genepis'      => 5,
            'reward_affluence'    => 1.02,
            'reward_reputation'   => 15,
            'active'              => 1,
        ],
    ];

    public function up() {
        // Create the table if it does not yet exist (supports fresh installs)
        if (!$this->db->table_exists('game_marketing_campaigns')) {
            $this->dbforge->add_field([
                'id_campaign' => [
                    'type'           => 'INT',
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'name_english' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 200,
                ],
                'name_french' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 200,
                ],
                'description_english' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'description_french' => [
                    'type' => 'TEXT',
                    'null' => TRUE,
                ],
                'cost' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'reward_cash' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'reward_genepis' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'reward_affluence' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'default'    => '1.00',
                ],
                'reward_reputation' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'active' => [
                    'type'    => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                ],
            ]);
            $this->dbforge->add_key('id_campaign', TRUE);
            $this->dbforge->create_table('game_marketing_campaigns');
        }

        // Seed new campaigns (idempotent – skip if name already exists)
        foreach ($this->campaigns as $campaign) {
            $exists = $this->db
                ->where('name_english', $campaign['name_english'])
                ->count_all_results('game_marketing_campaigns');
            if ($exists === 0) {
                $this->db->insert('game_marketing_campaigns', $campaign);
            }
        }
    }

    public function down() {
        foreach ($this->campaigns as $campaign) {
            $this->db->delete('game_marketing_campaigns', ['name_english' => $campaign['name_english']]);
        }
    }
}
