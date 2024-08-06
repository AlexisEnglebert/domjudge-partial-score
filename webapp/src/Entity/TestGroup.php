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
class TestGroup 
        {
    #[ORM\Column(nullable: false, options: ['comment' => 'testgroup name'])]
    #[Serializer\Exclude]
    private ?string $name;

    #[ORM\Column(type: 'blob', nullable: true, options: ['comment' => 'Description of this testgroup'])]
    #[Serializer\Exclude]
    private ?string $description;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['comment' => 'Testgroup ID', 'unsigned' => true])]
    private ?int $id;

    #[ORM\OneToMany(mappedBy: 'testgroup', targetEntity: Testcase::class)]
    #[Serializer\Exclude]
    private Collection $testcases;

    #[ORM\Column(options: ['comment' => 'testgroup score'])]
    private int $score; // HMM I don't really like this :(
    
    public function __construct() 
    {
        $this->testcases = new ArrayCollection();
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getTestcases(): Collection
    {
        return $this->testcases;
    }
}