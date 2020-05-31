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

* x : The X position
* y : The Y position
* img : the img number to display
* player : the player position of the owner boat (warning : it's not the player's ID)
* team : the team
* boat : the boat number
* shoot : The player position shoot on this box


#### Boat

* 0 : Boat number
* 1 : The lenght of the boat
* 2 : Number of touch

#### Mercure return

All responses content 2 items :
* topic : The topic URI
* content : The data
