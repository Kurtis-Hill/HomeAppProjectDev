<h1>HomeApp Project</h1>
<h2>Docker Installation</h2>
clone down the project <br>
<code>git@github.com:Kurtis-Hill/HomeAppProjectDev.git</code>
<br>
or
<br>
<code>https://github.com/Kurtis-Hill/HomeAppProjectDev.git</code>

Navigate back to the root of the project directory and run this command<br>
<code>cp .env.example .env && cp .env.test.example .env.test</code>

Now change directory to HomeAppDocker.
<code>cd HomeAppDocker</code>
In this directory run <code>docker-compose up --build</code>

This should have; created all the necessary containers, loaded up the initial database file with an admin user and also loaded the fixtures up for running tests.

<h2>Before we can do anything</h2>
There is a compatibility issue in the JWT refresh token library we are using with Doctrine 3 since doctrine common 3.0, Doctrine\Common\Persistence\ObjectManager became Doctrine\Persistence\ObjectManager
so to be able to get JWT tokens to authenticate you will need to adjust this manually in <code>SymfonyReact/vendor/gesdinet/jwt-refresh-token-bundle/Doctrine/RefreshTokenManager.php</code>
