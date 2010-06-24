@echo off

rem %~dp0 is expanded pathname of the current script under NT
set SCRIPT_DIR=%~dp0

if "%PHP_COMMAND%" == "" (
set PHP_COMMAND=php.exe
)

%PHP_COMMAND% -d html_errors=off -d open_basedir= -q "%SCRIPT_DIR%\limb" %1 %2 %3 %4 %5 %6 %7 %8 %9