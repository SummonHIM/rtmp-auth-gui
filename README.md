# rtmp-auth

Custom NGINX RTMP module auth scripts with html gui system

Base on [Sora012](https://github.com/Sora012)/[rtmp-auth](https://github.com/Sora012/rtmp-auth)

# Basic Prerequisites

Requires MySQL/MariaDB, PHP, [NGINX RTMP/FLV Module](https://github.com/winshining/nginx-http-flv-module)

# Readme variable
**{IP}** is your servers IP/Domain

**{port}** is your rtmp module's port

**{app}** is the application in the RTMP section

**{Key}** is the live stream key from the MySQL Database

# Basic Setup

1. Setup a webserver to listen on 127.0.0.1
2. Edit NGINX Configuration RTMP section to contain:
```conf
rtmp {
    server {
        listen **{port}**;

        application **{app}** {
            live on;

            on_publish http://**{IP}**/path/to/auth.php;
            on_publish_done http://**{IP}**/path/to/deauth.php;
            on_play http://**{IP}**/path/to/play.php;
        }
    }
}
```
1. Import MySQL/MariaDB SQL file
2. Edit the 'profile.php' to point to the proper Database information
3. Edit the SQL Database or access the 'index.php' to contain information for a valid key(s) & username(s).

## Warning

The 'profile.php' doesn't have any administrator auth protect. it mean anyone can edit the auth database

But you can use the nginx's auth to protect this page

1. Use OpesSSL to create a nginx user password
   
```shell
printf "{Fill Your Username}:$(openssl passwd -apr1)" >> /path/to/nginx/conf/passwords
```

1. Edit the nginx configure file and add auth_base to where your 'profile.php' in

```conf
location /path/to/profile/ {    
    auth_basic "Protected";
    auth_basic_user_file passwords;
}
```

# OBS

OBS Stream Settings

Server: rtmp://**{IP}**/**{app}**

Stream Key: **{Key}**

## Notes

Don't check "Use Authenication"

**The database should use all lower-case information and keys**

# Play Stream

URL: rtmp://**{IP}**/**{app}**/**{username}**

Private stream URP: rtmp://**{IP}**/**{app}**/**{username}**?key=**{private_key}**