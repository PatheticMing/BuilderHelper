<?php

namespace BuilderHelper;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\utils\TextFormat as C;

class builderhelper extends PluginBase implements Listener {
    
    private $pcount;
    private $id;
    private $count;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        $this->id = NULL;
        $this->getLogger()->info(C::GREEN . "Loaded!");
    }
    
    public function onId(PlayerDropItemEvent $event) {
        $player = $event->getPlayer();
        if(isset($this->pcount)) {
            $this->id = $event->getItem()->getId();
            $player->sendMessage(C::GREEN . "Started counting " . $this->pcount . " blocks!");
        }
    }

    public function blockPlaceCount(BlockPlaceEvent $event) {
        $player = $event->getPlayer();
        if(isset ($this->pcount)) {
            $id = $event->getBlock()->getId();
                if($id !== $this->id) {
                    $this->count--;
                    $this->count++;
                } else {
                    $this->count++;
                }
                if($this->count == $this->pcount) {
                    $player->sendMessage(C::AQUA . "[BuilderHelper]" . C:: GOLD . "You have placed " . $this->count . " blocks!");
                    unset($this->pcount);
                    unset($this->count);
                    unset($this->id);
                    }                  
            }
    }
    
    public function blockBreakCount(BlockBreakEvent $event) {
        if(isset($this->pcount)) {
            $id = $event->getBlock()->getId();
                if(isset($this->id)) {
                    if($id === $this->id) {
                        $this->count--;
                    } else {
                        $this->count--;
                        $this->count++;
                    }
                }
        }
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        if ($sender instanceof Player) {
            if(strtolower($command->getName() == "bh")) {
                switch($args[0]) {
                    case "pos":
                        $x = round($sender->getX());
                        $y = round($sender->getY());
                        $z = round($sender->getZ());
                        $sender->sendMessage(C::AQUA . "You are at" . C::RED . $x . C::AQUA . "," . C::RED . $y .  C:: AQUA . "," . C::RED .  $z);
                        break;
                    case "placed":
                        if(isset($this->count)) {
                        $sender->sendMessage(C::AQUA . "[BuilderHelper]" . C::GOLD . "You placed " . $this->count . "blocks!");
                        } else {
                            $sender->sendMessage(C::RED . "You haven't start to count yet!");
                        }
                        break;
                    case "start":
                        if(count($args) < 2) {
                            $sender->sendMessage(C::RED . "Please enter how many blocks you want to place!");
                        } else {
                                $this->pcount = $args[1];
                                $sender->sendMessage(C::GOLD . "Drop the block to let me know the block id!");
                        }
                        break;
                    default :
                        $sender->sendMessage(C::YELLOW . "----------" . C::GREEN . "BuilderHelper" . C::YELLOW . "----------");
                        $sender->sendMessage(C::AQUA . "/bh : Show the help list");
                        $sender->sendMessage(C::AQUA . "/bh pos : get your position");
                        $sender->sendMessage(C::AQUA . "/bh start <counts> : Tell you to stop before you build < counts");
                        $sender->sendMessage(C::AQUA . "/bh placed : Check how many blocks you placed");
                        break;
                }
            } return true;
        } else {
            $sender->sendMessage(C::RED . "CONSOLE don't build!!!XD");
            return true;
        }
    }
    
}
