<?php declare(strict_types=1);

namespace Tests\Utils\Scoreboard;

use App\Entity\Configuration;
use App\Entity\Contest;
use App\Entity\ContestProblem;
use App\Entity\Judgehost;
use App\Entity\Judging;
use App\Entity\Language;
use App\Entity\Problem;
use App\Entity\Submission;
use App\Entity\Team;
use App\Entity\TeamCategory;
use App\Service\DOMJudgeService;
use App\Service\ScoreboardService;
use App\Utils\Scoreboard\Scoreboard;
use App\Utils\Scoreboard\SingleTeamScoreboard;
use App\Utils\Scoreboard\ScoreboardMatrixItem;
use App\Utils\Scoreboard\TeamScore;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ScoreboardTest extends KernelTestCase
{
    public const CONTEST_NAME = 'scoretest';
    public const NUM_PROBLEMS = 3;
    public const NUM_TEAMS = 3;

    /**
     * @var \App\Service\DOMJudgeService
     */
    private $dj;

    /**
     * @var \App\Service\ScoreboardService
     */
    private $ss;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var App\Entity\Contest
     */
    private $contest;

    /**
     * @var App\Entity\Judgehost
     */
    private $judgehost;

    /**
     * @var App\Entity\Problem[]
     */
    private $problems;

    /**
     * @var App\Entity\Team[]
     */
    private $teams;

    protected function setUp()
    {
        self::bootKernel();

        $this->dj = self::$container->get(DOMJudgeService::class);
        $this->ss = self::$container->get(ScoreboardService::class);
        $this->em = self::$container->get('doctrine')->getManager();

        // Reset scoring related config to default.
        $config = $this->em->getRepository(Configuration::class);
        $config->findOneBy(['name' => 'verification_required'])->setValue(false);
        $config->findOneBy(['name' => 'penalty_time'])->setValue(20);
        $config->findOneBy(['name' => 'compile_penalty'])->setValue(false);
        $config->findOneBy(['name' => 'score_in_seconds'])->setValue(false);
        $config->findOneBy(['name' => 'data_source'])->setValue(0);

        // Create a contest, problems and teams for which to test the
        // scoreboard. These get deleted again in tearDown().
        $this->contest = new Contest();
        $this->contest
            ->setExternalid(self::CONTEST_NAME)
            ->setName(self::CONTEST_NAME)
            ->setShortname(self::CONTEST_NAME)
            ->setStarttimeString(date('Y').'-01-01 10:00:00 Europe/Amsterdam')
            ->setActivatetimeString('-01:00')
            ->setEndtimeString('+02:00');
        $this->em->persist($this->contest);

        $hostname = self::CONTEST_NAME.'-judgehost';
        $this->judgehost = $this->em->getRepository(Judgehost::class)
            ->findOneBy(['hostname' => $hostname]);
        if ( !$this->judgehost ) {
            $this->judgehost = new Judgehost();
            $this->judgehost
                ->setHostname($hostname);
            $this->em->persist($this->judgehost);
        }

        $category = $this->em->getRepository(TeamCategory::class)
            ->findOneBy(['sortorder' => 0]);

        for($i=0; $i<self::NUM_TEAMS; $i++) {
            $this->teams[$i] = new Team();
            $this->teams[$i]
                ->setName('Team '.$i)
                ->setCategory($category);
            $this->em->persist($this->teams[$i]);
       }

        for($i=0; $i<self::NUM_PROBLEMS; $i++) {
            $letter = chr(ord('a') + $i);
            $this->problems[$i] = new Problem();
            $this->problems[$i]
                ->setName('Problem '.$letter)
                ->setTimelimit(5);

            $cp = new ContestProblem();
            $cp
                ->setShortname($letter)
                ->setProblem($this->problems[$i])
                ->setContest($this->contest);

            $this->contest->addProblem($cp);
            $this->problems[$i]->addContestProblem($cp);

            $this->em->persist($this->problems[$i]);
            $this->em->persist($cp);
        }

        $this->em->flush();
    }

    protected function tearDown()
    {
        // Preserve the data for inspection if a test failed.
        if ( !$this->hasFailed() ) {
            $this->em->remove($this->contest);
            $this->em->remove($this->judgehost);
            foreach ($this->teams    as $team)    $this->em->remove($team);
            foreach ($this->problems as $problem) $this->em->remove($problem);
        }

        $this->em->flush();

        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

    public function testScoreboardsNoFreeze()
    {
        $this->contest->setFreezetimeString(null);
        $this->createDefaultSubmissions();
        $this->recalcScoreCaches();

        $expected_scores = [
            [ 'team' => $this->teams[0], 'rank' => 2, 'solved' => 0, 'time' => 0 ],
            [ 'team' => $this->teams[1], 'rank' => 1, 'solved' => 2, 'time' => 161 ],
        ];

        $expected_fts = [
            // problems[0] has earlier unjudged solution by teams[0]
            [ 'problem' => $this->problems[1], 'team' => $this->teams[1] ],
            // problems[2] solution by teams[1] is invalid
        ];

        foreach ([ false, true ] as $jury) {
            $scoreboard = $this->ss->getScoreboard($this->contest, $jury);
            $this->assertScoresMatch($expected_scores, $scoreboard);
            $this->assertFTSMatch($expected_fts, $scoreboard);
        }
    }

    public function testScoreboardJuryFreeze()
    {
        $this->createDefaultSubmissions();
        // Scoreboard cache is recalculated below for each freeze time.

        $expected_scores = [
            [ 'team' => $this->teams[0], 'rank' => 2, 'solved' => 0, 'time' => 0 ],
            [ 'team' => $this->teams[1], 'rank' => 1, 'solved' => 2, 'time' => 161 ],
        ];

        $expected_fts = [
            // problems[0] has earlier unjudged solution by teams[0]
            [ 'problem' => $this->problems[1], 'team' => $this->teams[1] ],
            // problems[2] solution by teams[1] is invalid
        ];

        // Jury scoreboard should not depend on freeze, so test a couple.
        foreach ([ '+0:30:00', '+1:00:00', '+1:20:00' ] as $freeze) {
            $this->contest->setFreezetimeString($freeze);
            $this->recalcScoreCaches();

            $scoreboard = $this->ss->getScoreboard($this->contest, true);
            $this->assertScoresMatch($expected_scores, $scoreboard);
            $this->assertFTSMatch($expected_fts, $scoreboard);
        }
    }

    public function testScoreboardPublicFreeze()
    {
        $this->contest->setFreezetimeString('+1:10:00');
        $this->createDefaultSubmissions();
        $this->recalcScoreCaches();

        $expected_scores = [
            [ 'team' => $this->teams[0], 'rank' => 2, 'solved' => 0, 'time' => 0 ],
            [ 'team' => $this->teams[1], 'rank' => 1, 'solved' => 1, 'time' => 69 ],
        ];

        $expected_fts = [
            // problems[0] has earlier unjudged solution by teams[0]
            // problems[1] solution by teams[1] is after freeze
            // problems[2] solution by teams[1] is invalid
        ];

        $scoreboard = $this->ss->getScoreboard($this->contest, false);
        $this->assertScoresMatch($expected_scores, $scoreboard);
        $this->assertFTSMatch($expected_fts, $scoreboard);
    }

    public function testTeamScoreboardFreezeFTS()
    {
        $this->contest->setFreezetimeString('+1:10:00');
        $this->createDefaultSubmissions();
        $this->recalcScoreCaches();

        $expected_fts = [
            // problems[0] has earlier unjudged solution by teams[0]
            // problems[1] solution by teams[1] is after freeze
            // problems[2] solution by teams[1] is invalid
        ];

        $team = $this->teams[1];

        $scoreboard = $this->ss->getTeamScoreboard($this->contest, $team->getTeamid(), false);

        $this->assertInstanceOf(SingleTeamScoreboard::class, $scoreboard);
        $this->assertFTSMatch($expected_fts, $scoreboard);
    }

    function assertScoresMatch($expected_scores, $scoreboard)
    {
        $scores = $scoreboard->getScores();

        foreach ( $expected_scores as $row ) {
            $team = $row['team'];
            $name = $team->getName();

            $score = $scores[$team->getTeamid()];
            $this->assertInstanceOf(TeamScore::class, $score);

            $this->assertEquals($row['rank'],   $score->getRank(), "Rank for '$name'");
            $this->assertEquals($row['solved'], $score->getNumberOfPoints(), "# solved for '$name'");
            $this->assertEquals($row['time'],   $score->getTotalTime(), "Total time for '$name'");
        }
    }

    function assertFTSMatch($expected_fts, $scoreboard)
    {
        $matrix = $scoreboard->getMatrix();
        $teams = [];
        $probs = [];
        foreach ($scoreboard->getTeams()    as $team) $teams[$team->getTeamid()] = $team;
        foreach ($scoreboard->getProblems() as $prob) $probs[$prob->getProbid()] = $prob;

        $fts_probid2teamid = [];
        foreach ($expected_fts as $row) {
            $fts_probid2teamid[$row['problem']->getProbid()] = $row['team']->getTeamid();
        }

        foreach ($matrix as $teamid => $row) {
            $teamname = $teams[$teamid]->getName();
            foreach ($row as $probid => $item) {
                $probname = $probs[$probid]->getShortname();

                $this->assertInstanceOf(ScoreboardMatrixItem::class, $item);

                $expected = (@$fts_probid2teamid[$probid] === $teamid);
                $this->assertEquals($expected, $item->isFirst(),
                                    "Check FTS matches for team $teamname, problem $probname");
            }
        }

    }

    function recalcScoreCaches()
    {
        $this->em->flush();
        $this->ss->refreshCache($this->contest);
    }

    function createDefaultSubmissions()
    {
        $lang = $this->em->getRepository(Language::class)->find('cpp');

        $team = $this->teams[0];
        $this->createSubmission($lang, $this->problems[0], $team, 53*60+15.053, 'no-output');
        $this->createSubmission($lang, $this->problems[0], $team, 53*60+57.240, null);

        $team = $this->teams[1];
        $this->createSubmission($lang, $this->problems[0], $team, 61*60+00.000, 'compiler-error');
        $this->createSubmission($lang, $this->problems[0], $team, 69*60+00.000, 'correct');
        $this->createSubmission($lang, $this->problems[0], $team, 72*60+07.824, 'wrong-answer');
        $this->createSubmission($lang, $this->problems[1], $team, 72*60+39.733, 'wrong-answer');
        $this->createSubmission($lang, $this->problems[1], $team, 72*60+59.999, 'correct');
        $this->createSubmission($lang, $this->problems[1], $team, 79*60+00.000, 'wrong-answer');
        $this->createSubmission($lang, $this->problems[2], $team, 84*60+42, null);
        $this->createSubmission($lang, $this->problems[2], $team, 85*60+42, 'correct')
            ->setValid(false);

        // No submissions for $this->teams[2]
    }

    function createSubmission(
        Language $language,
        Problem $problem,
        Team $team,
        float $contest_time_seconds,
        $verdict,
        bool $verified = false
    ) {
        $cp = $this->em->getRepository(ContestProblem::class)->find(
            [ 'contest' => $this->contest, 'problem' => $problem ]
        );
        $submittime = $this->contest->getStarttime()+$contest_time_seconds;

        $submission = new Submission();
        $submission
            ->setContestProblem($cp)
            ->setLanguage($language)
            ->setTeam($team)
            ->setSubmittime($submittime);
        $this->em->persist($submission);

        if ( $verdict!==null ) {
            $judging = new Judging();
            $judging
                ->setSubmission($submission)
                ->setContest($this->contest)
                ->setJudgehost($this->judgehost)
                ->setStarttime($submittime + 5)
                ->setEndtime($submittime + 10)
                ->setResult($verdict);

            if ( $verified ) {
                $judging
                    ->setVerified(true)
                    ->setJuryMember(self::CONTEST_NAME.'-auto-verifier');
            }
            $this->em->persist($judging);

            $submission
                ->addJudging($judging)
                ->setJudgehost($this->judgehost);
        }

        $this->em->flush();

        return $submission;
    }
}