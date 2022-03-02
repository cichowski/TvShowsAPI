# TV Shows
Simple application made as a part of recruitment process.
 
## Architecture & dependencies

- It is build on Lumen.
- It uses http://www.tvmaze.com/ API for taking results

## Requirements

Application needs for running:
- php 7.3+ server

Application needs for set up:
- composer

## Installation

1. Download the repository by preffered way

- https: `git clone https://github.com/cichowski/tvshows.git`
- ssh: `git@github.com:cichowski/tvshows.git`
- or just download archive and unzip files
 
2. Run `composer install`

3. Rename `.env.local` to `.env`

4. Change `APP_URL` or even other values in `.env`

## Configuration

Number of results per page returned by API:
- Set `resultsPerPage` in `config/tvshows.php` file

## Usage

API
- address:
    - `your.domain/`
- parameters:
    - `q` - search phrase (required, alphanumerical)
    - `p` - ask for a specific page (positive integer, default: 1)
    - `s` - page size: number o results on single page (positive integer, default: see Configuration)
- examples:
    - `localhost/?q=castle`
    - `json-api.local/?s=5&p=1&q=war`    
       

## Issues

1. For some reason http://www.tvmaze.com/ API right now returns maximum 10 results.
2. Keep in mind that either this application and TV Maze caches every search query for 1 hour.

## ToDo

* Build Swagger documentation
* May needs some sort option in a future
* For many users: maybe cache tv shows in advance 

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
