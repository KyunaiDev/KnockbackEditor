# KnockbackEditor

**KnockbackEditor** is a PocketMine-MP plugin that lets server administrators customize knockback and attack delay settings per world. With configurable options for horizontal and vertical knockback, as well as an attack delay between hits, you can fine-tune PvP dynamics on your server.

## Features

- **Per-World Knockback Settings**: Define unique knockback values for each world.
- **Configurable Attack Delay**: Set a delay between hits to control hit frequency.
- **User-Friendly GUI**: Edit settings through a custom UI with a SimpleForm and CustomForm.
- **World Selection**: Quickly view or edit settings for each world with an intuitive menu.

## Installation

1. Download the plugin's `.phar` file from [Poggit](https://poggit.pmmp.io).
2. Place the `.phar` file in the `plugins` folder of your PocketMine-MP server.
3. Start or restart the server to load the plugin.

## Commands & Permissions

### Commands

| Command        | Description                         | Usage           |
| -------------- | ----------------------------------- | --------------- |
| `/knockback`   | Opens the knockback settings menu   | `/knockback`    |

### Permissions

| Permission                         | Description                               | Default |
| ---------------------------------- | ----------------------------------------- | ------- |
| `knockbackeditor.command.knockback`| Allows use of the `/knockback` command    | OP      |

## Configuration

The configuration file (`knockback.yml`) is generated in the plugin's data folder. Each world has its own configurable settings:

```yaml
world1:
  Knockback-Enabled: true
  Horizontal-Knockback: 0.4
  Vertical-Knockback: 0.4
  Attack-Delay: 10

world2:
  Knockback-Enabled: true
  Horizontal-Knockback: 0.3
  Vertical-Knockback: 0.5
  Attack-Delay: 20
