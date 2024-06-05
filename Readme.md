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
<code>cp HomeAppDocker/docker-compose.override.yml.example HomeAppDocker/docker-compose.override.yml</code>
This will be needed to create the test databases and users 
<h3>Prod Mode</h3>
After running the command add you own variables values but you can exclude the variables with TEST in them. Be sure to mark the variable APP_ENV with <b>prod</b>.

<h2>SSL Setup</h2>
first we need to create; jwt public & private pem certificates, client & server pem files and ca-certificates these can be signed or self-signed. There are multiple guides online for generating these certs just ensure that the same ca signs both the cline and server certs. Once created places the client, server & ca certificate files in the SSL directory with the following but the naming convention should follow:
<ul>
    <li>ca-cert.pem</li>
    <li>client-cert.pem</li>
    <li>server-cert.pem</li>    
</ul>
you will notice that there is a cacert.pem in the SSL directory - leave it be this is used for downloading packages from github. Now drop the generated <i>public.pem</i> and <i>private.pem</i> jwt certs in the HomeAppProjectDev/app/config/private.pem. Remember if you set a passphrase for these files to update the <code>pass_phrase</code> in the <i>app/config/packages/lexik_jwt_authentication.yaml</i> file (default: HomeApp).

<h3>Elasticsearch</h3>
Elasticsearch is optional for the application at this point - by setting the environment variables to false first one being <b>ELASTIC_ENABLED</b> this can be found in the </i>.env</i> file in the HomeAppDocker folder and the other <b>HEAVY_WRITES_ELASTIC_ONLY</b> this can be found in <i>app/.env</i> and commenting out the Elasticsearch containers in the <i>HomeAppDocker/docker-compose.yml</i> file this will use the Mysql tables.
Although using Elasticsearch has slightly more overhead if you are planning on storing out of range temperatures and/or constantly recorded readings then I advice you enable Elasticsearch as its more efficient.

First we need to copy the .env.example for the certificate container <code>cp HomeAppDocker/elasticsearch/certs/.env.example HomeAppDocker/elasticsearch/certs/.env</code> and then enter the same name youve chosen for the project as the compoer project name, you can leave the other value to its default or change it if you wish its the directory where the certificates will be mounted in the container.
Now the Elasticsearch container needs to generate some certificates for secure communication. To do this run the following command: <code>docker-compose -f HomeAppDocker/elasticsearch/certs/create-certs.yml run --rm create_certs</code>
elastic username is: <code>elastic</code> and the password is set in the .env file

<h2>Running the application</h2>
First copy the auth.json.example file into the same directory <code>cp HomeAppDocker/webservers/auth.json.example HomeAppDocker/webservers/auth.json</code>.
Then generate a git hub access token and replace GIT_TOKEN_HERE with your token remember to keep the " marks.
After that run <code>cp app/.env.example app/.env</code> and proceed to alter the variables to suit your needs.

Remember to set the APP_ENV variable in the HomeAppDocker/.env file to <b>prod|dev</b> depending on your intentions.
Once your variables are set run
<code>docker-compose up --build</code>
from the HomeAppDocker directory.

This should have; created all the necessary containers, loaded up the initial database file with an admin user and also loaded the fixtures up for running tests (if in dev mode).

<h2>Running the front end</h2>
to sign in to the front end the admin username is : 
<code>admin</code>
and the password is: 
<code>admin1234</code>

<h1>Permission System</h1>
Permission system is rather simple, admin users have overview of everything on the system. Users are assigned to groups and devices are registered into these groups. None admin users that are granted access to a group can view/edit/update/delete the devices in that group. 

Rooms can be added by anybody but only devices registered to that users groups will be able to be been.


<h2>Admin account<h2>
Admin account email has to be changed first to a proper email address after that you are free to update any of the other user settings
