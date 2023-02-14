# WordleBot
A bot that can assist with playing NYT Wordle

## About

The New York Times has a game called Wordle that gives six chances to guess a five letter word. Given that there are 26 letters in the alphabet and there are 30 chances (6x5) this positions a bot to have a fair chance of solving each word. The premise is that wordlebot understaands words, the letters that make up words, a statistical analysis of the most common letters used in words, and as such can determine the best word to try with or without feedback.

## Status and usage

Set up a MySQL database using sql/wordle.sql and sql/wordlewords.sql

Install using composer:
```
composer require peeto/wordle-bot
```

See example.controller.php for an example on how to use WordleBot.
