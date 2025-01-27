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
* npm (a recent version-- used for the test form)
* Vite (also used for the test form)

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
* Save your key and use it when running tests requests from the form or your client app (cURL, PostMan, etc.)

6. Run `./artisan key:generate` to set up encryption for your app (required)
7. Run `npm install`
8. Run `npm run build`
9. Run `npm run dev` in a separate terminal tab or window (leave it running for the remaining steps)
10. Run `./artisan test` to confirm everything is set up correctly
11. Run `./artisan serve` to start the web interface
12. Go to http://127.0.0.1:8000/web/best-seller-search to use the test form

### Example API Requests
GET http://127.0.0.1:8000/api/1/nyt/best-sellers?apikey=[your_api_key]&isbn=0345545370

```
curl -X GET "http://127.0.0.1:8000/api/1/nyt/best-sellers?apikey=[your_api_key]&author=Sophia%20Amoruso" \
-H "Accept: application/json"
```

GET http://127.0.0.1:8000/api/1/nyt/best-sellers?apikey=[your_api_key]&author=Sophia%20Amoruso

```
curl -X GET "http://127.0.0.1:8000/api/1/nyt/best-sellers?apikey=[your_api_key]&author=Sophia%20Amoruso" \
     -H "Accept: application/json"
```

### Example API Response
```json
{
  "status": "OK",
  "copyright": "Copyright (c) 2025 The New York Times Company.  All Rights Reserved.",
  "num_results": 1,
  "results": [
    {
      "title": "\"MOST BLESSED OF THE PATRIARCHS\"",
      "description": "A character study that attempts to make sense of Jefferson’s contradictions.",
      "contributor": "by Annette Gordon-Reed and Peter S. Onuf",
      "author": "Annette Gordon-Reed and Peter S Onuf",
      "contributor_note": "",
      "price": "0.00",
      "age_group": "",
      "publisher": "Liveright",
      "isbns": [
        {
          "isbn10": "0871404427",
          "isbn13": "9780871404428"
        }
      ],
      "ranks_history": [
        {
          "primary_isbn10": "0871404427",
          "primary_isbn13": "9780871404428",
          "rank": 16,
          "list_name": "Hardcover Nonfiction",
          "display_name": "Hardcover Nonfiction",
          "published_date": "2016-05-01",
          "bestsellers_date": "2016-04-16",
          "weeks_on_list": 1,
          "rank_last_week": 0,
          "asterisk": 1,
          "dagger": 0
        }
      ],
      "reviews": [
        {
          "book_review_link": "",
          "first_chapter_link": "",
          "sunday_review_link": "",
          "article_chapter_link": ""
        }
      ]
    }
  ]
}
```
