# CuriosityStream Backend Tech Interview Project

Hello, and welcome to our coding "challenge". Get ready to show us your skills!

In this challenge you are given a very small Laravel 5.2 application.
Please improve the existing code base, while keeping the Laravel version fixed at 5.2 and PHP at 7.1.

You will also be given a task to implement new functionality that will make new user sign up easier for both our web frontend developers and backend developers.

Let's first get your environment up and running:

---
## Environment Setup
### Step #1: Clone Repository

Clone the project repository into a folder named `backend-tech-interview`.
(The folder name will make the Docker network matches the examples)

* Please note: The `.env` file has been committed to this project as it should work "as is" in your environment.

### Step #2 Setup Docker
This project uses Docker to setup the development environment.
If you are familiar with `make` you can use `make init` to setup the project.

So run `make init`.

Otherwise, you can run the following commands after cloning the repository:
```
docker-compose build
docker-compose up -d
docker-compose run --rm --volume `pwd`:/app --volume `pwd`/.composer:/.composer --user $(id -u):$(id -g) --workdir /app php-fpm composer --prefer-dist install
docker-compose run --rm --volume `pwd`:/app --workdir /app php-fpm php artisan migrate
```

### Step #3 Run Tests to Make Sure the Environment Works
Run `make test` or the following docker-compose commands:
```
docker-compose run --rm --volume `pwd`:/app --workdir /app php-fpm php artisan config:clear
docker-compose run --rm --volume `pwd`:/app --workdir /app php-fpm vendor/bin/phpunit
```

You should see all the tests pass `OK (7 tests, 10 assertions)`.

### Step #4 Make Sure Docker Environment Works with Http Requests:
Using Postman or your favorite browser hit `GET http://localhost:8080/plans` and you should see the following output (we will discuss the output later):
```json
[{"encoding":"4k","id":"4k_annual","name":"4K Definition Annual","is_annual":true,"price":"$69.99","description":"4K Definition Annual at $69.99 per year"},{"encoding":"4k","id":"4k_monthly","name":"4K Definition","is_annual":false,"price":"$9.99","description":"4K Definition at $9.99 per month"},{"encoding":"hd","id":"hd_annual","name":"High Definition Annual","is_annual":true,"price":"$19.99","description":"High Definition Annual at $19.99 per year"},{"encoding":"hd","id":"hd_monthly","name":"High Definition","is_annual":false,"price":"$2.99","description":"High Definition at $2.99 per month"}]
```

### Step #5 Connecting to the Database:
Connection details:
```
host: 127.0.0.1 (or localhost)
username: apiuser
password: interview
database: api
port: 8081
```

### Step #6 Extraneous `make` Commands

`make refresh` -- runs rollback and migrate to refresh the entire database

`make cache` -- clear the Redis cache

`make bash` -- open up a terminal on a new php-fpm container. Useful for running artisan commands or running PHPUnit with options.

Artisan Example:
```shell script
backend-tech-interview git:(master) make bash
docker-compose run --rm --volume `pwd`:/app --workdir /app php-fpm bash
Creating backend-tech-interview_php-fpm_run ... done

root@1cfff1762dcb:/app# php artisan route:list
+--------+----------+------------------+------+---------------------------------------------+------------+
| Domain | Method   | URI              | Name | Action                                      | Middleware |
+--------+----------+------------------+------+---------------------------------------------+------------+
|        | GET|HEAD | /                |      | Closure                                     |            |
|        | GET|HEAD | coupons/{coupon} |      | App\Http\Controllers\CouponsController@show |            |
|        | GET|HEAD | plans            |      | App\Http\Controllers\PlansController@index  |            |
|        | GET|HEAD | plans/{plan}     |      | App\Http\Controllers\PlansController@show   |            |
+--------+----------+------------------+------+---------------------------------------------+------------+
```

PHPUnit Example:
```shell script
backend-tech-interview git:(master) ✗ make bash
docker-compose run --rm --volume `pwd`:/app --workdir /app php-fpm bash
Creating backend-tech-interview_php-fpm_run ... done
root@df9103845a2b:/app# vendor/bin/phpunit
PHPUnit 4.8.36 by Sebastian Bergmann and contributors.

.......

Time: 3.71 seconds, Memory: 12.00MB

OK (7 tests, 10 assertions)
root@df9103845a2b:/app# 
```

---
# The Challenge:

## Background:

This background will give you a synopsis of the current state of the backend application, which will help you in your coding challenge.

When a user signs up for CuriosityStream they must select either HD or 4K resolution and annual or monthly recurring billing.
We refer to this combination of streaming resolution and billing cycle as a "Plan". Examples: "4K Monthly Plan" or "HD Annual Plan".

