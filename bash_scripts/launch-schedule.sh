#!/bin/bash

phpPath=$(which php)

"$phpPath" ./bin/console schedule:run
