<?php
// ============================================================
// api/AIApiController.php
// ============================================================

class AIApiController
{
    public function handle(string $action, array $input): void
    {
        switch ($action) {

            case 'ai_risk_report':
                $detector = new AILateReturnDetector();
                $data     = $detector->getAtRiskTransactions();
                Response::json(['success' => true, 'data' => $data]);
                break;

            case 'ai_run_reminders':
                $detector = new AILateReturnDetector();
                $results  = $detector->runDailyCheck();
                $count    = count($results);
                Response::json(['success' => true, 'data' => $results, 'message' => "AI check complete. {$count} reminder(s) sent."]);
                break;

            case 'ai_recommendations':
                $borrowerId = (int) ($input['borrower_id'] ?? $_GET['borrower_id'] ?? 0);
                $limit      = (int) ($input['limit'] ?? $_GET['limit'] ?? 5);

                if (!$borrowerId) {
                    Response::error('borrower_id is required.', 400);
                    break;
                }

                $recommender = new AIBookRecommender();
                $recs        = $recommender->getRecommendations($borrowerId, $limit);
                $profile     = $recommender->getBorrowerProfile($borrowerId);

                Response::json([
                    'success' => true,
                    'data'    => [
                        'recommendations' => $recs,
                        'reading_profile' => $profile,
                    ]
                ]);
                break;

            case 'ai_borrower_profile':
                $borrowerId = (int) ($input['borrower_id'] ?? $_GET['borrower_id'] ?? 0);

                if (!$borrowerId) {
                    Response::error('borrower_id is required.', 400);
                    break;
                }

                $recommender = new AIBookRecommender();
                Response::json([
                    'success' => true,
                    'data'    => [
                        'profile'        => $recommender->getBorrowerProfile($borrowerId),
                        'popular_genres' => $recommender->getPopularGenres(),
                    ]
                ]);
                break;

            case 'ai_reminder_log':
                $limit    = (int) ($input['limit'] ?? $_GET['limit'] ?? 15);
                $detector = new AILateReturnDetector();
                Response::json(['success' => true, 'data' => $detector->getRecentReminders($limit)]);
                break;

            default:
                Response::error("Unknown AI action: {$action}", 400);
        }
    }
}