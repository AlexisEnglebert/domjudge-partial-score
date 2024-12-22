<img align="right" width="96px" alt="DOMjudge-logo" src="./doc/logos/DOMjudgelogo-with-white-background.png">

Partial scoring for DOMjudge
--------

This repo was not designed to be fully modulable or compatible with the default scoring of Domjudge. (This is why there is some shortcuts in the code).

## Features
- Partial scoring
- Tests groups
- Showing test results for contestant

## Scoreboard
The ranking rule is as follows:
- First rank by score
- Then by penalties (if tie)
- Then by the last correct submission time
- Then by team name.

## Scoring
Each testgroups contains a `testdata.yaml`. with the following format
```console
name: name
grading:
  score: 0
  aggregation: sum
```
Note: tha aggregation field does nothing for the moment.

Each testcase within the same folder as the testdata.yaml are part of the same test group.

# Eyecandy
tbd