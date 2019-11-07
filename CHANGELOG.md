# CHANGELOG

## 4.3.0

- add task to stop and start the cron daemon (optional)
- add task to clear the magento config cache

## 4.2.0

- fixing an edge-case for multi-server deployments when it comes to creating release-pathes for environments that need the exact same release-path on each server regardless of time-differences by adding the possibility to define a key during the deployment initialising

## 4.1.0

- add task to link cachetool to release (optional)
- fixing an edge-case for multi-server deployments when it comes to creating release-pathes for environments that need the exact same release-path on each server regardless of time-differences

## 4.0.0

- macOS switch added for proper `readlink` options handling
- fix wrong `readlink_bin` default setting datatype
- make the `app_dir` overwriteable in case you use the `N98Magento2Recipe` instead of the default `Magento2Recipe`

## 3.0.1

- GNU readlink is required
- add readlink_bin config to make custom readlink-bin injectable 
    MacOS has a different readlink and you might have installed gnu-readlink to a different path
- add task to disable magento fpc

## 3.0.0

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
