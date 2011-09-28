 _        _______  _______  _______ _________ _______  _______ 
| \    /\(  ____ )(  ____ \(       )\__   __/(  ____ \(  ___  )
|  \  / /| (    )|| (    \/| () () |   ) (   | (    \/| (   ) |
|  (_/ / | (____)|| (__    | || || |   | |   | |      | (___) |
|   _ (  |     __)|  __)   | |(_)| |   | |   | |      |  ___  |
|  ( \ \ | (\ (   | (      | |   | |   | |   | |      | (   ) |
|  /  \ \| ) \ \__| (____/\| )   ( |___) (___| (____/\| )   ( |
|_/    \/|/   \__/(_______/|/     \|\_______/(_______/|/     \|
Kremica by Ryan Johnston
Character Drawings by Jess Steffan

1) Basic Overview

Kremica is a game about Life, Death, and Battling Overgrown Slugs.
The point behind the game is to give the player a nice quick little shot of game play, and then
deliver a crushing blow of defeat so devastating that the person decides to logoff and continue
living life. Compare this to other online Role Playing Games currently in mainstream media
(WoW, EverQuest, etc.).

2) Target Audience

The game was inspired primarily by my college student friend who got into trouble with grades
after being sucked into WoW his sophomore year. It follows that the primary target audience 
consists of college students. The game provides a brief intermission to life; if a person needs
a break from studying, for example, they may hop onto Kremica, play a character until it dies,
and then log off.

3) Where's the reward?

This has been asked time and time again by various people who I explain the basic concept
behind Kremica to. The long-winded answer to this is another question: where's the reward in
any online video game? How does a player reap the benefits of hours of play?

Kremica rewards players in two primary ways:

   1. A high score table
   2. Provides a much needed mindless break

Players are rewarded in game by having their characters entered into a database. This database
acts as a high score chart. The reason for the high scores table was to give some sort of 
parallel to showing off in other Massively Multiplayer Online games in which characters are 
alive forever (until their deletion by the player). Aka, this is ego inflation.

Second, the game itself provides a necessary break from studies, work, or any other stressor
in the player's life. In this way, it can also reward a player for the things they may
accomplish after logging off; letting a person get back to life rather than sucking them in
may be beneficial to the lives of those with an addictive personality.

4) Gameplay

There are currently some pictures of character models, but they will have no purpose other 
than to serve as illustrations of the in-game words. The game is also very simple, in that the
player doesn't need to learn a complex interface. Big buttons ahoy!

However, for what the game lacks in convoluted interfaces it makes up for in game play and 
strategy.

5) Strategy

While the game is meant to be very simple to play, each character would be thrown into a very 
harsh world where they'd have to carefully pick strategies rather than brute force their way 
through the game. The character is started out with nothing but his/her fists and bare arms to 
defend his or herself.

Later on, character may gain weapons or shields. From battles, they may obtain items which can
be used to change the type of these weapons at a non-player character (NPC). Type differences
can make or break a battle for a player; certain types are weaker or stronger than others and
may provide bonuses/disadvantages to the character.

6) Services Provided

The game has several services which were all a pain in the rear-end to implement. The following is a
list of things I consider to be seperate services crucial to playing the game. I also list
"sub-services," services which are a part of the larger main service.
   1. Battling
	a. Type Differences
	b. Bonus type effect (poisoning, lowering accuracy, lowering speed)
	c. Weapon/Shield Switching
	d. Potion Using
	e. Experience Gain (leveling)
   2. Inventory System
	a. Using Potions
	b. Equipping or discarding Weapons/Shields
	c. Discarding Items
   3. Character Sheet
	a. Display of character statistics
   4. Mail System
	a. Send/Receiving Mail
	b. Sentbox/Inbox
	c. Deleting Multiple Messages
   5. High Scores Table (Annals of Adventurers)
	a. Character name/Username search
   6. User Profiles
	a. Quick send mails
	b. Friend request
	c. Block (not implemented yet)
   7. Merchant System
	a. Money/Purchase system
   8. Infusion System
	a. Changes strategy of a character
   9. Quest System
	a. Dynamic and flexible quest creation
	b. Rewards (or punishes) character
	c. Adds a small element of story-telling

Battling is the core of the game. A character may randomly walk into an enemy determined by comparing
their level an the enemy's level. A character has a high chance of running into monsters his or her
own level. However, as the character progresses, the chance that they will run into a more powerful
enemy increases.

Many factors go into what determines the amount of damage done to an enemy or to a character.
Speed determins the order in which the battle takes place; whoever has the highest speed attacks first
during a turn. Strength and Weapon Power determine the gross damage dealt. Defense negates a certain
amount of the Strength, while Shield Power negates a certain amount of Weapon Power. This number 
is then multiplied by either 0.5, 1, or 2, depending on whether or not there is a type disadvantage:

Type Table: (- means .5, blank means 1, + means 2)
		Defender
	-----------------------------------------------------------------------------------------
	|           |Metal | Technology | Nature | Life | Death | Time | Valor | Poison | Flesh |
	|Metal      |      |            |        |      |       |      |       |        |   +   |

	|Technology |  -   |     -      |   +    |      |   +   |      |       |    +   |       |

	|Nature     |  +   |            |        |   -  |       |      |       |    +   |   -   |

	|Life       |      |            |   -    |      |   -   |      |       |        |   -   |
Attacker
	|Death      |  -   |     -      |   +    |   +  |       |      |   +   |        |   +   |

	|Time       |  +   |     +      |        |      |       |      |       |    -   |   +   |

	|Valor      |      |            |        |      |   +   |      |       |        |   -   |

	|Poison     |  -   |     -      |   +    |   +  |   -   |   +  |       |        |   +   |

	|Flesh      |  -   |     -      |        |      |   -   |   -  |       |        |       |

Certain types also have bonus effects. Poison weapons may poison the defender. Once poisoned, the
defender will lose 1/10 of his or her health per turn. Flesh decreases accuracy. Think of this as
punching or a boxing match, which may stun someone or blur the defender's vision. Time slows down
the opponent, temporarily lowering his or her speed.

A player may chose to switch in and out weapons or shields, depending on type bonuses. If they
switch out a weapon or shield, they may not attack for this turn. If they use a potion, they may
not attack that turn.

If a character survives the battle, they will be rewarded with experience points. They may also
be rewarded with gold and/or an item dropped by the enemy.

Once a character reaches enough experience points, they level up. This increases each attribute
by 1 automatically and also allows the character to spend 3 bonus attribute points as they see fit.

The Inventory System is how a character controls the items they have in their possession. Items
include potions, swords, shields, and items found from dead monsters. Characters may equip swords
and shields in the inventory. They may also use potions outside of battle. They may also throw out
any of their items (excluding fists or bare arms) to make room for other items.

A character sheet lists the stats for a gien character. Level, Health Points (HP), Experience 
Points (EXP), Strength, Defense, and Speed are all shown for a given character. One may also look
at character outside of their own account. They may get some sort of idea for what it takes
to reach a given level by scoping out another character's stat distribution.

The mail system is a fully functional messaging system. It does not use any email protocols.
The mail system can be used to communicate between different user accounts. A user has an inbox
and a sentbox. They may chose to delete messages as they see fit.

The Annals of Adventurers is a high scores table. Yes, it simply displays a list of all characters
and the user account they are associated with. Yes, this is a fairly basic function. However, in
the context of the game, this serves as a way for users to boast about high level characters and
a means for which newer character to either contact seasoned veterans or take tips from the stats
of a given character.

User Profiles are useful in that they list all of the characters created by a given user. They also
provide a way to "friend" another user. This serves no function yet, but in the future, there will
be a list of all online friends and you may message them during this time. In the far far future,
once pvp is implemented, you may challenge your online friends to battles. Blocking is not yet 
implemented, but will serve as a way to block annoying users from sending messages.

Merchants provide a way fro character to re-stock their inventory. Whether this means buying weapons,
shields, or potions, merchants have it all (though not all at once). A user may run into a potion,
shield, or weapon merchant, and then make purchase of items with gold dropped from monsters.
In the future, more merchants will be added and, based on the character level, they would only see
certain merchants. This provides a nice way to provide different weapon/shield/potion strengths
as the character progresses and needs change.

The Infuser allows the change of type for weapons and shields. This is done with items dropped by
monsters. If a monster drops an item and the character runs into the infuser, he or she may use
this item to change their weapon or shield type, fundamentally changing their play strategy.
Perhaps they wish to open up with a powerful flesh type move, decreasing the enemy's accuracy,
then finish them off with a Metal weapon. Maybe they are weak to certain types of monsters
due to their shield type choice and want a way to specifically kill that type of monster quickly.

Finally, the Quest system provides a way for characters to experience, grow, and possibly die in
tons of different ways. The way the quest system is structured is very flexible, so that almost
anything is possible to accomplish. For example, a quest could reward a character with 100 gold
after finding multiple pieces of a map, or it may provide an amulet which temporarily provides
extra dense, but then explodes later, taking some HP from the character. In the future, I'd like
to include quests that totally annihilate the character, but for now, I left that out.