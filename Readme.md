<h1>HomeApp Project</h1>
<h2>Docker Installation</h2>
clone down the project <br>
<code>git@github.com:Kurtis-Hill/HomeAppProjectDev.git</code>
<br>
or
<br>
<code>https://github.com/Kurtis-Hill/HomeAppProjectDev.git</code>

<h2>Docker env</h2>
Run this command from the project root:
<code>cp HomeAppDocker/.env.example HomeAppDocker/.env</code>
<h3>Dev Mode</h3>
After running the command add your own variable values to suit your machines configuration be sure to mark the variable APP_ENV with <b>dev</b>, if you plan on running the container in dev mode then be sure to fill in the variables with test in the name otherwise you can leave them. You will need to also run this command:
<code>cp HomeAppDocker/docker-compose.override.yml.dist HomeAppDocker/docker-compose.override.yml</code>
This will be needed to create the test databases and users 
<h3>Prod Mode</h3>
After running the command add you own variables values but you can exclude the variables with TEST in them. Be sure to mark the variable APP_ENV with <b>prod</b>.

<h2>SSL Setup</h2>
first we need to create; client server and ca-certificates these can be signed or self signed. There are multiple guides online for generating these certs but the naming convention should follow:
<ul>
    <li>ca-cert.pem</li>
    <li>client-cert.pem</li>
    <li>server-cert.pem</li>    
</ul>
you will notice that there is a cacert.pem in the SSL directory - leave it be this is used for downloading packages from github  

<h2>Running the application</h2>
Remember to set the APP_ENV variable in the HomeAppDocker/.env file to <b>prod|dev</b> depending on your intentions.
Once your variables are set run
<code>HomeAppDocker/docker-compose up --build</code>
from the root project directory.

This should have; created all the necessary containers, loaded up the initial database file with an admin user and also loaded the fixtures up for running tests (if in dev mode).

<h2>Running the front end</h2>
to sign in to the front end the admin username is : 
<code>admin</code>
and the password is: 
<code>admin1234</code>