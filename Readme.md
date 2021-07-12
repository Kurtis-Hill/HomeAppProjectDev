<h1>HomeApp Project</h1>
<h2>Docker Installation</h2>
clone down the project <br>
<code>git@github.com:Kurtis-Hill/HomeAppProjectDev.git</code>
<br>
or
<br>
<code>https://github.com/Kurtis-Hill/HomeAppProjectDev.git</code>

navigate to the root of the project directory and then change directory to HomeAppDocker.
In this directory run <code>docker-compose up --build</code>

This should have; created all the necessary containers, loaded up the initial database file with an admin user and also loaded the fixtures up for running tests.

<h2>Before we can do anything</h2>
There is a compatibility issue in the JWT refresh token library we are using with Doctrine 3 since doctrine common 3.0, Doctrine\Common\Persistence\ObjectManager became Doctrine\Persistence\ObjectManager
so to be able to get JWT tokens to authenticate you will need to adjust this manually in <code>SymfonyReact/vendor/gesdinet/jwt-refresh-token-bundle/Doctrine/RefreshTokenManager.php</code>
