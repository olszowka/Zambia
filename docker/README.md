# Docker for Zambia development

## Installation
1. Clone this repo.
1. Get docker. https://www.docker.com/community-edition
2. Make sure you have a working internet connection the first time you try and start things up.

That's it. Really.

## Usage
1. `cd docker` (this folder)
2. `docker-compose up -d`

You'll get 2 docker containers, called `zambia-web` and `zambia-db`, which, unsurprisingly, are the web and db
containers. The first time you do this, you'll need to wait a few minutes for the various images to download and
configure themselves.

Zambia is now running on http://localhost:18080/zambiademo! You can use the badgeid 1 and the password 'demo' to log
in.

In order to get command line interfaces to the different boxes, use the command `docker exec -it zambia-web bash` (or
replace zambia-web with zambia-db). The contents of the scripts folder is in /zambia/scripts on the container. 

### Making things stop
1. `docker-compose stop` (all the docker-compose commands need to be run from the docker folder to work.)

### I messed something up and now the db is in a state I can't get out of help I want to start over

USE THIS WITH CAUTION. IF YOU DO IT CASUALLY, YOU RISK SCREWING UP SOMETHING YOU WANT TO KEEP.
I'd try the scripts sitting in /zambia/scripts first and see if they help you.

1. Navigate to the docker folder (this one).
2. `docker-compose stop`
3. `docker-compose rm` and then type `y` at the prompt.
4. `rm -rf mysql-db` WARNING: THIS WILL BLOW AWAY YOUR DB. LIKE, GONE FOREVER.
5. uncomment the initial db setup lines in the docker-compose.yml file
6. `docker-compose up -d`
7. `docker exec -it zambia-db bash` - this will get you into a command line inside the db container. subsequent commands
   should be performed in that command line.
8. `mysql -u zambiademo -p` and type in the password at the prompt.
9. (inside the mysql session) `use zambiademo;`
10. (also inside the mysql session) `\. /zambia/Install/DemoDbase.dump` (or whichever of the dumps you want to restore.)
11. exit back to your dev machine (should be 2 `exit`s)
12. `docker-compose stop db`
13. `docker-compose rm db` and answer `y` at the prompt
14. recomment out the initial setup lines in docker-compose.yml (everything under that comment)
15. `docker-compose up -d`

### Useful aliases

- `alias dco=docker-compose`
- `alias dbash="docker exec -it $1 bash"`
