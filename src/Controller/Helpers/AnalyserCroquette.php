<?php


namespace App\Controller\Helpers;

class AnalyserCroquette
{
    // Parametre a modifier pour les chat 

    // Le taux de protéines conseillé pour le chat est de 40 % minimum et peut aller sans problème au-delà des 50 % dans la composition du produit.
    private const PROTEINE_VALUE = ['min' => 40, 'max' => 60];
    // Doit rester présent en petite quantité
    private const GLUCIDE_VALUE = ['min' => 0, 'max' => 20];
    // Si les graisses animales sont bénéfiques pour la santé du chat, les graisses végétales doivent être totalement proscrites.
    private const LIPIDE_VALUE = ['min' => 12, 'max' => 20];


    private $exposantBEE;
    private $coeffBEE; // 100 pour le chat
    private $K1 = 1;
    private $K2 = 1;
    private $K3 = 1;
    private $facteurActivite = 1;
    private $poidIdeal = 4; // en killogram 
    private $analyseQualitatifs = [];

    // Attribut energetique
    private float $bee;
    private float $be;
    private float $ena;
    private float $em;

    // Attribut de digestibilité
    private float $alimentEntrant;
    private float $excrement;
    private float $tauxDigestibilite;

    //  liste des croquettes analyser
    private $list_croquettes;

