<h1>The Home App Project</h1>
The project has been created to provide a backend and frontend solution to all my home projects and gadgets.
What started off as a small project to provide temperature sensor data to a frontend interface for my local aquatics center has since grown in scope,
the project now provides an interface for all sorts of sensor readings such as humidity, air pressure, latitude and analog sensor data provided through a ADC module that can take readings from soil etc. 

<h2>Backend</h2>

<h3>Server side RESTful API environment</h3>
The language used is PHP within the Symfony framework in an object orientated design and composer, yarn(/npm migrating from) package managers. 
Symfony has been configured with two firewalls the Symfony Guard Authenticator with custom authentication methods built into the system to treat the environment as an API rather than an all in one solution, this firewall secures the front end system only allowing fully authenticated users access to the interface. 
The second fire wall is JWT based authentication system for securing the API endpoints, this system also handles secure refresh tokens, this firewall is used by the front end interface to secure AJAX requests from the server in addition the ESP8266 micro controllers API endpoint is also secured by this firewall acting as one of the layers of security for cross platform data exchange.

<h3>Database</h3>
MariaDB hosted on a personal server that is accessed via https, the database has been sanitized and secured by removing all test users/tables and providing SSL certificates to the the mariaDB client and the project itself in the SSL folder (doctrine.yml DB SSL security), this ensures all queries to the system are made securely and also enables flexibility of having the database server (or backup) away from the hosted production server where the code is.

<h2>Frontend</h2>

<h3>React.js</h3>
The frontend system is built with the React.js library taking advantage of the React router to build a single page app to provide a seamless user experience. Components and context have been used to separate the logic and the interface as some areas of the application need/ will need access to the lifecycle methods. React hooks have been used where appropriate in some areas of the application such as the login form.

<h3>Bootstrap</h3>
Bootstrap 5 has been used to be able to quickly build a user interface that is both responsive and clean,

<h2>ESP-8266-01</h2>
These devices measuring just over 3.5cm long 2.5cm wide are brilliant wifi micro-controllers with a strong 2.4ghz signal, these devices have been developed in C using the Arduino IDE, these devices holding only 1MB of memory means that only efficient well architected code can be uploaded to the devices as they have a lot to do. These devices start in station mode if no/incorrect wifi credentials are detected in the memory and runs a server that the user can connect to which in turn will serve a webpage for the user to input all there device data such as; sensors that are connected, device secrets (as another layer of security), user data, wifi credentials and other options.

The devices have multiple layers of security involved in there data exchange not only do they connected over SSL they also check the fingerprint of the certificate the sensors are about to post to preventing man in the middle attacks, provided with a device secret that the server must verify before performing database transactions, request and respond with a JWT to get through the server API.

Sensors currently supported so far: DS18B20 waterproof temperature sensor, DHT22 temperature and humidity sensor, BMP280 temperature humidity and air pressure sensor and Capacitive Soil Moisture Sensor 


<h2>This project aims <h3>
The current iteration of the project is not yet finished although a working alpha has been successfully working for a few moths but a fully working beta that can be deployed by any user is hoped to be completed before Decemeber 2020.

When the project is finished one of the final steps will be to add the project to a docker container so the project can be brought up and down making it more flexible as a service



<h2>Running the Program</h2>
Master is always kept in a fully functioning conditions, to run the project locally clone down the repo and run these commands at the root of the dicretory;
composer install
npm install (migrating from currently)
yarn install

then running an instance of symfony php server with 'symfony serve'
the login page can be accessed at '/HomeApp/login'
and the user account 'admin' and password on 'HomeApp1234' can be used to see some test data.

To have a visual view of the database phpmyadmin can be accessed on 'https://klh17101990.asuscomm.com/phpmyadmin'
username 'HomeApp' password 'HomeApp1234'