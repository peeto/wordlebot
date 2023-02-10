# wordlebot
A bot that can assist with playing NYT Wordle

## About

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
