<h1>HomeApp Project</h1>
<h2>Docker Installation</h2>
clone down the project <br>
<code>git@github.com:Kurtis-Hill/HomeAppProjectDev.git</code>
<br>
or
<br>
<code>https://github.com/Kurtis-Hill/HomeAppProjectDev.git</code>

<h2>Docker env</h2>
Run this command from the project root
<code>cp HomeAppDocker/.env.example HomeAppDocker/.env</code>
and then add your own variables to suit your machines configuration
<h2>SSL Setup</h2>
first we need to create; client server and ca-certificates these can be signed or self signed. There are multiple guides online for generating these certs but the naming convention should follow:
<ul>
    <li>ca-cert.pem</li>
    <li>client-cert.pem</li>
    <li>server-cert.pem</li>    
</ul>
you will notice that there is a cacert.pem in the SSL directory - leave it be this is used for downloading packages from github  

navigate to the root of the project directory and then change directory to HomeAppDocker.
In this directory run <code>docker-compose up --build</code>

This should have; created all the necessary containers, loaded up the initial database file with an admin user and also loaded the fixtures up for running tests.

<h2>Before we can do anything</h2>
There is a compatibility issue in the JWT refresh token library we are using with Doctrine 3 since doctrine common 3.0, Doctrine\Common\Persistence\ObjectManager became Doctrine\Persistence\ObjectManager
so to be able to get JWT tokens to authenticate you will need to adjust this manually in <code>SymfonyReact/vendor/gesdinet/jwt-refresh-token-bundle/Doctrine/RefreshTokenManager.php</code>


<h2>Running the front end</h2>
to sign into the front end the admin username is : admin
and the password is : admin1234


when adding a new sensor to the system and updating the current readings use the following format to adjust the reading:
'readingType'Reading
e.g a temperature reading would be temperatureReading

a typical request may look something like this:
<code>
{
    "sensorType": "Dallas",
    "sensorData": [ 
        {
            "sensorName": "Dallas1",
            "currentReadings": {
                "temperatureReading": "12"
        }
        },
        {
            "sensorName": "Dallas1",
            "currentReadings": {
                "temperatureReading": "19"
            }
        }
    ]
}
</code>
