@echo off

"@PHP-BIN@" -d html_errors=off -d open_basedir= -q "@PHP-DIR@/limb/cli/bin/limb" %*
