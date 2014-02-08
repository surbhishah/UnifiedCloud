This project aims to bring Dropbox , Google drive and SkyDrive under the same umbrella.

#TODO

- Do not forget to uncomment lines RequestUtil.php in Dropbox which checks for 64 bit php.
- Add route group to restrict access to routes.
- getFolderContents return all data, refactor to limit data.
- sharing folder
- sharing encrypted files
- fix getFolderContents to preserve encryption_key_hash and is_encryption fields in DB.
- convert font-family to scss variables
#Testing

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

