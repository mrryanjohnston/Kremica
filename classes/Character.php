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

  function __construct($attributes) {
    $this->userID = $attributes['userID'];
    $this->characterID = $attributes['characterID'];
    $this->name = $attributes['name'];
    $this->gender = $attributes['gender'];

    $this->head = $attributes['head'];
    $this->hair = $attributes['hair'];
    $this->eyes = $attributes['eyes'];
    $this->nose = $attributes['nose'];

    $this->unusedstatpoints = $attributes['unusedstatpoints'];
    $this->level = $attributes['level'];
    $this->currentexperience = $attributes['currentexperience'];
    $this->maxexperience = $attributes['maxexperience'];
    $this->currenthealth = $attributes['currenthealth'];
    $this->maxhealth = $attributes['maxhealth'];
    $this->strength = $attributes['strength'];
    $this->defense = $attributes['defense'];
    $this->speed = $attributes['speed'];
    $this->agility = $attributes['agility'];
    $this->accuracy = $attributes['accuracy'];

    $this->weapons = $attributes['weapons'];
    $this->currentweapon = $attributes['currentweapon'];
    $this->shields = $attributes['shields'];
    $this->currentshield = $attributes['currentshield'];
    $this->inventory = $attributes['inventory'];
    $this->potionholster = $attributes['potionholster'];
    $this->gold = $attributes['gold'];
    $this->maxgold = $attributes['maxgold'];

    $this->dead = $attributes['dead'];
  }

  /*Getter and Setter as found on SO: http://stackoverflow.com/questions/4478661/getter-and-setter */
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
  * Get by character ID
  * Characters should be returnable via a character ID.
  * @return A Character object
  */
  public static function getByCharacterID($characterID) {
    
  }

  /**
  * Attack
  * The amount of damage taken by a blow from an enemy will vary
  * depending on type differences, the character's stats, the
  * monster's stats, and the equipment that the character and enemy own
  * wield.
  *
  * This damage may differ from the monster damage calculation
  *
  * @return an array that could contain the following keys:
  * - damage:    An array with keys:
  *   - points:  the amount of damage dealt. Ranges from 0 to n, where n
  *              is the max health of the enemy.
  *   - dead:    TRUE if enemy is dead
  * - condition: An array with keys: 
  *   - type:    character can inflict a condition of poison, sleep, slow,
  *              blind and others. FALSE if nothing.
  *   - applied: If the enemy is resistant or the condition didn't apply,
  *              this will be FALSE.
  * - miss:      If the character misses, this is TRUE.
  * - dodge:     If the enemy dodges, this is TRUE.
  */
  public function attack($enemy, $damager) {
    $random = mt_rand(1,100);
    if ($random < accuracy) {
      $random = mt_rand(1,100);
      if ($random > $enemy->agility) {
        $damage    = $damager->calculateDamage($this, $enemy);
        $condition = $damager->inflictCondition();
        $applied   = $enemy->takeCondition($condition);
        $dead      = $enemy->takeDamage($damage);
        $result    = array('damage' => array('points' => $damage, 'dead' => $dead), 'condition' => array('type' => $condition, 'applied' => $applied));
      } else {
        $result = array('damage' => 0, 'dodge' => TRUE);
      }
    } else {
      $result = array('damage' => 0, 'miss' => TRUE);
    }
    return $result;
  }

  /**
  * Take Condition
  * Check if a condition can be applied to character.
  * @return TRUE if applied, FALSE if not
  */
  public function takeCondition($condition) {
    if(in_array($condition, $this->resist) || in_array($condition, $shields[$currentshield]->resist)) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  /**
  * Take Damage
  * Take a given amount of damage from $currenthealth.
  * @return TRUE if dead, FALSE if still alive
  */
  public function takeDamage($damage) {
    $currenthealth -= $damage;
    return ($currenthealth<=0);
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
  * Any carry-over experience points are set to the current experience points
  * Character gains Attribute points to spend as they please
  * - Attributes are determined logarithmically
  */
  public function gainLevel($carryover) {
    $this->level++;
    $this->currentexperience = $carryover;
    $this->maxexperience     = calculateExperience();
    $this->unusedstatpoints += calculateAttributePoints();
  }

  /**
  * Gain Experience
  * When a character kills an enemey, they receive exeperience points.
  * If the amount of experience points they gain causes their current
  * experience points to excede their max, they will level. Extra experience
  * points will roll over. This will occur until a character finally
  * has less current experience points than their max
  */
  public function gainExperience($points) {
    $this->currentexperience+$points;
    while ($this->currentexperience >= $this->maxexperience) {
      $carryover = $this->maxexperience - ($this->currentexperience+$points);
      $this->gainLevel($carryover);
    }
  }
}