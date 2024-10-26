<?php

namespace KnockbackEditor\commands;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use KnockbackEditor\forms\KnockbackForm;
use KnockbackEditor\Main;

class KnockbackCommand extends Command {
    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("knockback", "View or edit knockback settings per world", "/knockback", []);
        $this->setPermission("knockbackeditor.command.knockback");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player || !$sender->hasPermission("knockbackeditor.command.knockback")) {
            $sender->sendMessage("You don't have permission to use this command.");
            return false;
        }

        $this->openWorldSelectionForm($sender);
        return true;
    }

    private function openWorldSelectionForm(Player $player): void {
        $form = new SimpleForm(function (Player $p, ?int $data) use ($player): void { 
            if ($data === null) {
                return;
            }
            $worlds = $this->plugin->getServer()->getWorldManager()->getWorlds();
            $worldList = array_values($worlds);
            if (isset($worldList[$data])) {
                $selectedWorld = $worldList[$data]->getFolderName();
                (new KnockbackForm($this->plugin, $selectedWorld))->open($player);
            } else {
                $player->sendMessage("Invalid world selection.");
            }
        });
        $form->setTitle("Knockback Settings");
        $form->setContent("Select a world to view or edit knockback settings.");
        $worlds = $this->plugin->getServer()->getWorldManager()->getWorlds();
        foreach ($worlds as $world) {
            $form->addButton($world->getFolderName(), 0, "textures/ui/op.png");
        }
        $player->sendForm($form);
    }
}
