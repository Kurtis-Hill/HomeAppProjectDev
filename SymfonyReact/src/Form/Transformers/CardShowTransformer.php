<?php


namespace App\Form\Transformers;


use App\Entity\Card\Cardshow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CardShowTransformer
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($cardshow)
    {
        if ($cardshow === null) {
            return '';
        }
        $cardshow->getCardshowid();
    }

    public function reverseTransform($cardShowid)
    {
        if (!$cardShowid){
            return;
        }

        $carshow = $this->entityManager->getRepository(Cardshow::class)->find($cardShowid);

        if ($carshow === null) {
            throw new TransformationFailedException(sprintf(
                'An cardshowid with number "%s" does not exist!',
                $cardShowid
            ));
        }

        return $carshow;
    }
}