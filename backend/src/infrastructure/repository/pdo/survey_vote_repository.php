<?php
declare(strict_types=1);
namespace app\infrastructure\repository\pdo;

require_once(__DIR__ . "/../../../domain/repository/survey_vote_repository.php");
require_once(__DIR__ . "/../../../domain/value_object/vote.php");
require_once(__DIR__ . "/../../../domain/value_object/user.php");
require_once(__DIR__ . "/../../../domain/value_object/token.php");

use app\domain\repository\VoteRepository;
use app\domain\value_object\Token;
use app\domain\value_object\User;
use app\domain\value_object\Vote;
use PDO;
use PDOException;
use Exception;

class SurveyVoteRepository implements VoteRepository
{
    private PDO $conn;

    function __construct(PDO $PDOconnection)
    {
        $this->conn = $PDOconnection;
    }

    public function save(Vote $vote): void
    {
        $addVoteSql = "UPDATE `option` SET votes = votes + 1 WHERE option_id = :option_id";
        $removeVoteSql = "UPDATE `option` SET votes = votes - 1 WHERE option_id = (SELECT option_id FROM vote WHERE vote_id = :vote_id)";

        $updateVoteSql = "UPDATE vote SET option_id = :option_id, user_token = :token WHERE vote_id = :vote_id;";
        $insertVoteSql = "INSERT INTO vote (option_id, user_token) VALUES (:option_id, :token)";


        if($vote->getId() === null)
        {
            $this->conn->beginTransaction();
            try
            {
                $stmt = $this->conn->prepare($insertVoteSql);
                $stmt->execute(["option_id" => $vote->optionId, "token" => $vote->user->token->value]);

                $stmt = $this->conn->prepare($addVoteSql);
                $stmt->execute(["option_id" => $vote->optionId]);

                $this->conn->commit();
            }
            catch(Exception $e)
            {
                $this->conn->rollBack();
                throw $e;
            }
            return;
        }

        $this->conn->beginTransaction();
        try
        {
            $stmt = $this->conn->prepare($removeVoteSql);
            $stmt->execute(["vote_id" => $vote->getId()]);
    
            $stmt = $this->conn->prepare($updateVoteSql);
            $stmt->execute(["option_id" => $vote->optionId, "token" => $vote->user->token->value, "vote_id" => $vote->getId()]);
    
            $stmt = $this->conn->prepare($addVoteSql);
            $stmt->execute(["option_id" => $vote->optionId]);
            $this->conn->commit();
        }
        catch(Exception $e)
        {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function hasVoted(string $surveyCode, User $user): bool
    {
        $token = $user->token->value;

        $sql = "SELECT 1 FROM vote JOIN `option` USING(option_id) JOIN survey USING(survey_id) WHERE vote.user_token = ? AND survey_code = ?;";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$token, $surveyCode]);
        return boolval($stmt->fetchColumn());
    }
}