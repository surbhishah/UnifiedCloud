#Kumo.
This project aims to bring Dropbox , Google drive and SkyDrive under the same umbrella.

#Instructions to future developers

   - app/clouds.json contains an array of names of classes corresponding to clouds
   - They are case sensitive.
   - CloudFactory can instantiate classes using case insensitive cloud names 
   - As a result, in the calls to controller , you may pass cloud names in any case  


##TODO

- Do not forget to uncomment lines RequestUtil.php in Dropbox which checks for 64 bit php.
- Add route group to restrict access to routes.
- sharing folder
- sharing encrypted files
- convert font-family to scss variables
- add dashboard nav button on landing page if user logged in and on nav page. or find alternate solution
- fix autosync ( autosync throw error when files are moved or deleted) 
- add groups to main UI
- add csrf tokens to ajax forms
- Oauth for autosyncer
- make installer for autosyncer
- handle controller generated messages in views
- handle the case of cloud data overflow
- postAddMember in GroupsController - check if user adding the person is admin of the group
- show message to user when shares with no user
##Testing

- Run test code using
``` bash
    $ phpunit
```

- Alternately to see which tests were run
``` bash
    $ phpunit --log-tap testResult.tap && cat testResult.tap
```

- To ignore tests, you can specify the test groups you wish to ignore by following command:
``` bash
    $ phpunit --exclude-group <group-name> 
```

#Ideas
- Refactor models and introduce CloudFactory through dependency injection
and write serviceProvider if necessary for this bindings, because
these bindings need to be executed before routes or controllers are called.
Therefore remove App::bind from routes.php



