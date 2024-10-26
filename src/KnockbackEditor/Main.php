<?php

namespace KnockbackEditor;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use KnockbackEditor\commands\KnockbackCommand;
use KnockbackEditor\events\KnockbackListener;

class Main extends PluginBase implements Listener {
    
    private Config $knockbackConfig;

    public function onEnable(): void {
        $this->saveResource("knockback.yml");
        $this->knockbackConfig = new Config($this->getDataFolder() . "knockback.yml", Config::YAML);
        $this->getServer()->getCommandMap()->register("knockback", new KnockbackCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new KnockbackListener($this), $this);
        $this->getLogger()->info("KnockbackEditor enabled successfully.");
    }

    public function getKnockbackConfig(): Config {
        return $this->knockbackConfig;
    }
}
