<?php
/**
* Character Class
* Users can have multiple characters
*/
class Character 
{
  /*General Demographics*/
  private $userID      = NULL;
  private $characterID = NULL;
  private $name        = NULL;
  private $gender      = NULL;

  /*Appearance*/
  private $head = 0;
  private $hair = 0;
  private $eyes = 0;
  private $nose = 0;

  /*Stats*/
  private $unusedstatpoints  = 0;
  private $level         = 0;
  private $currentexperience = 0;
  private $maxexperience = 0;
  private $currenthealth = 0;
  private $maxhealth     = 0;
  private $strength      = 0;
  private $defense       = 0;
  private $speed         = 0;
  private $agility       = 0;
  private $accuracy      = 0;

  /*Bags, Weapons, Potions, etc.*/
  private $weapons       = array();
  private $currentweapon = 0;
  private $shields       = array();
  private $currentshield = 0;
  private $inventory     = array();
  private $potionholster = array();
  private $gold          = 0;
  private $maxgold       = 0;

  private $dead = NULL;

  /*Getter/Setter as found on SO: http://stackoverflow.com/questions/4478661/getter-and-setter */
  public function __get($property) {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  public function __set($property, $value) {
    if (property_exists($this, $property)) {
      $this->$property = $value;
    }

    return $this;
  }

  /**
  * Calculate experience points
  * Based on a character's level, their experience points will fluctuate
  * Experience points are determined as follows:
  * 400 * ( level + ( level^2 * 20 ) )
  */
  public function calculateExperience() {
    return 400 * ( $this->level + ( pow($this->level,2) * 20 ));
  }

  /**
  * Calculate attribute points
  * Based on a character's level logarithmically
  * Attribute points are determined as follows:
  * 2 + log2(level)
  */
  public function calculateAttributePoints() {
    return 3 + (log($this->level, 2));
  }

  /**
  * Gain a level
  * When a character gains a level, a couple of things happen:
  * Level increases by 1
  * Experience points until next level are recalculated
  * Character gains Attribute points to spend as they please
  * - Attributes are determined logarithmically
  */
  public function gainLevel() {
    $this->level++;
    $this->maxexperience = calculateExperience();
    $this->unusedstatpoints = calculateAttributePoints();
  }
}