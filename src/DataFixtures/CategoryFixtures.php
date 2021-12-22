<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

/*
    Les Fixtures(notion) sont un jeu de donnees.
    Elles servent a remplir la bdd juste apres la creation de la BDD,
    pour pouvoir manipuler des donnees dans mon code. => des entites' 



*/
class CategoryFixtures extends Fixture
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
        {

            $this->slugger = $slugger;
        }

    public function load(ObjectManager $manager): void
    {
        
        $categories = [
            'Politique',
            'Société',
            'Économie',
            'Santé',
            'Environnement',
            'Sport',
            'Culture'
        ];

        foreach($categories as $category) {

            $cat = new Category();

            $cat->setName($category);
            $cat->setAlias($this->slugger->slug($category));
            // alias = même valeur que name mais nettoyé donc on va utilisé le slugger

            $manager->persist($cat);
        }

        $manager->flush();
    }
}
