Memo
====

This file content information I use while development.


#### Game options

List of game options available :  
* "penalty" (int) : Number of hour before penalty (0 => disabled)
* "bonus" (bool) : Enable/Disable bonus
* "weapon" (bool) : Enable/Disable weapon

#### Mercure URI

* /game/{slug}.json (match.ajax.infos) => Waiting page (update game)
* /game/{slug}-players.json (match.ajax.infos.players) => Waiting page (update players)

#### Box (grid)

* x {int} : The X position
* y {int} : The Y position
* img {?int} : the img number to display
* player {?int} : the player position of the owner boat (warning : it's not the player's ID)
* team {?int}: the team ID
* boat {?int} : the boat number
* shoot {?int} : The player position shoot on this box
* dead {?bool} : isDead
* explose {?bool} : currently explose


#### Boat

* 0 : Boat number
* 1 : The lenght of the boat
* 2 : Number of touch

#### Mercure return

All responses content 2 items :
* topic : The topic URI
* content : The data

The content :
* img[] {Object} :
    * x {int} : The X position
    * y {int} : The Y position
    * img {?int} : the img number to display
    * player {?int} : the player position of the owner boat (warning : it's not the player's ID)
    * team {?int} : the team ID
    * shoot {?int} : The player position shoot on this box
    * score[] {?Object} : List of score to update
        * playerPosition {int} => new score {int}
    * life[] {?Object} : List of life to update
        * playerPosition {int} => new life {int}
    * sink[] {?Object] : List of img to update after animation
        * x, y, img, shoot, player, dead
* finished {bool} : Game is over
