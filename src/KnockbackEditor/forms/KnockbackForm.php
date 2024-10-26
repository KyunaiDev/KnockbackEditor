<?php

namespace KnockbackEditor\forms;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use KnockbackEditor\Main;

class KnockbackForm {
    private Main $plugin;
    private string $worldName;

    public function __construct(Main $plugin, string $worldName) {
        $this->plugin = $plugin;
        $this->worldName = $worldName;
    }

    public function open(Player $player): void {
        $form = new SimpleForm(function (Player $player, ?int $data): void {
            if ($data === null) return;
            if ($data === 0) {
                $this->showSettings($player);
            } elseif ($data === 1) { 
                $this->editSettings($player);
            }
        });

        $form->setTitle("Knockback - {$this->worldName}");
        $form->setContent("Choose an option for knockback settings in {$this->worldName}");
        $form->addButton("View Knockback Settings", 0, "textures/ui/magnifyingGlass");
        $form->addButton("Edit Knockback Settings", 0, "textures/ui/debug_glyph_color.png");
        $player->sendForm($form);
    }

    private function showSettings(Player $player): void {
        $config = $this->plugin->getKnockbackConfig();
        $worldSettings = $config->get($this->worldName, [
            "x" => 0.4,
            "y" => 0.4,
            "z" => 0.4,
            "attack-delay" => 10 
        ]);

        $form = new SimpleForm(function (Player $player, ?int $data): void {});
        $form->setTitle("Knockback Settings - {$this->worldName}");
        $form->setContent(
            "Current knockback values:\n" .
            "X: {$worldSettings['x']}\n" .
            "Y: {$worldSettings['y']}\n" .
            "Z: {$worldSettings['z']}\n" .
            "Attack Delay: {$worldSettings['attack-delay']} ticks"
        );
        $form->addButton("Close");
        $player->sendForm($form);
    }

    public function editSettings(Player $player): void {
        $config = $this->plugin->getKnockbackConfig();
        $worldSettings = $config->get($this->worldName, [
            "x" => 0.4,
            "y" => 0.4,
            "z" => 0.4,
            "attack-delay" => 10
        ]);

        $form = new CustomForm(function (Player $player, ?array $data) use ($config): void {
            if ($data !== null) {
                $config->set($this->worldName, [
                    "x" => floatval($data[0]),
                    "y" => floatval($data[1]),
                    "z" => floatval($data[2]),
                    "attack-delay" => intval($data[3]) 
                ]);
                $config->save();
                $player->sendMessage("Knockback settings updated for {$this->worldName}.");
            }
        });

        $form->setTitle("Edit Knockback - {$this->worldName}");
        $form->addInput("Knockback X", "0.4", (string)$worldSettings["x"]);
        $form->addInput("Knockback Y", "0.4", (string)$worldSettings["y"]);
        $form->addInput("Knockback Z", "0.4", (string)$worldSettings["z"]);
        $form->addInput("Attack Delay (ticks)", "10", (string)$worldSettings["attack-delay"]); 
        $player->sendForm($form);
    }
}
