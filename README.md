# blog-app
Blog demo exercise containing React frontend, Symfony backend and MySQL database. 

### Frontend
Implemented using React JS. React app is rendered inside a Twig which  the server returns at route 'https://127.0.0.1:8000'. All the routing inside a React app is implemented using React Router.

Webpack Encore is used to bundle JavaScript modules, pre-process CSS & JS and compile and minify assets.

### Backend
Backend is implemented using PHP 8.2.12 and Symfony 6.3.7. Database is MySQL 8. 

To create a database sheme run **php bin/console doctrine:migrations:migrate** (Doctrine ORM and Doctrine-Migrations). 

To populate a database with some initial data run **php bin/console hautelook:fixtures:load** (NelmioAliceBundle - fixtures are defined in folder fixtures).  

Before starting a server run: **yarn install** and **composer install** to install dependancies. Create .env file based on .env.example.

To start a server run: **symfony sever:start**
Now you can access app at https://127.0.0.1:8000.

If app is still not working run **yarn run encore dev** before starting a server.

### Not implemented features

 - Internationalization (translations) - for some parts of Posts and Post components it can be chosen whether they are displayed in Croatian or English, but the content of posts is not translated
 - on new comment / admin gets an email - some code for it is written inside a CommentController.php, but I didn't configure a transport
 - Oauth (Gmail) login - user can log in only with a password
