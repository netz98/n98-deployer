# CHANGELOG

## next

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
