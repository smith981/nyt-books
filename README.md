<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## NYT Books Best Seller Wrapper

### Requirements:
* PHP 8.4
* npm
* Vite

### Getting Started
1. `git clone https://github.com/smith981/nyt-books.git`
2. `cd nyt-books`
3. `composer install`
4. `cp .env.example .env`
5. Register for an API key from the NYT Website:
* Create a New York Times developer account: https://developer.nytimes.com/accounts/create
* Go to create a New App: https://developer.nytimes.com/my-apps/new-app
* Enable the Books API. 
* Create your app.
* Place your API key in your .env file so that line reads`NYT_API_KEY=[your key]`

6. Run `./artisan key:generate` to set up encryption for your app (required)
7. Run `npm install`
8. Run `npm run build`
9. Run `npm run dev` in a separate terminal tab or window (leave it running for the remaining steps)
10. Run `./artisan test` to confirm everything is set up correctly
11. Run `./artisan serve` to start the web interface
12. Go to http://127.0.0.1:8000/web/best-seller-search to use the test form

If you miss a step and see any errors about `missing pipe` or a missing API key, place the key in .env as shown above, restart the server, and try again. 
