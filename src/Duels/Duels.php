<?php

namespace Duels;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as TE;
use pocketmine\Player;
use pocketmine\utils\Config;
use UHCM\score\ScoreAPI;
use pocketmine\item\Item;
use pocketmine\entity\Skin;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\{
LevelEventPacket,
PlaySoundPacket,
LevelSoundEventPacket
};

class Duels extends PluginBase implements Listener {
 
    public function onEnable() : void {
		        self::$instance = $this;
            
            $this->score = new ScoreAPI($this);
            Entity::registerEntity(DuelNPC::class,true);
            $this->getServer()->getLogger()->info("§a[DUELS] Is a go by @WEATHERCRAFTYT1");
            $this->getServer()->getPluginManager()->registerEvents($this ,$this);  
            foreach($this->skinsNPC as $file){
		              	    $this->saveResource($file);  
                    }
                  $this->getScheduler()->scheduleRepeatingTask(new DuelTask($this), 20);
            }
            
    public static function getInstance() {
         return self::$instance;
    }
    
    public function addLevelSound(Player $pl,int $pitch = 0) : void {
                $pl->getLevel()->broadcastLevelSoundEvent($pl, LevelSoundEventPacket::SOUND_LEVELUP,$pitch);
    }


    public function addSounds(Player $pl,string $name = "mob.guardian.ambient",int $pitch = 1) {
  	                      $play = new PlaySoundPacket();
                            $play->soundName = $name;
                            $play->x = $pl->x;
                            $play->y = $pl->y;
                            $play->z = $pl->z;
                            $play->volume = 2;
                            $play->pitch = $pitch;
                            $play->dataPacket($play);
    }
    
    public function setDuelNPC($player,$SkinName){
        $skin = $player->getSkin();
        $path = $this->getDataFolder().$SkinName.".png";
        $img = @imagecreatefrompng($path);
        $skinbytes = "";
        $s = (int)@getimagesize($path)[1];

        for($y = 0; $y < $s; $y++) {
            for($x = 0; $x < 64; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        $player->setSkin(new Skin($skin->getSkinId(), $skinbytes, "", "geometry.".$SkinName,file_get_contents($this->getDataFolder().$SkinName.".json")));
        $player->sendSkin();
    }
    
    public function getJoinItem(Player $player) {
		    $item = Item::get(Item::BED, 0, 1)->setCustomName(TE::GREEN . "Leave match");
        $player->getInventory()->setItem(8, $item);
    }
    
    public function removeItems(Player $pl) {
		$pl->getInventory()->removeItem(Item::get(Item::BED,0,1));
    }
}