Example of user selecting a plan via the web user interface:
![](https://curiositystream-downloads.s3.amazonaws.com/backend-tech-interview/select-plan.png)

Currently, our web application operates as a single page app built using React.js and Next.js.
This view of the "Plans" is populated by the frontend hitting the endpoint `GET http://localhost:8080/plans` and then doing customization and templating based on the response data.
You will find the plans in the `plans` table in the database.
Take note: some of the text on the page is currently hardcoded by the JavaScript frontend.

We sometimes offer discounts to these plans in the form of coupons that users can use to get a discount as an incentive to sign up for our service.

When the user has a coupon they either land on a specific page with the coupon already entered or they enter the coupon into a text input on the web application. Which should update the price.
The frontend consumes a backend API to load coupon details and does calculations in JavaScript to calculate the new price of the plans.
An example endpoint is `GET http://localhost:8080/coupons/halloween2020`

The complexity of coupons has grown over time and now support a number of different customizations when it comes to discounting prices.

All Coupons:
 * Are a unique coupon code forever; AND
 * Are a percentage discount (Examples: 25% off, 40% off, 50% off); AND
 * Can be turned off at any time by business admins (admin_invalidated=true in table) and should no longer be accessible to the outside world
 
Coupons may be any combination of the following:
 * Applied to only annual billing plans
 * Applied to all resolutions, or applied to only a single resolution plan (HD only or 4K only)
 * Have a maximum redemption count (number of users who can redeem the coupon before it is no longer valid)
 * May Expire (must be redeemed by a certain date) or have no expiration

If we look at the example coupon `halloween2020` you will see the output from the backend api `GET http://localhost:8080/coupons/halloween2020` that this coupon is valid on all resolutions and all plans until it expires:
```json
{
    "code": "halloween2020",
    "description": "Save 31% this Halloween!",
    "percent_off": 31,
    "hd": false,
    "4k": false,
    "annual": false,
    "disclaimer": "Use coupon code \"halloween2020\" before Sun, Nov 1, 2020 12:00 PM to get 31% off."
}
```

If we were to look at another example for a coupon worth 25% off for Annual plans `GET http://localhost:8080/coupons/email25` it would result in the user seeing this:
![](https://curiositystream-downloads.s3.amazonaws.com/backend-tech-interview/discounted-annual-plans.png)

----
## Your Mission:

You have been tasked with developing a new feature that will eliminate the need for the frontend to do all of the
calculations for the price of a plan and the applicability of coupons. This would be a feature that you would
develop that would take the input of a coupon and provide to the frontend developers everything that they need to display the price matrix shown above.
The frontend developers really want a single JSON endpoint that they can easily consume to build the template.
If a coupon is no longer valid the user would be presented with full price options.

We've also had a new feature sitting on our back burner and this might be a great chance to implement it at the same time!
We would like to run limited time promotions. We've decided that these will be coupons that are automatically applied
to the price of the applicable plans during the promotion.
They will be coupons so all the rules of coupons will still apply except promotions will never have a max redemption count.

However, we still want to accept other coupons during the promotion, and the discounts would never be compounding.
For example, a coupon with 40% off entered during a promotion with 25% off would result in a 40% off discount, the larger discount being applied. (Not 40% + 25%)

One of our backend developers has already planned out a table and repository for getting the current promotion. Our
sales team has started planning and has populated the table with a New Year's Day promotion. See `promotions` table and `\App\Promotions\PromotionRepository`.

The frontend should not have to know there is a promotion running before hitting your endpoint to get the prices.
But the response should tell them there is an active promotion to further customize the display of the page (they are thinking fireworks!).
And it should tell them via the JSON structure that the coupon has been applied.

So just to clarify, if there is a promotion currently active it should automatically apply the promotion coupon.
If the user enters a coupon then both the promotion coupon and the coupon entered should be applied (with the larger percentage discount given).
And let's assume for now that the user can only enter one coupon. But if there is a promotional coupon that would make 2 coupons be the most that could be applied when getting pricing.

## Frequently Asked Questions

#### Does the user have to select a specific plan after entering a coupon?
No, A user might enter a coupon, but then select a different plan that the coupon does not apply to. 

#### How will the user sign up work?
While sign up is not in the scope of this project. Usually a user would select a plan, give us their name and email address, enter a coupon, and credit card information as part of the sign up. Don't worry we use Stripe :) 
We don't want you to implement this, but for an example you can imagine the future version of the POST for the user sign up to look something like this:
```
curl --location --request POST 'http://localhost:8080/signup' \
--header 'Content-Type: application/json;charset=UTF-8' \
--header 'Accept: application/json, text/plain, */*' \
--header 'Accept-Language: en-US,en;q=0.9' \
--data-raw '{"email":"test@example.com","password":"password","first_name":"First","last_name":"Last","token":"stripeToken","coupon":"halloween2020","plan":"4k_annual"}'
```

#### Can I get a discount?
Sure, next time we run a promotion :)

---
"A job interview is not a test of your knowledge, but your ability to use it at the right time" - Anonymous

