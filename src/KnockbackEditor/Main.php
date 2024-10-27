<?php

namespace KnockbackEditor;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use KnockbackEditor\commands\KnockbackCommand;
use KnockbackEditor\events\KnockbackListener;

class Main extends PluginBase {

    private Config $knockbackConfig;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->knockbackConfig = new Config($this->getDataFolder() . "knockback.yml", Config::YAML);
        $this->registerCommands();
        $this->registerEvents();

        $this->getLogger()->info("KnockbackEditor enabled successfully.");
    }

    /**
     * Registers the KnockbackEditor commands.
     */
    private function registerCommands(): void {
        $this->getServer()->getCommandMap()->register("knockback", new KnockbackCommand($this));
    }

    /**
     * Registers the KnockbackEditor event listeners.
     */
    private function registerEvents(): void {
        $this->getServer()->getPluginManager()->registerEvents(new KnockbackListener($this), $this);
    }

    /**
     * Returns the knockback configuration.
     *
     * @return Config
     */
    public function getKnockbackConfig(): Config {
        return $this->knockbackConfig;
    }
}