    private $chats = [
        "Chat des Forêts Norvégiennes" => ["poids" => "4.5-9 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Scottish Fold" => ["poids" => "2.5-5.5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Bengal" => ["poids" => "4-7 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Birman" => ["poids" => "4-6 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Bombay" => ["poids" => "2.5-6 kg", "besoins" => "35-60 kcal/kg/jour"],
        "British Longhair" => ["poids" => "4-7 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Burmilla" => ["poids" => "3-6 kg", "besoins" => "50-70 kcal/kg/jour"],
        "California Spangled" => ["poids" => "3.5-6.5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Ceylan" => ["poids" => "2-4 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Chat des Sables" => ["poids" => "3-5 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Chat Turc de Van" => ["poids" => "3-7 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Cymric" => ["poids" => "3.5-6.5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "German Rex" => ["poids" => "2.5-4.5 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Havana Brown" => ["poids" => "2.5-5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Highlander" => ["poids" => "3-7 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Japanese Bobtail" => ["poids" => "2.5-5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Korat" => ["poids" => "2.5-5.5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "LaPerm" => ["poids" => "2.5-6 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Munchkin" => ["poids" => "2.5-4 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Nebelung" => ["poids" => "3.5-6.5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Ocicat" => ["poids" => "3.5-6.5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Chats de gouttière" => ["poids" => "3-5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Abyssin" => ["poids" => "2-4 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Burmese" => ["poids" => "3-4.5 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Devon Rex" => ["poids" => "3-5 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Maine Coon" => ["poids" => "5-7 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Persan" => ["poids" => "4-6 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Sacré de Birmanie" => ["poids" => "4-5 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Siamois" => ["poids" => "3-5 kg", "besoins" => "50-70 kcal/kg/jour"],
        "British Shorthair" => ["poids" => "4-7 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Chartreux" => ["poids" => "3.5-7 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Cornish Rex" => ["poids" => "2.5-4.5 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Exotic Shorthair" => ["poids" => "3-7 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Norvégien" => ["poids" => "4-7 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Sphynx" => ["poids" => "3-5 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Chat des forêts norvégiennes" => ["poids" => "4-8 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Ocicat" => ["poids" => "3.5-6 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Ojos Azules" => ["poids" => "2-4 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Bombay" => ["poids" => "2.5-5.5 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Bengal" => ["poids" => "3.5-7 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Munchkin" => ["poids" => "2-4 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Bleu russe" => ["poids" => "3-5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Chat des Forêts Norvégiennes" => ["poids" => "4.5-9 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Scottish Fold" => ["poids" => "2.5-5.5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Bengal" => ["poids" => "4-7 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Birman" => ["poids" => "4-6 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Bombay" => ["poids" => "2.5-6 kg", "besoins" => "35-60 kcal/kg/jour"],
        "British Longhair" => ["poids" => "4-7 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Burmilla" => ["poids" => "3-6 kg", "besoins" => "50-70 kcal/kg/jour"],
        "California Spangled" => ["poids" => "3.5-6.5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Ceylan" => ["poids" => "2-4 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Chat des Sables" => ["poids" => "3-5 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Chat Turc de Van" => ["poids" => "3-7 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Cymric" => ["poids" => "3.5-6.5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "German Rex" => ["poids" => "2.5-4.5 kg", "besoins" => "50-70 kcal/kg/jour"],
        "Havana Brown" => ["poids" => "2.5-5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Highlander" => ["poids" => "3-7 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Japanese Bobtail" => ["poids" => "2.5-5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Korat" => ["poids" => "2.5-5.5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "LaPerm" => ["poids" => "2.5-6 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Munchkin" => ["poids" => "2.5-4 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Nebelung" => ["poids" => "3.5-6.5 kg", "besoins" => "35-60 kcal/kg/jour"],
        "Ocicat" => ["poids" => "3.5-6.5 kg", "besoins" => "35-60 kcal/kg/jour"],
    ];
 

    /**
     * Le constructeur de la classe AnalyserCroquette 
     *
     * @param [0bjet] $data
     * @param [objet] $list_croquettes
     */
    public function __construct($data, $list_croquettes)
    {
        $this->list_croquettes = $list_croquettes;

        if ($data->animal == 'chat') {
            $this->setChatParameter($data->race, $data->stade, $data->activite, $data->morphologie, $data->sterilite);
            $this->besoinEnergetiqueEntretien();
            $this->besionEnergetique();
        }
    }


    /**
     * Analyse quantitative des croquette
     *
     * @return array
     */
    public function getAnalyse(): array
    {
        $list_croquettes = [];
        foreach ($this->list_croquettes as $key => $data) {

            $list_croquettes[$key]['marque'] = (string) $data->getBrand()->getName();
            $list_croquettes[$key]['name'] = (string) $data->getName();
            $data->isSterilise() == "false" ? $list_croquettes[$key]['sterilise'] = (bool)  false  : $list_croquettes[$key]['sterilise'] = (bool)  true;

            // en kcal/100g
            $list_croquettes[$key]['energie_metabolisable'] = $this->energieMetabolisable($data->getCharacteristic()->getProteine(), $data->getCharacteristic()->getLipide(), $this->ENA($data->getCharacteristic()->getProteine(), $data->getCharacteristic()->getLipide(), $data->getCharacteristic()->getFibre(), $data->getCharacteristic()->getCendres(), $data->getCharacteristic()->getEau()), $data->getCharacteristic()->getFibre(), $data->getCharacteristic()->getEau());

            // en g/jour
            $list_croquettes[$key]['analyse_quantitatif_nutriment'] = $this->analyseQualitatif($data->getCharacteristic()->getProteine(), $data->getCharacteristic()->getLipide(), $this->ENA($data->getCharacteristic()->getProteine(), $data->getCharacteristic()->getLipide(), $data->getCharacteristic()->getFibre(), $data->getCharacteristic()->getCendres(), $data->getCharacteristic()->getEau()));
            $list_croquettes[$key]['quantite_Journaliere'] = $this->quantiteJournaliere();

            $list_croquettes[$key]['url'] = (string) $data->getUrl();
            $list_croquettes[$key]['urlimage'] = (string) $data->getUrlimage();

            $list_croquettes[$key]['element_nutritif']['ENA'] = $this->ENA($data->getCharacteristic()->getProteine(), $data->getCharacteristic()->getLipide(), $data->getCharacteristic()->getFibre(), $data->getCharacteristic()->getCendres(), $data->getCharacteristic()->getEau());
            $list_croquettes[$key]['element_nutritif']['proteine'] = (float) $data->getCharacteristic()->getProteine();
            $list_croquettes[$key]['element_nutritif']['lipide'] = (float) $data->getCharacteristic()->getLipide();
            $list_croquettes[$key]['element_nutritif']['fibre'] = (float) $data->getCharacteristic()->getFibre();
            $list_croquettes[$key]['element_nutritif']['cendres'] = (float) $data->getCharacteristic()->getCendres();
            $list_croquettes[$key]['element_nutritif']['eau'] = (float) $data->getCharacteristic()->getEau();
            $list_croquettes[$key]['facteur'] = (float)  $this->K1 * $this->K2 * $this->K3 * $this->facteurActivite;
        }

        return $this->filter($list_croquettes);
    }






    /**
     * Analyse quantitative d'une marque de croquette
     *
     * @return 
     */
    public function getAnalyseOne()
    {
        $croquette = [];
        $data = $this->list_croquettes;


        $croquette['marque'] = (string) $data->getBrand()->getName();
        $croquette['name'] = (string) $data->getName();
        $data->isSterilise() == "false" ? $croquette['sterilise'] = (bool)  false  : $croquette['sterilise'] = (bool)  true;

        // Energie metabolisable en kcal/100g
        $croquette['energie_metabolisable'] = $this->energieMetabolisable($data->getCharacteristic()->getProteine(), $data->getCharacteristic()->getLipide(), $this->ENA($data->getCharacteristic()->getProteine(), $data->getCharacteristic()->getLipide(), $data->getCharacteristic()->getFibre(), $data->getCharacteristic()->getCendres(), $data->getCharacteristic()->getEau()), $data->getCharacteristic()->getFibre(), $data->getCharacteristic()->getEau());

        $croquette['analyse_quantitatif_nutriment'] = $this->analyseQualitatif($data->getCharacteristic()->getProteine(), $data->getCharacteristic()->getLipide(), $this->ENA($data->getCharacteristic()->getProteine(), $data->getCharacteristic()->getLipide(), $data->getCharacteristic()->getFibre(), $data->getCharacteristic()->getCendres(), $data->getCharacteristic()->getEau()));
        // Quantite journaliere en g/jour
        $croquette['quantite_Journaliere'] = $this->quantiteJournaliere();

        $croquette['url'] = (string) $data->getUrl();
        $croquette['urlimage'] = (string) $data->getUrlimage();

        $croquette['element_nutritif']['ENA'] = $this->ENA($data->getCharacteristic()->getProteine(), $data->getCharacteristic()->getLipide(), $data->getCharacteristic()->getFibre(), $data->getCharacteristic()->getCendres(), $data->getCharacteristic()->getEau());
        $croquette['element_nutritif']['proteine'] = (float) $data->getCharacteristic()->getProteine();
        $croquette['element_nutritif']['lipide'] = (float) $data->getCharacteristic()->getLipide();
        $croquette['element_nutritif']['fibre'] = (float) $data->getCharacteristic()->getFibre();
        $croquette['element_nutritif']['cendres'] = (float) $data->getCharacteristic()->getCendres();
        $croquette['element_nutritif']['eau'] = (float) $data->getCharacteristic()->getEau();

        return $croquette;
    }





    /**
     * Besoin énergétique d’entretien (BEE)
     *
     * @return float
     */
    private  function besoinEnergetiqueEntretien(): float
    {
        $this->bee = $this->coeffBEE * pow($this->poidIdeal, $this->exposantBEE);
        return $this->bee;
    }



    /**
     * Besoin énergétique propre à l’animal étudié
     *
     * @return float
     */
    private  function besionEnergetique(): float
    {
        $facteur = $this->K1 * $this->K2 * $this->K3 * $this->facteurActivite;

        if ($facteur < 0.5) {
            $facteur = 0.5;
        }

        $this->be = $this->besoinEnergetiqueEntretien() * $facteur;
        return $this->be;
    }



    /**
     * Energie brute
     *
     * @param float $proteine
     * @param float $lipide
     * @param float $ena
     * @param float $fibre
     * @return float
     */
    private function energieBrut(float $proteine, float $lipide, float $ena, float $fibre): float
    {
        return 5.7 * $proteine + 9.4 * $lipide + 4.1 * ($ena + $fibre);
    }



    /**
     * Calculer le pourcentage de digestibilité
     *
     * @param float $eau
     * @param float $fibre
     * @return float
     */
    private function pourcentageDigestibiliteChat(float $eau, float $fibre): float
    {
        return 87.9 - (0.88 * $fibre * 100) / (100 - $eau);
    }

    /**
     * Renvoie la quantité d’énergie digérée et absorbée par l’animal
     *
     * @param float $proteine
     * @param float $lipide
     * @param float $ena
     * @param float $fibre
     * @param float $eau
     * @return void
     */
    private function energieDigestible(float $proteine, float $lipide, float $ena, float $fibre, float $eau)
    {
        return $this->energieBrut($proteine, $lipide, $ena, $fibre) * $this->pourcentageDigestibiliteChat($eau, $fibre) / 100;
    }



    /**
     * Renvoie la teneur en glucides (hors fibres) est appelée ENA
     *
     * @param float $prot
     * @param float $lip
     * @param float $fibre
     * @param float $cendres
     * @param float $eau
     * @return float
     */
    private function ENA(float $prot, float $lip, float $fibre, float $cendres, float $eau): float
    {
        $this->ena = 100 - ($prot + $lip + $fibre + $cendres + $eau);
        return $this->ena;
    }


    /**
     * Undocumented function
     *
     * @param float $prot
     * @param float $lip
     * @return float
     */
    private function energieMetabolisable(float $proteine, float $lipide, float $ena, float $fibre, float $eau): float
    {
        $this->em = (float) $this->energieDigestible($proteine, $lipide, $ena, $fibre, $eau) - (0.77 * $proteine);
        return round($this->em);
    }



    /**
     * Undocumented function
     *
     * @param float $prot
     * @param float $lip
     * @param float $ENA
     * @return array
     */
    private function analyseQualitatif(float $prot, float $lip, float $ENA): array
    {
        $analyseQualitatif = [];

        if ($prot <= self::PROTEINE_VALUE['max'] && $prot >= self::PROTEINE_VALUE['min']) {
            $analyseQualitatif['proteine'] = true;
        } else {
            $analyseQualitatif['proteine'] = false;
        }


        if ($lip <= self::LIPIDE_VALUE['max'] &&  $lip >= self::LIPIDE_VALUE['min']) {
            $analyseQualitatif['lipide'] = true;
        } else {
            $analyseQualitatif['lipide'] = false;
        }


        if ($ENA <= self::GLUCIDE_VALUE['max']  &&  $ENA  >= self::GLUCIDE_VALUE['max']) {
            $analyseQualitatif['ENA'] = true;
        } else {
            $analyseQualitatif['ENA'] = false;
        }

        $this->analyseQualitatifs[] = $analyseQualitatif;
        return $analyseQualitatif;
    }



    /**
     * QUANTITÉ DE CROQUETTE À DISTRIBUER PAR JOUR:
     *
     * @return float
     */
    private function quantiteJournaliere(): float
    {
        // Valeur en kcal/jour
        return  $this->be * 100 /  $this->em;
    }


    /**
     * Undocumented function
     *
     * @param string $race
     * @param string $stade
     * @param string $activite
     * @param string $morphologie
     * @param boolean $sterilite
     * @return void
     */
    private function setChatParameter(string $race, string $stade, string $activite, string $morphologie, bool $sterilite)
    {
        $this->exposantBEE = 0.67;
        $this->coeffBEE = 100;

        if ($race == "Abyssin" || $race == "Sphynx") {
            $this->K1 = 1.2;
        } elseif ($race == "Bengal" || $race == "Oriental Shorthair" || $race == "Savannah" || $race == "Sphynx" || $race == "Devon Rex" || $race == "Scottish Fold" || $race == "Maine Coon" || $race == "Siamois") {
            $this->K1 = 1.1;
        } else {
            $this->K1 = 1;
        }


        if ($stade == "De 2 à 4 mois") {
            $this->K2 = 2;
        } elseif ($stade == "De 4 à 6 mois") {
            $this->K2 = 1.6;
        } elseif ($stade == "De 6 à 8 mois") {
            $this->K2 = 1.3;
        } elseif ($stade == "De 8 à 12 mois") {
            $this->K2 = 1.1;
        } else {
            $this->K2 = 1;
        }


        if ($morphologie == "Surpoids") {
            $this->K3 = 1;
        } elseif ($morphologie == "Obèse") {
            $this->K3 = 0.85;
        } elseif ($morphologie == "Mince") {
            $this->K3 = 0.7;
        } elseif ($morphologie == "Maigre") {
            $this->K3 = 1.1;
        } else {
            $this->K3 = 1.3;
        }


        /*

            Méthode de l'entretien ajusté : Cette méthode prend en compte 
            le niveau d'activité physique de votre chat en plus de son poids, 
            en multipliant le résultat de la méthode de l'entretien par 
            un facteur correspondant au niveau d'activité physique :
            
            
            Chat peu actif : Besoins énergétiques x 1,2
            Chat modérément actif : Besoins énergétiques x 1,4
            Chat très actif : Besoins énergétiques x 1,6

        */

        if ($activite == "Calme") {
            $this->facteurActivite = 0.9;
        } elseif ($activite == "Très Calme") {
            $this->facteurActivite = 0.8;
        } elseif ($activite == "Agité") {
            $this->facteurActivite = 1.1;
        } else {
            $this->facteurActivite = 1;
        }
    }


    /**
     * Undocumented function
     *
     * @param array $list_croquettes
     * @return array
     */
    private function filter(array $list_croquettes): array
    {
        $filter = [];

        foreach ($list_croquettes as $list_croquette) {

            if ($list_croquette['analyse_quantitatif_nutriment']['proteine'] == true && $list_croquette['analyse_quantitatif_nutriment']['lipide'] == true && $list_croquette['analyse_quantitatif_nutriment']['ENA'] == true) {
                $filter['tres_bon'][] = $list_croquette;
            }


            if ($list_croquette['analyse_quantitatif_nutriment']['proteine'] == true && $list_croquette['analyse_quantitatif_nutriment']['lipide'] == true && $list_croquette['analyse_quantitatif_nutriment']['ENA'] == false) {
                $filter['assez_bon'][] = $list_croquette;
            }

            if ($list_croquette['analyse_quantitatif_nutriment']['proteine'] == true && $list_croquette['analyse_quantitatif_nutriment']['lipide'] == false && $list_croquette['analyse_quantitatif_nutriment']['ENA'] == true) {
                $filter['assez_bon'][] = $list_croquette;
            }



            if ($list_croquette['analyse_quantitatif_nutriment']['proteine'] == true && $list_croquette['analyse_quantitatif_nutriment']['lipide'] == false && $list_croquette['analyse_quantitatif_nutriment']['ENA'] == false) {
                $filter['bon'][] = $list_croquette;
            }




            if ($list_croquette['analyse_quantitatif_nutriment']['proteine'] == false && $list_croquette['analyse_quantitatif_nutriment']['lipide'] == true && $list_croquette['analyse_quantitatif_nutriment']['ENA'] == true) {
                $filter['mauvais'][] = $list_croquette;
            }

            if ($list_croquette['analyse_quantitatif_nutriment']['proteine'] == false && $list_croquette['analyse_quantitatif_nutriment']['lipide'] == false && $list_croquette['analyse_quantitatif_nutriment']['ENA'] == true) {
                $filter['mauvais'][] = $list_croquette;
            }

            if ($list_croquette['analyse_quantitatif_nutriment']['proteine'] == false && $list_croquette['analyse_quantitatif_nutriment']['lipide'] == true && $list_croquette['analyse_quantitatif_nutriment']['ENA'] == false) {
                $filter['mauvais'][] = $list_croquette;
            }

            if ($list_croquette['analyse_quantitatif_nutriment']['proteine'] == false && $list_croquette['analyse_quantitatif_nutriment']['lipide'] == false && $list_croquette['analyse_quantitatif_nutriment']['ENA'] == false) {
                $filter['mauvais'][] = $list_croquette;
            }
        }
        return $filter;
    }


    // private function tauxDIgestibilite()
    // {
    //     $this->tauxDigestibilite = ($this->alimentEntrant - $this->excrement) / $this->alimentEntrant;
    //     return $this->tauxDigestibilite;
    // }
}


/**
 *  quantite journalier 
 * 
 *  de 4 a 6 mois => 4 repas de 55g à 60g
 *  de 6 a 8 mois => 3 repas de 60g à 75g
 *  plus de 8 mois => 2 repas de 100g*/
 


/*
 $chats = [
    "Chat des Forêts Norvégiennes" => [
        "poids_min" => 4.5,
        "poids_max" => 9,
        "besoins_min" => 35,
        "besoins_max" => 60
    ],
    "Scottish Fold" => [
        "poids_min" => 2.5,
        "poids_max" => 5.5,
        "besoins_min" => 35,
        "besoins_max" => 60
    ],
    "Bengal" => [
        "poids_min" => 4,
        "poids_max" => 7,
        "besoins_min" => 50,
        "besoins_max" => 70
    ],
    "Birman" => [
        "poids_min" => 4,
        "poids_max" => 6,
        "besoins_min" => 35,
        "besoins_max" => 60
    ],
    "Bombay" => [
        "poids_min" => 2.5,
        "poids_max" => 6,
        "besoins_min" => 35,
        "besoins_max" => 60
    ],
    "British Longhair" => [
        "poids_min" => 4,
        "poids_max" => 7,
        "besoins_min" => 35,
        "besoins_max" => 60
    ],
    "Burmilla" => [
        "poids_min" => 3,
        "poids_max" => 6,
        "besoins_min" => 50,
        "besoins_max" => 70
    ],
    "California Spangled" => [
        "poids_min" => 3.5,
        "poids_max" => 6.5,
        "besoins_min" => 35,
        "besoins_max" => 60
    ],
    "Ceylan" => [
        "poids_min" => 2,
        "poids_max" => 4,
        "besoins_min" => 50,
        "besoins_max" => 70
    ],
    "Chat des Sables" => [
        "poids_min" => 3,
        "poids_max" => 5,
        "besoins_min" => 50,
        "besoins_max" => 70
    ],
    "Chat Turc de Van" => [
        "poids_min" => 3,
        "poids_max" => 7,
        "besoins_min" => 50,
        "besoins_max" => 70
    ],
    "Cymric" => [
        "poids_min" => 3.5,
        "poids_max" => 6.5,
        "besoins_min" => 35,
        "besoins_max" => 60
    ],
    "German Rex" => [
        "poids_min" => 2.5,
        "poids_max" => 4.5,
        "besoins_min" => 50,
        "besoins_max" => 70
    ],
    "Havana Brown" => [
        "poids_min" => 2.5,
        "poids_max" => 5,
        "besoins_min" => 35,
        "besoins_max" => 60
    ],
    "Highlander" => [
        "poids_min" => 3, 
        "poids_max" => 7, 
        "besoins_min" => 35, 
        "besoins_max" => 60
    ],
    "Japanese Bobtail" => [
        "poids_min" => 2.5, 
        "poids_max" => 5, 
        "besoins_min" => 35, 
        "besoins_max" => 60
    ],
    "Korat" => [
        "poids_min" => 2.5, 
        "poids_max" => 5.5, 
        "besoins_min" => 35, 
        "besoins_max" => 60
    ],
    "LaPerm" => [
        "poids_min" => 2.5, 
        "poids_max" => 6, 
        "besoins_min" => 35, 
        "besoins_max" => 60
    ],
    "Munchkin" => [
        "poids_min" => 2.5, 
        "poids_max" => 4, 
        "besoins_min" => 35, 
        "besoins_max" => 60
    ],
    "Nebelung" => [
        "poids_min" => 3.5, 
        "poids_max" => 6.5, 
        "besoins_min" => 35, 
        "besoins_max" => 60
    ],
    "Ocicat" => [
        "poids_min" => 3.5, 
        "poids_max" => 6.5, 
        "besoins_min" => 35, 
        "besoins_max" => 60
    ],
    "Chats de gouttière" => [
        "poids_min" => 3, 
        "poids_max" => 5, 
        "besoins_min" => 35, 
        "besoins_max" => 60
    ],
    "Abyssin" => [
        "poids_min" => 2, 
        "poids_max" => 4, 
        "besoins_min" => 50, 
        "besoins_max" => 70
    ],
    "Burmese" => [
        "poids_min" => 3, 
        "poids_max" => 4.5, 
        "besoins_min" => 50, 
        "besoins_max" => 70
    ],
    "Devon Rex" => [
        "poids_min" => 3, 
        "poids_max" => 5, 
        "besoins_min" => 50, 
        "besoins_max" => 70
    ],
    "Maine Coon" => [
        "poids_min" => 5, 
        "poids_max" => 7, 
        "besoins_min" => 35, 
        "besoins_max" => 60
    ],
    "Persan" => [
        "poids_min" => 4, 
        "poids_max" => 6, 
        "besoins_min" => 35, 
        "besoins_max" => 60
   
*/