<?php
// ============================================================
// includes/AIBookRecommender.php
// ============================================================

class AIBookRecommender
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getRecommendations(int $borrowerId, int $limit = 5): array
    {
        $recommendations = [];
        $alreadyBorrowed = $this->getBorrowedBookIds($borrowerId);

        $genreRecs = $this->getGenreBasedRecommendations($borrowerId, $alreadyBorrowed, $limit);
        foreach ($genreRecs as $rec) {
            $recommendations[$rec['id']] = array_merge($rec, ['rec_type' => 'genre_based']);
        }

        if (count($recommendations) < $limit) {
            $collab = $this->getCollaborativeRecommendations($borrowerId, $alreadyBorrowed, $limit);
            foreach ($collab as $rec) {
                if (!isset($recommendations[$rec['id']])) {
                    $recommendations[$rec['id']] = array_merge($rec, ['rec_type' => 'collaborative']);
                }
                if (count($recommendations) >= $limit) break;
            }
        }

        if (count($recommendations) < $limit) {
            $trending = $this->getTrendingBooks($alreadyBorrowed, $limit);
            foreach ($trending as $rec) {
                if (!isset($recommendations[$rec['id']])) {
                    $recommendations[$rec['id']] = array_merge($rec, ['rec_type' => 'trending']);
                }
                if (count($recommendations) >= $limit) break;
            }
        }

        $result = array_values(array_slice($recommendations, 0, $limit));
        $this->logRecommendations($borrowerId, $result);
        return $result;
    }

    private function getGenreBasedRecommendations(int $borrowerId, array $excludeIds, int $limit): array
    {
        $stmt = $this->db->prepare("
            SELECT genre, COUNT(*) AS borrow_count
            FROM lbr_borrow_history
            WHERE borrower_id = ?
              AND genre IS NOT NULL
            GROUP BY genre
            ORDER BY borrow_count DESC
            LIMIT 3
        ");
        $stmt->execute([$borrowerId]);
        $topGenres = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($topGenres)) {
            return [];
        }

        $excludePlaceholders = $this->placeholders($excludeIds ?: [0]);
        $genrePlaceholders   = $this->placeholders($topGenres);
        $params = array_merge($topGenres, $excludeIds ?: [0]);

        $stmt2 = $this->db->prepare("
            SELECT
                b.id, b.title, b.author, b.genre, b.cover_url, b.year,
                b.status, b.description,
                COUNT(h.id) AS popularity,
                CASE
                    WHEN b.genre = ? THEN 1.0
                    WHEN b.genre = ? THEN 0.8
                    ELSE 0.6
                END AS similarity_score
            FROM lbr_books b
            LEFT JOIN lbr_borrow_history h ON h.book_id = b.id
            WHERE b.genre IN ({$genrePlaceholders})
              AND b.status = 'available'
              AND b.id NOT IN ({$excludePlaceholders})
            GROUP BY b.id
            ORDER BY similarity_score DESC, popularity DESC
            LIMIT {$limit}
        ");

        $stmt2->execute(array_merge(
            [$topGenres[0], $topGenres[1] ?? $topGenres[0]],
            $params
        ));

        return $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getCollaborativeRecommendations(int $borrowerId, array $excludeIds, int $limit): array
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT h2.borrower_id AS similar_borrower,
                   COUNT(*) AS shared_books
            FROM lbr_borrow_history h1
            JOIN lbr_borrow_history h2
                ON h2.book_id = h1.book_id
               AND h2.borrower_id != h1.borrower_id
            WHERE h1.borrower_id = ?
            GROUP BY h2.borrower_id
            HAVING shared_books >= 1
            ORDER BY shared_books DESC
            LIMIT 5
        ");
        $stmt->execute([$borrowerId]);
        $similarBorrowers = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($similarBorrowers)) {
            return [];
        }

        $borrowerPlaceholders = $this->placeholders($similarBorrowers);
        $excludePlaceholders  = $this->placeholders($excludeIds ?: [0]);
        $params = array_merge($similarBorrowers, $excludeIds ?: [0]);

        $stmt2 = $this->db->prepare("
            SELECT
                b.id, b.title, b.author, b.genre, b.cover_url, b.year,
                b.status, b.description,
                COUNT(h.borrower_id) AS borrow_count,
                ROUND(COUNT(h.borrower_id) / ? , 4) AS similarity_score
            FROM lbr_borrow_history h
            JOIN lbr_books b ON b.id = h.book_id
            WHERE h.borrower_id IN ({$borrowerPlaceholders})
              AND b.status = 'available'
              AND b.id NOT IN ({$excludePlaceholders})
            GROUP BY b.id
            ORDER BY borrow_count DESC
            LIMIT {$limit}
        ");

        $stmt2->execute(array_merge([count($similarBorrowers)], $params));
        return $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTrendingBooks(array $excludeIds, int $limit): array
    {
        $excludePlaceholders = $this->placeholders($excludeIds ?: [0]);

        $stmt = $this->db->prepare("
            SELECT
                b.id, b.title, b.author, b.genre, b.cover_url, b.year,
                b.status, b.description,
                COUNT(h.id) AS borrow_count,
                ROUND(COUNT(h.id) / 10.0, 4) AS similarity_score
            FROM lbr_books b
            LEFT JOIN lbr_borrow_history h
                ON h.book_id = b.id
               AND h.borrow_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            WHERE b.status = 'available'
              AND b.id NOT IN ({$excludePlaceholders})
            GROUP BY b.id
            ORDER BY borrow_count DESC, b.id DESC
            LIMIT {$limit}
        ");

        $stmt->execute($excludeIds ?: [0]);

        return array_map(function ($row) {
            $row['rec_type'] = 'trending';
            return $row;
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getBorrowerProfile(int $borrowerId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                genre,
                COUNT(*) AS count,
                ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER(), 1) AS percentage
            FROM lbr_borrow_history
            WHERE borrower_id = ?
              AND genre IS NOT NULL
            GROUP BY genre
            ORDER BY count DESC
            LIMIT 5
        ");
        $stmt->execute([$borrowerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPopularGenres(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT genre, COUNT(*) AS count
            FROM lbr_borrow_history
            WHERE genre IS NOT NULL
            GROUP BY genre
            ORDER BY count DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getBorrowedBookIds(int $borrowerId): array
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT book_id FROM lbr_borrow_history
            WHERE borrower_id = ?
            UNION
            SELECT book_id FROM lbr_transactions
            WHERE borrower_id = ? AND status IN ('active','overdue')
        ");
        $stmt->execute([$borrowerId, $borrowerId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    private function logRecommendations(int $borrowerId, array $recommendations): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO lbr_recommendations (borrower_id, book_id, recommendation_type, score)
            VALUES (?, ?, ?, ?)
        ");
        foreach ($recommendations as $rec) {
            $stmt->execute([
                $borrowerId,
                $rec['id'],
                $rec['rec_type'],
                $rec['similarity_score'] ?? 0,
            ]);
        }
    }

    private function placeholders(array $arr): string
    {
        return implode(',', array_fill(0, count($arr), '?'));
    }
}