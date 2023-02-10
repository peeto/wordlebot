# wordlebot
A bot that can assist with playing NYT Wordle

## About

The New York Times has a game called Wordle that gives six chances to guess a five letter word. Given that there are 26 letters in the alphabet and there are 30 chances (6x5) this positions a bot to have a fair chance of solving each word. The premise is that wordlebot understaands words, the letters that make up words, a statistical analysis of the most common letters used in words, and as such can determine the best word to try with or without feedback.

## Status and usage

This is currently a work in progress however the SQL is currently working. Run wordle.sql in MySQL and import wordlewords.csv into the 'words' table to get it working.

An example of using it is as follows:

```
select * 
from view_wordle_word_stats
where
word like '%a%'
and word like '%e%'
and word not like '%r%'
and word not like '%o%'
and word not like '%s%'
and word not like '%n%'
and word not like '%t%'
and word not like '%l%'
and l2 = 'e'
and l3 = 'a'
and l4 = 'd'
and l5 = 'y';
```

This can really only be used by someone proficient in SQL however this will change.
