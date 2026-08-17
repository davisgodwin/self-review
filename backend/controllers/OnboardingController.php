<?php
namespace Controllers;

use Config\Database;
use Helpers\Response;
use Middleware\AuthMiddleware;
use PDO;

class OnboardingController {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // GET /api/onboarding/options
    public function getOptions(): void {
        AuthMiddleware::authenticate();

        $goals = $this->db->query("SELECT id, name FROM spiritual_goals ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $interests = $this->db->query("SELECT id, name FROM interests ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $talents = $this->db->query("SELECT id, name FROM talents ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        $dailyGoals = $this->db->query("SELECT id, name FROM daily_goals ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

        Response::success([
            'spiritual_goals' => $goals,
            'interests' => $interests,
            'talents' => $talents,
            'daily_goals' => $dailyGoals
        ]);
    }

    // POST /api/onboarding/complete
    public function complete(): void {
        $authUser = AuthMiddleware::authenticate();
        $userId = $authUser['user_id'];

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        try {
            $this->db->beginTransaction();

            // 1. Profile Details
            $stmt = $this->db->prepare("
                INSERT INTO profiles (user_id, date_of_birth, country, profile_photo)
                VALUES (:user_id, :dob, :country, :photo)
                ON CONFLICT (user_id) DO UPDATE 
                SET date_of_birth = EXCLUDED.date_of_birth, country = EXCLUDED.country, profile_photo = EXCLUDED.profile_photo
            ");
            $stmt->execute([
                'user_id' => $userId,
                'dob' => $data['date_of_birth'] ?? null,
                'country' => $data['country'] ?? null,
                'photo' => $data['profile_photo'] ?? null
            ]);

            // 2. User Preferences (Preferred SR Hour)
            $stmt = $this->db->prepare("
                INSERT INTO user_preferences (user_id, preferred_hour, custom_time)
                VALUES (:user_id, :hour, :custom_time)
                ON CONFLICT (user_id) DO UPDATE 
                SET preferred_hour = EXCLUDED.preferred_hour, custom_time = EXCLUDED.custom_time
            ");
            $stmt->execute([
                'user_id' => $userId,
                'hour' => $data['preferred_hour'] ?? '12:00 AM',
                'custom_time' => $data['custom_time'] ?? null
            ]);

            // 3. Spiritual Goals
            if (!empty($data['spiritual_goals']) && is_array($data['spiritual_goals'])) {
                $this->db->prepare("DELETE FROM user_spiritual_goals WHERE user_id = :user_id")->execute(['user_id' => $userId]);
                $stmt = $this->db->prepare("INSERT INTO user_spiritual_goals (user_id, goal_id) VALUES (:user_id, :goal_id) ON CONFLICT DO NOTHING");
                foreach ($data['spiritual_goals'] as $goalId) {
                    $stmt->execute(['user_id' => $userId, 'goal_id' => $goalId]);
                }
            }

            // 4. Interests
            if (!empty($data['interests']) && is_array($data['interests'])) {
                $this->db->prepare("DELETE FROM user_interests WHERE user_id = :user_id")->execute(['user_id' => $userId]);
                $stmt = $this->db->prepare("INSERT INTO user_interests (user_id, interest_id) VALUES (:user_id, :interest_id) ON CONFLICT DO NOTHING");
                foreach ($data['interests'] as $interestId) {
                    $stmt->execute(['user_id' => $userId, 'interest_id' => $interestId]);
                }
            }

            // 5. Talents
            if (!empty($data['talents']) && is_array($data['talents'])) {
                $this->db->prepare("DELETE FROM user_talents WHERE user_id = :user_id")->execute(['user_id' => $userId]);
                $stmt = $this->db->prepare("INSERT INTO user_talents (user_id, talent_id) VALUES (:user_id, :talent_id) ON CONFLICT DO NOTHING");
                foreach ($data['talents'] as $talentId) {
                    $stmt->execute(['user_id' => $userId, 'talent_id' => $talentId]);
                }
            }

            // 6. Daily Goals
            if (!empty($data['daily_goals']) && is_array($data['daily_goals'])) {
                $this->db->prepare("DELETE FROM user_daily_goals WHERE user_id = :user_id")->execute(['user_id' => $userId]);
                $stmt = $this->db->prepare("INSERT INTO user_daily_goals (user_id, daily_goal_id) VALUES (:user_id, :daily_goal_id) ON CONFLICT DO NOTHING");
                foreach ($data['daily_goals'] as $dailyGoalId) {
                    $stmt->execute(['user_id' => $userId, 'daily_goal_id' => $dailyGoalId]);
                }
            }

            // 7. Update onboarding_completed = TRUE
            $stmt = $this->db->prepare("UPDATE users SET onboarding_completed = TRUE WHERE id = :user_id");
            $stmt->execute(['user_id' => $userId]);

            $this->db->commit();
            Response::success([], 'Onboarding completed successfully');

        } catch (\Exception $e) {
            $this->db->rollBack();
            Response::error('Failed to save onboarding selections: ' . $e->getMessage(), [], 500);
        }
    }
}