<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration: Add Ski School
 *
 * Creates two tables:
 *
 * game_ski_school_types – catalogue of lesson types (seeded here)
 *   id_lesson_type, name_english, name_french, description_english,
 *   description_french, skill_level (beginner/intermediate/advanced),
 *   price_per_guest, instructor_cost, max_guests_per_session,
 *   rep_bonus, active
 *
 * game_ski_school_sessions – records of sessions run by a resort
 *   id_session, id_resort, id_lesson_type, session_date,
 *   guests_enrolled, revenue, rep_earned, created_at
 */
class Migration_Add_ski_school extends CI_Migration {

    private $lesson_types = [
        [
            'name_english'        => 'Beginner Group Lesson',
            'name_french'         => 'Cours Collectif Débutants',
            'description_english' => 'Introductory group lesson for first-time skiers. Covers basic stance, snowplough turns and stopping safely.',
            'description_french'  => 'Cours collectif d\'initiation pour les skieurs débutants. Couvre la position de base, le chasse-neige et les arrêts en sécurité.',
            'skill_level'         => 'beginner',
            'price_per_guest'     => 40,
            'instructor_cost'     => 80,
            'max_guests_per_session' => 10,
            'rep_bonus'           => 2,
            'active'              => 1,
        ],
        [
            'name_english'        => 'Intermediate Technique Clinic',
            'name_french'         => 'Clinic Technique Intermédiaire',
            'description_english' => 'Half-day clinic focused on parallel turns, carving basics and off-piste awareness for intermediate skiers.',
            'description_french'  => 'Clinic d\'une demi-journée axé sur les virages parallèles, les bases du carving et la conscience du hors-piste pour les skieurs de niveau intermédiaire.',
            'skill_level'         => 'intermediate',
            'price_per_guest'     => 70,
            'instructor_cost'     => 120,
            'max_guests_per_session' => 8,
            'rep_bonus'           => 4,
            'active'              => 1,
        ],
        [
            'name_english'        => 'Advanced Freeride Workshop',
            'name_french'         => 'Atelier Freeride Avancé',
            'description_english' => 'Expert-led workshop covering moguls, powder skiing and advanced carving for experienced riders.',
            'description_french'  => 'Atelier animé par un expert couvrant les bosses, le ski en poudreuse et le carving avancé pour les riders expérimentés.',
            'skill_level'         => 'advanced',
            'price_per_guest'     => 120,
            'instructor_cost'     => 200,
            'max_guests_per_session' => 6,
            'rep_bonus'           => 7,
            'active'              => 1,
        ],
        [
            'name_english'        => 'Children\'s Ski Club',
            'name_french'         => 'Club de Ski Enfants',
            'description_english' => 'Fun-filled full-day ski programme for children aged 5–12. Includes games, lunch and snow safety education.',
            'description_french'  => 'Programme de ski ludique d\'une journée complète pour les enfants de 5 à 12 ans. Inclut des jeux, le déjeuner et une sensibilisation à la sécurité.',
            'skill_level'         => 'beginner',
            'price_per_guest'     => 60,
            'instructor_cost'     => 100,
            'max_guests_per_session' => 12,
            'rep_bonus'           => 3,
            'active'              => 1,
        ],
        [
            'name_english'        => 'Private Lesson',
            'name_french'         => 'Cours Particulier',
            'description_english' => 'One-to-one lesson with a certified instructor. Tailored to any skill level for rapid improvement.',
            'description_french'  => 'Cours en tête-à-tête avec un moniteur certifié. Adapté à tous les niveaux pour une progression rapide.',
            'skill_level'         => 'intermediate',
            'price_per_guest'     => 150,
            'instructor_cost'     => 150,
            'max_guests_per_session' => 1,
            'rep_bonus'           => 5,
            'active'              => 1,
        ],
    ];

    public function up() {
        // ── game_ski_school_types ──────────────────────────────────────────
        if (!$this->db->table_exists('game_ski_school_types')) {
            $this->dbforge->add_field([
                'id_lesson_type' => [
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
                'skill_level' => [
                    'type'       => 'ENUM',
                    'constraint' => ["'beginner'", "'intermediate'", "'advanced'"],
                    'default'    => "'beginner'",
                ],
                'price_per_guest' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'instructor_cost' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'max_guests_per_session' => [
                    'type'    => 'TINYINT',
                    'unsigned' => TRUE,
                    'default' => 10,
                ],
                'rep_bonus' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'active' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
            ]);
            $this->dbforge->add_key('id_lesson_type', TRUE);
            $this->dbforge->create_table('game_ski_school_types');
        }

        // Seed lesson types (idempotent)
        foreach ($this->lesson_types as $lt) {
            $exists = $this->db
                ->where('name_english', $lt['name_english'])
                ->count_all_results('game_ski_school_types');
            if ($exists === 0) {
                $this->db->insert('game_ski_school_types', $lt);
            }
        }

        // ── game_ski_school_sessions ────────────────────────────────────────
        if (!$this->db->table_exists('game_ski_school_sessions')) {
            $this->dbforge->add_field([
                'id_session' => [
                    'type'           => 'INT',
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'id_resort' => [
                    'type'     => 'INT',
                    'unsigned' => TRUE,
                    'null'     => FALSE,
                ],
                'id_lesson_type' => [
                    'type'     => 'INT',
                    'unsigned' => TRUE,
                    'null'     => FALSE,
                ],
                'session_date' => [
                    'type' => 'DATE',
                    'null' => FALSE,
                ],
                'guests_enrolled' => [
                    'type'    => 'TINYINT',
                    'unsigned' => TRUE,
                    'default' => 0,
                ],
                'revenue' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'rep_earned' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => FALSE,
                ],
            ]);
            $this->dbforge->add_key('id_session', TRUE);
            $this->dbforge->add_key('id_resort');
            $this->dbforge->add_key('session_date');
            $this->dbforge->create_table('game_ski_school_sessions');
        }
    }

    public function down() {
        $this->dbforge->drop_table('game_ski_school_sessions', TRUE);
        $this->dbforge->drop_table('game_ski_school_types',    TRUE);
    }
}
