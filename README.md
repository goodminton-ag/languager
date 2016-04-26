# Languager by Goodminton AG
Languager is a Magento 1.* module that makes the management of multi-language store more efficient and easier than ever. Install the module and start internationalizing!

# Installation
In your composer.json add the following lines:
```
{
    ...
    "require": {
        ...
        "goodminton/languager": "dev-master"
    }
    ...
    "repositories" : [
        ...
        {
            "type": "vcs",
            "url": "https://github.com/goodminton-ag/languager.git"
        },
        ...
    ]
}
```

Then run:
```bash
composer.phar update
```

# Configuration
There are four different configuration pages.
Three pages for the configuration of the behavior of the module, and one for development configuration.

![menu](doc/images/menu.png)