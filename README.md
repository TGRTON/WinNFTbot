# WinNFTbot - Telegram Bot

Welcome to the WinNFTbot project! This README is your gateway to creating an amazing Telegram bot.

## Explore More

- **Documentation:** [Explore the docs »](https://github.com/TGRTON/WinNFTbot)
- **Demonstration:** [View Demo](https://github.com/TGRTON/WinNFTbot)
- **Feedback:** [Report Bug](https://github.com/TGRTON/WinNFTbot/issues) | [Request Feature](https://github.com/TGRTON/WinNFTbot/issues)

![Downloads](https://img.shields.io/github/downloads/TGRTON/WinNFTbot/total)
![Contributors](https://img.shields.io/github/contributors/TGRTON/WinNFTbot?color=dark-green)
![Issues](https://img.shields.io/github/issues/TGRTON/WinNFTbot)
![License](https://img.shields.io/github/license/TGRTON/WinNFTbot)

## Table of Contents

- [About the Project](#about-the-project)
- [Built With](#built-with)
- [Getting Started](#getting-started)
    * [Prerequisites](#prerequisites)
    * [Installation](#installation)
- [Usage](#usage)
- [Roadmap](#roadmap)
- [Contributing](#contributing)
- [License](#license)
- [Authors](#authors)
- [Acknowledgements](#acknowledgements)

## About The Project

Embark on a journey to launch your own Telegram bot with ease. WinNFTbot is designed to facilitate seamless communication with an interested audience, enabling participation in NFT raffles and offering a referral program.

## Built With

Crafted using procedural PHP version 7+, this project eschews libraries for simplicity and universality, ensuring compatibility with any PHP and MySQL supporting hosting. This approach also simplifies code editing.

## Getting Started

Follow these steps to set up your project locally.

### Prerequisites

- Hosting supporting PHP 7 and MySQL.

### Installation 

#### Main Executable Script
The core of the bot is the `tgbot.php` script, serving as the main executable.

#### Step 1: Configuring `config.php`
First, tailor the `config.php` file to your specific settings: 

```php
############################
$admin = 00000; // ChatID of the bot's manager or owner
$NFTwallet = "XXXXX"; // TON Wallet for receiving payments
$WinNFTbotRefPercent = 10; // Percentage for the referral program
$XAPIKey = ""; // API Key for Toncenter website
$CryptoPayAPIToken = ""; // CryptoPay API Token
define('TOKEN', 'XXXXX'); // Your Bot's API Token
########### TEGRO DATA ################
$user_id = 0000; // User ID for Tegro Money
$api_key = 'XXX'; // API Key for Tegro Money
$roskassa_publickey = 'XXXX'; // Public Key for Tegro Money
$roskassa_secretkey = 'XXXX'; // Secret Key for Tegro Money
############################
```

#### Step 2: Registering Bot with Cryptopay
Register your bot in Cryptopay by specifying the postback URL for seamless transaction processing.
Postback URL for Cryptopay: [https://yourdomain/BotFolder/postback_cryptopay.php](https://yourdomain/BotFolder/postback_cryptopay.php)

#### Step 3: Configuring Tegro Money Postback URL
Set up the postback URL in your Tegro Money account to handle payments efficiently.
Tegro Money Postback URL: [https://yourdomain/BotFolder/postback.php](https://yourdomain/BotFolder/postback.php)

#### Step 4: MySQL Database Configuration
Fill in your MySQL database details in the `global.php` file to ensure proper data management and storage.

#### Step 5: Importing MySQL Database Structure
Use the `database.sql` file to import the necessary database structure. This step is vital for setting up the bot's database schema.

#### Step 6: Installing the Webhook
Set up the webhook for your bot on the Telegram API. This step is crucial for enabling real-time communication between your bot and Telegram.
Webhook Installation URL: [https://api.telegram.org/botXXXXX/setWebhook?url=https://yourdomain/BotFolder/tgbot.php](https://api.telegram.org/botXXXXX/setWebhook?url=https://yourdomain/BotFolder/tgbot.php)

#### Step 7: Customizing Bot Texts
Edit the `lang.php` file to customize your bot's responses and texts. This allows for a personalized user experience.


## Usage

Search for `@YourBot` in Telegram and start it with `/start`.

## Roadmap

View our [planned features and issues](https://github.com/TGRTON/WinNFTbot/issues).

## Contributing

Your contributions drive the open-source community. Please follow our guidelines:

1. Fork the Project
2. Create a Feature Branch
3. Commit your Changes
4. Push to the Branch
5. Open a Pull Request

Read through the [Code Of Conduct](https://github.com/TGRTON/WinNFTbot/blob/main/CODE_OF_CONDUCT.md) for good practices.

## License

Distributed under the MIT License. See `LICENSE` for more information.

## Authors

- **Lana Cool** - *Developer* - [Profile](https://github.com/lana4cool/)

## Acknowledgements

- Kudos to [Lana](https://github.com/lana4cool/) for the tremendous work.
