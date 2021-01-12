<?php 
 
namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Article;
use App\DataFixtures\AuthorFixtures;
use App\DataFixtures\PictureFixtures;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class Article fixtures
 * 
 */
class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    private ObjectManager $_manager;
    private \Faker\Generator $faker;
    private SluggerInterface $slugger;

    /**
     * Constructor
     * @param \Symfony\Component\String\Slugger\SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger){
        $this->slugger = $slugger;  
             
    }

    /**
     * Load Fixture
     * 
     * @param \Doctrine\Persistence\ObjectManager $_manager
     * 
     * @return void     * 
     */
    public function load(ObjectManager $_manager): void
    {
        $this->_manager = $_manager;
        $this->faker = Factory::create();
        $this->_generateArticles(8); 
        $this->_manager->flush();
    }

    /**
     * @return Array<class-string>
     */
    public function getDependencies(): Array
    {
        return [
            PictureFixtures::class, 
            AuthorFixtures::class, 
            CategoryFixtures::class, 
        ];
        
    }

   /**        
    *      [_generateArticles description]
    *
    * @param int $number [$number description]
    *
    * @return void           [return description]
    */
    private function _generateArticles(int $number): void
    {
        for ($i = 0; $i < $number; $i++) {
            $article = new Article();
            

            [
                "dateObject" => $dateObject,
                "dateString" => $dateString
            ] = $this->_generateRandomDateBetweenRange('01/01/2021', '01/05/2021');

            $title = $this->faker->sentence(4);
            $slug = $this->slugger->slug(\strtolower($title).'-' .$dateString);
            
            $picture = $this->getReference("picture{$i}");

            $article->setTitle($title)
                ->setContent($this->faker->paragraph())
                ->setSlug($slug)
                ->setCreatedAt($dateObject)
                ->setIsPublished(false)
                ->setAuthor($this->getReference("author".mt_rand(0, 1)))                    
                ->addCategory($this->getReference("category".mt_rand(1, 3)))  
                ->setPicture($picture);

                $this->

            $this->_manager->persist($article);
            $picture->setArticle($article);
        }
    }   

    /**
     * Generate Date DatetimesStamp object
     * 
     * @param string $start Date string with format 'd/m/Y'
     * 
     * @param string   $end Date string with format 'd/m/Y' 
     * 
     * @return array{dateObject: \DateTimeImmutable, dateString: string} "d-m-Y"
     */
    private function _generateRandomDateBetweenRange(string $start, string $end): array
    {

        $startDate = \DateTime::createFromFormat('d/m/Y', $start);
        $endDate = \DateTime::createFromFormat('d/m/Y', $end);
        if (!$startDate || !$endDate) {
            throw new HttpException(400, "La date saisie doit etre sous format d/m/Y");
        }
        //format date fr DD/MM/YY
        $startDateTimestamp = ($startDate)->getTimestamp();
        $endDateTimestamp = ($endDate)->getTimestamp();

        $ranTimestamp = \mt_rand($startDateTimestamp, $endDateTimestamp);
        $dateTimeImmutable = (new \DateTimeImmutable())->setTimestamp($ranTimestamp);

        return [
            "dateObject" => $dateTimeImmutable,
            "dateString" => $dateTimeImmutable->format('d-m-Y')
        ];

    }

}
