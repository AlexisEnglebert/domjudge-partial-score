<?php declare(strict_types=1);
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;     
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Entity]       
#[ORM\Table(options: [
    'collation' => 'utf8mb4_unicode_ci',
    'charset' => 'utf8mb4',     
    'comment' => 'Stores testgroup per problem',
])]

//TODO ADD A RANK TO THE TEST GROUPS ....
class TestGroup 
{
    #[ORM\Column(nullable: false, options: ['comment' => 'testgroup name'])]
    #[Serializer\Exclude]
    private ?string $name;

    #[ORM\Column(type: 'blob', nullable: true, options: ['comment' => 'Description of this testgroup'])]
    #[Serializer\Exclude]
    private $description;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['comment' => 'Testgroup ID', 'unsigned' => true])]
    private ?int $testGroupId;

    #[ORM\Column(nullable: true, options: ['comment' => 'testgroup score'])]
    private int $score; // HMM I don't really like this :(

    #[ORM\Column(options: ['comment' => 'testgroup aggregation'])]
	private ?string $aggregation;
    
	#[ORM\ManyToOne(inversedBy: 'test_group')]
    #[ORM\JoinColumn(name: 'probid', referencedColumnName: 'probid', onDelete: 'CASCADE')]
    #[Serializer\Exclude]
    private ?Problem $problem = null;

    public function __construct() 
    {
        $this->testcases = new ArrayCollection();
    }
    

    public function getTestGroupId(): ?int
    {
        return $this->testGroupId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

	//TODO
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }

	public function getScore() : int
	{
		return $this->score;
	}

	public function setScore(int $score): void
	{
		$this->score = $score;
	}

	public function getAggregation(): ?string
	{
		return $this->aggregation;
	}
	public function setAggregation(string $aggregation): void
	{
		$this->aggregation = $aggregation;
	}

	public function getProblem() : ?Problem
	{
		return $this->problem;
	}

	public function setProblem(Problem $problem): void
	{
		$this->problem = $problem;
	}
}