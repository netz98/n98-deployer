# CHANGELOG

## next

- GNU readlink is required
- add readlink_bin config to make custom readlink-bin injectable 
    MacOS has a different readlink and you might have installed gnu-readlink to a different path
- add task to disable magento fpc

## 3.0

- upgrade to deployer 6.0.x:
    - changing behaviour of \Deployer\run which returns string
    - read the deployer CHANGELOG https://github.com/deployphp/deployer/blob/master/UPGRADE.md

## 2.0.2

- Use readlink in favour of realpath #3
- Add ability to dump/backup Magento core_config_data #5

## 2.0.1

- use deployer Task onRoles

## 2.0.0

- add compatibility with deployer 5.0.x
- CleanupTask no longer needs to be overwritten, use cleanup_use_sudo if neded
- use onHosts to registerTask for a specific role
- this version is no longer compatible with deployer 4.x

## 1.3.3

- allow dots in release-names of branches

## 1.3.2

- Clean branch names for release directory: remove special-chars [bd9e563]

## 1.3.1

- N98Magento2Recipe: remove seperate config artifact

## 1.3.0

- add config value for config import dir `config_store_dir`
